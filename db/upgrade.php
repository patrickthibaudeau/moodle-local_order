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



    return true;
}