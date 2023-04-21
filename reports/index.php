<?php

require_once('../../../config.php');
require_once('../lib.php');

use local_order\request;

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

if (has_capability('local/order:reports_view', $context)) {
    $can_edit = true;
}

\local_order\base::page($CFG->wwwroot . '/local/order/reports/index.php',
    get_string('reports', 'local_order'),
    get_string('reports', 'local_order'),
    $context);

// Load JS
//$PAGE->requires->js('/local/order/js/rooms_dashboard.js', true);
$PAGE->requires->css('/local/order/css/general.css');
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();
local_order_navdrawer_items();
$output = $PAGE->get_renderer('local_order');

$reports = new \local_order\output\reports_dashboard();
echo $output->render_reports_dashboard($reports);
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
<?php
