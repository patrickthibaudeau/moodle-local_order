<?php

require_once('../../../config.php');
require_once('../lib.php');

use local_order\request;

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

$default_date_range = date('m/d/Y', strtotime('05/14/2023')) . ' - ' . date('m/d/Y', strtotime('06/10/2023'));

$date_range = optional_param('daterange', $default_date_range, PARAM_TEXT);
$building = optional_param('building',null, PARAM_TEXT);
$room = optional_param('room',null, PARAM_TEXT);
$status = optional_param('status',-1, PARAM_INT);
$organization = optional_param('organization',-1, PARAM_INT);

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
