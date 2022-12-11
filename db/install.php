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

    // Make phone fields bigger
    // Changing precision of field code on table order_organization to (50).
    $table = new xmldb_table('user');
    $field = new xmldb_field('phone1', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'emailstop');
    $field2 = new xmldb_field('phone2', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'phone1');

    // Launch change of precision for field code.
    $dbman->change_field_precision($table, $field);
    $dbman->change_field_precision($table, $field2);

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