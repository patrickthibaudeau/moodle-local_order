<?php
include_once('../../../config.php');
include_once('../lib.php');

use local_order\inventory;
use local_order\inventories;
use local_order\event_inventory;
use local_order\vendors;

global $PAGE, $OUTPUT;

$context = context_system::instance();

$PAGE->set_context($context);

$id = optional_param('id', 0, PARAM_INT); // event inventory id
$event_inventory_category_id = required_param('eicid', PARAM_INT); // event id
$event_id = required_param('eventid', PARAM_INT); // event id

$EVENT_INVENTORY = new \local_order\event_inventory($id);

// Prepare inventory items select menu items.
$INVENTORY = new inventories();
$event_inventory_category = $EVENT_INVENTORY->get_event_inventory_category_details($event_inventory_category_id);
$inventory_items_by_category = $INVENTORY->get_records_by_category($event_inventory_category->inventorycategoryid);

$inventory_items = [];
$i = 0;
// To use with cost to format with current currency
$amount = new \NumberFormatter(get_string('currency_locale', 'local_order'),
    \NumberFormatter::CURRENCY);
foreach($inventory_items_by_category as $iibc) {
    $inventory_items[$i]['id'] = $iibc->id;
    $inventory_items[$i]['name'] = $iibc->name;
    $inventory_items[$i]['cost'] = $iibc->cost;
    if ($iibc->id == $EVENT_INVENTORY->get_inventoryid()) {
        $inventory_items[$i]['selected'] = 'selected';
    } else {
        $inventory_items[$i]['selected'] = '';
    }
    $inventory_items[$i]['cost'] = $iibc->cost;
    $inventory_items[$i]['cost_formatted'] = $amount->format($iibc->cost);
    $i++;
}

// Get vendors
$VENDORS = new vendors();
$all_vendors = $VENDORS->get_records();
$vendors = [];
$x = 0;
foreach($all_vendors as $v) {
    $vendors[$x]['id'] = $v->id;
    $vendors[$x]['name'] = $v->name;
    if ($v->id == $EVENT_INVENTORY->get_vendorid()) {
        $vendors[$x]['selected'] = 'selected';
    } else {
        $vendors[$x]['selected'] = '';
    }
    $x++;
}


$items = [
    'id' => $id,
    'eventinventorycategoryid' => $event_inventory_category_id,
    'eventid' => $event_id,
    'description' => $EVENT_INVENTORY->get_description(),
    'cost' => $EVENT_INVENTORY->get_cost(),
    'inventory' => $inventory_items,
    'vendors' => $vendors
];

echo $OUTPUT->render_from_template('local_order/event_inventory_form', $items);