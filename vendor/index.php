<?php

require_once('../../../config.php');
require_once('../lib.php');

use local_order\vendors;

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();


\local_order\base::page($CFG->wwwroot . '/local/order/vendor/index.php',
    get_string('vendors', 'local_order'),
    get_string('vendors', 'local_order'),
    $context);

// Load JS
$PAGE->requires->js('/local/order/js/vendors_dashboard.js', true);
$PAGE->requires->css('/local/order/css/general.css');
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();
local_order_navdrawer_items();
$output = $PAGE->get_renderer('local_order');
$vendors = new \local_order\output\vendors_dashboard();
echo $output->render_vendors_dashboard($vendors);
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
<?php
