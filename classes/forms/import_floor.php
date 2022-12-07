<?php
namespace local_order;

use local_order\base;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');
require_once($CFG->dirroot . '/config.php');

class import_floor extends \moodleform
{

    protected function definition()
    {
        global $DB, $OUTPUT;

        // Create form object
        $mform = &$this->_form;

        $context = \CONTEXT_SYSTEM::instance();

        $mform->addElement('hidden', 'id');
//        $mform->setDefault('id', 1);
        $mform->setType('id', PARAM_INT);

        //Header: General
        $mform->addElement('header', 'request_data', get_string('floors', 'local_order'));

        $samples = $OUTPUT->render_from_template('local_order/import_samples',[]);
        $mform->addElement('html', $samples);
        
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
