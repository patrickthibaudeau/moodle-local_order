$(document).ready(function () {
    let wwwroot = M.cfg.wwwroot;
    $('#local_order_events_daterange').daterangepicker({
        autoApply: true
    });

    let dateRange = $('#local_order_events_daterange').val();
    let selectedBuilding = $('#local-order-building-filter').val();
    let room = $('#local-order-room-filter').val();
    let currentStatus = $('#local-order-status-filter').val();
    let currentOrg = $('#local-order-organization-filter').val();
console.log(dateRange);
    let eventsTable = $('#local_order_events_table').DataTable({
        // dom: 'Blfprtip',
        dom: 'lfprtip',
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": wwwroot + "/local/order/ajax/events_dashboard.php?daterange=" + dateRange
                + "&building=" + selectedBuilding + "&room=" + room + "&status=" + currentStatus
                + "&organization=" + currentOrg,
            "type": "POST"
        },
        'deferRender': true,
        "columns": [
            {"data": "id"},
            {"data": "code"},
            {"data": "title"},
            {"data": "organization"},
            {"data": "room"},
            {"data": "type"},
            {"data": "status"},
            {"data": "workorder"},
            {"data": "date", name: "starttime"},
            {"data": "start"},
            {"data": "end"},
            {"data": "actions"},
        ],
        'columnDefs': [
            {
                "searchable": false,
                "targets": [0, 8, 9, 10, 11]
            },
            {
                "sortable": false,
                "targets": [0, 11]
            }],
        'order': [[8, ' asc']],
        // buttons: [
        //     'excelHtml5',
        // ],
        "drawCallback": function (settings) {
            // Click on delete button
            $('.btn-delete-event').on('click', function(){
                let id = $(this).data('id');
                $('#eventDeleteModal').modal('show');
                // Delete the row and refresh table
                $('.btn-delete-event-confirm').on('click', function(){
                    $.ajax({
                        type: "POST",
                        url: M.cfg.wwwroot + "/local/order/ajax/delete.php?id=" + id + "&action=event" ,
                        dataType: "html",
                        success: function (resultData) {
                            if (resultData == 1) {
                                location.reload();
                            } else {
                                alert(M.util.get_string('error_deleting', 'local_order'));
                            }
                        }
                    });
                });
            });

            $("#select-all").change(function() {
                if (this.checked) {
                    $(".event-checkbox").each(function() {
                        this.checked=true;
                    });
                    approve_all();
                } else {
                    $(".event-checkbox").each(function() {
                        this.checked=false;
                    });
                }
            });

            approve_all();

            // edit event
            $('.btn-edit-event').on('click', function() {
                let dateRange = $('#local_order_events_daterange').val();
                location.href = M.cfg.wwwroot + '/local/order/events/edit_event.php?id=' + $(this).data('id') +
                 '&daterange=' + dateRange;
            });
        },
        "lengthMenu": [[50, 100, 250, 500, 1000], [50, 100, 250, 500, 1000]],
        "pageLength": 50,
        stateSave: false,
    });

    let html = `<span class="approve-all-container">
                            <br>
                            <button type="button" class="btn btn-primary btn-approve-all">Approve selected</button>
                            </span>`;
    if ($('#local-order-can-approve').val() == 1) {
        $('#local_order_events_table_length').append(html);
    }

    // // Add some top spacing
    $('.dataTables_length').css('margin-top', '.5rem');

    $('.buttons-html5').addClass('btn-outline-primary');
    $('.buttons-html5').removeClass('btn-secondary');

    $('#local_order_events_daterange').on('apply.daterangepicker', function (ev, picker) {
        let wwwroot = M.cfg.wwwroot;
        let dateRange = $('#local_order_events_daterange').val();
        location.href = wwwroot + '/local/order/events/index.php?daterange=' + dateRange;
    });

    // Add new event
    $('.btn-add-new').on('click', function(){
        let dateRange = $('#local_order_events_daterange').val();
        location.href = M.cfg.wwwroot + '/local/order/events/edit_event.php?id=0&daterange=' + dateRange;
    });

    // Download PDF button
    $('.btn-local-order-export-pdf').on('click', function(){
        let inventoryCategoryId = $(this).data('inventorycategory');
        let room = $('#local-order-room-filter').val();
        let building = $('#local-order-building-filter').val();
        let status = $('#local-order-status-filter').val();
        let org = $('#local-order-organization-filter').val();
        let dateRange = $('#local_order_events_daterange').val();
        window.open(
            M.cfg.wwwroot + '/local/order/export/pdf.php?icid=' + inventoryCategoryId  + '&daterange=' + dateRange
            + '&room=' + room + '&building=' + building + '&status=' + status + '&organization=' + org,
            '_blank'
        );
    });

    // Download EXCEL button
    $('.btn-local-order-export-excel').on('click', function(){
        let inventoryCategoryId = $(this).data('inventorycategory');
        let room = $('#local-order-room-filter').val();
        let building = $('#local-order-building-filter').val();
        let status = $('#local-order-status-filter').val();
        let org = $('#local-order-organization-filter').val();
        let dateRange = $('#local_order_events_daterange').val();
        window.open(
            M.cfg.wwwroot + '/local/order/export/excel.php?icid=' + inventoryCategoryId  + '&daterange=' + dateRange
            + '&room=' + room + '&building=' + building + '&status=' + status + '&organization=' + org,
            '_blank'
        );
    });

    // Return to today's date
    $('#local-order-reset-date').on('click', function(){
        location.href = M.cfg.wwwroot + '/local/order/events/index.php';
    });

    //Building filter
    // Initiate select2 for building
    const building = $('#local-order-building-filter').select2({
        theme: 'bootstrap4',
        placeholder: M.util.get_string('building_placeholder', 'local_order')
    });


    // Initiate select2 for status
    const status = $('#local-order-status-filter').select2({
        theme: 'bootstrap4',
        placeholder: 'Status'
    });

    // Initiate select2 for status
    const organization = $('#local-order-organization-filter').select2({
        theme: 'bootstrap4',
        placeholder: M.util.get_string('organization', 'local_order')
    });

    // Build room menu on building selected
    building.on('select2:select', function (e) {
        let data = e.params.data;
        let id = data.id;
        // Empty list
        $('#id_room').empty();
        $.ajax({
            type: "POST",
            url: M.cfg.wwwroot + "/local/order/ajax/get_rooms.php?building=" + id,
            dataType: "json",
            success: function (results) {
                $('#local-order-room-filter')
                    .find('option')
                    .remove()
                    .end();
                // Add empty option
                $('#local-order-room-filter').append($('<option>', {
                    value: '',
                    text: ''
                }));
                $.each(results, function (value, text) {
                    $('#local-order-room-filter').append($('<option>', {
                        value: value,
                        text: text
                    }));
                });
                $('#local-order-room-filter').off();
                $('#local-order-room-filter').on('change', function(){
                    let room = $(this).val();
                    let building = $('#local-order-building-filter').val();
                    let status = $('#local-order-status-filter').val();
                    let org = $('#local-order-organization-filter').val();
                    let dateRange = $('#local_order_events_daterange').val();
                    location.href = M.cfg.wwwroot + "/local/order/events/index.php?daterange==" + dateRange + "&room=" + room
                        + '&building=' + building + '&status=' + status + '&organization=' + org;
                });
            }
        });
    });

    $('.refresh-change').off();
    $('.refresh-change').on('change', function(){
        let room = $('#local-order-room-filter').val();
        let building = $('#local-order-building-filter').val();
        let status = $('#local-order-status-filter').val();
        let org = $('#local-order-organization-filter').val();
        let dateRange = $('#local_order_events_daterange').val();
        location.href = M.cfg.wwwroot + "/local/order/events/index.php?daterange=" + dateRange + "&room=" + room
            + '&building=' + building + '&status=' + status + '&organization=' + org;
    });

    $('#clear-filters').on('click', function(){
        let dateRange = $('#local_order_events_daterange').val();
        location.href = M.cfg.wwwroot + "/local/order/events/index.php?daterange=" + dateRange;

    });
});

// Approval all selected events
function approve_all() {
    $('.btn-approve-all').off();
    $('.btn-approve-all').on('click', function(){
        let ids = [];
        $(".event-checkbox:checked").each(function(){
            ids.push($(this).data('id'));
        });
        ids = JSON.stringify(ids);
        $.ajax({
            type: "POST",
            url: M.cfg.wwwroot + "/local/order/ajax/save.php?ids=" + ids + "&action=approve",
            dataType: "html",
            success: function (resultData) {
                location.reload();
            }
        });
    });
}