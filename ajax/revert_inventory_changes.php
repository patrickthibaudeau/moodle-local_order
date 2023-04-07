<?php

require_once('../../../config.php');

use local_order\event;

$id = required_param('id', PARAM_INT);

$EVENT = new event($id);
// Revert changes
$EVENT->revert_inventory_changes();
