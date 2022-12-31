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
     * @param $start
     * @param $end
     * @param $term
     * @param $order_column
     * @param $order_direction
     * @return \stdClass
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_events_datatable($date_range, $start, $end, $term, $order_column = 'starttime', $order_direction = 'DESC')
    {
        global $CFG, $DB, $OUTPUT, $PAGE;

        $context = \context_system::instance();
        $PAGE->set_context($context);
        require_login(1, false);

        $date_range = explode(' - ', $date_range);
        $start_time = strtotime($date_range[0] . ' 00:00:00');
        $end_time = strtotime($date_range[1] . ' 23:59:59');

        $sql = "Select
                    e.id,
                    e.code,
                    e.name as title,
                    e.starttime,
                    e.endtime,
                    e.eventtype,
                    o.name As organization
                From
                    {order_event} e Inner Join
                    {order_organization} o On o.id = e.organizationid
                Where
                    e.starttime BETWEEN $start_time AND $end_time";

        if ($term) {
            $sql .= " AND (e.name LIKE '%$term%' ";
            $sql .= " OR e.code LIKE '%$term%' ";
            $sql .= " OR e.eventtype LIKE '%$term%' ";
            $sql .= " OR o.name LIKE '%$term%') ";
        }

        $total_found = count($DB->get_records_sql($sql));

        switch ($order_column) {
            case 'code':
                $order_column = 'e.code';
                break;
            case 'title':
                $order_column = 'e.name';
                break;
            case 'end':
                $order_column = 'e.endtime';
                break;
            case 'type':
                $order_column = 'e.eventtype';
                break;
            case 'organization':
                $order_column = 'o.name';
                break;
            default:
                $order_column = 'e.starttime';
                break;
        };
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
                'type' => 'event'
            ];
            $events[$i]['code'] = $r->code;
            $events[$i]['title'] = $r->title;
            $events[$i]['date'] = $event_start_date;
            $events[$i]['start'] = $event_start_time;
            $events[$i]['end'] = $event_end_time;
            $events[$i]['type'] = $r->eventtype;
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

}