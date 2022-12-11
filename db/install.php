<?php
defined('MOODLE_INTERNAL') || die();

/**
 *
 * @param type $oldversion
 * @return boolean
 * @global type $CFG
 * @global \moodle_database $DB
 */
function xmldb_local_order_install()
{
    global $CFG, $DB, $USER;

    $dbman = $DB->get_manager();

    // Add default Inventory categories
    $av = [
        'name' => 'Audio/Visual',
        'code' => 'AV',
        'parent' => 1,
        'path' => '/1',
        'usermodified' => $USER->id,
        'timecreated' => time(),
        'timemodified' => time()
    ];

    $DB->insert_record('order_inventory_category', $av);

    $catering = [
        'name' => 'Catering',
        'code' => 'C',
        'parent' => 2,
        'path' => '/2',
        'usermodified' => $USER->id,
        'timecreated' => time(),
        'timemodified' => time()
    ];

    $DB->insert_record('order_inventory_category', $catering);

    $furnishing = [
        'name' => 'Furnishing',
        'code' => 'F',
        'parent' => 3,
        'path' => '/3',
        'usermodified' => $USER->id,
        'timecreated' => time(),
        'timemodified' => time()
    ];

    $DB->insert_record('order_inventory_category', $furnishing);

    return true;
}