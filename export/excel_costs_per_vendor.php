<?php
include_once('../../../config.php');
require_once($CFG->libdir . '/phpspreadsheet/vendor/autoload.php');

use local_order\vendor;
use local_order\helper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

global $DB, $CFG, $PAGE, $OUTPUT, $USER;

$context = context_system::instance();

$PAGE->set_context($context);

$id = required_param('id', PARAM_INT);

$VENDOR = new vendor($id);

$filename = 'vendor_cost_' . $VENDOR->get_name() . '.xlsx';

$columns = [
    'Vendor',
    'Event',
    'Event Code',
    'Date',
    'Association',
    'Association Code',
    'Category',
    'Description',
    'Quantity',
    'Cost',
    'Subtotal',
    'Taxes',
    'Total'
];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->fromArray($columns, 'A1');

$sql = "Select
    v.name As vendor,
    e.name As event,
    e.code As event_code,
    FROM_UNIXTIME(e.starttime, '%Y-%m-%d') As date,
    o.name As association,
    o.code As association_code,
    eic.name As category,
    ei.name As description,
    ei.quantity,
    ei.cost,
    ei.cost As subtotal,
    (ei.cost * 0.13) As taxes,
    (ei.cost + (ei.cost * 0.13)) As total
From
    {order_vendor} v Inner Join
    {order_event_inventory} ei On ei.vendorid = v.id Inner Join
    {order_event_inv_category} eic On eic.id = ei.eventcategoryid Inner Join
    {order_event} e On e.id = eic.eventid Left Join
    {order_organization} o On o.id = e.organizationid
Where
    v.id = ? And
    ei.cost != 0 
Order By
    e.code";

$results = $DB->get_recordset_sql($sql, [$id]);

$i = 2;
$subtotal = 0;
$taxes= 0;
$total = 0;
foreach ($results as $result) {
    $sheet->setCellValue('A' . $i, $result->vendor);
    $sheet->setCellValue('B' . $i, $result->event);
    $sheet->setCellValue('C' . $i, $result->event_code);
    $sheet->setCellValue('D' . $i, $result->date);
    $sheet->setCellValue('E' . $i, $result->association);
    $sheet->setCellValue('F' . $i, $result->association_code);
    $sheet->setCellValue('G' . $i, $result->category);
    $sheet->setCellValue('H' . $i, $result->description);
    $sheet->setCellValue('I' . $i, $result->quantity);
    $sheet->setCellValue('J' . $i, helper::convert_to_float($result->cost));
    $sheet->getStyle("J$i")
        ->getNumberFormat()
        ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    $sheet->setCellValue('K' . $i, helper::convert_to_float($result->subtotal));
    $sheet->getStyle("K$i")
        ->getNumberFormat()
        ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    $sheet->setCellValue('L' . $i, helper::convert_to_float($result->taxes));
    $sheet->getStyle("L$i")
        ->getNumberFormat()
        ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    $sheet->setCellValue('M' . $i, helper::convert_to_float($result->total));
    $sheet->getStyle("M$i")
        ->getNumberFormat()
        ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    $subtotal = $subtotal + $result->subtotal;
    $taxes = $taxes + $result->taxes;
    $total = $total + $result->total;
    $i++;
}

$sheet->setCellValue('K' . $i, $subtotal);
$sheet->getStyle("K$i")
    ->getNumberFormat()
    ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
$sheet->setCellValue('L' . $i, $taxes);
$sheet->getStyle("L$i")
    ->getNumberFormat()
    ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
$sheet->setCellValue('M' . $i, $total);
$sheet->getStyle("M$i")
    ->getNumberFormat()
    ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

unset($VENDOR);

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . urlencode($filename) . '"');
$writer->save('php://output');