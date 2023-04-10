<?php
use local_order\room_basics;

include_once('../../../config.php');

global $DB, $USER;

$context = context_system::instance();

$building_code = required_param('building', PARAM_TEXT);

$ROOMS = new room_basics();

// Return json objecy
echo json_encode($ROOMS->get_rooms_based_on_building_for_js($building_code));