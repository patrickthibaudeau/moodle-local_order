<?php
/*
 * Author: Admin User
 * Create Date: 15-12-2022
 * License: LGPL 
 * 
 */
namespace local_order;

use local_order\crud;

class vendor extends crud {


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
	private $shortname;

	/**
	 *
	 *@var string
	 */
	private $description;

	/**
	 *
	 *@var string
	 */
	private $address;

	/**
	 *
	 *@var string
	 */
	private $postal;

	/**
	 *
	 *@var string
	 */
	private $province;

	/**
	 *
	 *@var string
	 */
	private $country;

	/**
	 *
	 *@var string
	 */
	private $phone;

	/**
	 *
	 *@var string
	 */
	private $email;

	/**
	 *
	 *@var string
	 */
	private $website;

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

		$this->table = 'order_vendor';

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

		$this->name = $result->name ?? '';
		$this->shortname = $result->shortname ?? '';
		$this->description = $result->description ?? '';
		$this->address = $result->address ?? '';
		$this->postal = $result->postal ?? '';
		$this->province = $result->province ?? '';
		$this->country = $result->country ?? '';
		$this->phone = $result->phone ?? '';
		$this->email = $result->email ?? '';
		$this->website = $result->website ?? '';
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
	 * @return description - longtext (-1)
	 */
	public function get_description(){
		return $this->description;
	}

	/**
	 * @return address - longtext (-1)
	 */
	public function get_address(){
		return $this->address;
	}

	/**
	 * @return postal - varchar (15)
	 */
	public function get_postal(){
		return $this->postal;
	}

	/**
	 * @return province - varchar (255)
	 */
	public function get_province(){
		return $this->province;
	}

	/**
	 * @return country - varchar (4)
	 */
	public function get_country(){
		return $this->country;
	}

	/**
	 * @return phone - varchar (25)
	 */
	public function get_phone(){
		return $this->phone;
	}

	/**
	 * @return email - varchar (255)
	 */
	public function get_email(){
		return $this->email;
	}

	/**
	 * @return website - varchar (255)
	 */
	public function get_website(){
		return $this->website;
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
	 * @param Type: varchar (50)
	 */
	public function set_shortname($shortname){
		$this->shortname = $shortname;
	}

	/**
	 * @param Type: longtext (-1)
	 */
	public function set_description($description){
		$this->description = $description;
	}

	/**
	 * @param Type: longtext (-1)
	 */
	public function set_address($address){
		$this->address = $address;
	}

	/**
	 * @param Type: varchar (15)
	 */
	public function set_postal($postal){
		$this->postal = $postal;
	}

	/**
	 * @param Type: varchar (255)
	 */
	public function set_province($province){
		$this->province = $province;
	}

	/**
	 * @param Type: varchar (4)
	 */
	public function set_country($country){
		$this->country = $country;
	}

	/**
	 * @param Type: varchar (25)
	 */
	public function set_phone($phone){
		$this->phone = $phone;
	}

	/**
	 * @param Type: varchar (255)
	 */
	public function set_email($email){
		$this->email = $email;
	}

	/**
	 * @param Type: varchar (255)
	 */
	public function set_website($website){
		$this->website = $website;
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