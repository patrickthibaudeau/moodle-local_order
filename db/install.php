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
        'name' => 'Audiovisual',
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

    // Add all event types
    $event_types = [
        'AGM',
        'AGM, Banquet',
        'AGM, Board / business meeting',
        'AGM, Board / business meeting, Keynote / plenary, Other',
        'AGM, Board / business meeting, Keynote / plenary, Panel, Paper presentation, Roundtable, Workshop',
        'AGM, Board / business meeting, Keynote / plenary, Panel, Paper presentation, Workshop',
        'AGM, Board / business meeting, Panel, Paper presentation',
        'AGM, Board / business meeting, Panel, Roundtable',
        'AGM, Display / exhibit',
        'AGM, Display / exhibit, Panel',
        'AGM, Keynote / plenary',
        'AGM, Keynote / plenary, Other',
        'AGM, Keynote / plenary, Panel',
        'AGM, Keynote / plenary, Panel, Paper presentation',
        'AGM, Keynote / plenary, Panel, Paper presentation, Roundtable, Workshop',
        'AGM, Keynote / plenary, Panel, Paper presentation, Workshop',
        'AGM, Keynote / plenary, Paper presentation',
        'AGM, Panel',
        'AGM, Panel, Paper presentation',
        'AGM, Panel, Paper presentation, Roundtable',
        'AGM, Paper presentation',
        'AGM, Reception',
        'AGM, Reception, Board / business meeting, Keynote / plenary, Panel, Paper presentation, Workshop',
        'AGM, Reception, Display / exhibit, Keynote / plenary, Other',
        'AGM, Reception, Display / exhibit, Keynote / plenary, Panel, Paper presentation',
        'AGM, Reception, Keynote / plenary, Other',
        'AGM, Reception, Roundtable, Workshop',
        'AGM, Roundtable',
        'Banquet',
        'Banquet, Board / business meeting',
        'Banquet, Keynote / plenary, Panel, Workshop',
        'Banquet, Reception',
        'Board / business meeting',
        'Board / business meeting, Display / exhibit, Panel, Paper presentation',
        'Board / business meeting, Keynote / plenary',
        'Board / business meeting, Keynote / plenary, Panel',
        'Board / business meeting, Keynote / plenary, Panel, Paper presentation',
        'Board / business meeting, Keynote / plenary, Panel, Paper presentation, Roundtable, Workshop',
        'Board / business meeting, Keynote / plenary, Panel, Paper presentation, Workshop',
        'Board / business meeting, Keynote / plenary, Workshop',
        'Board / business meeting, Panel',
        'Board / business meeting, Panel, Paper presentation',
        'Board / business meeting, Panel, Paper presentation, Roundtable, Workshop',
        'Board / business meeting, Roundtable',
        'Display / exhibit',
        'Display / exhibit, Keynote / plenary, Panel, Roundtable',
        'Display / exhibit, Other',
        'Display / exhibit, Panel',
        'Display / exhibit, Panel, Roundtable, Workshop',
        'Display / exhibit, Paper presentation, Workshop',
        'Display / exhibit, Workshop',
        'Keynote / plenary',
        'Keynote / plenary, Other',
        'Keynote / plenary, Panel',
        'Keynote / plenary, Panel, Paper presentation',
        'Keynote / plenary, Panel, Paper presentation, Roundtable',
        'Keynote / plenary, Panel, Paper presentation, Roundtable, Workshop',
        'Keynote / plenary, Panel, Paper presentation, Workshop',
        'Keynote / plenary, Panel, Roundtable',
        'Keynote / plenary, Panel, Workshop',
        'Keynote / plenary, Paper presentation',
        'Keynote / plenary, Paper presentation, Roundtable',
        'Keynote / plenary, Paper presentation, Workshop',
        'Keynote / plenary, Roundtable',
        'Other',
        'Panel',
        'Panel, Other',
        'Panel, Paper presentation',
        'Panel, Paper presentation, Roundtable',
        'Panel, Paper presentation, Roundtable, Workshop',
        'Panel, Paper presentation, Roundtable, Workshop, Other',
        'Panel, Roundtable',
        'Panel, Workshop',
        'Paper presentation',
        'Paper presentation, Roundtable',
        'Paper presentation, Roundtable, Workshop',
        'Paper presentation, Workshop',
        'Reception',
        'Reception, Display / exhibit',
        'Reception, Display / exhibit, Keynote / plenary, Roundtable, Other',
        'Reception, Display / exhibit, Other',
        'Reception, Display / exhibit, Panel',
        'Reception, Keynote / plenary',
        'Reception, Keynote / plenary, Other',
        'Reception, Keynote / plenary, Panel, Roundtable',
        'Reception, Keynote / plenary, Workshop',
        'Reception, Other',
        'Reception, Panel, Paper presentation',
        'Reception, Panel, Paper presentation, Roundtable',
        'Roundtable',
        'Workshop',
        'Workshop, Other',
    ];

    foreach($event_types as $key => $description) {
        $data = [
            'description' => $description,
            'usermodified' => $USER->id,
            'timecreated' => time(),
            'timemodified' => time()
        ];
        $DB->insert_record('order_event_type', $data);
    }

    return true;
}