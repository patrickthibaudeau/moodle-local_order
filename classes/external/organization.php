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

class local_order_external_organization extends external_api
{
    //**************************** GET USERS **********************
    /**
     * Returns users parameters
     * @return external_function_parameters
     */
    public static function get_organizations_parameters()
    {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'Organization id', false, -1),
                'name' => new external_value(PARAM_TEXT, 'Organization name', false)
            )
        );
    }

    /**
     * Returns users
     * @return string users
     * @global moodle_database $DB
     */
    public static function get_organizations($id, $name = "")
    {
        global $DB;

        $params = self::validate_parameters(self::get_organizations_parameters(), array(
                'id' => $id,
                'name' => $name
            )
        );

        if (strlen($name) >= 3) {
            $sql = "select id, name, code, email from {order_organization} where ";
            $name = str_replace(' ', '%', $name);
            $sql .= " (name like '%$name%') or (code like '%$name%')"; //How the ajax call with search via the form autocomplete
            $sql .= " Order by name";
            //How the ajax call with search via the form autocomplete
            $results = $DB->get_records_sql($sql, array($name));
        } else {
            $results = [];
        }

        $organizations = [];
        $i = 0;
        foreach ($results as $r) {
            $organizations[$i]['id'] = $r->id;
            $organizations[$i]['name'] = $r->name;
            $organizations[$i]['code'] = $r->code;
            $organizations[$i]['email'] = $r->email;
            $i++;
        }
        return $organizations;
    }

    /**
     * Get Users
     * @return single_structure_description
     */
    public static function organizations_details()
    {
        $fields = array(
            'id' => new external_value(PARAM_INT, 'Record id', false),
            'name' => new external_value(PARAM_TEXT, 'Organization name', true),
            'code' => new external_value(PARAM_TEXT, 'code', true),
            'email' => new external_value(PARAM_TEXT, 'email', true)
        );
        return new external_single_structure($fields);
    }

    /**
     * Returns users result value
     * @return external_description
     */
    public static function get_organizations_returns()
    {
        return new external_multiple_structure(self::organizations_details());
    }
}
