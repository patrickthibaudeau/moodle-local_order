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

\local_order\base::page($CFG->wwwroot . '/local/order/reports/event_cost_by_org.php',
    get_string('report_detail_event_cost_by_org', 'local_order'),
    get_string('report_detail_event_cost_by_org', 'local_order'),
    $context);

// Load JS
$PAGE->requires->js('/local/order/js/report_detail_event_cost_by_org.js', true);
$PAGE->requires->css('/local/order/css/general.css');
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();
local_order_navdrawer_items();
$output = $PAGE->get_renderer('local_order');

$report = new \local_order\output\report_event_cost_by_org();
echo $output->render_report_event_cost_by_org($report);
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
<?php
