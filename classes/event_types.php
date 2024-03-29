<?php
/*
 * Author: Admin User
 * Create Date: 15-12-2022
 * License: LGPL 
 * 
 */
namespace local_order;

class event_types {

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
	    $this->results = $DB->get_records('order_event_type');
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
                if (strlen($r->description) > 75) {
                    $description = substr($r->description, 0, 75) . '...';
                } else {
                    $description = $r->description;
                }
	            $array[$r->id] = $description;
	      }
	    return $array;
	}

}