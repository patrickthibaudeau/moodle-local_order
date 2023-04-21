<?php
include_once('../../../config.php');

use local_order\organization;

global $CFG, $PAGE, $OUTPUT, $USER;
include_once($CFG->libdir . '/tcpdf/tcpdf.php');

$context = context_system::instance();

$PAGE->set_context($context);

$id = required_param('id',  PARAM_INT);
// Set up PDF

$pdf = new TCPDF('P', 'in', 'letter', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(fullname($USER));
$pdf->SetTitle(get_string('organization', 'local_order'));
$pdf->SetSubject('');
$pdf->SetKeywords('');

// Remove Header and Footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont('times');

//set margins
$pdf->SetMargins(0.5, 0.5, 0.5);


//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 0.5);


$pdf->SetFont('times', '', 10);


    $ORGANIZATION = new organization($id);
    $data = [];

    $data['organization'] = $ORGANIZATION->get_record();
    $cost_data = $ORGANIZATION->get_inventory_cost();
    $data['av']  = $ORGANIZATION->get_inventory_cost_per_category('AV');
    $data['catering']  = $ORGANIZATION->get_inventory_cost_per_category('C');
    $data['furnishing']  = $ORGANIZATION->get_inventory_cost_per_category('F');
    $data['subtotal']  = $cost_data->subtotal;
    $data['taxes']  = $cost_data->taxes;
    $data['total']  = $cost_data->total;
    $data['hst_number'] = $CFG->local_order_hst_number;
//    print_object($data);
    $html = $OUTPUT->render_from_template('local_order/pdf_order_summary_by_org', $data);

//    echo $html;
    $pdf->AddPage();
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->lastPage();

$pdf->Output('association_order_' . $ORGANIZATION->get_code() . '.pdf', 'D');
unset($ORGANIZATION);