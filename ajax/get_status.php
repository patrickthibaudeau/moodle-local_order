<?php
use local_order\event;

include_once('../../../config.php');

global $DB, $USER;

$context = context_system::instance();

$id = required_param('id', PARAM_INT);

$EVENT = new event($id);

if ($EVENT->get_statusid() == 1) {
    echo 1;
} else {
    echo 2;
}