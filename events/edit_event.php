<?php

require_once('../../../config.php');
require_once('../lib.php');
require_once('../classes/forms/event_form.php');

use local_order\event;
use local_order\room;
use local_order\event_type;

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

$date_range = optional_param('daterange',
    date('Y/m/d H:i', round(time() / (15 * 60)) * (15 * 60)) . ' - '
    . date('Y/m/d H:i', round(time() / (15 * 60)) * (15 * 60)), PARAM_TEXT);
$id = optional_param('id', 0, PARAM_INT);

$EVENT = new event($id);

if ($id) {
    $ROOM = new room($EVENT->get_roomid());
    $formdata = $EVENT->get_record();
    $formdata->title = $formdata->name;
    $formdata->daterange = $date_range;
    $formdata->organization = $EVENT->get_organization();
    $formdata->eventtype = $EVENT->get_event_type();
    $formdata->inventory_categories = $EVENT->get_inventory_categories_with_items();
    $formdata->event_total_cost = $EVENT->get_total_cost_of_event();
    $financials = $EVENT->get_organization_budget_codes();
    $formdata->costcentre = $financials->costcentre;
    $formdata->fund = $financials->fund;
    $formdata->activitycode = $financials->activitycode;
//    $formdata->building = $ROOM->get_building_code();
    $formdata->starttime = date('Y/m/d H:i', round($EVENT->get_starttime() / (15 * 60)) * (15 * 60));
    $formdata->endtime = date('Y/m/d H:i', round($EVENT->get_endtime() / (15 * 60)) * (15 * 60));

} else {
    $formdata = new stdClass();
    $formdata->id = 0;
    $formdata->daterange = $date_range;
    $formdata->organization = [];
    $formdata->eventtype = [];
    $formdata->starttime = date('Y/m/d H:i', round(time() / (15 * 60)) * (15 * 60));
    $formdata->endtime = date('Y/m/d H:i', round(time() / (15 * 60)) * (15 * 60));
}

$mform = new \local_order\event_form(null, array('formdata' => $formdata));

if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot . '/local/order/events/index.php?daterange=' . $date_range);
} else if ($data = $mform->get_data()) {

    // Set proper fields
    $data->name = $data->title;
    unset($data->title);
//    if (isset($data->room)) {
//        $data->roomid = $data->room;
//        unset($data->room);
//    }
    $data->starttime = strtotime($data->starttime);
    $data->endtime = strtotime($data->endtime);

    if ($data->eventtypename && !$data->eventtypeid) {
        $EVENTTYPE = new event_type();
        $event_params = new stdClass();
        $event_params->description = $data->eventtypename;
        $event_type_id = $EVENTTYPE->insert_record($event_params);
        $data->eventtypeid = $event_type_id;
        unset($data->eventtypename);
    }


    if ($data->id) {
        $EVENT = new event($data->id);
        if (!has_capability('local/order:event_change_status', $context)) {
            $data->status = $EVENT::STATUS_PENDING;
        }
        // Update status to approved if approved button clicked
        $send_approve_message = false;
        if (isset($data->approvebutton)) {
            $data->status = $EVENT::STATUS_APPROVED;
            $send_approve_message = true;

        }
        $EVENT->update_record($data);
        // Send notification to organizer
        if ($data->status == $EVENT::STATUS_PENDING) {
            $EVENT->send_notification_to_organizer();
            $EVENT->send_notification_to_vendors_event_pending();
        }

        if ($send_approve_message == true) {
            $EVENT->send_notification_to_vendors_event_approved();
        }
        // remove all history for this record
        $EVENT->delete_inventory_history();
        unset($EVENT);
        redirect($CFG->wwwroot . '/local/order/events/index.php?daterange=' . $data->daterange);
    } else {
        $EVENT = new event();
        if (!has_capability('local/order:event_view', $context)) {
            $data->status = $EVENT::STATUS_NEW;
        }
        $event_id = $EVENT->insert_record($data);
        // remove all history for this record
        $EVENT->delete_inventory_history();
        unset($EVENT);
        redirect($CFG->wwwroot . '/local/order/events/edit_event.php?id=' . $event_id . '&daterange=' . $data->daterange);
    }


} else {
    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.
    //Set default data (if any)
    $mform->set_data($mform);
}

\local_order\base::page($CFG->wwwroot . '/local/order/events/edit_event.php?id=' . $id . '&daterange=' . $date_range,
    get_string('event', 'local_order'),
    '',
    $context);

// Load JS
$PAGE->requires->jquery();
$PAGE->requires->jquery('ui');
//$PAGE->requires->js_call_amd('local_order/edit_event', 'init()');
$PAGE->requires->js('/local/order/js/edit_event.js', true);
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
