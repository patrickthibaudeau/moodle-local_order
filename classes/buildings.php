<?php
/*
 * Author: Admin User
 * Create Date: 30-12-2022
 * License: LGPL 
 * 
 */
namespace local_order;

class buildings {

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
	    $this->results = $DB->get_records('order_building');
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
    public function get_buildings_by_campus() {
        global $DB;

        $campuses = $DB->get_records(TABLE_CAMPUS, [], 'sortorder');
        $campus_buildings = [
            'select' => [
                '' => get_string('select', 'local_order')
            ]
        ];
        foreach ($campuses as $c) {
            // Get buildings based on campus
            $all_buildings = $DB->get_records(TABLE_BUILDING, ['campus_code' => $c->code], 'name');
            $buildings = [];
            // Create buildings array to be added to campus_buildings for this campius
            foreach($all_buildings as $b) {
                // If shortname available add to full building name
                if ($b->shortname) {
                    $shortname = " ($b->shortname)";
                } else {
                    $shortname = '';
                }
                $buildings[$b->code] = $b->name . $shortname;
            }
            $campus_buildings[$c->name] = $buildings;
        }

        return $campus_buildings;
    }

}