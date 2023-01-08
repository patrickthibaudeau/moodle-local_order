<?php

require_once('../../../config.php');
require_once('../lib.php');
require_once('../classes/forms/inventory_form.php');

use local_order\inventory;

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

$id = optional_param('id', 0, PARAM_INT);

$INVENTORY = new inventory($id);

if ($id) {
    $formdata = $INVENTORY->get_record();
} else {
    $formdata = new stdClass();
    $formdata->id = 0;
    $formdata->cost = 0.00;
}

$mform = new \local_order\inventory_form(null, array('formdata' => $formdata));

if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot . '/local/order/inventory/index.php');
} else if ($data = $mform->get_data()) {

    if ($data->id) {
        $INVENTORY->update_record($data);
    } else {
        $INVENTORY->insert_record($data);
    }

    redirect($CFG->wwwroot . '/local/order/inventory/index.php');
} else {
    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.
    //Set default data (if any)
    $mform->set_data($mform);
}

\local_order\base::page($CFG->wwwroot . '/local/order/inventory/edit_inventory.php?id=' . $id,
    get_string('inventory', 'local_order'),
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
