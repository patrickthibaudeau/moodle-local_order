<?php

namespace local_order;

use local_order\event;
use local_order\buildings;
use local_order\rooms;
use local_order\inventory_categories;
use local_order\event_types;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');
require_once($CFG->dirroot . '/config.php');

class event_form extends \moodleform
{

    protected function definition()
    {
        global $DB, $OUTPUT;

        $formdata = $this->_customdata['formdata'];
        // Create form object
        $mform = &$this->_form;

        $context = \context_system::instance();

        // Prepare data for select menu options
        // User menu
        $user_options = [
            'multiple' => false,
            'ajax' => 'local_order/user_selector',
            'noselectionstring' => get_string('user')
        ];
        // Organization options
        $organization_options = [
            'multiple' => false,
            'ajax' => 'local_order/organization_selector',
            'noselectionstring' => get_string('select', 'local_order')
        ];

        $organization_array = [];
        if (isset($formdata->organization['id'])) {
            $organization_array[$formdata->organization['id']] = $formdata->organization['name'];
        }

        $EVENT_TYPES = new event_types();
        $event_types = $EVENT_TYPES->get_select_array();

        // Get buildings and rooms
        $BUILDINGS = new buildings();
        $buildings = $BUILDINGS->get_buildings_by_campus();
        // Rooms empty unless a room id exists. Otherwise, will dynamically be updated when building is selected
        $rooms = [];
        $ROOMS = new rooms();
        if (isset($formdata->roomid)) {
            if ($formdata->roomid) {
                $rooms = $ROOMS->get_rooms_by_building_floor($formdata->building);
            }
        } else {
            $rooms = $ROOMS->get_select_array();
        }

        // Get inventory categories for pdf buttons
        $INVENTORY_CATEGORIES = new inventory_categories();
        $inventory_categories_records = $INVENTORY_CATEGORIES->get_records();
        $inventory_categories = [];
        $i = 0;
        foreach ($inventory_categories_records as $icr) {
            $inventory_categories[$i]['id'] = $icr->id;
            $inventory_categories[$i]['name'] = $icr->name;
            $inventory_categories[$i]['code'] = $icr->code;
            $i++;
        }

        $pdf_buttons = [
            'inventory_categories' => $inventory_categories
        ];

        $status_array = [
            0 => get_string('status_new', 'local_order'),
            1 => get_string('status_approved', 'local_order'),
            2 => get_string('status_pending', 'local_order'),
            3 => get_string('status_cancelled', 'local_order'),
        ];

        // event id
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        // Date range
        $mform->addElement('hidden', 'daterange');
        $mform->setType('daterange', PARAM_TEXT);

        //Status display
        $status_display = '';
        switch($formdata->status) {
            case 0:
                $status_display = '<div class="alert alert-info w-100 d-flex justify-content-center">'
                    . get_string('status_new', 'local_order') . '</div>';
                break;
            case 1:
                $status_display = '<div class="alert alert-success w-100 d-flex justify-content-center">'
                    . get_string('status_approved', 'local_order') . '</div>';
                break;
            case 2:
                $status_display = '<div class="alert alert-warning w-100 d-flex justify-content-center">'
                    . get_string('status_pending', 'local_order') . '</div>';
                break;
            case 3:
                $status_display = '<div class="alert alert-danger w-100 d-flex justify-content-center">'
                    . get_string('status_cancelled', 'local_order') . '</div>';
                break;
        }

        $mform->addElement('html', '<div class="container-fluid">');
        /**
         * Button row
         */
        $mform->addElement('html', '<div class="row">');
        $mform->addElement('html', '<div class="col d-flex justify-content-start">');
        $mform->addElement('html', '<span style="font-size: 1.5rem; font-weight:500;">'
            . get_string('event', 'local_order')
            . '</span>'      );
        $mform->addElement('html', '</div>');
        $mform->addElement('html', '<div class="col d-flex justify-content-center">');
        $mform->addElement('html', $status_display);
        $mform->addElement('html', '</div>');
        $mform->addElement('html', '<div class="col d-flex justify-content-end">');

        $buttonarray = array();
        $buttonarray[] = $mform->createElement('html', $OUTPUT->render_from_template('local_order/pdf_buttons', $pdf_buttons));
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);

        $mform->addElement('html', '</div>'); // End button col
        $mform->addElement('html', '</div>'); // End button row

        $mform->addElement('html', '<div class="row">');
        $mform->addElement('html', '<div class="col-md-6">');
        // Form content for col-md-6
        // Create card
        $mform->addElement('html', '<div class="card">');
        $mform->addElement('html', '<div id="local-order-main-container" class="card-body">');

        // Name
        $mform->addElement('text', 'title', get_string('title', 'local_order'), ['style' => 'width: 100%;']);
        $mform->addHelpButton('title', 'title', 'local_order');
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', get_string('required_field', 'local_order'), 'required');
        // code
        $mform->addElement('text', 'code', get_string('event_code', 'local_order'), ['style' => 'width: 40%;']);
        $mform->setType('code', PARAM_TEXT);
        if (has_capability('local/order:event_view', $context)) {
            // Status
            $mform->addElement('select', 'status', get_string('status', 'local_order'), $status_array);
            $mform->setType('status', PARAM_INT);
        }
        //Start time
        $mform->addElement('text', 'starttime', get_string('start_time', 'local_order'));
        $mform->setType('starttime', PARAM_TEXT);
        //Start time
        $mform->addElement('text', 'endtime', get_string('end_time', 'local_order'));
        $mform->setType('endtime', PARAM_TEXT);

        //Organization
        $mform->addElement('autocomplete', 'organizationid', get_string('organization', 'local_order'),
            $organization_array, $organization_options);
        $mform->setType('organizationid', PARAM_INT);


        // Event type
        $mform->addElement('select', 'eventtypeid', get_string('event_type', 'local_order'), $event_types);
        $mform->setType('eventtypeid', PARAM_INT);

        // Allow to add an event type if one is not available in the list
        $mform->addElement('hidden', 'eventtypename');
        $mform->setType('eventtypename', PARAM_TEXT);


        $mform->addElement('selectgroups', 'building', get_string('building', 'local_order'), $buildings);
        $mform->addHelpButton('building', 'building', 'local_order');

        $mform->addElement('select', 'room', get_string('room', 'local_order'), $rooms);
        $mform->setType('room', PARAM_INT);

        $mform->addElement('text', 'attendance', get_string('estimated_attendance', 'local_order'), ['style' => 'width: 40%;']);
        $mform->setType('attendance', PARAM_TEXT);


        $mform->addElement('html', '</div>'); // End div card-body
        $mform->addElement('html', '</div>'); // End div card
        $mform->addElement('html', '</div>'); // End div col-md-6

        $mform->addElement('html', '<div class="col-md-6">');
        // Form content for col-md-5
        // Create card
        $mform->addElement('html', '<div class="card">');
        $mform->addElement('html', '<div class="card-body">');

        // Print each inventory categories
        $mform->addElement('html', '<div id="event_inventory_container">');
        $mform->addElement('html', $OUTPUT->render_from_template('local_order/edit_event_inventory', $formdata));
        $mform->addElement('html', '</div>'); // End event_inventory_container

        // Work order
        $mform->addElement('text', 'workorder', get_string('work_order', 'local_order'));
        $mform->setType('workorder', PARAM_TEXT);

        // Charge back Account
        $mform->addElement('text', 'chargebackaccount', get_string('chargeback_account', 'local_order'));
        $mform->setType('chargebackaccount', PARAM_TEXT);

        $mform->addElement('textarea', 'setupnotes', get_string('setup_notes', 'local_order'),
            'wrap="virtual" rows="9"');

        $mform->addElement('textarea', 'othernotes', get_string('other_notes', 'local_order'),
            'wrap="virtual" rows="9"');

        // Edit inventory modal
        $edit_modal = new \stdClass();
        $edit_modal->modal_id = "localOrderEditEvent";
        $edit_modal->class = "modal-xl";
        $edit_modal->title = get_string('edit_items', 'local_order');
        $edit_modal->content = $OUTPUT->render_from_template('local_order/event_inventory_form', []);
        $edit_modal->close_button_name = get_string('close', 'local_order');
        $edit_modal->action_button_name = get_string('save', 'local_order');
        $edit_modal->action_button = 'event-inventory-item-save';
        $mform->addElement('html', $OUTPUT->render_from_template('local_order/modal', $edit_modal));


        $mform->addElement('html', '</div>'); // End div card-body
        $mform->addElement('html', '</div>'); // End div card
        $mform->addElement('html', '</div>'); // End div col-md-6
        $mform->addElement('html', '</div>'); // End div row
        /**
         * Button row
         */
        $mform->addElement('html', '<div class="row mb-5">');
        $mform->addElement('html', '<div class="col d-flex justify-content-end">');

        $this->add_action_buttons();

        $mform->addElement('html', '</div>'); // End button col
        $mform->addElement('html', '</div>'); // End button row

        $mform->addElement('html', '</div>'); // End container-fluid

        $this->set_data($formdata);

    }

    // Perform some extra moodle validation
    public function validation($data, $files)
    {
        global $DB;

//        $errors = parent::validation($data, $files);


//        return $errors;
    }

}
