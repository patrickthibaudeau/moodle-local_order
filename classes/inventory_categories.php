<?php
/*
 * Author: Admin User
 * Create Date: 10-12-2022
 * License: LGPL 
 * 
 */
namespace local_order;

class inventory_categories {

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
	    $this->results = $DB->get_records('order_inventory_category', [], 'name');
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
	public function get_select_array($use_select = false) {
        // What should the 0 key be called
        if ($use_select) {
            $name = get_string('select', 'local_order');
        } else {
            $name = get_string('all', 'local_order');
        }
	    $array = [
	        '0' => $name
	      ];
	      foreach($this->results as $r) {
	            $array[$r->id] = $r->name;
	      }
	    return $array;
	}

}