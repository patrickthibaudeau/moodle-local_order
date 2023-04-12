// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Potential user selector module.
 *
 * @module     local_order/import
 * @class      import
 * @package
 */

import $ from 'jquery';

export const init = () => {
    $('#local_order_inventory_instructions').hide();
    $('#local_order_event_instructions').hide();


    // onload display the menu
    if ($('#id_import_type').val() == 'inventory' || $('#id_import_type').val() == 'event') {
        $('#fitem_id_inventory_category').show();
    } else {
        $('#fitem_id_inventory_category').hide();
    }

    // Show timezone
    if ($('#id_import_type').val() == 'event') {
        $('#fitem_id_timezone').show();
    } else {
        $('#fitem_id_timezone').hide();
    }

    // on import type change, display or hide the menu
    $('#id_import_type').on('change', function() {
        if ($('#id_import_type').val() == 'event' || $('#id_import_type').val() == 'event_inventory' || $('#id_import_type').val() == 'inventory') {
            $('#fitem_id_inventory_category').show();
            $('#local_order_inventory_instructions').hide();
            $('#local_order_event_instructions').hide();
        } else {
            $('#fitem_id_inventory_category').hide();
            $('#local_order_inventory_instructions').hide();
            $('#local_order_event_instructions').hide();
        }

        if ($('#id_import_type').val() == 'event') {
            $('#fitem_id_timezone').show();
        } else {
            $('#fitem_id_timezone').hide();
        }
    });

    // Update instruction base on inventory category
    $('#id_inventory_category').on('change', function () {
        if ($(this).val() !== '') {
            if ($('#id_import_type').val() == 'inventory') {
                $('.local_order_type').html($('#id_inventory_category option:selected').text());
                $('#local_order_inventory_instructions').show();
                $('#local_order_event_instructions').hide();
            } else if ($('#id_import_type').val() == 'event') {
                $('.local_order_event').html($('#id_inventory_category option:selected').text());
                $('#local_order_event_instructions').show();
                $('#local_order_inventory_instructions').hide();
            }

        } else {
            $('#local_order_inventory_instructions').hide();
            $('#local_order_event_instructions').hide();
        }
    });
};