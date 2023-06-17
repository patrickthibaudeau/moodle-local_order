<?php
include_once('../../../config.php');
require_once($CFG->libdir . '/phpspreadsheet/vendor/autoload.php');

use local_order\organizations;
use local_order\organization;
use local_order\helper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

global $CFG, $PAGE, $OUTPUT, $USER;
include_once($CFG->libdir . '/tcpdf/tcpdf.php');

$context = context_system::instance();

$PAGE->set_context($context);

$id = required_param('id', PARAM_INT);

$columns = ['Association',
    'AV',
    'Catering',
    'Furnishing',
    'Subtotal',
    'Taxes',
    'Total',
    'Cost-Centre',
    'Fund',
    'Activity code',
    'HST Number'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->fromArray($columns, 'A1');




if ($id > 0) {
    $ORGANIZATION = new organization($id);
    $cost_data = $ORGANIZATION->get_inventory_cost();
    $filename = 'event_summary_' . $ORGANIZATION->get_code() . '.xlsx';

    $data['organization'] = $ORGANIZATION->get_record();

// Write each row of data to the file
    $sheet->setCellValue('A2', $ORGANIZATION->get_name());
    $sheet->setCellValue('B2', helper::convert_to_float($ORGANIZATION->get_inventory_cost_per_category('AV')));
    $sheet->setCellValue('C2', helper::convert_to_float($ORGANIZATION->get_inventory_cost_per_category('C')));
    $sheet->setCellValue('D2', helper::convert_to_float($ORGANIZATION->get_inventory_cost_per_category('F')));
    $sheet->setCellValue('E2', helper::convert_to_float($cost_data->subtotal));
    $sheet->setCellValue('F2', helper::convert_to_float($cost_data->taxes));
    $sheet->setCellValue('G2', helper::convert_to_float($cost_data->total));
    $sheet->setCellValue('H2', $ORGANIZATION->get_costcentre());
    $sheet->setCellValue('I2', $ORGANIZATION->get_fund());
    $sheet->setCellValue('J2', $ORGANIZATION->get_activitycode());
    $sheet->setCellValue('K2', $CFG->local_order_hst_number);

    $sheet->getStyle('B2:G2')
        ->getNumberFormat()
        ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

    unset($ORGANIZATION);
} else {
    $ORGANIZATIONS = new organizations();
    $results = $ORGANIZATIONS->get_records();
    $i = 2;
    foreach ($results as $organization) {
        $ORGANIZATION = new organization($organization->id);
        $cost_data = $ORGANIZATION->get_inventory_cost();
        $filename = 'event_summary_all.xlsx';

        $data['organization'] = $ORGANIZATION->get_record();

// Write each row of data to the file
        $sheet->setCellValue('A' . $i, $ORGANIZATION->get_name());
        $sheet->setCellValue('B' . $i, helper::convert_to_float($ORGANIZATION->get_inventory_cost_per_category('AV')));
        $sheet->setCellValue('C' . $i, helper::convert_to_float($ORGANIZATION->get_inventory_cost_per_category('C')));
        $sheet->setCellValue('D' . $i, helper::convert_to_float($ORGANIZATION->get_inventory_cost_per_category('F')));
        $sheet->setCellValue('E' . $i, helper::convert_to_float($cost_data->subtotal));
        $sheet->setCellValue('F' . $i, helper::convert_to_float($cost_data->taxes));
        $sheet->setCellValue('G' . $i, helper::convert_to_float($cost_data->total));
        $sheet->setCellValue('H' . $i, $ORGANIZATION->get_costcentre());
        $sheet->setCellValue('I' . $i, $ORGANIZATION->get_fund());
        $sheet->setCellValue('J' . $i, $ORGANIZATION->get_activitycode());
        $sheet->setCellValue('K' . $i, $CFG->local_order_hst_number);

        $sheet->getStyle("B$i:G$i")
            ->getNumberFormat()
            ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $i++;
    }
    unset($ORGANIZATION);
}
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . urlencode($filename) . '"');
$writer->save('php://output');