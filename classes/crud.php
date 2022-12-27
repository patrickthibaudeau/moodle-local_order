<?php

namespace local_order;

abstract class crud
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var int
     */
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
        if ($this->id) {
            $result = $DB->get_record($this->table, ['id' => $this->id]);
            return $result;
        } else {
            error_log('No id number provided');
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
            $DB->delete_records($this->table, ['id' => $this->id]);
        } else {
            error_log('No id number provided');
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

            $id = $DB->insert_record($this->table, $data);

            return $id;
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
}