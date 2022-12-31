<?php
/*
 * Author: Admin User
 * Create Date: 30-12-2022
 * License: LGPL 
 * 
 */
namespace local_order;

use local_order\crud;

class building extends crud {


	/**
	 *
	 *@var int
	 */
	private $id;

	/**
	 *
	 *@var string
	 */
	private $campus_code;

	/**
	 *
	 *@var string
	 */
	private $name;

	/**
	 *
	 *@var string
	 */
	private $shortname;

	/**
	 *
	 *@var string
	 */
	private $code;

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

		$this->table = 'order_building';

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

		$this->campus_code = $result->campus_code ?? '';
		$this->name = $result->name ?? '';
		$this->shortname = $result->shortname ?? '';
		$this->code = $result->code ?? '';
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
	 * @return campus_code - varchar (255)
	 */
	public function get_campus_code(){
		return $this->campus_code;
	}

	/**
	 * @return name - varchar (255)
	 */
	public function get_name(){
		return $this->name;
	}

	/**
	 * @return shortname - varchar (50)
	 */
	public function get_shortname(){
		return $this->shortname;
	}

	/**
	 * @return code - varchar (50)
	 */
	public function get_code(){
		return $this->code;
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
	 * @param Type: varchar (255)
	 */
	public function set_campus_code($campus_code){
		$this->campus_code = $campus_code;
	}

	/**
	 * @param Type: varchar (255)
	 */
	public function set_name($name){
		$this->name = $name;
	}

	/**
	 * @param Type: varchar (50)
	 */
	public function set_shortname($shortname){
		$this->shortname = $shortname;
	}

	/**
	 * @param Type: varchar (50)
	 */
	public function set_code($code){
		$this->code = $code;
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