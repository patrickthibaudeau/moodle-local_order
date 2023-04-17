<?php
include_once('../../../config.php');

use local_order\event;

global $DB, $OUTPUT, $PAGE;

$context = context_system::instance();

$PAGE->set_context($context);

$id = required_param('id', PARAM_INT);


$EVENT = new event($id);

echo $EVENT->get_total_amount_with_taxes();
