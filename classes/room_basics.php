<?php
/*
 * Author: Admin User
 * Create Date: 9-04-2023
 * License: LGPL 
 * 
 */
namespace local_order;

class room_basics {

	/**
	 *
	 *@var string
	 */
	private $results;

	/**
	 *
	 *@global \moodle_database $DB
	 */
	public function __construct() {
	    global $DB;
	    $this->results = $DB->get_records('order_room_basic');
	}

	/**
	  * Get records
	 */
	public function get_records() {
	    return $this->results;
	}

	/**
	  * Array to be used for selects
	  * Defaults used key = record id, value = name 
	  * Modify as required. 
	 */
	public function get_select_array() {
	    $array = [
	        '' => get_string('select', 'local_order')
	      ];
	      foreach($this->results as $r) {
	            $array[$r->id] = $r->name;
	      }
	    return $array;
	}

    /**
     * Returns an multidimensional array with campuses and their buildings
     * @return array
     * @throws \dml_exception
     */
    public function get_buildings_for_template($selected = null) {
        global $DB;

        $sql = "SELECT DISTINCT 
                    building_name, 
                    building_shortname
                FROM
                    {order_room_basic}
                ORDER BY 
                    building_shortname";

        $buildings = $DB->get_recordset_sql($sql);
        $buildings_list = [];
        $i = 0;
        foreach($buildings as $building) {
            if ($selected == $building->building_shortname) {
                $buildings_list[$i]['selected'] = true;
            } else {
                $buildings_list[$i]['selected'] = false;
            }
            $buildings_list[$i]['building_name'] = $building->building_name;
            $buildings_list[$i]['building_shortname'] = $building->building_shortname;
            $i++;
        }

        return $buildings_list;
    }

    /**
     * Returns an multidimensional array with campuses and their buildings
     * @return array
     * @throws \dml_exception
     */
    public function get_rooms_based_on_building_for_template($building_shortname, $selected = null) {
        global $DB;

        $sql = "SELECT DISTINCT 
                    name, 
                    building_shortname
                FROM
                    {order_room_basic}
                WHERE 
                    building_shortname = ?
                ORDER BY 
                    name";

        $rooms = $DB->get_recordset_sql($sql, [$building_shortname]);
        $room_list = [];
        $i = 0;
        foreach($rooms as $room) {
            if ($selected == $room->name) {
                $room_list[$i]['selected'] = true;
            } else {
                $room_list[$i]['selected'] = false;
            }
            $room_list[$i]['name'] = $room->name;
            $room_list[$i]['building_shortname'] = $room->building_shortname;
            $i++;
        }

        return $room_list;
    }

    /**
     * Returns an multidimensional array with campuses and their buildings
     * @return array
     * @throws \dml_exception
     */
    public function get_rooms_based_on_building_for_js($building_shortname) {
        global $DB;

        $sql = "SELECT DISTINCT 
                    name, 
                    building_shortname
                FROM
                    {order_room_basic}
                WHERE 
                    building_shortname = ?
                ORDER BY 
                    name";

        $rooms = $DB->get_recordset_sql($sql, [$building_shortname]);
        $room_list = [];
        foreach($rooms as $room) {
            $room_list[$room->name] =  $room->name;
        }

        return $room_list;
    }

}