<?php

require_once('../../../config.php');
require_once('../lib.php');

use local_order\request;

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

// Get session variables and set defautls
if (isset($_SESSION['date_range'])) {
    $_date_range = $_SESSION['date_range'];
} else {
    $_date_range = date('m/d/Y', strtotime('05/14/2023')) . ' - ' . date('m/d/Y', strtotime('06/10/2023'));
}

if (isset($_SESSION['building'])) {
    $_building = $_SESSION['building'];
} else {
    $_building = -1;
}

if (isset($_SESSION['room'])) {
    $_room = $_SESSION['room'];
} else {
    $_room = -1;
}

if (isset($_SESSION['status'])) {
    $_status = $_SESSION['status'];
} else {
    $_status = -1;
}

if (isset($_SESSION['organization'])) {
    $_organization = $_SESSION['organization'];
} else {
    $_organization = -1;
}

if ($_date_range) {
    $default_date_range = $_date_range;
} else {
    $default_date_range = date('m/d/Y', strtotime('05/14/2023')) . ' - ' . date('m/d/Y', strtotime('06/10/2023'));
}

if ($_status) {
    $default_status = $_status;
} else {
    $default_status = -1;
}

if ($_organization) {
    $default_organization = $_organization;
} else {
    $default_organization = -1;
}

// Get data from query strings
$date_range = optional_param('daterange', $default_date_range, PARAM_TEXT);
$building = optional_param('building',$_building, PARAM_TEXT);
$room = optional_param('room',$_room, PARAM_TEXT);
$status = optional_param('status',$default_status, PARAM_INT);
$organization = optional_param('organization',$default_organization, PARAM_INT);

// Set session variables
$_SESSION['date_range'] = $date_range;
$_SESSION['building'] = $building;
$_SESSION['room'] = $room;
$_SESSION['status'] = $status;
$_SESSION['organization'] = $organization;

\local_order\base::page($CFG->wwwroot . '/local/order/events//index.php',
    get_string('events', 'local_order'),
    get_string('events', 'local_order'),
    $context);

// Load JS
$PAGE->requires->js('/local/order/js/events_dashboard.js', true);
$PAGE->requires->css('/local/order/css/general.css');
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();
local_order_navdrawer_items();
$output = $PAGE->get_renderer('local_order');

$events = new \local_order\output\events_dashboard($date_range, $building, $room, $status, $organization);
echo $output->render_events_dashboard($events);
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
<?php
