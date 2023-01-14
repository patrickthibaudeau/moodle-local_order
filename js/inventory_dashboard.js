$(document).ready(function () {
    let wwwroot = M.cfg.wwwroot;
    let categoryId = $('#local_order_inventory_category').val();

    let inventoryTable = $('#local_order_inventory_table').DataTable({
        // dom: 'Blfprtip',
        dom: 'lfprtip',
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": wwwroot + "/local/order/ajax/inventory_dashboard.php",
            "data": {
                category: categoryId
            },
            "type": "POST"
        },
        'deferRender': true,
        "columns": [
            {"data": "id", name: 'name', className: 'editable'},
            {"data": "name", name: 'name', className: 'editable'},
            {"data": "shortname", name: 'code', className: 'editable'},
            {"data": "cost", name: 'cost', className: 'editable'},
            {"data": "category"},
            {"data": "actions"},
        ],
        'columnDefs': [
            {
                "searchable": false,
                "targets": [4]
            },
            {
                'target': 0,
                'searchable': false,
                'visible': false
            }],
        'order': [[1, ' asc']],
        // buttons: [
        //     'excelHtml5',
        // ],
        "drawCallback": function (settings) {
            // Click on delete button
            $('.btn-delete-inventory').on('click', function () {
                let id = $(this).data('id');
                $('#inventoryDeleteModal').modal('show');
                // Delete the row and refresh table
                $('.btn-delete-inventory-confirm').on('click', function () {
                    $.ajax({
                        type: "POST",
                        url: M.cfg.wwwroot + "/local/order/ajax/delete.php?id=" + id + "&action=inventory",
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
            $('.btn-edit-inventory').on('click', function () {
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

    // Edit table
    // when the mouse enters a cell, create an editor.
    $('#local_order_inventory_table').on('click', 'td.editable', function (e) {
        e.preventDefault()
        // prevents accidently creating another input element
        if (e.target.localName != 'input') {
            let row = e.target._DT_CellIndex.row
            let col = e.target._DT_CellIndex.column
            if (!e.target.children.length) {
                e.target.innerHTML = `<input id="${row}-${col}" type="text" class="editor form-control" value="${e.target.innerHTML}">`
                $(`#${row}-${col}`).focus();
            }
        }
    })

// when the mouse exits the editor, write the data into the table and redraw
    $('#local_order_inventory_table').on('change', 'td.editable', function (e) {
        e.preventDefault()
        console.log('perform this');
        let [row, col] = e.target.id.split('-')
        let id = inventoryTable.cell(Number(row), 0).data();
        let column = inventoryTable.column(Number(col)).dataSrc();
        $.ajax({
            type: "POST",
            url: M.cfg.wwwroot + "/local/order/ajax/update_inventory.php?id=" + id + "&column=" + column +
                '&value=' + e.target.value,
            dataType: "html",
            success: function (resultData) {
                inventoryTable.draw()
            }
        });
    })


    // Add new inventory item
    $('.btn-add-new').on('click', function () {
        location.href = M.cfg.wwwroot + '/local/order/inventory/edit_inventory.php';
    });

    // Filter refresh
    $('#local_order_inventory_category').on('change', function () {
        let id = $(this).val();
        location.href = M.cfg.wwwroot + '/local/order/inventory/index.php?category=' + id;
    });
});