<?php
include_once('../../../config.php');

use local_order\event;

global $PAGE, $OUTPUT;

$context = context_system::instance();

$PAGE->set_context($context);

$event_inventory_category_id = required_param('id', PARAM_INT); // event category id
$event_id = required_param('eventid', PARAM_INT); // event id

$EVENT = new event($event_id);

$items = [
    'eventinventorycategoryid' => $event_inventory_category_id,
    'eventid' => $event_id,
    'items' => $EVENT->get_inventory_items_by_category($event_inventory_category_id)
];

echo $OUTPUT->render_from_template('local_order/event_inventory', $items);