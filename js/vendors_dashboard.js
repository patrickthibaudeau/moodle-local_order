$(document).ready(function () {
    let wwwroot = M.cfg.wwwroot;

    let vendorsTable = $('#local_order_vendors_table').DataTable({
        // dom: 'Blfprtip',
        dom: 'lfprtip',
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": wwwroot + "/local/order/ajax/vendors_dashboard.php",
            "type": "POST"
        },
        'deferRender': true,
        "columns": [
            {"data": "name"},
            {"data": "email"},
            {"data": "phone"},
            {"data": "actions"},
        ],
        'columnDefs': [
            {
                "searchable": false,
                "targets": [3]
            }],
        'order': [[0, ' asc']],
        // buttons: [
        //     'excelHtml5',
        // ],
        "drawCallback": function (settings) {
            // Click on delete button
            $('.btn-delete-vendor').on('click', function(){
                let id = $(this).data('id');
                $('#vendorDeleteModal').modal('show');
                // Delete the row and refresh table
                $('.btn-delete-vendor-confirm').on('click', function(){
                    $.ajax({
                        type: "POST",
                        url: M.cfg.wwwroot + "/local/order/ajax/delete.php?id=" + id + "&action=vendor" ,
                        dataType: "html",
                        success: function (resultData) {
                            if (resultData == 1) {
                                location.reload();
                            } else {
                                $('#vendorDeleteModal').modal('hide');
                                $('#vendorAlertModal').modal('show');
                            }
                        }
                    });
                });
            });

            // edit event
            $('.btn-edit-vendor').on('click', function() {
                let dateRange = $('#local_order_events_daterange').val();
                location.href = M.cfg.wwwroot + '/local/order/vendor/edit_vendor.php?id=' + $(this).data('id');
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

    // Add new event
    $('.btn-add-new').on('click', function(){
        location.href = M.cfg.wwwroot + '/local/order/vendor/edit_vendor.php'
    });
});