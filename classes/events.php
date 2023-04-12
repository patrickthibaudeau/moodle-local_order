<?php
/*
 * Author: Admin User
 * Create Date: 15-12-2022
 * License: LGPL 
 * 
 */

namespace local_order;

class events
{

    /**
     *
     * @var string
     */
    private $results;

    /**
     *
     * @global \moodle_database $DB
     */
    public function __construct()
    {
        global $DB;
        $this->results = $DB->get_records('order_event');
    }

    /**
     * Get records
     */
    public function get_records()
    {
        return $this->results;
    }

    /**
     * Array to be used for selects
     * Defaults used key = record id, value = name
     * Modify as required.
     */
    public function get_select_array()
    {
        $array = [
            '' => get_string('select', 'local_order')
        ];
        foreach ($this->results as $r) {
            $array[$r->id] = $r->name;
        }
        return $array;
    }

    /**
     * Returns number of events today or None
     */
    public function get_events_count_today()
    {
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

    /**
     * @param $date_range
     * @param $building
     * @param $room
     * @param $status
     * @param $organization
     * @param $start
     * @param $end
     * @param $term
     * @param $order_column
     * @param $order_direction
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     * @throws \require_login_exception
     */
    public function get_datatable($date_range, $building, $room = null, $status = -1, $organization = -1, $start, $end, $term, $order_column = 'starttime', $order_direction = 'DESC')
    {
        global $CFG, $DB, $OUTPUT, $PAGE, $USER;

        $context = \context_system::instance();
        $PAGE->set_context($context);
        require_login(1, false);

        $date_range = explode(' - ', $date_range);
        $start_time = strtotime($date_range[0] . ' 00:00:00');
        $end_time = strtotime($date_range[1] . ' 23:59:59');
        $can_edit = has_capability('local/order:event_edit', $context);
        $can_delete = has_capability('local/order:event_delete', $context);

        // Is user a vendor? If so, vendors can only see their own events.
        $vendor_role = $DB->get_record('role', ['shortname' => 'vendor']);
        if (user_has_role_assignment($USER->id, $vendor_role->id, $context->id)) {
            // Get current user vendor contacts
            $vendor_contacts = $DB->get_records('order_vendor_contact', ['userid' => $USER->id]);
            $vendorids = "";
            // Add each vendor id to the string
            foreach ($vendor_contacts as $vc) {
                $vendorids .= $vc->vendorid . ',';
            }
            // Remove last comma to prepare string for IN statement
            $vendorids = rtrim($vendorids, ',');

            $sql = "Select DISTINCT
                        e.id,
                        e.code,
                        e.name As title,
                        e.status,
                        e.starttime,
                        e.endtime,
                        e.setuptype,
                        e.workorder,
                        rb.building_shortname,
                        rb.name as room_name,
                        o.name As organization
                    From
                        {order_event} e Inner Join
                        {order_organization} o On o.id = e.organizationid Inner Join
                        {order_event_inv_category} eic On eic.eventid = e.id Inner Join
                        {order_event_inventory} ei On ei.eventcategoryid = eic.id Left JOIN 
                        {order_room_basic} rb On rb.id = e.roomid                       
                    Where 
                        (e.starttime BETWEEN $start_time AND $end_time) 
                        AND ei.vendorid IN ($vendorids)";
        } else {
            $sql = "Select
                    e.id,
                    e.code,
                    e.name as title,
                    e.status,   
                    e.starttime,
                    e.endtime,
                    e.setuptype,
                    e.workorder,
                    rb.building_shortname,
                    rb.name as room_name,
                    o.name As organization
                From
                    {order_event} e Inner Join
                    {order_organization} o On o.id = e.organizationid Left JOIN 
                    {order_room_basic} rb On rb.id = e.roomid
                Where
                    e.starttime BETWEEN $start_time AND $end_time";
        }

        if ($term) {
            $sql .= " AND (e.name LIKE '%$term%' ";
            $sql .= " OR e.status LIKE '%$term%' ";
            if ($status) {
                $sql .= " OR e.status = $status ";
            }
            $sql .= " OR rb.building_shortname LIKE '%$term%' ";
            $sql .= " OR rb.name LIKE '%$term%' ";
            $sql .= " OR e.code LIKE '%$term%' ";
            $sql .= " OR e.setuptype LIKE '%$term%' ";
            $sql .= " OR e.workorder LIKE '%$term%' ";
            $sql .= " OR o.name LIKE '%$term%') ";
        } else {
            if ($status != -1) {
                $sql .= " AND e.status = $status ";
            }
        }

        if ($organization > 0) {
            $sql .= " AND e.organizationid = $organization ";
        }

        if ($building) {
            $sql .= " AND rb.building_shortname='$building' ";
        }

        if ($room) {
            $sql .= " AND rb.name='$room' ";
        }

//        $sql .= " ORDER BY $order_column $order_direction";
        $total_found = count($DB->get_records_sql($sql));

        switch ($order_column) {
            case 'code':
                $order_column = 'e.code+0';
                break;
            case 'title':
                $order_column = 'e.name';
                break;
            case 'end':
                $order_column = 'e.endtime';
                break;
            case 'type':
                $order_column = 'e.setuptype';
                break;
            case 'organization':
                $order_column = 'o.name';
                break;
            default:
                $order_column = 'e.starttime';
                break;
        };
        $sql .= " Group by e.id";
        $sql .= " Order by $order_column $order_direction";

        $sql .= " LIMIT $start, $end";

        $results = $DB->get_recordset_sql($sql);

        $events = [];
        $i = 0;
        foreach ($results as $r) {
            $event_start_date = strftime(
                get_string('strftimedate', 'local_order'), $r->starttime);
            $event_start_time = strftime(
                get_string('strftime', 'local_order'), $r->starttime);
            $event_end_time = strftime(get_string('strftime', 'local_order'), $r->endtime);

            $actions = [
                'id' => $r->id,
                'type' => 'event',
                'can_edit' => $can_edit,
                'can_delete' => $can_delete
            ];

            $status = '';
            switch ($r->status) {
                case 0:
                    $status = get_string('status_new', 'local_order');
                    break;
                case 1:
                    $status = get_string('status_approved', 'local_order');
                    break;
                case 2:
                    $status = get_string('status_pending', 'local_order');
                    break;
                case 3:
                    $status = get_string('status_rej', 'local_order');
                    break;
            }
            $events[$i]['code'] = $r->code;
            $events[$i]['title'] = $r->title;
            $events[$i]['status'] = $status;
            $events[$i]['date'] = $event_start_date;
            $events[$i]['start'] = $event_start_time;
            $events[$i]['end'] = $event_end_time;
            $events[$i]['type'] = $r->setuptype;
            $events[$i]['workorder'] = $r->workorder;
            $events[$i]['room'] = $r->building_shortname . ' ' . $r->room_name;
            $events[$i]['organization'] = $r->organization;
            $events[$i]['actions'] = $OUTPUT->render_from_template('local_order/action_buttons', $actions);;
            $i++;
        }

        $data = new \stdClass();
        $data->total_found = $total_found;
        $data->total_displayed = count($events);
        $data->results = $events;

        return $data;

    }

    /**
     * @param $id int
     * @return string
     * @throws \coding_exception
     */
    private function draw_action_buttons($id)
    {
        $html = '<a href="#" class="btn btn-outline-primary btn-sm btn-edit-event" data-id="' . $id . '"';
        $html .= ' title="' . get_string('edit', 'local_order') . '"><i class="fa fa-pencil"></i></a>';
        $html .= '<a href="#" class="btn btn-outline-danger btn-sm btn-delete-event ml-2" data-id="' . $id . '"';
        $html .= ' title="' . get_string('delete', 'local_order') . '"><i class="fa fa-trash"></i></a>';
        return $html;
    }

    /**
     * Returns array of event ids based on date range
     * @param $date_range
     * @return array
     * @throws \dml_exception
     */
    public function get_event_ids_by_daterange($date_range, $building = null, $room = null, $status = -1, $organization = -1)
    {
        global $DB;

        $date_range = explode(' - ', $date_range);
        $start_time = strtotime($date_range[0] . ' 00:00:00');
        $end_time = strtotime($date_range[1] . ' 23:59:59');

        $sql = "SELECT 
                    e.id 
                FROM 
                    {order_event} e Left JOIN 
                    {order_room_basic} rb On rb.id = e.roomid 
                WHERE starttime BETWEEN $start_time AND $end_time ";

        if ($status != -1) {
            $sql .= " AND e.status = $status ";
        }

        if ($building) {
            $sql .= " AND rb.building_shortname='$building' ";
        }

        if ($room) {
            $sql .= " AND rb.name = '$room' ";
        }

        if ($organization > 0) {
            $sql .= " AND e.organizationid = $organization ";
        }
    $sql .= " ORDER BY e.starttime ASC";

        return $DB->get_records_sql($sql, [$start_time, $end_time]);
    }

}