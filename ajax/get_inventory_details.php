<?php
include_once('../../../config.php');

use local_order\event;

global $PAGE, $OUTPUT;

$context = context_system::instance();

$PAGE->set_context($context);

$id = required_param('id', PARAM_INT); // event category id
$event = required_param('event', PARAM_INT); // event id

$EVENT = new event($event);

$items = [
    'id' => $id,
    'event' => $event,
    'items' => $EVENT->get_inventory_items_by_category($id)
];

echo $OUTPUT->render_from_template('local_order/event_inventory', $items);