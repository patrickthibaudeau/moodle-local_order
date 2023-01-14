<?php
include_once('../../../config.php');

use local_order\event;
use local_order\inventory;
use local_order\organization;

$action = required_param('action', PARAM_TEXT);
$id = required_param('id', PARAM_INT);

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
}