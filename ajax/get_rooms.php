<?php
use local_order\rooms;

include_once('../../../config.php');

global $DB, $USER;

$context = context_system::instance();

$building_code = required_param('id', PARAM_TEXT);

$ROOMS = new rooms();

// Return json objecy
echo json_encode($ROOMS->get_rooms_by_building_floor($building_code));