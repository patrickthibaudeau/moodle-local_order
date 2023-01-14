<?php

require_once('../../../config.php');
require_once('../lib.php');
require_once('../classes/forms/vendor_form.php');

use local_order\vendor;

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

$id = optional_param('id', 0, PARAM_INT);

$VENDOR = new vendor($id);

if ($id) {
    $formdata = $VENDOR->get_record();
    $formdata->contact = $VENDOR->get_contact_user();
} else {
    $formdata = new stdClass();
    $formdata->id = 0;
    $formdata->contact = '';
}

$mform = new \local_order\vendor_form(null, array('formdata' => $formdata));

if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot . '/local/order/vendor/index.php');
} else if ($data = $mform->get_data()) {

    $contact = new stdClass();
    $contact->userid = $data->contact;

    if ($data->id) {
        $VENDOR->update_record($data);
        $contact->vendorid = $data->id;
        $VENDOR->update_contact_record($contact);

    } else {
        $new_id = $VENDOR->insert_record($data);
        $contact->vendorid = $new_id;
        $VENDOR->insert_contact_record($contact);
    }

    redirect($CFG->wwwroot . '/local/order/vendor/index.php');
} else {
    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.
    //Set default data (if any)
    $mform->set_data($mform);
}

\local_order\base::page($CFG->wwwroot . '/local/order/vendor/edit_vendor.php?id=' . $id,
    get_string('vendor', 'local_order'),
    '',
    $context);

// Load JS
$PAGE->requires->css('/local/order/css/general.css');
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();
local_order_navdrawer_items();

$mform->display();
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();
