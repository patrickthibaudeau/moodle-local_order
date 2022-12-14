<?php
/*
 * Author: Admin User
 * Create Date: 13-12-2022
 * License: LGPL 
 * 
 */
namespace local_order;

class inventories {

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
	    $this->results = $DB->get_records('order_inventory', [], "name");
	}

	/**
	  * Get records
	 */
	public function get_records() {
	    return $this->results;
	}

    /**
     * Get records based on inventory category
     * @param $inventory_category int default 1 Audovisual
     * @return array
     * @throws \dml_exception
     */
    public function get_records_by_category($inventory_category = 1) {
        global $DB;

        return $DB->get_records(TABLE_INVENTORY, ['inventorycategoryid' => $inventory_category], "name");
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

}