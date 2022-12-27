<?php
include_once('../../../config.php');

use local_order\event;

$action = required_param('action', PARAM_TEXT);
$id = required_param('id', PARAM_INT);

switch ($action) {
    case 'event':
        $EVENT = new event($id);
        $EVENT->delete_record();
        echo true;
        break;
}