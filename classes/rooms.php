<?php
/*
 * Author: Admin User
 * Create Date: 30-12-2022
 * License: LGPL 
 * 
 */
namespace local_order;

//require_once('../lib.php');

class rooms {

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
	    $this->results = $DB->get_records('order_room');
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
     * @param $building_code
     * @return array
     * @throws \dml_exception
     */
    public function get_rooms_by_building_floor($building_code) {
        global $DB;
        // Get floors
        $floors = $DB->get_records(TABLE_FLOOR, ['building_code' => $building_code], 'code');
        // Array that will be returned key = room id value = room code and capacity
        $rooms = [];
        // In statement variable
        $in_ids = '';
        foreach($floors as $f) {
            $in_ids .= $f->id . ',';
        }
        // Prepare values for in statement
        $in_ids = rtrim($in_ids, ',');

        // Get rooms
        $sql = "SELECT * FROM {order_room} WHERE floor_id IN ($in_ids) ORDER BY code ASC";
        $floor_rooms = $DB->get_recordset_sql($sql);

        // Loop through records and build room array
        foreach($floor_rooms as $fr) {
            // Get room type
            $room_type = $DB->get_record(TABLE_ROOM_TYPE, ['id' => $fr->room_type_id]);
            if ($fr->capacity) {
                $capacity = " ($fr->capacity)";
            } else {
                $capacity = '';
            }
            $rooms[$fr->id] = $fr->code . ' - ' . $room_type->name . $capacity;
        }
        return $rooms;
    }

}