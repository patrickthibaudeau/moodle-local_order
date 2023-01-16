$(document).ready(function () {
    let wwwroot = M.cfg.wwwroot;
    $('#local_order_events_daterange').daterangepicker({
        autoApply: true
    });

    let $dateRange = $('#local_order_events_daterange').val();

    let eventsTable = $('#local_order_events_table').DataTable({
        // dom: 'Blfprtip',
        dom: 'lfprtip',
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": wwwroot + "/local/order/ajax/events_dashboard.php?daterange=" + $dateRange,
            "type": "POST"
        },
        'deferRender': true,
        "columns": [
            {"data": "code"},
            {"data": "title"},
            {"data": "organization"},
            {"data": "type"},
            {"data": "date"},
            {"data": "start"},
            {"data": "end"},
            {"data": "actions"},
        ],
        'columnDefs': [
            {
                "searchable": false,
                "targets": [4, 5, 6]
            }],
        'order': [[4, ' asc']],
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

            // edit event
            $('.btn-edit-event').on('click', function() {
                let dateRange = $('#local_order_events_daterange').val();
                location.href = M.cfg.wwwroot + '/local/order/events/edit_event.php?id=' + $(this).data('id') +
                 '&daterange=' + dateRange;
            });
        },
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "pageLength": 10,
        stateSave: false,
    });

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
        let dateRange = $('#local_order_events_daterange').val();
        window.open(
            M.cfg.wwwroot + '/local/order/export/pdf.php?icid=' + inventoryCategoryId  + '&daterange=' + dateRange,
            '_blank'
        );
    });

    // Return to today's date
    $('#local-order-reset-date').on('click', function(){
        location.href = M.cfg.wwwroot + '/local/order/events/index.php';
    });
});