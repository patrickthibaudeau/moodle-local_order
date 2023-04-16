<?php
include_once('../../../config.php');
include_once('../lib.php');

use local_order\event;

global $DB, $PAGE, $USER, $OUTPUT;

$context = context_system::instance();

$PAGE->set_context($context);

$action = required_param('action', PARAM_TEXT);
$id = optional_param('id', 0, PARAM_INT);
$ids = json_decode($_REQUEST['ids']);

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
        foreach($ids as $key => $id) {
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
        }
        break;
        case 'approve_inventory':
            $inventory_type = required_param('type', PARAM_TEXT);
            $eis = $DB->get_record(TABLE_EVENT_INVENTORY_STATUS, ['eventid' => $id]);
            $event_inventory_status = new stdClass();
            $event_inventory_status->id = $eis->id;
            switch ($inventory_type) {
                case 'AV':
                    $event_inventory_status->av = true;
                    $event_inventory_status->usermodified = $USER->id;
                    $event_inventory_status->timemodified = time();
                    $DB->update_record('order_event_inv_status', $event_inventory_status);
                    break;
                case 'C':
                    $event_inventory_status->catering = true;
                    $event_inventory_status->usermodified = $USER->id;
                    $event_inventory_status->timemodified = time();
                    break;
                case 'F':
                    $event_inventory_status->furnishing = true;
                    $event_inventory_status->usermodified = $USER->id;
                    $event_inventory_status->timemodified = time();
                    $DB->update_record('order_event_inv_status', $event_inventory_status);
                    break;
            }
            $DB->update_record(TABLE_EVENT_INVENTORY_STATUS, $event_inventory_status);
            $EVENT = new event($id);
            // Check to see if all category statuses are true
            $updated_eis = $DB->get_record(TABLE_EVENT_INVENTORY_STATUS, ['eventid' => $id]);
            if ($updated_eis->av == true && $updated_eis->catering == true && $updated_eis->furnishing == true) {
                // Update event status to 'Approved'
                $DB->update_record('order_event', [
                    'id' => $id,
                    'status' => 1,
                    'usermodified' => $USER->id,
                    'timemodified' => time()]);
                $EVENT->send_notification_to_vendors_event_approved();
            } else {
                // Update event status to 'Pending'
                $DB->update_record('order_event', [
                    'id' => $id,
                    'status' => 2,
                    'usermodified' => $USER->id,
                    'timemodified' => time()]);
                $EVENT->send_notification_to_vendors_event_pending();
            }

            $items = [
                'inventory_categories' => $EVENT->get_inventory_categories_with_items()
            ];
            echo $OUTPUT->render_from_template('local_order/edit_event_inventory_items', $items);
            break;

}