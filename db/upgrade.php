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

    if ($oldversion < 2023040500) {

        // Define field workorder to be added to order_event.
        $table = new xmldb_table('order_event');
        $field = new xmldb_field('workorder', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'chargebackaccount');

        // Conditionally launch add field workorder.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Order savepoint reached.
        upgrade_plugin_savepoint(true, 2023040500, 'local', 'order');
    }

    if ($oldversion < 2023040600) {

        // Define table order_event_inventory_hist to be created.
        $table = new xmldb_table('order_event_inventory_hist');

        // Adding fields to table order_event_inventory_hist.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('eventinventoryid', XMLDB_TYPE_INTEGER, '10', null, null, null, 0);
        $table->add_field('eventcategoryid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('vendorid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('inventoryid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('quantity', XMLDB_TYPE_INTEGER, '7', null, null, null, '0');
        $table->add_field('cost', XMLDB_TYPE_NUMBER, '12, 2', null, null, null, '0');
        $table->add_field('roomid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table order_event_inventory_hist.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

        // Conditionally launch create table for order_event_inventory_hist.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Order savepoint reached.
        upgrade_plugin_savepoint(true, 2023040600, 'local', 'order');
    }

    if ($oldversion < 2023040700) {

        // Define field eventid to be added to order_event_inventory_hist.
        $table = new xmldb_table('order_event_inventory_hist');
        $field = new xmldb_field('eventid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'id');

        // Conditionally launch add field eventid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Order savepoint reached.
        upgrade_plugin_savepoint(true, 2023040700, 'local', 'order');
    }

    if ($oldversion < 2023040701) {

        // Define field status to be added to order_event.
        $table = new xmldb_table('order_event');
        $field = new xmldb_field('status', XMLDB_TYPE_INTEGER, '2', null, null, null, '0', 'name');

        // Conditionally launch add field status.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Order savepoint reached.
        upgrade_plugin_savepoint(true, 2023040701, 'local', 'order');
    }

    if ($oldversion < 2023040900) {

        // Define field costcentre to be added to order_organization.
        $table = new xmldb_table('order_organization');
        $field = new xmldb_field('costcentre', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'website');

        // Conditionally launch add field costcentre.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field fund to be added to order_organization.
        $table = new xmldb_table('order_organization');
        $field = new xmldb_field('fund', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'costcentre');

        // Conditionally launch add field fund.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field activitycode to be added to order_organization.
        $table = new xmldb_table('order_organization');
        $field = new xmldb_field('activitycode', XMLDB_TYPE_CHAR, '25', null, null, null, null, 'fund');

        // Conditionally launch add field activitycode.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Order savepoint reached.
        upgrade_plugin_savepoint(true, 2023040900, 'local', 'order');
    }

    if ($oldversion < 2023040901) {

        // Define field ccemail to be added to order_organization.
        $table = new xmldb_table('order_organization');
        $field = new xmldb_field('ccemail', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'email');

        // Conditionally launch add field ccemail.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Order savepoint reached.
        upgrade_plugin_savepoint(true, 2023040901, 'local', 'order');
    }

    if ($oldversion < 2023040902) {

        // Define table order_room_basic to be created.
        $table = new xmldb_table('order_room_basic');

        // Adding fields to table order_room_basic.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('building_name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('building_shortname', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table order_room_basic.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

        // Conditionally launch create table for order_room_basic.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Order savepoint reached.
        upgrade_plugin_savepoint(true, 2023040902, 'local', 'order');
    }

    if ($oldversion < 2023041300) {

        // Define field section to be added to order_event_inventory.
        $table = new xmldb_table('order_event_inventory');
        $field = new xmldb_field('section', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'inventoryid');

        // Conditionally launch add field section.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Order savepoint reached.
        upgrade_plugin_savepoint(true, 2023041300, 'local', 'order');
    }

    if ($oldversion < 2023041502) {

        // Define table order_event_inv_status to be created.
        $table = new xmldb_table('order_event_inv_status');

        // Adding fields to table order_event_inv_status.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('av', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('catering', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('furnishing', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table order_event_inv_status.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

        // Conditionally launch create table for order_event_inv_status.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Order savepoint reached.
        upgrade_plugin_savepoint(true, 2023041502, 'local', 'order');
    }

    if ($oldversion < 2023041503) {

        // Define field eventid to be added to order_event_inv_status.
        $table = new xmldb_table('order_event_inv_status');
        $field = new xmldb_field('eventid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'id');

        // Conditionally launch add field eventid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Order savepoint reached.
        upgrade_plugin_savepoint(true, 2023041503, 'local', 'order');
    }
    return true;
}