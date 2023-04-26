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
switch ($inventory_category_id) {
    case 1:
        $filename = 'congress_' . date('Y', time()) . '_export_av.csv';
        break;
    case 2:
        $filename = 'congress_' . date('Y', time()) . '_export_catering.csv';
        break;
    case 3:
        $filename = 'congress_' . date('Y', time()) . '_export_furnishing.csv';
        break;
    default:
        $filename = 'congress_' . date('Y', time()) . 'all_export.csv';
        break;
}

$columns = set_columns($inventory_category_id);
// tell the browser it's going to be a csv file
header('Content-type: text/csv;');
// tell the browser we want to save it instead of displaying it
header('Content-Disposition: attachment; filename="' . $filename . '";');
// Open a file for writing

$fp = fopen('php://output', 'w');
// Put column names first
fputcsv($fp, $columns);

if ($id) {
    $EVENT = new event($id);

    // Get event data
    $data = $EVENT->get_data_for_pdf($inventory_category_id);
    // Prepare data for CSV row
    $event_data = prepare_data_single_event($data, $columns, $inventory_category_id);
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
        $event_data = prepare_data_single_event($data, $columns, $inventory_category_id);

        fputcsv($fp, $event_data);
        unset($EVENT);
    }
}

// Close the file
fclose($fp);

function set_columns($inventory_category_id = 0)
{
    global $DB;

// Get all inventory items as columns by category
    if ($inventory_category_id == 0 || $inventory_category_id == 1) {
        $av_inventory = $DB->get_records('order_inventory', ['inventorycategoryid' => 1]);
    }
    if ($inventory_category_id == 0 || $inventory_category_id == 2) {
        $catering_inventory = $DB->get_records('order_inventory', ['inventorycategoryid' => 2], 'name DESC');
    }
    if ($inventory_category_id == 0 || $inventory_category_id == 3) {
        $furnishing_inventory = $DB->get_records('order_inventory', ['inventorycategoryid' => 3]);
    }

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
    if ($inventory_category_id == 0 || $inventory_category_id == 3) {
        foreach ($furnishing_inventory as $fi) {
            $columns[] = $fi->name;
        }
    }

    // AV
    if ($inventory_category_id == 0 || $inventory_category_id == 1) {
        foreach ($av_inventory as $ai) {
            $columns[] = $ai->name;
        }
    }
    // Catering
    if ($inventory_category_id == 0 || $inventory_category_id == 2) {
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
    }

    if ($inventory_category_id == 0) {
        $columns[] = 'cost';
        $columns[] = 'taxes';
        $columns[] = 'total';
        $columns[] = 'chargebackaccount';
        $columns[] = 'HST Number';
    }

    return $columns;
}

function prepare_data_single_event($event_data, $columns, $inventory_category_id = 0)
{
    global $CFG;
    $data = [];

    if ($inventory_category_id == 0 || $inventory_category_id == 1) {
        $av = @$event_data->inventory_items[0]->items[0]['items'];
    }
    if ($inventory_category_id == 0 || $inventory_category_id == 2) {
        if ($inventory_category_id == 2) {
            $catering = @$event_data->inventory_items[0]->items;
        } else {
            $catering = @$event_data->inventory_items[1]->items;
        }
    }
    if ($inventory_category_id == 0 || $inventory_category_id == 3) {
        if ($inventory_category_id == 3) {
            $furnishing = @$event_data->inventory_items[0]->items[0]['items'];
        } else {
            $furnishing = @$event_data->inventory_items[2]->items[0]['items'];
        }

    }

//print_object($event_data);
    $data[] = $event_data->code;
    $data[] = $event_data->organization->code . ' - ' . $event_data->organization->name;
    $data[] = $event_data->name;
    $data[] = $event_data->date_short;
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

    if ($inventory_category_id == 0) {
        $data[429] = $event_data->cost;
        $data[430] = $event_data->taxes;
        $data[431] = $event_data->total_cost;
        $data[432] = $event_data->organization->costcentre . '-'
            . $event_data->organization->fund . '-'
            . $event_data->organization->activitycode;
        $data[433] = $CFG->local_order_hst_number;
    }


    // Get furnishing Data
    if ($inventory_category_id == 0 || $inventory_category_id == 3) {
        if (isset($furnishing)) {
            foreach ($furnishing as $f) {
                $key = array_search(trim($f->name), $columns);
                $data[$key] = trim($f->quantity . ' ' . $f->description);
            }
        }
    }


    //Get AV Data
    if ($inventory_category_id == 0 || $inventory_category_id == 1) {
        if (isset($av)) {
            foreach ($av as $a) {
                $key = array_search(trim($a->name), $columns);
                $data[$key] = trim($a->quantity . ' ' . $a->description);
            }
        }
    }

    if ($inventory_category_id == 0 || $inventory_category_id == 2) {
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
    }

    return $data;
}