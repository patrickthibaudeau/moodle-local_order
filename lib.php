<?php

use theme_congress\navdrawer;

/**
 * Build navdrawer menu items
 * @return array
 * @throws coding_exception
 * @throws dml_exception
 */
function local_order_navdrawer_items()
{

    $context = context_system::instance();
    $items = [];

    // Create submenu for import
    $import_menu = [
        navdrawer::add(
            get_string('inventory', 'local_order'),
            null,
            'https://google.ca',
            'fas fa-boxes'),
        navdrawer::add(
            get_string('campuses', 'local_order'),
            null,
            'https://google.ca',
            'fas fa-university'),
        navdrawer::add(
            get_string('buildings', 'local_order'),
            null,
            'https://microsoft.com',
            'fas fa-building'),
        navdrawer::add(
            get_string('floors', 'local_order'),
            null,
            'https://microsoft.com',
            'fas fa-grip-lines'),
        navdrawer::add(
            get_string('rooms', 'local_order'),
            null,
            'https://microsoft.com',
            'fas fa-door-open'),
    ];

    // Only add import submenu if user has capability
    if (has_capability('local/order:import', $context)) {
        $items[] = navdrawer::add(
            get_string('import', 'local_order'),
            $import_menu,
            '#',
            'fas fa-upload');
    }

    // Only add reports if user has capability
    if (has_capability('local/order:view_reports', $context)) {
        $items[] = navdrawer::add(
            get_string('reports', 'local_order'),
            null,
            '#',
            'fas fa-file-invoice');
    }



    return $items;
}