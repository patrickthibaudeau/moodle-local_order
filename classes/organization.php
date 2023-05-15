<?php
/*
 * Author: Admin User
 * Create Date: 15-12-2022
 * License: LGPL 
 * 
 */
namespace local_order;

use local_order\crud;

class organization extends crud {


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
	private $code;

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
    private $costcentre;

    /**
     *
     *@var int
     */
    private $fund;

    /**
     *
     *@var string
     */
    private $activitycode;

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

		$this->table = 'order_organization';

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
		$this->code = $result->code ?? '';
		$this->description = $result->description ?? '';
		$this->address = $result->address ?? '';
		$this->postal = $result->postal ?? '';
		$this->province = $result->province ?? '';
		$this->country = $result->country ?? '';
		$this->phone = $result->phone ?? '';
		$this->email = $result->email ?? '';
		$this->website = $result->website ?? '';
        $this->costcentre = $result->costcentre ?? 0;
        $this->fund = $result->fund ?? 0;
        $this->activitycode = $result->activitycode ?? '';
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
	 * @return code - varchar (50)
	 */
	public function get_code(){
		return $this->code;
	}

    /**
     * @return int
     */
    public function get_costcentre(){
        return $this->costcentre;
    }

    /**
     * @return int
     */
    public function get_fund(){
        return $this->fund;
    }

    /**
     * @return string
     */
    public function get_activitycode(){
        return $this->activitycode;
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
	 * @return phone - varchar (100)
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
	 * @param Type: varchar (50)
	 */
	public function set_code($code){
		$this->code = $code;
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
	 * @param Type: varchar (100)
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

    /**
     * Get the contact user and return array
     * @return array|false
     * @throws \dml_exception
     */
    public function get_contact_user() {
        global $DB;

        if ($found = $DB->get_record('order_organization_contact', ['organizationid' => $this->id])) {
            $user = $DB->get_record('user', ['id' => $found->userid]);
            return $user->id;
        }

        return false;
    }

    /**
     * Checks to see if this organization is used in an event
     * @return int
     * @throws \dml_exception
     */
    public function is_used() {
        global $DB;
        return $DB->count_records('order_event', ['organizationid' => $this->id]);
    }

    /**
     * @param $data stdClass
     * @return bool
     * @throws \dml_exception
     */
    public function update_contact_record($data)
    {
        global $DB, $USER;

        // Get contact record
        $record = $DB->get_record('order_organization_contact', ['organizationid' => $data->organizationid]);
        $data->id = $record->id;
        if ($data) {
            // Set timemodified
            if (!isset($data->timemodified)) {
                $data->timemodified = time();
            }

            //Set user
            $data->usermodified = $USER->id;

            $id = $DB->update_record('order_organization_contact', $data);

            return $id;
        } else {
            error_log('No data provided');
        }
    }

    /**
     * @param $id int
     * @return void
     * @throws \dml_exception
     */
    public function delete_record()
    {
        global $DB;
        if ($this->id) {
            // Delete contact record
            $DB->delete_records('order_organization_contact', ['organizationid' => $this->id]);
            $DB->delete_records($this->table, ['id' => $this->id]);
        } else {
            error_log('No id number provided');
        }

    }

    /**
     * @param $data stdClass
     * @return bool
     * @throws \dml_exception
     */
    public function insert_contact_record($data)
    {
        global $DB, $USER;

        if ($data) {
            // Set timemodified
            if (!isset($data->timemodified)) {
                $data->timemodified = time();
            }

            //Set user
            $data->usermodified = $USER->id;

            // Only add record if one doesn't exist for this organization. Otherwise, update the record
            if (!$record = $DB->get_record('order_organization_contact', ['organizationid' => $data->organizationid])) {
                $id = $DB->insert_record('order_organization_contact', $data);
            } else {
                $data->id = $record->id;
                $DB->update_record('order_organization_contact', $data);
                $id = $record->id;
            }
            return $id;
        } else {
            error_log('No data provided');
        }
    }

    /**
     * Returns the inventory cost per item
     * @return \stdClass
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_inventory_cost() {
        global $DB, $CFG;
        $sql = "Select
                     oei.name  as name,
                    (SELECT SUM(oei.cost) WHERE oei.name = oei.name) as cost,
                    (SELECT count(oei.id) WHERE oei.name = oei.name) as total
                From
                    {order_event} oe Inner Join
                    {order_event_inv_category} oeic On oeic.eventid = oe.id Inner Join
                    {order_event_inventory} oei On oei.eventcategoryid = oeic.id
                Where
                    oe.organizationid = ?
                    Group By oei.name";
        $results = $DB->get_recordset_sql($sql, [$this->id]);
        $items = [];
        $total_cost = 0;
        $i = 0;
        // Format the cost
        $amount = new \NumberFormatter(get_string('currency_locale', 'local_order'),
            \NumberFormatter::CURRENCY);
        foreach($results as $result) {
            if ($result->cost != 0) {
                $items[$i] = new \stdClass();
                $items[$i]->name = preg_replace ("/^1-/", "", $result->name, 1);
                $items[$i]->cost = $amount->format($result->cost);
                $items[$i]->cost_as_number = $result->cost;
                $items[$i]->quantity = $result->total;
                $total_cost += $result->cost;
                $i++;
            }

        }
        $taxes = (($CFG->local_order_gst + $CFG->local_order_pst) / 100) * $total_cost;
        $data = new \stdClass();
        $data->subtotal = $amount->format($total_cost);
        $data->taxes = $amount->format($taxes);
        $data->total = $amount->format($total_cost + $taxes);
        $data->items = $items;

        return $data;
    }

    public function get_inventory_cost_per_category($category = 'AV') {
        global $DB;

        // Format the cost
        $amount = new \NumberFormatter(get_string('currency_locale', 'local_order'),
            \NumberFormatter::CURRENCY);

        $sql = "Select
    (SELECT SUM(oei.cost) WHERE oei.name = oei.name) as cost
        From
            {order_event} oe Inner Join
            {order_event_inv_category} oeic On oeic.eventid = oe.id Inner Join
            {order_event_inventory} oei On oei.eventcategoryid = oeic.id
        Where
            oe.organizationid = ? And
            oeic.inventorycategorycode = ?
        Group By oei.name";

        $results = $DB->get_recordset_sql($sql, [$this->id, $category]);
        $total_cost = 0;
        foreach ($results as $result) {
            $total_cost += $result->cost;
        }

        return $amount->format($total_cost);
    }

}