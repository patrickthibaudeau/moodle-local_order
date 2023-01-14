<?php
/*
 * Author: Admin User
 * Create Date: 15-12-2022
 * License: LGPL 
 * 
 */

namespace local_order;

class organizations
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
        $this->results = $DB->get_records('order_organization');
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
    public function get_number_of_organizations()
    {
        global $DB;

        $number_of_organizations = get_string('none', 'local_order');

        if ($count = $DB->count_records(TABLE_ORGANIZATION, [])) {
            $number_of_organizations = $count;
        }

        return $number_of_organizations;
    }

    /**
     * Return data for DataTables
     * @param $start
     * @param $end
     * @param $term
     * @param $order_column
     * @param $order_direction
     * @return \stdClass
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     * @throws \require_login_exception
     */
    public function get_datatable($start, $end, $term, $order_column = 'name', $order_direction = 'ASC')
    {
        global $CFG, $DB, $OUTPUT, $PAGE;

        $context = \context_system::instance();
        $PAGE->set_context($context);
        require_login(1, false);


        $sql = "Select
                    o.id,
                    o.name,
                    o.code,
                    o.email,
                    o.phone,
                    u.firstname,
                    u.lastname
                From
                    {order_organization} o Left Join
                    {order_organization_contact} oc On o.id = oc.organizationid Left Join
                    {user} u  On oc.userid = u.id
                Where
                    o.deleted = 0";
        
        if ($term) {
            $sql .= " AND (o.name LIKE '%$term%' ";
            $sql .= " OR o.code LIKE '%$term%' ";
            $sql .= " OR o.email LIKE '%$term%' ";
            $sql .= " OR o.phone LIKE '%$term%' ";
            $sql .= " OR u.firstname LIKE '%$term%' ";
            $sql .= " OR u.lastname LIKE '%$term%') ";
        }

        $total_found = count($DB->get_records_sql($sql));

        switch ($order_column) {
            case 'code':
                $order_column = 'o.code+0';
                break;
            case 'name':
                $order_column = 'o.name';
                break;
            case 'contact':
                $order_column = 'u.lastname, u.firstname';
                break;
            case 'email':
                $order_column = 'o.email';
                break;
                case 'phone':
                $order_column = 'o.phone';
                break;
            default:
                $order_column = 'o.name';
                break;

        };
        $sql .= " Order by $order_column $order_direction";
        $sql .= " LIMIT $start, $end";

        $results = $DB->get_recordset_sql($sql);

        $organization = [];
        $i = 0;
        foreach ($results as $r) {
            $actions = [
                'id' => $r->id,
                'type' => 'organization'
            ];

            if ($r->firstname && $r->lastname) {
                $contact = $r->lastname . ', ' . $r->firstname;
            } else {
                $contact = '-';
            }

            $organization[$i]['id'] = $r->id;
            $organization[$i]['name'] = $r->name;
            $organization[$i]['code'] = $r->code;
            $organization[$i]['contact'] = $contact;
            $organization[$i]['email'] = $r->email;
            $organization[$i]['phone'] = $r->phone;
            $organization[$i]['actions'] = $OUTPUT->render_from_template('local_order/action_buttons', $actions);;
            $i++;
        }

        $data = new \stdClass();
        $data->total_found = $total_found;
        $data->total_displayed = count($organization);
        $data->results = $organization;

        return $data;

    }

}