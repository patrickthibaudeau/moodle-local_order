<?php

namespace local_order\task;

class sampletask extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('sampletask', 'local_order');
    }

    /**
     * Run forum cron.
     */
    public function execute() {

    }

    public function get_run_if_component_disabled() {
        return true;
    }

}
