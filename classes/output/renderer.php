<?php

namespace local_order\output;

/**
 * Description of renderer
 *
 * @author patrick
 */
class renderer extends \plugin_renderer_base {

    /**
     * Used with root/index.php
     * @param \templatable $dashboard
     * @return type
     */
    public function render_dashboard(\templatable $dashboard) {
        $data = $dashboard->export_for_template($this);
        return $this->render_from_template('local_order/dashboard', $data);
    }

    /**
     * Used with root/index.php
     * @param \templatable $dashboard
     * @return type
     */
    public function render_events_dashboard(\templatable $events) {
        $data = $events->export_for_template($this);
        return $this->render_from_template('local_order/events_dashboard', $data);
    }

    /**
     * Used with root/index.php
     * @param \templatable $inventory
     * @return type
     */
    public function render_inventory_dashboard(\templatable $inventory) {
        $data = $inventory->export_for_template($this);
        return $this->render_from_template('local_order/inventory_dashboard', $data);
    }


}
