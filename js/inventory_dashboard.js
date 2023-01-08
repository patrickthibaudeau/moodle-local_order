$(document).ready(function () {
    let wwwroot = M.cfg.wwwroot;
    let categoryId = $('#local_order_inventory_category').val();

    let inventoryTable = $('#local_order_inventory_table').DataTable({
        // dom: 'Blfprtip',
        dom: 'lfprtip',
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": wwwroot + "/local/order/ajax/inventory_dashboard.php?category=" + categoryId,
            "type": "POST"
        },
        'deferRender': true,
        "columns": [
            {"data": "name"},
            {"data": "shortname"},
            {"data": "cost"},
            {"data": "category"},
            {"data": "actions"},
        ],
        'columnDefs': [
            {
                "searchable": false,
                "targets": [4]
            }],
        'order': [[4, ' asc']],
        // buttons: [
        //     'excelHtml5',
        // ],
        "drawCallback": function (settings) {
            // Click on delete button
            $('.btn-delete-inventory').on('click', function(){
                let id = $(this).data('id');
                $('#inventoryDeleteModal').modal('show');
                // Delete the row and refresh table
                $('.btn-delete-inventory-confirm').on('click', function(){
                    $.ajax({
                        type: "POST",
                        url: M.cfg.wwwroot + "/local/order/ajax/delete.php?id=" + id + "&action=inventory" ,
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
            $('.btn-edit-inventory').on('click', function() {
                let dateRange = $('#local_order_events_daterange').val();
                location.href = M.cfg.wwwroot + '/local/order/inventory/edit_inventory.php?id=' + $(this).data('id');
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

    // Add new inventory item
    $('.btn-add-new').on('click', function(){
        location.href = M.cfg.wwwroot + '/local/order/inventory/edit_inventory.php';
    });

    // Filter refresh
    $('#local_order_inventory_category').on('change', function (){
        let id = $(this).val();
        location.href = M.cfg.wwwroot + '/local/order/inventory/index.php?category=' + id;
    });
});