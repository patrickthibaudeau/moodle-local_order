<?php
namespace local_order;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');
require_once($CFG->dirroot . '/config.php');

use local_order\room_basics;

class room_form extends \moodleform
{

    protected function definition()
    {
        global $DB;

        $formdata = $this->_customdata['formdata'];
        // Create form object
        $mform = &$this->_form;

        $ROOMS = new room_basics();


        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('html', '<div class="container-fluid">');
        $mform->addElement('html', '<div class="row">');
        $mform->addElement('html', '<div class="col-md-6">');

        //Header: General
        $mform->addElement('header', 'general_data', get_string('general', 'core'));

        // Request for : Building
        $mform->addElement('select', 'building', get_string('building', 'local_order'), $ROOMS->get_buildings_array());
        $mform->setType('building', PARAM_TEXT);
        $mform->addRule('building', get_string('required_field', 'local_order'), 'required');

        // Name
        $mform->addElement('text', 'name', get_string('name', 'local_order'), '');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required_field', 'local_order'), 'required');

        // Description
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
        if ($exists = $DB->get_record('order_room_basic', ['name' => $data['name'], 'building_shortname' => $data['building']])) {
            $errors['name'] = get_string('room_exists', 'local_order');
        }

        return $errors;
    }

}
