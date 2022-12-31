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
 * @module     local_order/event_type_selector
 * @class      user_selector
 * @package
 */

define(['jquery', 'core/ajax', 'core/templates', 'core/str'], function($, Ajax, Templates, Str) {

    return /** @alias module:local_order/event_type_selector */ {

        transport: function(selector, query, success, failure) { //Fetches results via ajax call
            let promise;
            let perpage = 50;

            promise = Ajax.call([{
                methodname: 'local_order_get_event_types',
                args: {
                    name: query,
                }
            }]);

            promise[0].then(function(results) {
                if (results.length <= perpage) {
                    success(results); //Callback function returns an array to processResults containing the results
                    // obtained from the Ajax call
                    return;
                }
                else {
                    return Str.get_string('toomanyresults', 'local_order', '>' + perpage)
                        .then(function(toomanyresults)
                        {
                            success(toomanyresults);
                            return;
                        });
                }


            }).fail(failure);
        },

        processResults: function(selector, results) { //Fetches results from transport and returns to form menu
            let records = [];
            if ($.isArray(results)) {
                $.each(results, function(index, record) {
                    records.push({
                        value: record.id, //the value of the item selected and that is passed into the form?
                        label: record.name
                        //The text that displays inside the selection menu
                    });
                });
                return records;

            } else {
                return results;
            }
        }
    };

});
