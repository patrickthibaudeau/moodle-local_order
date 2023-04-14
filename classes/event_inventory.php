<?php
/*
 * Author: Admin User
 * Create Date: 3-01-2023
 * License: LGPL 
 * 
 */

namespace local_order;

use local_order\crud;

class event_inventory extends crud
{


    /**
     *
     * @var int
     */
    private $id;

    /**
     *
     * @var int
     */
    private $eventcategoryid;

    /**
     *
     * @var int
     */
    private $vendorid;

    /**
     *
     * @var int
     */
    private $inventoryid;

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var string
     */
    private $description;

    /**
     *
     * @var int
     */
    private $quantity;

    /**
     *
     * @var string
     */
    private $cost;

    /**
     *
     * @var int
     */
    private $roomid;

    /**
     *
     * @var int
     */
    private $usermodified;

    /**
     *
     * @var int
     */
    private $timecreated;

    /**
     *
     * @var string
     */
    private $timecreated_hr;

    /**
     *
     * @var int
     */
    private $timemodified;

    /**
     *
     * @var string
     */
    private $timemodified_hr;

    /**
     *
     * @var string
     */
    private $section;

    /**
     *
     * @var string
     */
    private $table;


    /**
     *
     *
     */
    public function __construct($id = 0)
    {
        global $CFG, $DB, $DB;

        $this->table = 'order_event_inventory';

        parent::set_table($this->table);

        if ($id) {
            $this->id = $id;
            parent::set_id($this->id);
            $result = $this->get_record($this->table, $this->id);
            $event_category = $DB->get_record('order_event_inv_category', ['id' => $result->eventcategoryid]);
            $this->eventid = $event_category->eventid;
        } else {
            $result = new \stdClass();
            $this->id = 0;
            $this->eventid = 0;
            parent::set_id($this->id);
        }

        $this->eventcategoryid = $result->eventcategoryid ?? 0;
        $this->vendorid = $result->vendorid ?? 0;
        $this->inventoryid = $result->inventoryid ?? 0;
        $this->name = $result->name ?? '';
        $this->section = $result->section ?? '';
        $this->description = $result->description ?? '';
        $this->quantity = $result->quantity ?? 0;
        $this->cost = $result->cost ?? 0;
        $this->roomid = $result->roomid ?? 0;
        $this->usermodified = $result->usermodified ?? 0;
        $this->timecreated = $result->timecreated ?? 0;
        $this->timecreated_hr = '';
        if ($this->timecreated) {
            $this->timecreated_hr = strftime(get_string('strftimedate'), $result->timecreated);
        }
        $this->timemodified = $result->timemodified ?? 0;
        $this->timemodified_hr = '';
        if ($this->timemodified) {
            $this->timemodified_hr = strftime(get_string('strftimedate'), $result->timemodified);
        }
    }

    /**
     * @param $data stdClass
     * @return bool|int
     * @throws \dml_exception
     */
    public function insert_record($data)
    {
        global $DB, $USER;

        if ($data) {
            if (!isset($data->timecreated)) {
                $data->timecreated = time();
            }

            if (!isset($data->timemodified)) {
                $data->timemodified = time();
            }

            //Set user
            $data->usermodified = $USER->id;

            // only insert if record doesn't already exist
            $params = [
                'eventcategoryid' => $data->eventcategoryid,
                'inventoryid' => $data->inventoryid,
                'section' => $data->section
            ];

            if (!$exists = $DB->get_record($this->table, $params)) {
                $id = $DB->insert_record($this->table, $data);
                // Now insert into history table
                $NEW_RECORD = new event_inventory($id);
                $new_record = $NEW_RECORD->get_record();
                unset($new_record->id);
                $new_record->eventinventoryid = $id;
                $new_record->eventid = $NEW_RECORD->get_eventid();
                $DB->insert_record('order_event_inventory_hist', $new_record);

                return $id;
            }

            return false;

        } else {
            error_log('No data provided');
        }

    }

    /**
     * @param $data stdClass
     * @return bool
     * @throws \dml_exception
     */
    public function update_record($data)
    {
        global $DB, $USER;

        //Get current record and save to history table
        $this->create_history_record();

        if ($data) {
            // Set timemodified
            if (!isset($data->timemodified)) {
                $data->timemodified = time();
            }

            //Set user
            $data->usermodified = $USER->id;

            $id = $DB->update_record($this->table, $data);

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
        //Get current record and save to history table
        $this->create_history_record();

        if ($this->id) {
            $DB->delete_records($this->table, ['id' => $this->id]);
        } else {
            error_log('No id number provided');
        }

    }

    private function create_history_record()
    {
        global $DB;
        $current_record = $this->get_record();
        $current_record->eventinventoryid = $current_record->id;
        $current_record->eventid = $this->eventid;
        unset($current_record->id);
        $DB->insert_record('order_event_inventory_hist', $current_record);
    }

    public function get_table()
    {
        return $this->table;
    }

    /**
     * @return id - bigint (18)
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @return eventid - bigint (18)
     */
    public function get_eventid()
    {
        return $this->eventid;
    }

    /**
     * @return eventcategoryid - bigint (18)
     */
    public function get_eventcategoryid()
    {
        return $this->eventcategoryid;
    }

    public function get_event_inventory_category_details($event_inventory_category_id)
    {
        global $DB;
        return $DB->get_record(TABLE_EVENT_INVENTORY_CATEGORY, ['id' => $event_inventory_category_id]);
    }

    /**
     * @return vendorid - bigint (18)
     */
    public function get_vendorid()
    {
        return $this->vendorid;
    }

    /**
     * @return inventoryid - bigint (18)
     */
    public function get_inventoryid()
    {
        return $this->inventoryid;
    }

    /**
     * @return name - varchar (255)
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @return section - varchar (255)
     */
    public function get_section()
    {
        return $this->section;
    }

    /**
     * @return description - longtext (-1)
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     * @return quantity - int (9)
     */
    public function get_quantity()
    {
        return $this->quantity;
    }

    /**
     * @return cost - decimal (12)
     */
    public function get_cost()
    {
        return $this->cost;
    }

    /**
     * @return roomid - bigint (18)
     */
    public function get_roomid()
    {
        return $this->roomid;
    }

    /**
     * @return usermodified - bigint (18)
     */
    public function get_usermodified()
    {
        return $this->usermodified;
    }

    /**
     * @return timecreated - bigint (18)
     */
    public function get_timecreated()
    {
        return $this->timecreated;
    }

    /**
     * @return timemodified - bigint (18)
     */
    public function get_timemodified()
    {
        return $this->timemodified;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_eventcategoryid($eventcategoryid)
    {
        $this->eventcategoryid = $eventcategoryid;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_vendorid($vendorid)
    {
        $this->vendorid = $vendorid;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_inventoryid($inventoryid)
    {
        $this->inventoryid = $inventoryid;
    }

    /**
     * @param Type: varchar (255)
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     * @param Type: longtext (-1)
     */
    public function set_description($description)
    {
        $this->description = $description;
    }

    /**
     * @param Type: int (9)
     */
    public function set_quantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @param Type: decimal (12)
     */
    public function set_cost($cost)
    {
        $this->cost = $cost;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_roomid($roomid)
    {
        $this->roomid = $roomid;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_usermodified($usermodified)
    {
        $this->usermodified = $usermodified;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_timecreated($timecreated)
    {
        $this->timecreated = $timecreated;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_timemodified($timemodified)
    {
        $this->timemodified = $timemodified;
    }

}