<?php
include_once('../../../config.php');

use local_order\events;
use local_order\event;

global $CFG, $PAGE, $OUTPUT, $USER;
include_once($CFG->libdir . '/tcpdf/tcpdf.php');

$context = context_system::instance();

$PAGE->set_context($context);

$id = optional_param('id', 0, PARAM_INT);
$inventory_category_id = optional_param('icid', 0, PARAM_INT);
$daterange = optional_param('daterange', date('m/d/Y - m/d/Y', time()), PARAM_TEXT); // event id

// Set up PDF

$pdf = new TCPDF('P', 'in', 'letter', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(fullname($USER));
$pdf->SetTitle(get_string('order', 'local_order'));
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

if ($id) {
    $EVENT = new event($id);
    $data = $EVENT->get_data_for_pdf($inventory_category_id);
    $html = $OUTPUT->render_from_template('local_order/pdf_order', $data);
    unset($EVENT);
//    echo $html;
    $pdf->AddPage();
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->lastPage();
} else {
    // Export all events based on date range
    $EVENTS = new events();
    $events = $EVENTS->get_event_ids_by_daterange($daterange);
    // loop through events
    foreach ($events as $e) {
        $EVENT = new event($e->id);
        $data = $EVENT->get_data_for_pdf($inventory_category_id);
        $html = $OUTPUT->render_from_template('local_order/pdf_order', $data);
        unset($EVENT);

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->lastPage();

    }
}

$pdf->Output('order.pdf', 'I');