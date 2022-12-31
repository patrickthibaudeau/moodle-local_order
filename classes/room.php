<?php
/*
 * Author: Admin User
 * Create Date: 30-12-2022
 * License: LGPL 
 * 
 */
namespace local_order;

use local_order\crud;

class room extends crud {


	/**
	 *
	 *@var int
	 */
	private $id;

	/**
	 *
	 *@var int
	 */
	private $floor_id;

	/**
	 *
	 *@var int
	 */
	private $room_type_id;

	/**
	 *
	 *@var string
	 */
	private $code;

	/**
	 *
	 *@var string
	 */
	private $name;

	/**
	 *
	 *@var int
	 */
	private $capacity;

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

		$this->table = 'order_room';

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

		$this->floor_id = $result->floor_id ?? 0;
		$this->room_type_id = $result->room_type_id ?? 0;
		$this->code = $result->code ?? '';
		$this->name = $result->name ?? '';
		$this->capacity = $result->capacity ?? 0;
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
	 * @return floor_id - bigint (18)
	 */
	public function get_floor_id(){
		return $this->floor_id;
	}

	/**
	 * @return room_type_id - bigint (18)
	 */
	public function get_room_type_id(){
		return $this->room_type_id;
	}

	/**
	 * @return code - varchar (50)
	 */
	public function get_code(){
		return $this->code;
	}

	/**
	 * @return name - varchar (255)
	 */
	public function get_name(){
		return $this->name;
	}

	/**
	 * @return capacity - int (9)
	 */
	public function get_capacity(){
		return $this->capacity;
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
	public function set_floor_id($floor_id){
		$this->floor_id = $floor_id;
	}

	/**
	 * @param Type: bigint (18)
	 */
	public function set_room_type_id($room_type_id){
		$this->room_type_id = $room_type_id;
	}

	/**
	 * @param Type: varchar (50)
	 */
	public function set_code($code){
		$this->code = $code;
	}

	/**
	 * @param Type: varchar (255)
	 */
	public function set_name($name){
		$this->name = $name;
	}

	/**
	 * @param Type: int (9)
	 */
	public function set_capacity($capacity){
		$this->capacity = $capacity;
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