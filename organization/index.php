<?php

require_once('../../../config.php');
require_once('../lib.php');

use local_order\organizations;

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();


\local_order\base::page($CFG->wwwroot . '/local/order/events//index.php',
    get_string('organizations', 'local_order'),
    get_string('organizations', 'local_order'),
    $context);

// Load JS
$PAGE->requires->js('/local/order/js/organizations_dashboard.js', true);
$PAGE->requires->css('/local/order/css/general.css');
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();
local_order_navdrawer_items();
$output = $PAGE->get_renderer('local_order');
$organizations = new \local_order\output\organizations_dashboard();
echo $output->render_organizations_dashboard($organizations);
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
<?php
