<?php
include_once('../../../config.php');

global $DB, $PAGE, $USER;

$action = required_param('action', PARAM_TEXT);
$id = optional_param('id', 0, PARAM_INT);

switch ($action) {
    case 'vendor_contact':
        $context = context_system::instance();
        $vendorid = required_param('vendorid', PARAM_INT);
        $userid = required_param('userid', PARAM_INT);
        $params = [
            'vendorid' => $vendorid,
            'userid' => $userid,
            'usermodified' => $USER->id,
            'timecreated' => time(),
            'timemodified' => time()
        ];

        $id = $DB->insert_record('order_vendor_contact', $params);
        $role = $DB->get_record('role', ['shortname' => 'vendor']);
        role_assign($role->id, $userid, $context->id);
        echo $id;
        break;
    case 'approve':
        // Update event status to 'Approved'
        $event_params = new stdClass();
        $event_params->id = $id;
        $event_params->status = 1;
        $event_params->usermodified = $USER->id;
        $event_params->timemodified = time();

        $DB->update_record('order_event', $event_params);
        // Update event inventory status to 'Approved'
        $event_inventory_status = $DB->get_record('order_event_inv_status', ['eventid' => $id]);
        $event_inventory_status->av = 1;
        $event_inventory_status->catering = 1;
        $event_inventory_status->furnishing = 1;
        $event_inventory_status->usermodified = $USER->id;
        $event_inventory_status->timemodified = time();

        $DB->update_record('order_event_inv_status', $event_inventory_status);
        break;

}