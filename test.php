<?php

require_once('../../config.php');


// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

 \local_order\base::page($CFG->wwwroot . '/local/cto_co/test.php', 'Test', 'Test', $context);
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();

$IMPORT = new \local_order\import('/var/www/html/local/order/sample.xlsx');
print_object($IMPORT->get_rows());
//print_object($IMPORT->clean_column_names());
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
