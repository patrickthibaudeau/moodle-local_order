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
            },
            {
                "order": 'desc',
                "targets": [4]
            }],
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



    // Click on row
    // $('#order_my_request_table').on('click', 'tbody tr', function (){
    //     var row = request_table.row($(this)).data();
    //     window.location.href = wwwroot + "/local/order/details.php?id=" + row.id;   //full row of array data
    // });
});