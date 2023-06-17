<?php
include_once('../../../config.php');
require_once($CFG->libdir . '/phpspreadsheet/vendor/autoload.php');

use local_order\organization;
use local_order\helper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

global $DB, $CFG, $PAGE, $OUTPUT, $USER;

$context = context_system::instance();

$PAGE->set_context($context);

$id = required_param('id', PARAM_INT);

$ORGANIZATION = new organization($id);

$filename = 'detailed_organization_cost_' . $ORGANIZATION->get_code() . '.xlsx';

$columns = [
    'Event',
    'Event Code',
    'Date',
    'Vendor',
    'Category',
    'Description',
    'Quantity',
    'cost',
    'Subtotal',
    'Taxes',
    'Total'
];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->mergeCells('A1:K1');
$sheet->setCellValue('A1', $ORGANIZATION->get_name());
$sheet->mergeCells('A2:K2');
$sheet->setCellValue('A2', 'Cost centre - fund - activity code: ' . $ORGANIZATION->get_costcentre() .'-' .
    $ORGANIZATION->get_fund() .'-' . $ORGANIZATION->get_activitycode());
$sheet->fromArray($columns, null,'A3');
$sheet->freezePane('A4');

$sql = "Select
    e.name As event,
    e.code As event_code,
    From_UnixTime(e.starttime, '%Y-%m-%d') As date,
    v.name As vendor,
    eic.name As category,
    ei.name As description,
    ei.quantity,
    (ei.cost / ei.quantity) As cost,
    ei.cost As subtotal,
    (ei.cost * 0.13) As taxes,
    (ei.cost + (ei.cost * 0.13)) As total
From
    mdl_order_vendor v Inner Join
    mdl_order_event_inventory ei On ei.vendorid = v.id Inner Join
    mdl_order_event_inv_category eic On eic.id = ei.eventcategoryid Inner Join
    mdl_order_event e On e.id = eic.eventid Left Join
    mdl_order_organization o On o.id = e.organizationid
Where
    o.id = ? And
    ei.cost != 0
Order By
    event_code,
    vendor";

$results = $DB->get_recordset_sql($sql, [$id]);

$i = 4;
$subtotal = 0;
$taxes= 0;
$total = 0;
foreach ($results as $result) {
    $sheet->setCellValue('A' . $i, $result->event);
    $sheet->setCellValue('B' . $i, $result->event_code);
    $sheet->setCellValue('C' . $i, $result->date);
    $sheet->setCellValue('D' . $i, $result->vendor);
    $sheet->setCellValue('E' . $i, $result->category);
    $sheet->setCellValue('F' . $i, $result->description);
    $sheet->setCellValue('G' . $i, $result->quantity);
    $sheet->setCellValue('H' . $i, helper::convert_to_float($result->cost));
    $sheet->getStyle("H$i")
        ->getNumberFormat()
        ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    $sheet->setCellValue('I' . $i, helper::convert_to_float($result->subtotal));
    $sheet->getStyle("I$i")
        ->getNumberFormat()
        ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    $sheet->setCellValue('J' . $i, helper::convert_to_float($result->taxes));
    $sheet->getStyle("J$i")
        ->getNumberFormat()
        ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    $sheet->setCellValue('K' . $i, helper::convert_to_float($result->total));
    $sheet->getStyle("K$i")
        ->getNumberFormat()
        ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    $subtotal = $subtotal + $result->subtotal;
    $taxes = $taxes + $result->taxes;
    $total = $total + $result->total;
    $i++;
}

$sheet->setCellValue('I' . $i, $subtotal);
$sheet->getStyle("I$i")
    ->getNumberFormat()
    ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
$sheet->setCellValue('J' . $i, $taxes);
$sheet->getStyle("J$i")
    ->getNumberFormat()
    ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
$sheet->setCellValue('K' . $i, $total);
$sheet->getStyle("K$i")
    ->getNumberFormat()
    ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

unset($ORGANIZATION);

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . urlencode($filename) . '"');
$writer->save('php://output');