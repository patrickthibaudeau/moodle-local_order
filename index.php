<?php

require_once('../../config.php');

use local_order\request;

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

 \local_order\base::page($CFG->wwwroot . '/local/cto_co/index.php', get_string('pluginname', 'local_order'), get_string('pluginname', 'local_order'), $context);

 // Load JS
$PAGE->requires->js('/local/order/js/dashboard.js', true);
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();

$output = $PAGE->get_renderer('local_order');
$dashboard = new \local_order\output\dashboard();
echo $output->render_dashboard($dashboard);
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
