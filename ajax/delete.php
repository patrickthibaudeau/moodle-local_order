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
        $INVENTORY->delete_record();
        echo true;
        break;
    case 'organization':
        $ORGANIZATION = new organization($id);
        $ORGANIZATION->delete_record();
        echo true;
        break;
}