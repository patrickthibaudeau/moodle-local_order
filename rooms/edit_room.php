<?php

require_once('../../../config.php');
require_once('../lib.php');
require_once('../classes/forms/room_form.php');


// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

$id = optional_param('id', 0, PARAM_INT);

if ($id) {
    $formdata = $DB->get_record('order_room_basic', ['id' => $id]);

} else {
    $formdata = new stdClass();
    $formdata->id = 0;
}

$mform = new \local_order\room_form(null, array('formdata' => $formdata));

if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot . '/local/order/rooms/index.php');
} else if ($data = $mform->get_data()) {
// Get a room to get the building names
    $sql = "SELECT * FROM {order_room_basic} WHERE building_shortname = ? LIMIT 1";
    $room = $DB->get_record_sql($sql, [$data->building]);
    $data->building_name = $room->building_name;
    $data->building_shortname = $room->building_shortname;
    unset($data->building);

    if ($data->id) {
        $DB->update_record('order_room_basic', $data);
    } else {

        $DB->insert_record('order_room_basic', $data);

    }
    redirect($CFG->wwwroot . '/local/order/rooms/index.php');
} else {
    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.
    //Set default data (if any)
    $mform->set_data($mform);
}

\local_order\base::page($CFG->wwwroot . '/local/order/rooms/edit_room.php?id=' . $id,
    get_string('room', 'local_order'),
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
