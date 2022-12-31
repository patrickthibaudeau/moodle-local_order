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

    $('#fitem_id_eventtypename').hide();

    $('.btn-add-event-type').on('click', function(){
        if ($(this).hasClass('close-event')) {
            $('#id_eventtypename').val('');
            $('#fitem_id_eventtypename').hide();
            $(this).html('<i class= "fa fa-plus"></i>');
            $(this).removeClass('close-event');
        } else {
            $('#fitem_id_eventtypename').show();
            $(this).html('<i class= "fa fa-minus"></i>');
            $(this).addClass('close-event');
        }
    });
};