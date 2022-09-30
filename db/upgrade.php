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


    return true;
}