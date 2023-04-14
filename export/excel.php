<?php
include_once('../../../config.php');

use local_order\events;
use local_order\event;

global $CFG, $PAGE, $OUTPUT, $USER;


$context = context_system::instance();

$PAGE->set_context($context);

$id = optional_param('id', 0, PARAM_INT);
$inventory_category_id = optional_param('icid', 0, PARAM_INT);
$daterange = optional_param('daterange', date('m/d/Y - m/d/Y', time()), PARAM_TEXT); // event id
$building = optional_param('building', '', PARAM_TEXT);
$room = optional_param('room', '', PARAM_TEXT);
$status = optional_param('status', -1, PARAM_INT);
$organization = optional_param('organization', -1, PARAM_INT);


if ($id) {
    $EVENT = new event($id);
    $date_name = date('Y-m-d H:i', $EVENT->get_starttime()) . ' - ' . date('H:i', $EVENT->get_endtime());
    $data = $EVENT->get_data_for_pdf($inventory_category_id);

    $columns = set_columns_single_event($data);
    print_object($columns);

//print_object($data);
    unset($EVENT);

} else {
    // Set document name
    $date_name = str_replace('/', '-', $daterange);
    // Export all events based on date range
    $EVENTS = new events();
    $events = $EVENTS->get_event_ids_by_daterange($daterange, $building, $room, $status, $organization);
    // loop through events
    foreach ($events as $e) {
        $EVENT = new event($e->id);
        $data = $EVENT->get_data_for_pdf($inventory_category_id);
        $html = $OUTPUT->render_from_template('local_order/pdf_order', $data);
        unset($EVENT);
//        print_object($data);

    }
}

function set_columns_single_event($data) {
    global $DB;
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

    // Get all inventory items as columns
    for ($i = 0; $i < count($data->inventory_items); $i++) {
        $inventory_items = $data->inventory_items[$i]->items;
        $number_of_items = count($inventory_items);
        if ($number_of_items == 1) {
            $items = $inventory_items[0]['items'];
            for($x = 0; $x < count($items); $x++) {
                $columns[] = $items[$x]->name;
            }
        } else {
            $z = 1;
            foreach($inventory_items as $key => $intventory) {
                $items = $inventory_items[$key]['items'];
                for($x = 0; $x < count($items); $x++) {
                    $name = str_replace('1-', $z . '-', $items[$x]->name);
                    $columns[] = $name;
                }
                $z++;
            }
        }
    }

    $columns[] = 'cost';
    $columns[] = 'chargebackaccount';
    return $columns;
}

function prepare_data_single_event($data) {


    // Define an array of data
    $data = [
        ['Symbol', 'Company', 'Price'],
        ['GOOG', 'Google Inc.', '800'],
        ['AAPL', 'Apple Inc.', '500'],
        ['AMZN', 'Amazon.com Inc.', '250']
    ];

// Open a file for writing
    $filename = 'stock.csv';
    $fp = fopen($filename, 'w');

// Write each row of data to the file
    foreach ($data as $row) {
        fputcsv($fp, $row);
    }

// Close the file
    fclose($fp);
}