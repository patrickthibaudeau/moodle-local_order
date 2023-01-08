<?php
namespace local_order;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');
require_once($CFG->dirroot . '/config.php');

use local_order\inventory_categories;

class inventory_form extends \moodleform
{

    protected function definition()
    {
        global $DB;

        $formdata = $this->_customdata['formdata'];
        // Create form object
        $mform = &$this->_form;

        $INVENTORY_CATEGORIES = new inventory_categories();
        $categories = $INVENTORY_CATEGORIES->get_select_array(true);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('html', '<div class="container-fluid">');
        $mform->addElement('html', '<div class="row">');
        $mform->addElement('html', '<div class="col-md-6">');

        //Header: General
        $mform->addElement('header', 'general_data', get_string('general', 'core'));

        // Request for : userid
        $mform->addElement('select', 'inventorycategoryid', get_string('category', 'local_order'), $categories);
        $mform->setType('inventorycategoryid', PARAM_INT);
        $mform->addRule('inventorycategoryid', get_string('required_field', 'local_order'), 'required');

        // Name
        $mform->addElement('text', 'name', get_string('name', 'local_order'), '');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required_field', 'local_order'), 'required');

        // Code
        $mform->addElement('text', 'code', get_string('shortname', 'core'), '');
        $mform->setType('code', PARAM_TEXT);

        // Cost
        $mform->addElement('text', 'cost', get_string('cost', 'local_order'), '');
        $mform->setType('cost', PARAM_FLOAT);


        $this->add_action_buttons();

        $mform->addElement('html', '</div>'); //col-md-6"
        $mform->addElement('html', '</div>'); //row
        $mform->addElement('html', '</div>'); //container-fluid


        $this->set_data($formdata);
    }

    // Perform some extra moodle validation
    public function validation($data, $files)
    {
        global $DB;

        $errors = parent::validation($data, $files);

        if ($data['inventorycategoryid'] == 0) {
            $errors['inventorycategoryid'] = get_string('field_required', 'local_order');
        }

        return $errors;
    }

}
