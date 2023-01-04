<?php
namespace local_order;

use local_order\event;
use local_order\buildings;

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
            'noselectionstring' => get_string('organization', 'local_order')
        ];
        // Event type options
        $event_type_options = [
            'multiple' => false,
            'ajax' => 'local_order/event_type_selector',
            'noselectionstring' => get_string('event_type', 'local_order')
        ];
        // Get buildings and rooms
        $BUILDINGS = new buildings();
        $buildings = $BUILDINGS->get_buildings_by_campus();
        // Rooms will always be empty. Will dynamically be updated when building is selected
        $rooms = [];

        // event id
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        // Date range
        $mform->addElement('hidden', 'daterange');
        $mform->setType('daterange', PARAM_TEXT);

        $mform->addElement('html', '<div class="container-fluid">');
        $mform->addElement('html', '<div class="row">');
        $mform->addElement('html', '<div class="col-md-6">');
        // Form content for col-md-7
        // Create card
        $mform->addElement('html', '<div class="card">');
        $mform->addElement('html', '<div class="card-body">');

        // Summary
        $mform->addElement('text', 'name', get_string('title', 'local_order'), '');
        $mform->addHelpButton('name', 'title', 'local_order');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required_field', 'local_order'), 'required');
        // code
        $mform->addElement('text', 'code', get_string('code', 'local_order'), ['style' => 'width: 40%;']);
        $mform->setType('code', PARAM_TEXT);
        //Start time
        $mform->addElement('date_time_selector', 'starttime', get_string('start_time', 'local_order'));
        $mform->setType('starttime', PARAM_INT);
        //Start time
        $mform->addElement('date_time_selector', 'endtime', get_string('end_time', 'local_order'));
        $mform->setType('endtime', PARAM_INT);

        //Organization
        $mform->addElement('autocomplete', 'organizationid', get_string('organization', 'local_order'), [], $organization_options);
        $mform->setType('organizationid', PARAM_INT);


        // Event type
        $event_group = [];
        $event_group[] =&  $mform->createElement('autocomplete', 'eventtypeid', '', [], $event_type_options);
        $event_group[] =&  $mform->createElement('html', '<button type="button" 
                                                class="btn btn-link btn-add-event-type" style="margin-top: 1.8rem;">
                                <i class="fa fa-plus"></i></button>');
        $mform->addGroup($event_group, 'event_array', get_string('event_type', 'local_order'),
            array(' '), false);

        $mform->setType('eventtypeid', PARAM_INT);
        // Allow to add an event type if one is not available in the list
        $mform->addElement('text', 'eventtypename', get_string('event_type', 'local_order'));
        $mform->setType('eventtypename', PARAM_TEXT);


        $mform->addElement('selectgroups', 'building', get_string('building', 'local_order'), $buildings);
        $mform->addHelpButton('building', 'building', 'local_order');

        $mform->addElement('select', 'room', get_string('room', 'local_order'), $rooms);
        $mform->setType('room', PARAM_INT);

        $mform->addElement('text', 'attendance', get_string('estimated_attendance', 'local_order'),  ['style' => 'width: 40%;']);
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
        $mform->addElement('html', $OUTPUT->render_from_template('local_order/edit_event_inventory', $formdata) );
        $mform->addElement('html', '</div>'); // End event_inventory_container
        // Edit inventory modal
        $edit_modal = new \stdClass();
        $edit_modal->modal_id = "localOrderEditEvent";
        $edit_modal->class = "modal-xl";
        $edit_modal->title = get_string('edit_items', 'local_order');
        $edit_modal->content = '<div id="local_order_inventory_edit_container"></div>';
        $edit_modal->close_button_name = get_string('close', 'local_order');
        $mform->addElement('html', $OUTPUT->render_from_template('local_order/modal', $edit_modal) );


        $mform->addElement('html', '</div>'); // End div card-body
        $mform->addElement('html', '</div>'); // End div card
        $mform->addElement('html', '</div>'); // End div col-md-6
        $mform->addElement('html', '</div>'); // End div row
        /**
         * Button row
         */
        $mform->addElement('html', '<div class="row mb-5">');
        $mform->addElement('html', '<div class="col">');

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

        $errors = parent::validation($data, $files);

//        if (is_null($data['id'])) {
//            $id = -1;
//        } else {
//            $id = $data['id'];
//        }
//
//        if ($data['yulearncategoryid'] == 0) {
//            $errors['yulearncategoryid'] = get_string('field_required', 'local_yulearn');
//        }
//
//        if ($data['id'] < 1) {
//            $sql = 'SELECT * FROM {yulearn_course} WHERE shortname = "'
//                . trim($data['shortname']) . '" AND '
//                . 'id != ' . $id;
//            if ($foundcourses = $DB->get_records_sql($sql)) {
//
//                if (!empty($foundcourses)) {
//                    foreach ($foundcourses as $foundcourse) {
//                        $foundcoursenames[] = $foundcourse->fullname;
//                    }
//                    $foundcoursenamestring = implode(',', $foundcoursenames);
//                    $errors['shortname'] = get_string('shortnametaken', '', $foundcoursenamestring);
//                }
//            }
//
//            if ($foundMoodleCourse = $DB->get_record('course', ['shortname' => trim($data['shortname'])])) {
//                $errors['shortname'] = get_string('shortnametaken');
//            }
//
//
//            $sql = 'SELECT * FROM {yulearn_course} WHERE externalcode = "'
//                . trim($data['externalcode']) . '" AND '
//                . 'id != ' . $id;
//            if ($foundcourses = $DB->get_records_sql($sql)) {
//
//                if (!empty($foundcourses)) {
//                    foreach ($foundcourses as $foundcourse) {
//                        $foundcoursenames[] = $foundcourse->fullname;
//                    }
//                    $foundcoursenamestring = implode(',', $foundcoursenames);
//                    $errors['externalcode'] = get_string('externalcode_taken', 'local_yulearn', $foundcoursenamestring);
//                }
//            }
//
//            if ($data['hascertificate']) {
//                if (!$data['certificatetemplateid']) {
//                    $errors['certificatetemplateid'] = get_string('required', 'local_yulearn');
//                }
//            }
//        }
//
//        // Certificate notifications
//        for ($i = 0; $i < count($data['rnotificationruleid']); $i++) {
//            if ($data['remailtemplateid'][$i] == 0) {
//                $errors['remailtemplateid'][$i] = get_string('required', 'local_yulearn');
//            }
//        }
//
//        for ($i = 0; $i < count($data['remailtemplateid']); $i++) {
//            if (isset($data['rnotificationruleid'])) {
//                if ($data['rnotificationruleid'][$i] == 0) {
//                    $errors['rnotificationruleid'][$i] = get_string('required', 'local_yulearn');
//                }
//            } else {
//                $errors['rnotificationruleid'][$i]  = get_string('required', 'local_yulearn');
//            }
//        }

        return $errors;
    }

}
