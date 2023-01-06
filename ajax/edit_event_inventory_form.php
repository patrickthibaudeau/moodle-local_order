<?php
include_once('../../../config.php');

use local_order\inventory;
use local_order\inventories;
use local_order\event_inventory;

global $PAGE, $OUTPUT;

$context = context_system::instance();

$PAGE->set_context($context);

$id = optional_param('id', 0, PARAM_INT); // event inventory id
$event_inventory_category_id = required_param('eicid', PARAM_INT); // event id
$event_id = required_param('eventid', PARAM_INT); // event id

$EVENT_INVENTORY = new \local_order\event_inventory($id);

$items = [
    'id' => $id,
    'eventinventorycategoryid' => $event_inventory_category_id,
    'eventid' => $event_id,
    'description' => $EVENT_INVENTORY->get_description()
];

echo $OUTPUT->render_from_template('local_order/event_inventory_form', $items);