$(document).ready(function () {
    $('#id_starttime_calendar').hide();
    $('#id_endtime_calendar').hide();
    // Initiate select2 for room
    $('#id_room').select2({
        theme: 'bootstrap4',
        width: '500px',
        placeholder: M.util.get_string('room_placeholder', 'local_order')
    })
    // Initiate select2 for building
    const building = $('#id_building').select2({
        theme: 'bootstrap4',
        placeholder: M.util.get_string('building_placeholder', 'local_order')
    });

    building.on('select2:select', function (e) {
        let data = e.params.data;
        let id = data.id;
        // Empty list
        $('#id_room').empty();
        $.ajax({
            type: "POST",
            url: M.cfg.wwwroot + "/local/order/ajax/get_rooms.php?id=" + id,
            dataType: "json",
            success: function (results) {
                $.each(results, function (value, text) {
                    $('#id_room').append($('<option>', {
                        value: value,
                        text: text
                    }));
                });

                // console.log(results);
            }
        });
    });

    /**
     * Edit inventory items
     */
    $('.btn-inventory-edit').on('click', function () {
        let eventCategoryId = $(this).data('eventinventorycategoryid');
        let eventId = $(this).data('eventid');

        $.ajax({
            type: "GET",
            url: M.cfg.wwwroot + "/local/order/ajax/get_inventory_details.php?id=" + eventCategoryId + '&eventid=' + eventId,
            dataType: "html",
            success: function (results) {
                $('#local_order_inventory_edit_container').html(results);
                $('#localOrderEditEventModal').modal('show');
                add_event_inventory_item();
            }
        });
    });

    // Download pdf of event
    $('.btn-local-order-export-pdf').on('click', function () {
        let id = $("input[name='id']").val();
        let inventoryCategoryId = $(this).data('inventorycategory');
        window.open(M.cfg.wwwroot + '/local/order/export/pdf.php?id=' + id + '&icid=' + inventoryCategoryId,
            '_blank'
        );
    });
});

function add_event_inventory_item() {
    $('#local_order_add_event_inventory').on('click', function () {
        let eventInventoryCategoryId = $(this).data('eventinventorycategoryid');
        let eventId = $(this).data('eventid');

        $.ajax({
            type: "GET",
            url: M.cfg.wwwroot + "/local/order/ajax/edit_event_inventory_form.php?eicid=" + eventInventoryCategoryId + '&eventid=' + eventId,
            dataType: "html",
            success: function (results) {
                $('#event_inventory_edit_form_container').html(results);
                // Make quantity availabe only when an inventory package is selected
                $('#event_inventory_name').on('change', function () {

                    if ($(this).val() != 0) {
                        console.log($(this).val());
                        $('#event_inventory_quantity').removeAttr('disabled');
                    }
                });
                // Adjust cost once quantity changed
                $('#event_inventory_quantity').on('change', function () {
                    let quantity = Number($(this).val());
                    let itemCostArray = $('#event_inventory_name').val().split('|');
                    console.log(itemCostArray);
                    let itemCost = Number(itemCostArray[1]);
                    let cost = quantity * itemCost;
                    $('#event_inventory_costy').val(cost);
                });
            }
        });
    });
}