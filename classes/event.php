<?php
/*
 * Author: Admin User
 * Create Date: 15-12-2022
 * License: LGPL 
 * 
 */

namespace local_order;

use local_order\crud;
use local_order\vendor;
use local_order\room;

include_once('../lib.php');

class event extends crud
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
    private $organizationid;

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var string
     */
    private $code;

    /**
     *
     * @var int
     */
    private $starttime;

    /**
     *
     * @var int
     */
    private $endtime;

    /**
     *
     * @var string
     */
    private $eventtype;

    /**
     *
     * @var int
     */
    private $eventtypeid;

    /**
     *
     * @var string
     */
    private $attendance;

    /**
     *
     * @var int
     */
    private $roomid;

    /**
     *
     * @var string
     */
    private $setuptype;

    /**
     *
     * @var string
     */
    private $setupnotes;

    /**
     *
     * @var string
     */
    private $adminnotes;

    /**
     *
     * @var int
     */
    private $requirescatering;

    /**
     *
     * @var string
     */
    private $othernotes;

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
    private $table;

    /**
     *
     * @var stdClass
     */
    private $record;


    /**
     *
     *
     */
    public function __construct($id = 0)
    {
        global $CFG, $DB, $DB;

        $this->table = 'order_event';

        parent::set_table($this->table);

        if ($id) {
            $this->id = $id;
            parent::set_id($this->id);
            $result = $this->get_record($this->table, $this->id);
            $this->record = $result;
        } else {
            $result = new \stdClass();
            $this->id = 0;
            parent::set_id($this->id);
            $this->record = $result;
        }

        $this->organizationid = $result->organizationid ?? 0;
        $this->name = $result->name ?? '';
        $this->code = $result->code ?? '';
        $this->starttime = $result->starttime ?? 0;
        $this->endtime = $result->endtime ?? 0;
        $this->eventtype = $result->eventtype ?? '';
        $this->eventtypeid = $result->eventtypeid ?? '';
        $this->attendance = $result->attendance ?? '';
        $this->roomid = $result->roomid ?? 0;
        $this->setuptype = $result->setuptype ?? '';
        $this->setupnotes = $result->setupnotes ?? '';
        $this->adminnotes = $result->adminnotes ?? '';
        $this->requirescatering = $result->requirescatering ?? 0;
        $this->othernotes = $result->othernotes ?? '';
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
     * @return id - bigint (18)
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @return organizationid - bigint (18)
     */
    public function get_organizationid()
    {
        return $this->organizationid;
    }

    /**
     * Returns organizatin id and name in an array
     * @return organization - array
     */
    public function get_organization()
    {
        global $DB;
        $result = $DB->get_record(TABLE_ORGANIZATION, ['id' => $this->organizationid]);
        // Swetup array
        $organization = [
            'id' => $this->organizationid,
            'name' => $result->name
        ];
        return $organization;
    }

    /**
     * @return name - varchar (255)
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @return code - varchar (255)
     */
    public function get_code()
    {
        return $this->code;
    }

    /**
     * @return starttime - bigint (18)
     */
    public function get_starttime()
    {
        return $this->starttime;
    }

    /**
     * @return endtime - bigint (18)
     */
    public function get_endtime()
    {
        return $this->endtime;
    }

    /**
     * @return eventtypeid - bigint (10)
     */
    public function get_eventtypeid()
    {
        return $this->eventtypeid;
    }

    public function get_event_type() {
        global $DB;
        $result = $DB->get_record(TABLE_EVENT_TYPE, ['id' => $this->eventtypeid]);
        $event_type = [
            'id' => $this->eventtypeid,
            'name' => $result->description
        ];

        return $event_type;
    }

    /**
     * @return attendance - varchar (100)
     */
    public function get_attendance()
    {
        return $this->attendance;
    }

    /**
     * @return roomid - bigint (18)
     */
    public function get_roomid()
    {
        return $this->roomid;
    }

    /**
     * @return setuptype - longtext (-1)
     */
    public function get_setuptype()
    {
        return $this->setuptype;
    }

    /**
     * @return setupnotes - longtext (-1)
     */
    public function get_setupnotes()
    {
        return $this->setupnotes;
    }

    /**
     * @return adminnotes - longtext (-1)
     */
    public function get_adminnotes()
    {
        return $this->adminnotes;
    }

    /**
     * @return requirescatering - tinyint (2)
     */
    public function get_requirescatering()
    {
        return $this->requirescatering;
    }

    /**
     * @return othernotes - longtext (-1)
     */
    public function get_othernotes()
    {
        return $this->othernotes;
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
     * Returns all inventory categories for this event
     * @return array|false
     * @throws \dml_exception
     */
    public function get_inventory_categories()
    {
        global $DB;
        if ($this->id) {
            $categories = $DB->get_records(TABLE_EVENT_INVENTORY_CATEGORY,
                ['eventid' => $this->id], 'inventorycategoryid');
            return $categories;
        }
        return false;
    }

    /**
     * Returns all ievent inventory items by event category id.
     * @param $event_category_id int
     * @return array
     * @throws \dml_exception
     */
    public function get_inventory_items_by_category($event_category_id)
    {
        global $DB, $OUTPUT;
        $inventory = [];
        $i = 0;
        $inventory_items = $DB->get_records(TABLE_EVENT_INVENTORY, ['eventcategoryid' => $event_category_id]);
        // Set currency object (Format number)
        $amount = new \NumberFormatter( get_string('currency_locale', 'local_order'),
            \NumberFormatter::CURRENCY );
        foreach($inventory_items as $item) {
            $ROOM = new room($item->roomid);
            $actions = [
                'id' => $item->id,
                'type' => 'event-inventory-item'
            ];
            // format cost based on language currency
            $item->cost_formatted = $amount->format($item->cost);
            $item->vendor_name = $this->get_vendor_name($item->vendorid);
            $item->room_name = $ROOM->get_full_name();
            $item->actions = $OUTPUT->render_from_template('local_order/action_buttons', $actions);
            $inventory[$i] = $item;
            unset($ROOM);
            $i++;
        }
        return $inventory;
    }

    /**
     * Returns all inventory categories with their items
     * @return array|false
     * @throws \dml_exception
     */
    public function get_inventory_categories_with_items() {
        global $DB;
        $categories = $this->get_inventory_categories();
        $results = [];
        $i = 0;
        $amount = new \NumberFormatter( get_string('currency_locale', 'local_order'),
            \NumberFormatter::CURRENCY );
        foreach ($categories as $c) {
            $c->total_cost = $amount->format($this->get_total_cost_by_category($c->id));
            $c->items = array_values($this->get_inventory_items_by_category($c->id));
            $results[$i] = $c;
            $i++;
        }
        return $results;
    }

    /**
     * Return total cost of inventory items in category
     * @param $event_category_id
     * @return float|mixed
     * @throws \dml_exception
     */
    public function get_total_cost_by_category($event_category_id)  {
        global $CFG, $DB;

        $sql = "SELECT SUM(cost) as amount FROM {order_event_inventory} WHERE eventcategoryid = $event_category_id";
        $result = $DB->get_record_sql($sql);

        return $result->amount;
    }

    /**
     * Returns total cost of all items from all categories
     * @return false|string
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_total_cost_of_event() {
        $sum = 0;
        foreach($this->get_inventory_categories() as $c) {
            $sum = $sum + $this->get_total_cost_by_category($c->id);
        }
        $amount = new \NumberFormatter( get_string('currency_locale', 'local_order'),
            \NumberFormatter::CURRENCY );
        return $amount->format($sum);
    }

    /**
     * Return vendor name
     * @param $vendor_id
     * @return name|string
     */
    public function get_vendor_name($vendor_id) {
        $VENDOR = new vendor($vendor_id);
        return $VENDOR->get_name();
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
    public function set_organizationid($organizationid)
    {
        $this->organizationid = $organizationid;
    }

    /**
     * @param Type: varchar (255)
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     * @param Type: varchar (255)
     */
    public function set_code($code)
    {
        $this->code = $code;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_starttime($starttime)
    {
        $this->starttime = $starttime;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_endtime($endtime)
    {
        $this->endtime = $endtime;
    }

    /**
     * @param Type: longtext (-1)
     */
    public function set_eventtype($eventtype)
    {
        $this->eventtype = $eventtype;
    }

    /**
     * @param Type: varchar (100)
     */
    public function set_attendance($attendance)
    {
        $this->attendance = $attendance;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_roomid($roomid)
    {
        $this->roomid = $roomid;
    }

    /**
     * @param Type: longtext (-1)
     */
    public function set_setuptype($setuptype)
    {
        $this->setuptype = $setuptype;
    }

    /**
     * @param Type: longtext (-1)
     */
    public function set_setupnotes($setupnotes)
    {
        $this->setupnotes = $setupnotes;
    }

    /**
     * @param Type: longtext (-1)
     */
    public function set_adminnotes($adminnotes)
    {
        $this->adminnotes = $adminnotes;
    }

    /**
     * @param Type: tinyint (2)
     */
    public function set_requirescatering($requirescatering)
    {
        $this->requirescatering = $requirescatering;
    }

    /**
     * @param Type: longtext (-1)
     */
    public function set_othernotes($othernotes)
    {
        $this->othernotes = $othernotes;
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

    public function delete_record()
    {
        global $DB;
        $inventory_categories = $this->get_inventory_categories();
        //Delete all items and their category
        foreach ($inventory_categories as $ic) {
            $DB->delete_records(TABLE_EVENT_INVENTORY, ['eventcategoryid' => $ic->id]);
            $DB->delete_records(TABLE_EVENT_INVENTORY_CATEGORY, ['id' => $ic->id]);
        }
        if ($DB->delete_records(TABLE_EVENT, ['id' => $this->id])) {
            return true;
        }
        return false;
    }

}