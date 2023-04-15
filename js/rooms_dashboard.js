$(document).ready(function () {
    let wwwroot = M.cfg.wwwroot;

    let inventoryTable = $('#local_order_rooms_table').DataTable({
        // dom: 'Blfprtip',
        dom: 'lfprtip',
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": wwwroot + "/local/order/ajax/rooms_dashboard.php",
            "data": {
            },
            "type": "POST"
        },
        'deferRender': true,
        "columns": [
            {"data": "building_name", name: 'building_name'},
            {"data": "building_shortname", name: 'building_shortname'},
            {"data": "name", name: 'name'},
            {"data": "actions"},
        ],
        'pageLength': 100,
        'lengthMenu': [100, 200, 500],
        'columnDefs': [
            {
                "searchable": false,
                "targets": [3]
            },
],
        'order': [[1, ' asc']],
        // buttons: [
        //     'excelHtml5',
        // ],
        "drawCallback": function (settings) {
            // Click on delete button
            $('.btn-delete-room').on('click', function () {
                let id = $(this).data('id');
                 $('#roomDeleteModal').modal('show');
                // Delete the row and refresh table
                $('.btn-delete-room-confirm').on('click', function () {
                    $.ajax({
                        type: "POST",
                        url: M.cfg.wwwroot + "/local/order/ajax/delete.php?id=" + id + "&action=room",
                        dataType: "html",
                        success: function (resultData) {
                            console.
                            if (resultData == 1) {
                                location.reload();
                            } else {
                                $('#roomDeleteModal').modal('hide');
                                $('#roomAlertModal').modal('show');
                            }
                        }
                    });
                });
            });

            // edit event
            $('.btn-edit-room').on('click', function () {
                location.href = M.cfg.wwwroot + '/local/order/rooms/edit_room.php?id=' + $(this).data('id');
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


    // Add new room item
    $('.btn-add-new').on('click', function () {
        location.href = M.cfg.wwwroot + '/local/order/rooms/edit_room.php';
    });

});