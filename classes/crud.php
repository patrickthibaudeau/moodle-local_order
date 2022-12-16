<?php

namespace local_order;

abstract class crud
{

    private $table;

    private $id;

    public function set_table($table)
    {
        $this->table = $table;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     * @param $table string
     * @param $id int
     * @param $order string
     * @return void
     * @throws \dml_exception
     */
    public function get_record()
    {
        global $DB;
        $result = $DB->get_record($this->table, ['id' => $this->id]);
        return $result;

    }

    /**
     * @param $id int
     * @return void
     * @throws \dml_exception
     */
    public function delete_record($id)
    {
        global $DB;
        $DB->delete_records($this->table, ['id' => $id]);
    }

    /**
     * @param $data stdClass
     * @return bool|int
     * @throws \dml_exception
     */
    public function insert_record($data)
    {
        global $DB, $USER;


        if (!isset($data->timecreated)) {
            $data->timecreated = time();
        }

        if (!isset($data->timemodified)) {
            $data->timemodified = time();
        }

        //Set user
        $data->usermodified = $USER->id;

        $id = $DB->insert_record($this->table, $data);

        return $id;
    }

    /**
     * @param $data stdClass
     * @return bool
     * @throws \dml_exception
     */
    public function update_record( $data)
    {
        global $DB, $USER;

        // Set timemodified
        if (!isset($data->timemodified)) {
            $data->timemodified = time();
        }

        //Set user
        $data->usermodified = $USER->id;

        $id = $DB->update_record($this->table, $data);

        return $id;
    }
}