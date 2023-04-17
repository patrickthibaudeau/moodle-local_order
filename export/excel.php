<?php
include_once('../../../config.php');

use local_order\events;
use local_order\event;

global $DB, $CFG, $PAGE, $OUTPUT, $USER;


$context = context_system::instance();

$PAGE->set_context($context);

$id = optional_param('id', 0, PARAM_INT);
$inventory_category_id = optional_param('icid', 0, PARAM_INT);
$daterange = optional_param('daterange', date('m/d/Y - m/d/Y', time()), PARAM_TEXT); // event id
$building = optional_param('building', '', PARAM_TEXT);
$room = optional_param('room', '', PARAM_TEXT);
$status = optional_param('status', -1, PARAM_INT);
$organization = optional_param('organization', -1, PARAM_INT);
$filename = 'data_export.csv';
$columns = set_columns();
// tell the browser it's going to be a csv file
header('Content-Type: text/csv');
// tell the browser we want to save it instead of displaying it
header('Content-Disposition: attachment; filename="'.$filename.'";');
// Open a file for writing

$fp = fopen('php://output', 'w');
// Put column names first
fputcsv($fp, $columns);

if ($id) {
    $EVENT = new event($id);

    // Get event data
    $data = $EVENT->get_data_for_pdf($inventory_category_id);
    // Prepare data for CSV row
    $event_data = prepare_data_single_event($data, $columns);
// Write each row of data to the file
    fputcsv($fp, $event_data);
    unset($EVENT);

} else {
    // Export all events based on date range
    $EVENTS = new events();
    $events = $EVENTS->get_event_ids_by_daterange($daterange, $building, $room, $status, $organization);
    // loop through events
    foreach ($events as $e) {
        $EVENT = new event($e->id);
        // get event inventory category
        // Get event data
        $data = $EVENT->get_data_for_pdf($inventory_category_id);
        // Prepare data for CSV row
        $event_data = prepare_data_single_event($data, $columns);
        fputcsv($fp, $event_data);
        unset($EVENT);
    }
}

// Close the file
fclose($fp);

function set_columns()
{
    global $DB;

// Get all inventory items as columns by category
    $av_inventory = $DB->get_records('order_inventory', ['inventorycategoryid' => 1]);
    $catering_inventory = $DB->get_records('order_inventory', ['inventorycategoryid' => 2], 'name DESC');
    $furnishing_inventory = $DB->get_records('order_inventory', ['inventorycategoryid' => 3]);

    $columns = array();
    $columns[] = 'registration_id';
    $columns[] = 'association';
    $columns[] = 'event_title';
    $columns[] = 'date';
    $columns[] = 'start_time';
    $columns[] = 'end__time';
    $columns[] = 'room';
    $columns[] = 'setup_type';
    $columns[] = 'setup_notes';
    $columns[] = 'other_notes';
    // Furnishing
    foreach ($furnishing_inventory as $fi) {
        $columns[] = $fi->name;
    }
    // AV
    foreach ($av_inventory as $ai) {
        $columns[] = $ai->name;
    }
    // Catering
    // For catering we must repeat for each meal
    // Posisble numebr of meals is 4
    // Meal 1
    foreach ($catering_inventory as $ci) {
        $columns[] = $ci->name;
    }
    // Meal 2
    foreach ($catering_inventory as $ci) {
        $columns[] = str_replace('1-', '2-', $ci->name);
    }
    // Meal 3
    foreach ($catering_inventory as $ci) {
        $columns[] = str_replace('1-', '3-', $ci->name);
    }
    // Meal 4
    foreach ($catering_inventory as $ci) {
        $columns[] = str_replace('1-', '4-', $ci->name);
    }

    $columns[] = 'cost';
    $columns[] = 'chargebackaccount';
    return $columns;
}

function prepare_data_single_event($event_data, $columns)
{
    $data = [];

    $furnishing = @$event_data->inventory_items[2]->items[0]['items'];
    $av = @$event_data->inventory_items[0]->items[0]['items'];
    $catering = @$event_data->inventory_items[1]->items;

//print_object($event_data);
    $data[] = $event_data->code;
    $data[] = $event_data->organization->name;
    $data[] = $event_data->name;
    $data[] = $event_data->date;
    $data[] = $event_data->start_time;
    $data[] = $event_data->end_time;
    $data[] = $event_data->room;
    $data[] = $event_data->setup_type;
    $data[] = $event_data->setup_notes;
    $data[] = $event_data->other_notes;

    for ($i = 10; $i < count($columns); $i++) {
        if (!isset($data[$i])) {
            $data[$i] = '';
        }
    }

    $data[429] = $event_data->cost;
    $data[430] = $event_data->organization->costcentre . '-'
        . $event_data->organization->fund . '-'
        . $event_data->organization->activitycode;

    // Get furnishing Data
    if (isset($furnishing)) {
        foreach ($furnishing as $f) {
            $key = array_search(trim($f->name), $columns);
            $data[$key] = trim($f->quantity . ' ' . $f->description);
        }
    }


    //Get AV Data
    if (isset($av)) {
        foreach ($av as $a) {
            $key = array_search(trim($a->name), $columns);
            $data[$key] = trim($a->quantity . ' ' . $a->description);
        }
    }

    if (isset($catering)) {
        foreach ($catering as $section_key => $section) {
            $section_key = $section_key + 1;
            $items = $section['items'];
            foreach ($items as $item_key => $c) {
                $name = str_replace('1-', $section_key . '-', $c->name);
                $key = array_search(trim($name), $columns);
                $data[$key] = trim(str_replace('1 ', '', $c->quantity . ' ' . $c->description));
            }
        }
    }
    return $data;
}