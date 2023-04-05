<?php
namespace local_order;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');
require_once($CFG->dirroot . '/config.php');

class vendor_form extends \moodleform
{

    protected function definition()
    {
        global $DB, $PAGE;

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
        $user = $DB->get_record('user', ['id' => $formdata->contact]);
        $user_array = [];
        if ($formdata->contact) {
            $user_array[$user->id] = fullname($user) . " ($user->email)";
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('html', '<div class="container-fluid">');
        $mform->addElement('html', '<div class="row">');
        $mform->addElement('html', '<div class="col-md-6">');
        $mform->addElement('html', '<div class="card">');
        $mform->addElement('html', '<div class="card-body">');

        //Header: General
        $mform->addElement('header', 'general_data', get_string('general', 'core'));

        // Name
        $mform->addElement('text', 'name', get_string('name', 'local_order'), '');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required_field', 'local_order'), 'required');

        // Code
        $mform->addElement('text', 'code', get_string('code', 'local_order'), '');
        $mform->setType('code', PARAM_TEXT);

        // description
        $mform->addElement('textarea', 'description', get_string('description', 'local_order'),
            'wrap="virtual" rows="10"');
        $mform->setType('description', PARAM_TEXT);

        //Contact
        $mform->addElement('autocomplete', 'contact', get_string('contact', 'local_order'),
            $user_array, $user_options);
        $mform->setType('contact', PARAM_INT);

        // Email
        $mform->addElement('text', 'email', get_string('email', 'core'));
        $mform->setType('email', PARAM_TEXT);

        // Email
        $mform->addElement('text', 'phone', get_string('phone', 'core'));
        $mform->setType('phone', PARAM_TEXT);

        $this->add_action_buttons();

        $mform->addElement('html', '</div>'); //card-body"
        $mform->addElement('html', '</div>'); //card"
        $mform->addElement('html', '</div>'); //col-md-6"
        // Right column
        $mform->addElement('html', '<div class="col-md-6">');
        $mform->addElement('html', '<div class="card">');
        $mform->addElement('html', '<div class="card-body">');
        $output = $PAGE->get_renderer('local_order');
        $vendor_contacts = new \local_order\output\vendor_contacts($formdata->id);

        $mform->addElement('html', $output->render_vendor_contacts($vendor_contacts));  //vendor_contacts

        $mform->addElement('html', '</div>'); // card-body
        $mform->addElement('html', '</div>'); // card
        $mform->addElement('html', '</div>'); // col-md-6
        // Right column end
        $mform->addElement('html', '</div>'); //row
        $mform->addElement('html', '</div>'); //container-fluid


        $this->set_data($formdata);
    }

    // Perform some extra moodle validation
    public function validation($data, $files)
    {
        global $DB;

        $errors = parent::validation($data, $files);

        return $errors;
    }

}
