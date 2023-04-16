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

ob_start();
$events = $DB->get_records('order_event', ['status' => 1]);

foreach ($events as $e) {
    $data = new stdClass();
    $data->eventid = $e->id;
    $data->usermodified = $USER->id;
    $data->timemodified = time();
    $data->timecreated = time();
    if ($new_id = $DB->insert_record('order_event_inv_status', $data)) {
        \core\notification::success('Status record added');
    }

    $eis = $DB->get_record('order_event_inv_status', ['eventid' => $e->id]);
    $params = new stdClass();
    $params->id = $eis->id;
    $params->av = true;
    $params->catering = true;
    $params->furnishing = true;
    $params->usermodified = $USER->id;
    $params->timemodified = time();
    $DB->update_record('order_event_inv_status', $params);
    \core\notification::success('Event Approved');
    ob_flush();
    flush();
}

//$events = $DB->get_recordset_sql('SELECT *
//        FROM `mdl_order_events_to_sql`
//        WHERE `registrant_id` IN (21236379,
//21313714,
//21313811,
//21313906,
//24585127,
//21367650,
//21313176,
//21313177,
//21313178,
//21313180,
//21313181,
//21313182,
//21313183,
//21313186,
//21313187,
//21313188,
//21313189,
//21313190,
//21313191,
//21313192,
//21313193,
//21313194,
//21313195,
//21313196,
//21313197,
//21313198,
//21313199,
//21313200,
//21313201,
//21313202,
//21313203,
//21313204,
//21313205,
//21313206,
//21313207,
//21313209,
//21313210,
//21313211,
//21313212,
//21313213,
//21313214,
//21313215,
//21313227,
//21313228,
//21313229,
//21313231,
//21313232,
//21313233,
//21313235,
//21313236,
//21313237,
//21313238,
//21313239,
//21313240,
//21313241,
//21313242,
//21313243,
//21313244,
//21313245,
//21313246,
//21313247,
//21313248,
//21313249,
//21313250,
//21313251,
//21313252,
//21313253,
//21313254,
//21313255,
//21313256,
//21313258,
//21313259,
//21313260,
//21313261,
//21313262,
//21313263,
//21313264,
//21313265,
//21313266,
//21313267,
//21313268,
//21313269,
//21313271,
//21313272,
//21313273,
//21313274,
//21313319,
//21313320,
//21313321,
//21313322,
//21313323,
//21313324,
//21313325,
//21313326,
//21313327,
//21313329,
//21313331,
//21313332,
//21313333,
//21313334,
//21313335,
//21313336,
//21313338,
//21313339,
//21313340,
//21313341,
//21313342,
//21313343,
//21313344,
//21313345,
//21313346,
//21313347,
//21313348,
//21313349,
//21313350,
//21313351,
//21313352,
//21313353,
//21313355,
//21313356,
//21313357,
//21313359,
//21313360,
//21313361,
//21313362,
//21313363,
//21313364,
//21313365,
//21313367,
//21313369,
//21313397,
//21313398,
//21313399,
//21313400,
//21313401,
//21313402,
//21313403,
//21313404,
//21313405,
//21313406,
//21313407,
//21313411,
//21313414,
//21313415,
//21313416,
//21313418,
//21313420,
//21313423,
//21313424,
//21313425,
//21313426,
//21313427,
//21313428,
//21313429,
//21313430,
//21313431,
//21313432,
//21313433,
//21313434,
//21313444,
//21313445,
//21313446,
//21313447,
//21313448,
//21313449,
//21313450,
//21313451,
//21313452,
//21313454,
//21313455,
//21313456,
//21313457,
//21313458,
//21313459,
//21313460,
//21313461,
//21313462,
//21313463,
//21313464,
//21313465,
//21313466,
//21313467,
//21313468,
//21313469,
//21313470,
//21313471,
//21313472,
//21313473,
//21313474,
//21313475,
//21313476,
//21313477,
//21313478,
//21313479,
//21313480,
//21313481,
//21313482,
//21313483,
//21313484,
//21313485,
//21313486,
//21313487,
//21313488,
//21313504,
//21313505,
//21313506,
//21313508,
//21313509,
//21313510,
//21313511,
//21313512,
//21313513,
//21313514,
//21313515,
//21313516,
//21313517,
//21313518,
//21313519,
//21313555,
//21313557,
//24663810,
//24663895,
//24664080,
//24664148,
//24664185,
//24664246,
//24664281,
//24664426,
//24664462,
//24664480,
//24699064,
//24793444,
//24793573,
//24793614)');
//foreach($events as $e) {
//    $event = $DB->get_record('order_event', ['code' => $e->registrant_id]);
//    print_object($e->registrant_id);
//    $starttime = strtotime($e->request_date);
//    $endtime = $starttime + (60*60*24) - 1;
//    print_object($starttime . ' = ' . date('m/d/Y H:i', $starttime));
//    print_object($endtime. ' = ' . date('m/d/Y H:i', $endtime));
//    $event_params = new stdClass();
//    $event_params->id = $event->id;
//    $event_params->starttime = $starttime;
//    $event_params->endtime = $endtime;
//
//    $DB->update_record('order_event', $event_params);
//    ob_flush();
//    flush();
//}
ob_clean();
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
