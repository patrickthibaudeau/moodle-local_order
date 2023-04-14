<?php
include_once('../../../config.php');

use local_order\event;
use local_order\inventory;
use local_order\event_inventory;

global $DB, $OUTPUT, $PAGE;

$context = context_system::instance();

$PAGE->set_context($context);

$id = optional_param('id', 0,PARAM_INT);
$eventid = required_param('eventid', PARAM_INT);
$event_inventory_category_id = required_param('eventinventorycategoryid', PARAM_TEXT);
$quantity = optional_param('quantity', 0,PARAM_INT);
$cost = optional_param('cost', 0,PARAM_FLOAT);
$description = optional_param('description', 0,PARAM_TEXT);
$section = optional_param('section', '',PARAM_TEXT);
$inventory_id= optional_param('inventory_id', 0,PARAM_INT);
$vendorid = optional_param('vendorid', 0,PARAM_INT);

$INVENTORY = new inventory($inventory_id);

$data = new stdClass();

$data->eventcategoryid = $event_inventory_category_id;
$data->vendorid = $vendorid;
$data->inventoryid = $inventory_id;
$data->name = $INVENTORY->get_name();
$data->description = $description;
$data->section = $section;
$data->quantity = $quantity;
$data->cost = $cost;

if ($id) {
    $EVENT_INVENTORY = new event_inventory($id);
    $data->id= $id;
    $EVENT_INVENTORY->update_record($data);
} else {
    $EVENT_INVENTORY = new event_inventory();
    $event_inventory_id = $EVENT_INVENTORY->insert_record($data);
}
unset ($EVENT_INVENTORY);

$EVENT = new event($eventid);

$items = [
    'inventory_categories' => $EVENT->get_inventory_categories_with_items()
];

echo $OUTPUT->render_from_template('local_order/edit_event_inventory_items', $items);
