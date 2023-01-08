<?php
include_once('../../../config.php');

use local_order\inventory;


$id = required_param('id', PARAM_INT);
$column = required_param('column', PARAM_TEXT);
$value = required_param('value', PARAM_RAW);

switch ($column) {
    case 'shortname':
        $column = 'code';
        break;
}

$data = new stdClass();
$data->id = $id;
$data->$column = ltrim($value, '$');

$INVENTORY = new inventory($id);
$INVENTORY->update_record($data);

return true;