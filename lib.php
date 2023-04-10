<?php

use theme_congress\navdrawer;

/**
 * Define all tables
 */
define("TABLE_BUILDING", "order_building");
define("TABLE_CAMPUS", "order_campus");
define("TABLE_EVENT", "order_event");
define("TABLE_EVENT_INVENTORY", "order_event_inventory");
define("TABLE_EVENT_INVENTORY_HISTORY", "order_event_inventory_hist");
define("TABLE_EVENT_INVENTORY_CATEGORY", "order_event_inv_category");
define("TABLE_EVENT_TYPE", "order_event_type");
define("TABLE_FLOOR", "order_floor");
define("TABLE_INVENTORY", "order_inventory");
define("TABLE_INVENTORY_CATEGORY", "order_inventory_category");
define("TABLE_ORGANIZATION", "order_organization");
define("TABLE_ORGANIZATION_CONTACT", "order_organization_contact");
define("TABLE_ROOM", "order_room");
define("TABLE_ROOM_BASIC", "order_room_basic");
define("TABLE_ROOM_TYPE", "order_room_type");
define("TABLE_SET_TYPE", "order_setup_type");
define("TABLE_VENDOR", "order_vendor");
define("TABLE_VENDOR_CONTACT", "order_vendor_contact");

/**
 * Build navdrawer menu items
 * @return array
 * @throws coding_exception
 * @throws dml_exception
 */
function local_order_navdrawer_items()
{
    global $CFG;

    $context = context_system::instance();
    $items = [];

    // Create submenu for import
    $import_menu = [
        navdrawer::add(
            get_string('inventory', 'local_order'),
            null,
            '/local/order/import/index.php?import=inventory',
            'fas fa-boxes'),
        navdrawer::add(
            get_string('organizations', 'local_order'),
            null,
            '/local/order/import/index.php?import=organization',
            'fas fa-layer-group',
            true),
        navdrawer::add(
            get_string('campuses', 'local_order'),
            null,
            $CFG->wwwroot . '/local/order/import/index.php?import=campus',
            'fas fa-university'),
        navdrawer::add(
            get_string('buildings', 'local_order'),
            null,
            $CFG->wwwroot . '/local/order/import/index.php?import=building',
            'fas fa-building'),
        navdrawer::add(
            get_string('floors', 'local_order'),
            null,
            $CFG->wwwroot . '/local/order/import/index.php?import=floor',
            'fas fa-grip-lines'),
        navdrawer::add(
            get_string('room_types', 'local_order'),
            null,
            $CFG->wwwroot . '/local/order/import/index.php?import=room_type',
            'fas fa-border-style'),
        navdrawer::add(
            get_string('rooms', 'local_order'),
            null,
            $CFG->wwwroot . '/local/order/import/index.php?import=room',
            'fas fa-door-open',
            true),
        navdrawer::add(
            get_string('events', 'local_order'),
            null,
            $CFG->wwwroot . '/local/order/import/index.php?import=event',
            'fas fa-calendar-check',
            true),
    ];

    // Only add import submenu if user has capability
    if (has_capability('local/order:import', $context)) {
        $items[] = navdrawer::add(
            get_string('import', 'local_order'),
            $import_menu,
            '#',
            'fas fa-upload',
        );
    }

    // Only add reports if user has capability
    if (has_capability('local/order:reports_view', $context)) {
        $items[] = navdrawer::add(
            get_string('reports', 'local_order'),
            null,
            '#',
            'fas fa-file-invoice',
        );
    }


    return $items;
}

function local_order_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array())
{
    global $DB;

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

//    require_login(1, true);

    $fileAreas = array(
        'import',
    );

    if (!in_array($filearea, $fileAreas)) {
        return false;
    }


    $itemid = array_shift($args);
    $filename = array_pop($args);
    $path = !count($args) ? '/' : '/' . implode('/', $args) . '/';

    $fs = get_file_storage();

    $file = $fs->get_file($context->id, 'local_order', $filearea, $itemid, $path, $filename);

    // If the file does not exist.
    if (!$file) {
        send_file_not_found();
    }

    send_stored_file($file, 86400, 0, $forcedownload); // Options.
}