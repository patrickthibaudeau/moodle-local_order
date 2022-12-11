<?php
/*
 * Author: PAtrick Thibaudeau
 * Create Date: 10-12-2022
 * License: LGPL 
 * 
 */
namespace local_order;

class inventory_category {


	/**
	 *
	 *@var int
	 */
	private $id;

	/**
	 *
	 *@var string
	 */
	private $name;

	/**
	 *
	 *@var string
	 */
	private $code;

	/**
	 *
	 *@var int
	 */
	private $parent;

	/**
	 *
	 *@var string
	 */
	private $path;

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

		$this->table = 'order_inventory_category';

      if ($id) {
         $this->id = $id;
         $result = $this->get_record();
      } else {
        $result = new \stdClass();
         $this->id = 0;
      }

		$this->name = $result->name ?? '';
		$this->code = $result->code ?? '';
		$this->parent = $result->parent ?? 0;
		$this->path = $result->path ?? '';
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
     * Get record
     *
     * @global \moodle_database $DB
     * 
     */
	public function get_record(){
	    global $DB;
	    $result = $DB->get_record($this->table, ['id' => $this->id]);
	    return  $result;

	}

    /**
     * Delete the row 
     *
     * @global \moodle_database $DB
     *
     */
	public function delete_record(){
	    global $DB;
		$DB->delete_records($this->table,['id' => $this->id]);
	}

    /**
     * Insert record into selected table
     * @global \moodle_database $DB
     * @global \stdClass $USER
     * @param array or object $data
     */
	public function insert_record($data){
		global $DB, $USER;

		if (is_object($data)) {
		    $data = convert_to_array($data);
		}

		if (!isset($data['timecreated'])) {
		    $data['timecreated'] = time();
		}

		if (!isset($data['timemodified'])) {
		    $data['timemodified'] = time();
		}

		//Set user
		$data['usermodified'] = $USER->id;

		$id = $DB->insert_record($this->table, $data);

		return $id;
	}

    /**
     * Update record into selected table
     * @global \moodle_database $DB
     * @global \stdClass $USER
     * @param array or object $data
     */
	public function update_record($data){
		global $DB, $USER;

		if (is_object($data)) {
		    $data = convert_to_array($data);
		}

		if (!isset($data['timemodified'])) {
		    $data['timemodified'] = time();
		}

		//Set user
		$data['usermodified'] = $USER->id;

		$id = $DB->update_record($this->table, $data);

		return $id;
	}

	/**
	 * @return id - bigint (18)
	 */
	public function get_id(){
		return $this->id;
	}

	/**
	 * @return name - varchar (255)
	 */
	public function get_name(){
		return $this->name;
	}

	/**
	 * @return code - varchar (255)
	 */
	public function get_code(){
		return $this->code;
	}

	/**
	 * @return parent - bigint (18)
	 */
	public function get_parent(){
		return $this->parent;
	}

	/**
	 * @return path - longtext (-1)
	 */
	public function get_path(){
		return $this->path;
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
	public function set_name($name){
		$this->name = $name;
	}

	/**
	 * @param Type: varchar (255)
	 */
	public function set_code($code){
		$this->code = $code;
	}

	/**
	 * @param Type: bigint (18)
	 */
	public function set_parent($parent){
		$this->parent = $parent;
	}

	/**
	 * @param Type: longtext (-1)
	 */
	public function set_path($path){
		$this->path = $path;
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