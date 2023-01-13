<?php
include_once('../../../config.php');

use local_order\event;
use local_order\event_inventory;

global $DB, $OUTPUT, $PAGE;

$context = context_system::instance();

$PAGE->set_context($context);

$id = optional_param('id', 0, PARAM_INT);
$eventid = required_param('eventid', PARAM_INT);

$EVENT_INVENTORY = new event_inventory($id);
$EVENT_INVENTORY->delete_record();
unset ($EVENT_INVENTORY);

$EVENT = new event($eventid);

$items = [
    'inventory_categories' => $EVENT->get_inventory_categories_with_items()
];

echo $OUTPUT->render_from_template('local_order/edit_event_inventory_items', $items);
