<?php
include_once('../../../config.php');

global $DB, $PAGE, $USER;

$action = required_param('action', PARAM_TEXT);

switch ($action) {
    case 'vendor_contact':
        $vendorid = required_param('vendorid', PARAM_INT);
        $userid = required_param('userid', PARAM_INT);
        $params = [
            'vendorid' => $vendorid,
            'userid' => $userid,
            'usermodified' => $USER->id,
            'timecreated' => time(),
            'timemodified' => time()
        ];

        $id = $DB->insert_record('order_vendor_contact', $params);
        echo $id;
        break;
}