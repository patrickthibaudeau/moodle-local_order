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
     * Used with events/index.php
     * @param \templatable $dashboard
     * @return type
     */
    public function render_events_dashboard(\templatable $events) {
        $data = $events->export_for_template($this);
        return $this->render_from_template('local_order/events_dashboard', $data);
    }

    /**
     * Used with inventory/index.php
     * @param \templatable $inventory
     * @return type
     */
    public function render_inventory_dashboard(\templatable $inventory) {
        $data = $inventory->export_for_template($this);
        return $this->render_from_template('local_order/inventory_dashboard', $data);
    }

    /**
     * Used with organization/index.php
     * @param \templatable $organizations
     * @return type
     */
    public function render_organizations_dashboard(\templatable $organizations) {
        $data = $organizations->export_for_template($this);
        return $this->render_from_template('local_order/organizations_dashboard', $data);
    }

    /**
     * Used with vendor/index.php
     * @param \templatable $vendors
     * @return type
     */
    public function render_vendors_dashboard(\templatable $vendors) {
        $data = $vendors->export_for_template($this);
        return $this->render_from_template('local_order/vendors_dashboard', $data);
    }

    /**
     * Returns a table of vendor contacts
     * Used with vendor/edit_vendor.php
     * @param \templatable $vendors
     * @return type
     */
    public function render_vendor_contacts(\templatable $vendor) {
        $data = $vendor->export_for_template($this);
        return $this->render_from_template('local_order/vendor_contacts', $data);
    }

    /**
     * Used with rooms/index.php
     * @param \templatable $inventory
     * @return type
     */
    public function render_rooms_dashboard(\templatable $inventory) {
        $data = $inventory->export_for_template($this);
        return $this->render_from_template('local_order/rooms_dashboard', $data);
    }

    /**
     * Used with reports/index.php
     * @param \templatable $inventory
     * @return type
     */
    public function render_reports_dashboard(\templatable $reports) {
        $data = $reports->export_for_template($this);
        return $this->render_from_template('local_order/reports_dashboard', $data);
    }

    /**
     * Used with reports/index.php
     * @param \templatable $inventory
     * @return type
     */
    public function render_report_event_cost_by_org(\templatable $report) {
        $data = $report->export_for_template($this);
        return $this->render_from_template('local_order/report_event_cost_by_org', $data);
    }

    /**
     * Used with reports/event_summary_by_org.php
     * @param \templatable $inventory
     * @return type
     */
    public function render_report_event_summary_by_org(\templatable $report) {
        $data = $report->export_for_template($this);
        return $this->render_from_template('local_order/report_event_summary_by_org', $data);
    }

    /**
     * Used with reports/index.php
     * @param \templatable $inventory
     * @return type
     */
    public function render_report_event_cost_by_vendor(\templatable $report) {
        $data = $report->export_for_template($this);
        return $this->render_from_template('local_order/report_event_cost_by_vendor', $data);
    }

}
