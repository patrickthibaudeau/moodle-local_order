<?php

namespace local_order;

use local_order\base;
use local_order\inventory_categories;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');
require_once($CFG->dirroot . '/config.php');

class import_form extends \moodleform
{

    protected function definition()
    {
        global $DB, $OUTPUT;

        // Create form object
        $mform = &$this->_form;

        $context = \CONTEXT_SYSTEM::instance();

        $import_types = [
            'inventory' => 'Inventory',
            'organization' => 'Organization',
            'campus' => 'Campus',
            'building' => 'Building',
            'floor' => 'Floor',
            'room_type' => 'Room type',
            'room' => 'Room',
            'event' => 'Event'
        ];

        $timezones = timezone_identifiers_list();
        $timezones_select = [];
        foreach ($timezones as $key => $timezone) {
            $timezones_select[$timezone] = $timezone;
        }
        $samples = $OUTPUT->render_from_template('local_order/import_samples', []);
        $mform->addElement('html', $samples);

        $import = optional_param('import', 'campus', PARAM_TEXT);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'import');
        $mform->setType('import', PARAM_TEXT);
        $mform->setDefault('import', $import);


        //Header: General
        $mform->addElement('header', 'request_data', get_string('import', 'local_order'));
        // Import type select
        $mform->addElement('select', 'import_type', get_string('import_type', 'local_order'), $import_types);
        $mform->setType('import_type', PARAM_TEXT);
        $mform->setDefault('import_type', $import);

        // Add timezone
        $mform->addElement('select', 'timezone', get_string('timezone', 'core'), $timezones_select);
        $mform->setDefault('timezone', date_default_timezone_get());

        if ($import == 'event') {
            $mform->addRule('timezone', get_string('required_field', 'local_order'), 'required');
        }

        // Add inventory category
        $INVENTORY_CATEGORIES = new \local_order\inventory_categories();
        $inventory_categories = $INVENTORY_CATEGORIES->get_select_array();
        $mform->addElement('select', 'inventory_category', get_string('inventory_category', 'local_order'), $inventory_categories);
        $mform->setType('inventory_category', PARAM_INT);
        if ($import == 'inventory' || $import == 'event') {
            $mform->addRule('inventory_category', get_string('required_field', 'local_order'), 'required');
        }
        $mform->addHelpButton('inventory_category', 'inventory_category', 'local_order');


        // Instructiions for AV inventory upload
        $inventory_import = $OUTPUT->render_from_template('local_order/inventory_import', []);
        $mform->addElement('html', $inventory_import);

        if ($import == 'organization') {
            $organization_import = $OUTPUT->render_from_template('local_order/organization_import', []);
            $mform->addElement('html', $organization_import);
        }


        // Summary
        $mform->addElement('filepicker', 'file', get_string('file', 'local_order'), null, base::get_file_picker_import_ptions($context));
        $mform->addHelpButton('file', 'file', 'local_order');
        $mform->setType('file', PARAM_RAW);
        $mform->addRule('file', get_string('required_field', 'local_order'), 'required');

        $this->add_action_buttons();
    }

    // Perform some extra moodle validation
    public function validation($data, $files)
    {
        global $DB;

        $errors = parent::validation($data, $files);


        return $errors;
    }

}
