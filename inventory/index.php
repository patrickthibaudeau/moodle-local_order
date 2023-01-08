<?php

require_once('../../../config.php');
require_once('../lib.php');

use local_order\inventory;

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

$category_id = optional_param('category', 0, PARAM_INT);

\local_order\base::page($CFG->wwwroot . '/local/order/events//index.php',
    get_string('inventory', 'local_order'),
    get_string('inventory', 'local_order'),
    $context);

// Load JS
$PAGE->requires->js('/local/order/js/inventory_dashboard.js', true);
$PAGE->requires->css('/local/order/css/general.css');
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();
local_order_navdrawer_items();
$output = $PAGE->get_renderer('local_order');
$inventory = new \local_order\output\inventory_dashboard($category_id);
echo $output->render_inventory_dashboard($inventory);
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
<?php
