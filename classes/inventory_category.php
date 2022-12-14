<?php
/*
 * Author: Admin User
 * Create Date: 15-12-2022
 * License: LGPL 
 * 
 */
namespace local_order;

use local_order\crud;

class inventory_category extends crud {


	/**
	 *
	 *@var int
	 */
	private $id;

	/**
	 *
	 *@var int
	 */
	private $eventid;

	/**
	 *
	 *@var int
	 */
	private $inventorycategoryid;

	/**
	 *
	 *@var string
	 */
	private $name;

	/**
	 *
	 *@var string
	 */
	private $notes;

	/**
	 *
	 *@var string
	 */
	private $adminnotes;

	/**
	 *
	 *@var int
	 */
	private $setuptime;

	/**
	 *
	 *@var int
	 */
	private $teardowntime;

	/**
	 *
	 *@var int
	 */
	private $usermodified;

	/**
	 *
	 *@var int
	 */
	private $timecreated;

	/**
	 *
	 *@var string
	 */
	private $timecreated_hr;

	/**
	 *
	 *@var int
	 */
	private $timemodified;

	/**
	 *
	 *@var string
	 */
	private $timemodified_hr;

	/**
	 *
	 *@var string
	 */
	private $table;


    /**
     *  
     *
     */
	public function __construct($id = 0){
  	global $CFG, $DB, $DB;

		$this->table = 'order_event_inv_category';

		parent::set_table($this->table);

      if ($id) {
         $this->id = $id;
         parent::set_id($this->id);
         $result = $this->get_record($this->table, $this->id);
      } else {
        $result = new \stdClass();
         $this->id = 0;
         parent::set_id($this->id);
      }

		$this->eventid = $result->eventid ?? 0;
		$this->inventorycategoryid = $result->inventorycategoryid ?? 0;
		$this->name = $result->name ?? '';
		$this->notes = $result->notes ?? '';
		$this->adminnotes = $result->adminnotes ?? '';
		$this->setuptime = $result->setuptime ?? 0;
		$this->teardowntime = $result->teardowntime ?? 0;
		$this->usermodified = $result->usermodified ?? 0;
		$this->timecreated = $result->timecreated ?? 0;
          $this->timecreated_hr = '';
          if ($this->timecreated) {
		        $this->timecreated_hr = strftime(get_string('strftimedate'),$result->timecreated);
          }
		$this->timemodified = $result->timemodified ?? 0;
      $this->timemodified_hr = '';
          if ($this->timemodified) {
		        $this->timemodified_hr = strftime(get_string('strftimedate'),$result->timemodified);
          }
	}

	/**
	 * @return id - bigint (18)
	 */
	public function get_id(){
		return $this->id;
	}

	/**
	 * @return eventid - bigint (18)
	 */
	public function get_eventid(){
		return $this->eventid;
	}

	/**
	 * @return inventorycategoryid - bigint (18)
	 */
	public function get_inventorycategoryid(){
		return $this->inventorycategoryid;
	}

	/**
	 * @return name - varchar (255)
	 */
	public function get_name(){
		return $this->name;
	}

	/**
	 * @return notes - longtext (-1)
	 */
	public function get_notes(){
		return $this->notes;
	}

	/**
	 * @return adminnotes - longtext (-1)
	 */
	public function get_adminnotes(){
		return $this->adminnotes;
	}

	/**
	 * @return setuptime - bigint (18)
	 */
	public function get_setuptime(){
		return $this->setuptime;
	}

	/**
	 * @return teardowntime - bigint (18)
	 */
	public function get_teardowntime(){
		return $this->teardowntime;
	}

	/**
	 * @return usermodified - bigint (18)
	 */
	public function get_usermodified(){
		return $this->usermodified;
	}

	/**
	 * @return timecreated - bigint (18)
	 */
	public function get_timecreated(){
		return $this->timecreated;
	}

	/**
	 * @return timemodified - bigint (18)
	 */
	public function get_timemodified(){
		return $this->timemodified;
	}

	/**
	 * @param Type: bigint (18)
	 */
	public function set_id($id){
		$this->id = $id;
	}

	/**
	 * @param Type: bigint (18)
	 */
	public function set_eventid($eventid){
		$this->eventid = $eventid;
	}

	/**
	 * @param Type: bigint (18)
	 */
	public function set_inventorycategoryid($inventorycategoryid){
		$this->inventorycategoryid = $inventorycategoryid;
	}

	/**
	 * @param Type: varchar (255)
	 */
	public function set_name($name){
		$this->name = $name;
	}

	/**
	 * @param Type: longtext (-1)
	 */
	public function set_notes($notes){
		$this->notes = $notes;
	}

	/**
	 * @param Type: longtext (-1)
	 */
	public function set_adminnotes($adminnotes){
		$this->adminnotes = $adminnotes;
	}

	/**
	 * @param Type: bigint (18)
	 */
	public function set_setuptime($setuptime){
		$this->setuptime = $setuptime;
	}

	/**
	 * @param Type: bigint (18)
	 */
	public function set_teardowntime($teardowntime){
		$this->teardowntime = $teardowntime;
	}

	/**
	 * @param Type: bigint (18)
	 */
	public function set_usermodified($usermodified){
		$this->usermodified = $usermodified;
	}

	/**
	 * @param Type: bigint (18)
	 */
	public function set_timecreated($timecreated){
		$this->timecreated = $timecreated;
	}

	/**
	 * @param Type: bigint (18)
	 */
	public function set_timemodified($timemodified){
		$this->timemodified = $timemodified;
	}

}