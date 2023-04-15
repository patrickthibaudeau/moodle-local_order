<?php
include_once('../../../config.php');

use local_order\event;
use local_order\inventory;
use local_order\organization;
use local_order\vendor;
use local_order\vendor_contact;

global $DB;

$action = required_param('action', PARAM_TEXT);
$id = required_param('id', PARAM_INT);

$context = context_system::instance();

switch ($action) {
    case 'event':
        $EVENT = new event($id);
        $EVENT->delete_record();
        echo true;
        break;
    case 'inventory':
        $INVENTORY = new inventory($id);
        if (!$INVENTORY->is_used()) {
            $INVENTORY->delete_record();
            echo true;
        } else {
            echo false;
        }
        break;
    case 'organization':
        $ORGANIZATION = new organization($id);
        if (!$ORGANIZATION->is_used()) {
            $ORGANIZATION->delete_record();
            echo true;
        } else {
            echo false;
        }
        break;
    case 'vendor':
        $VENDOR = new vendor($id);
        if (!$VENDOR->is_used()) {
            $VENDOR->delete_record();
            echo true;
        } else {
            echo false;
        }
        break;
    case 'vendor_contact':
        $vendor_contact = $DB->get_record('order_vendor_contact', ['id' => $id]);
        $role = $DB->get_record('role', ['shortname' => 'vendor']);
        role_unassign($role->id, $vendor_contact->userid, $context->id);
        $DB->delete_records('order_vendor_contact', ['id' => $id]);
        return true;
        break;
    case 'room':
        $DB->delete_records('order_room_basic', ['id' => $id]);
        echo true;
        break;
}