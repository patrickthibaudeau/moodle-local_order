<?php
/*
 * Author: Admin User
 * Create Date: 15-12-2022
 * License: LGPL 
 * 
 */
namespace local_order;

class events {

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
	    $this->results = $DB->get_records('order_event');
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
     * Returns number of events today or None
     */
    public function get_events_count_today() {
        global $DB;

        $number_of_events = get_string('none', 'local_order');

        $sql = 'SELECT count(id) as total FROM {' . TABLE_EVENT . '} WHERE starttime BETWEEN ? AND ?';
        $starttime = strtotime(date('Y-m-d 00:00:00', time()));
        $endtime = strtotime(date('Y-m-d 23:59:59', time()));

        $events = $DB->get_record_sql($sql, [$starttime, $endtime]);

        if ($events->total > 0) {
            $number_of_events = $events->total;
        }

        return $number_of_events;
    }

}