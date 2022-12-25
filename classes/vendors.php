<?php
/*
 * Author: Admin User
 * Create Date: 15-12-2022
 * License: LGPL 
 * 
 */
namespace local_order;

class vendors {

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
	    $this->results = $DB->get_records('order_vendor', [], 'name');
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

    public function get_number_of_vendors() {
        global $DB;

        $number_of_vendors = get_string('none', 'local_order');

        if ($count = $DB->count_records(TABLE_VENDOR, [])) {
            $number_of_vendors = $count;
        }

        return $number_of_vendors;
    }

}