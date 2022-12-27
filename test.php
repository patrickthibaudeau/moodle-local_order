<?php

require_once('../../config.php');

use local_order\organization;
use local_order\event;
// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

 \local_order\base::page($CFG->wwwroot . '/local/cto_co/test.php', 'Test', 'Test', $context);
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();

$EVENT = new event(1);
print_object($EVENT->get_inventory_items_by_category(2154)); // 1 2154

//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
