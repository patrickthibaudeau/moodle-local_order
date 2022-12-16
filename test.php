<?php

require_once('../../config.php');

use local_order\organization;
// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

 \local_order\base::page($CFG->wwwroot . '/local/cto_co/test.php', 'Test', 'Test', $context);
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();

$ORG = new organization(1);

print_object($ORG->get_name());

//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
