<?php

require_once('../../config.php');
require_once('lib.php');

use local_order\organization;
use local_order\event;
use local_order\buildings;
use local_order\rooms;
use local_order\room_basics;

// CHECK And PREPARE DATA
global $CFG, $OUTPUT, $SESSION, $PAGE, $DB, $COURSE, $USER;

require_login(1, false);
$context = context_system::instance();

\local_order\base::page($CFG->wwwroot . '/local/cto_co/test.php', 'Test', 'Test', $context);
//**************** ******
//*** DISPLAY HEADER ***
//**********************
echo $OUTPUT->header();

$ROOMS = new room_basics();
$BUILDINGS = new buildings();
print_object($ROOMS->get_rooms_for_form());
//print_object($BUILDINGS->get_buildings_by_campus());

//$events = $DB->get_records(TABLE_EVENT, []);
//
//foreach ($events as $e) {
//    $sql = "SELECT * FROM {order_event_type} WHERE description = ?";
//    if ($event_type = $DB->get_record_sql($sql, [$e->eventtype])) {
//        $DB->update_record(TABLE_EVENT, ['id' => $e->id, 'eventtypeid' => $event_type->id]);
//    }
//}


//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>
