$(document).ready(function () {
    let wwwroot = M.cfg.wwwroot;

    let organizationsTable = $('#local_order_organizations_table').DataTable({
        // dom: 'Blfprtip',
        dom: 'lfprtip',
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": wwwroot + "/local/order/ajax/organizations_dashboard.php",
            "type": "POST"
        },
        'deferRender': true,
        "columns": [
            {"data": "code"},
            {"data": "name"},
            {"data": "contact"},
            {"data": "email"},
            {"data": "phone"},
            {"data": "actions"},
        ],
        'columnDefs': [
            {
                "searchable": false,
                "targets": [5]
            }],
        'order': [[1, ' asc']],
        // buttons: [
        //     'excelHtml5',
        // ],
        "drawCallback": function (settings) {
            // Click on delete button
            $('.btn-delete-organization').on('click', function(){
                let id = $(this).data('id');
                $('#organizationDeleteModal').modal('show');
                // Delete the row and refresh table
                $('.btn-delete-organization-confirm').on('click', function(){
                    $.ajax({
                        type: "POST",
                        url: M.cfg.wwwroot + "/local/order/ajax/delete.php?id=" + id + "&action=organization" ,
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
            $('.btn-edit-organization').on('click', function() {
                let dateRange = $('#local_order_events_daterange').val();
                location.href = M.cfg.wwwroot + '/local/order/events/edit_organization.php?id=' + $(this).data('id');
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
        location.href = M.cfg.wwwroot + '/local/order/events/edit_organization.php'
    });
});