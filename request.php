<?php

require_once("../../config.php");

require_once($CFG->dirroot . "/local/order/classes/forms/request_form.php");

use local_order\base;
use local_order\haloitsm;
use local_order\jira;
use local_order\request;

global $CFG, $OUTPUT, $USER, $PAGE, $DB, $SITE;

$id = optional_param('id', 0, PARAM_INT);

$context = CONTEXT_SYSTEM::instance();

require_login(1, false);

$formdata = new stdClass();
$formdata->userid = [$USER->id => fullname($USER) . '(' . $USER->email . ')'];

$mform = new \local_order\request_form(null, array('formdata' => $formdata));
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot . '/local/order/index.php');
} else if ($data = $mform->get_data()) {
    // Set objects for HALO and JIRA
    $HALO = new haloitsm();
    $JIRA = new jira();
    $REQUEST = new request();
    // Make sure a userid is set
    if (!$data->userid) {
        $data->userid = $USER->id;
    }

    $description = $data->description_editor['text'];

    // Create HALO ticket
    $new_ticket = $HALO->create_ticket($USER->username, $data->summary, $description);
    if (is_object($new_ticket)) {
        // Remove HTML tags for JIRA
        $jira_description = str_replace('<br>', "\n\n", $description);
        $jira_description = str_replace('<p>', "\n\n\n\n", $description);
        $jira_description = str_replace('</p>', "", $description);

        // Add HALO Ticket ID to JIRA description
        $jira_description .= "\n\nHalo Ticket ID: " . $new_ticket->id;
        $jira_description = strip_tags($jira_description);
        // Create JIRA issue
        $new_jira_issue = $JIRA->create_issue($data->summary, $jira_description);

        // Create request record
        $params = new stdClass();
        $params->userid = $data->userid;
        $params->summary = $data->summary;
        $params->description = $description;
        $params->halo_ticket_id = $new_ticket->id;
        $params->jira_issue_id = $new_jira_issue->id;
        $params->jira_issue_key = $new_jira_issue->key;
        $params->jira_issue_url = $new_jira_issue->self;
        $params->usermodified = $USER->id;

        $REQUEST->insert_record($params);

        redirect($CFG->wwwroot . '/local/order/index.php');
    } else {
        \core\notification::error('Could not find user in HALO');
    }


} else {
    $mform->set_data($mform);
}

base::page(
    '/local/order/request.php',
    get_string('request_form', 'local_order'),
    get_string('request_form', 'local_order'),
    $context
);

echo $OUTPUT->header();
//**********************
//*** DISPLAY HEADER ***
//

$mform->display();
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();
?>