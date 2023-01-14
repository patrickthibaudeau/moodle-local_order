<?php

include_once('../../../config.php');

use local_order\inventories;

global $DB, $USER;

$context = context_system::instance();

// get Values from Data
$category_id = optional_param('category', 0, PARAM_INT);
$draw = optional_param('draw', 1, PARAM_INT);
$start = optional_param('start', 0, PARAM_INT);
$length = optional_param('length', 50, PARAM_INT);
// Calculate actual Limit end based on start and length values
$end = $start + $length;
// Using $_REQUEST as optional_param_array was not working
if (isset($_REQUEST['search'])) {
    $search = $_REQUEST['search'];
} else {
    $search = [];
}

if (isset($_REQUEST['order'])) {
    $order = $_REQUEST['order'];
} else {
    $order = [];
}

if (isset($_REQUEST['columns'])) {
    $columns = $_REQUEST['columns'];
} else {
    $columns = [];
}

// Set term value
if (isset($search['value'])) {
    $term = $search['value'];
} else {
    $term = '';
}

// Get column to be sorted
if (isset($order[0]['column'])) {
    $orderColumn = $columns[$order[0]['column']]['data'];
    $orderDirection = $order[0]['dir'];
} else {
    $orderColumn = 'name';
    $orderDirection = 'ASC';
}

$INVENTORIES = new inventories();

// Get data
$data = $INVENTORIES->get_datatable($category_id, $start, $end, $term, $orderColumn, $orderDirection);

// Create datatables object
$params = [
    "draw" => $draw,
    "recordsTotal" => $data->total_found,
    "recordsFiltered" => $data->total_found,
    "data" => $data->results
];

// Return Datatables json object
echo json_encode($params);