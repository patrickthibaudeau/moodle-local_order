<?php

namespace local_order;

abstract class crud
{
    /**
     * @param $table string
     * @param $id int
     * @param $order string
     * @return void
     * @throws \dml_exception
     */
    public function get_record($table, $id){
        global $DB;
        $result = $DB->get_record($table, ['id' => $id]);
        return  $result;

    }

    /**
     * @param $table string
     * @param $id int
     * @return void
     * @throws \dml_exception
     */
    public function delete_record($table, $id){
        global $DB;
        $DB->delete_records($table,['id' => $id]);
    }

    /**
     * @param $table string
     * @param $data stdClass
     * @return bool|int
     * @throws \dml_exception
     */
    public function insert_record($table, $data){
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
     * @param $table string
     * @param $data stdClass
     * @return bool
     * @throws \dml_exception
     */
    public function update_record($table, $data){
        global $DB, $USER;

        // Set timemodified
        if (!isset($data->timemodified)) {
            $data->timemodified = time();
        }

        //Set user
        $data->usermodified = $USER->id;

        $id = $DB->update_record($table, $data);

        return $id;
    }
}