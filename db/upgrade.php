<?php
defined('MOODLE_INTERNAL') || die();

/**
 *
 * @param type $oldversion
 * @return boolean
 * @global type $CFG
 * @global \moodle_database $DB
 */
function xmldb_local_order_upgrade($oldversion)
{
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2022122901) {

        // Define field eventtypeid to be added to order_event.
        $table = new xmldb_table('order_event');
        $field = new xmldb_field('eventtypeid', XMLDB_TYPE_INTEGER, '6', null, null, null, '0', 'eventtype');

        // Conditionally launch add field eventtypeid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Order savepoint reached.
        upgrade_plugin_savepoint(true, 2022122901, 'local', 'order');
    }

    if ($oldversion < 2022123000) {

        // Define field sortorder to be added to order_campus.
        $table = new xmldb_table('order_campus');
        $field = new xmldb_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'code');

        // Conditionally launch add field sortorder.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Order savepoint reached.
        upgrade_plugin_savepoint(true, 2022123000, 'local', 'order');
    }

    if ($oldversion < 2023010500) {

        // Changing type of field building_code on table order_floor to char.
        $table = new xmldb_table('order_floor');
        $field = new xmldb_field('building_code', XMLDB_TYPE_CHAR, '50', null, null, null, null, 'id');

        // Launch change of type for field building_code.
        $dbman->change_field_type($table, $field);

        // Order savepoint reached.
        upgrade_plugin_savepoint(true, 2023010500, 'local', 'order');
    }

    if ($oldversion < 2023011401) {

        // Define field deleted to be added to order_organization.
        $table = new xmldb_table('order_organization');
        $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'website');

        // Conditionally launch add field deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field deleted to be added to order_vendor.
        $table = new xmldb_table('order_vendor');
        $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'website');

        // Conditionally launch add field deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field deleted to be added to order_inventory.
        $table = new xmldb_table('order_inventory');
        $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'cost');

        // Conditionally launch add field deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Order savepoint reached.
        upgrade_plugin_savepoint(true, 2023011401, 'local', 'order');
    }

    if ($oldversion < 2023011404) {

        // Define field chargebackaccount to be added to order_event.
        $table = new xmldb_table('order_event');
        $field = new xmldb_field('chargebackaccount', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'othernotes');

        // Conditionally launch add field chargebackaccount.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Order savepoint reached.
        upgrade_plugin_savepoint(true, 2023011404, 'local', 'order');
    }


    return true;
}