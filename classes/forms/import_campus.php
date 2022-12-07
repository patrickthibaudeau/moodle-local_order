<?php
namespace local_order;

use local_order\base;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');
require_once($CFG->dirroot . '/config.php');

class import_campus extends \moodleform
{

    protected function definition()
    {
        global $DB;

        // Create form object
        $mform = &$this->_form;

        $context = \CONTEXT_SYSTEM::instance();

        $mform->addElement('hidden', 'id');
//        $mform->setDefault('id', 1);
        $mform->setType('id', PARAM_INT);

        //Header: General
        $mform->addElement('header', 'request_data', get_string('campus', 'local_order'));


        // Summary
        $mform->addElement('filepicker', 'campus', get_string('campus', 'local_order'), null, base::get_file_picker_import_ptions($context));
        $mform->addHelpButton('campus', 'campus', 'local_order');
        $mform->setType('campus', PARAM_RAW);
        $mform->addRule('campus', get_string('required_field', 'local_order'), 'required');

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
