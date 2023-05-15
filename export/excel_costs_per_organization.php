<?php
include_once('../../../config.php');
require_once($CFG->libdir . '/phpspreadsheet/vendor/autoload.php');

use local_order\organizations;
use local_order\organization;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

global $DB, $CFG, $PAGE, $OUTPUT, $USER;


$context = context_system::instance();

$PAGE->set_context($context);

$id = optional_param('id', 0, PARAM_INT);


$columns = set_columns();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->fromArray($columns, 'A1');

$ORGANIZATION = new organization($id);
$data = [];

$filename = 'association_order_' . $ORGANIZATION->get_code() . '.xlsx';

$data['organization'] = $ORGANIZATION->get_record();
$cost_data = $ORGANIZATION->get_inventory_cost();
$items = $cost_data->items;
// Write each row of data to the file

$cell_letter = "B";
$i = 2;
foreach ($items as $item) {
    $sheet->setCellValue('A' . $i, $ORGANIZATION->get_name());
    $sheet->setCellValue('B' . $i, $item->name);
    $sheet->setCellValue('C' . $i, $item->quantity);
    $sheet->setCellValue('D' . $i, $item->cost);
    $i++;
}
$sheet->setCellValue('C' . $i, 'Subtotal');
$sheet->setCellValue('D' . $i, $cost_data->subtotal);
$sheet->setCellValue('C' . ++$i, 'Taxes');
$sheet->setCellValue('D' . $i, $cost_data->taxes);
$sheet->setCellValue('C' . ++$i, 'Total');
$sheet->setCellValue('D' . $i, $cost_data->total);
$sheet->setCellValue('C' . ++$i, 'Cost-Centre');
$sheet->setCellValue('D' . $i, $ORGANIZATION->get_costcentre());
$sheet->setCellValue('C' . ++$i, 'HST Number');
$sheet->setCellValue('D' . $i, $CFG->local_order_hst_number);

unset($ORGANIZATION);


$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . urlencode($filename) . '"');
$writer->save('php://output');


function set_columns($inventory_category_id = 0)
{
    global $DB;


    $columns = array();
    $columns[] = 'association';
    $columns[] = 'item';
    $columns[] = 'quantity';
    $columns[] = 'cost';
    return $columns;
}

/**
 * @deprecated  No longer used. keeping it here for another excel.
 * @param $event_data
 * @param $columns
 * @param $inventory_category_id
 * @return array
 */
function prepare_data_single_event($event_data, $columns, $inventory_category_id = 0)
{
    global $CFG;
    print_object($event_data);
    $data = [];
    $number_of_columns = count($columns);

    $av_subtotal = 0.00;
    $catering_subtotal = 0.00;
    $furnishing_subtotal = 0.00;

    if ($inventory_category_id == 0 || $inventory_category_id == 1) {
        $av = @$event_data->inventory_items[0]->items[0]['items'];

        if (isset($event_data->inventory_items[0]->items[0]['subtotal'])) {
            $av_subtotal = $event_data->inventory_items[0]->items[0]['subtotal'];
        }
    }
    if ($inventory_category_id == 0 || $inventory_category_id == 2) {

        if ($inventory_category_id == 2) {
            $catering = @$event_data->inventory_items[0]->items;
            if (isset($event_data->inventory_items[0]->items[0]['subtotal'])) {
                $catering_subtotal = $event_data->inventory_items[0]->items[0]['subtotal'];
            }
        } else {
            $catering = @$event_data->inventory_items[1]->items;
            if (isset($event_data->inventory_items[1]->items[0]['subtotal'])) {
                $catering_subtotal = $event_data->inventory_items[1]->items[0]['subtotal'];
            }
        }
    }
    if ($inventory_category_id == 0 || $inventory_category_id == 3) {
        if ($inventory_category_id == 3) {
            $furnishing = @$event_data->inventory_items[0]->items[0]['items'];
            if (isset($event_data->inventory_items[0]->items[0]['subtotal'])) {
                $furnishing_subtotal = $event_data->inventory_items[0]->items[0]['subtotal'];
            }
        } else {
            $furnishing = @$event_data->inventory_items[2]->items[0]['items'];
            if (isset($event_data->inventory_items[2]->items[0]['subtotal'])) {
                $furnishing_subtotal = $event_data->inventory_items[2]->items[0]['subtotal'];
            }
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
        $data[$number_of_columns - 8] = ''; // AV subtotal
        $data[$number_of_columns - 7] = ''; // Catering subtotal
        $data[$number_of_columns - 6] = ''; // Furnishing subtotal
        $data[$number_of_columns - 5] = $event_data->cost;
        $data[$number_of_columns - 4] = $event_data->taxes;
        $data[$number_of_columns - 3] = $event_data->total_cost;
        $data[$number_of_columns - 2] = $event_data->organization->costcentre . '-'
            . $event_data->organization->fund . '-'
            . $event_data->organization->activitycode;
        $data[$number_of_columns - 1] = $CFG->local_order_hst_number;
    }


    // Get furnishing Data
    if ($inventory_category_id == 0 || $inventory_category_id == 3) {
        if (isset($furnishing)) {
            foreach ($furnishing as $f) {
                $key = array_search(trim($f->name), $columns);
                $data[$key] = trim($f->quantity . ' ' . $f->description);
                if ($inventory_category_id == 0) {
                    $data[$number_of_columns - 6] = $furnishing_subtotal;
                }
            }
        }
    }


    //Get AV Data
    if ($inventory_category_id == 0 || $inventory_category_id == 1) {
        if (isset($av)) {
            foreach ($av as $a) {
                $key = array_search(trim($a->name), $columns);
                $data[$key] = trim($a->quantity . ' ' . $a->description);
                if ($inventory_category_id == 0) {
                    $data[$number_of_columns - 8] = $av_subtotal;
                }
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
                    if ($inventory_category_id == 0) {
                        $data[$number_of_columns - 7] = $catering_subtotal;
                    }
                }
            }
        }
    }

    return $data;
}