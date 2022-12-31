<?php

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
 * External Web Service Template
 *
 * @package    localwstemplate
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");
require_once("$CFG->dirroot/config.php");

class local_order_external_event_type extends external_api
{
    //**************************** GET USERS **********************
    /**
     * Returns users parameters
     * @return external_function_parameters
     */
    public static function get_event_types_parameters()
    {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'Event type id', false, -1),
                'name' => new external_value(PARAM_TEXT, 'Description', false)
            )
        );
    }

    /**
     * Returns users
     * @return string users
     * @global moodle_database $DB
     */
    public static function get_event_types($id, $name = "")
    {
        global $DB;

        $params = self::validate_parameters(self::get_event_types_parameters(), array(
                'id' => $id,
                'name' => $name
            )
        );

        if (strlen($name) >= 3) {
            $sql = "select id, description from {order_event_type} where ";
            $name = str_replace(' ', '%', $name);
            $sql .= " (description like '%$name%')"; //How the ajax call with search via the form autocomplete
            $sql .= " Order by description";
            //How the ajax call with search via the form autocomplete
            $results = $DB->get_records_sql($sql, array($name));
        } else {
            $results = [];
        }

        $event_types = [];
        $i = 0;
        foreach ($results as $r) {
            $event_types[$i]['id'] = $r->id;
            $event_types[$i]['name'] = $r->description;
            $i++;
        }
        return $event_types;
    }

    /**
     * Get Users
     * @return single_structure_description
     */
    public static function event_types_details()
    {
        $fields = array(
            'id' => new external_value(PARAM_INT, 'Record id', false),
            'name' => new external_value(PARAM_TEXT, 'Description', true),
        );
        return new external_single_structure($fields);
    }

    /**
     * Returns users result value
     * @return external_description
     */
    public static function get_event_types_returns()
    {
        return new external_multiple_structure(self::event_types_details());
    }
}
