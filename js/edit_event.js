$(document).ready(function () {
    $('#id_starttime_calendar').hide();
    $('#id_endtime_calendar').hide();
    // Initiate select2 elements
    $('.select2-element').select2({
        theme: 'bootstrap4'
    });
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

   init_event_inventory_items();

    // Download pdf of event
    $('.btn-local-order-export-pdf').on('click', function () {
        let id = $("input[name='id']").val();
        let inventoryCategoryId = $(this).data('inventorycategory');
        window.open(M.cfg.wwwroot + '/local/order/export/pdf.php?id=' + id + '&icid=' + inventoryCategoryId,
            '_blank'
        );
    });
});

/**
 * Initialize inventory items fucntionality
 * This is required so that all fucntions can be reloaded on saving items
 */
function init_event_inventory_items() {
    /**
     * Edit inventory items
     */
    $('.btn-inventory-add-item').off();
    $('.btn-inventory-add-item').on('click', function () {
        let eventInventoryCategoryId = $(this).data('eventinventorycategoryid');
        let eventId = $(this).data('eventid');

        $.ajax({
            type: "GET",
            url: M.cfg.wwwroot + "/local/order/ajax/edit_event_inventory_form.php?eicid=" + eventInventoryCategoryId + '&eventid=' + eventId,
            dataType: "json",
            success: function (results) {
                // console.log(results.inventory);
                // Add values to hidden fields
                $('input[name="eventid"]').val(eventId);
                $('input[name="eventinventorycategoryid"]').val(eventInventoryCategoryId);
                // Clear all options
                $('#event_inventory_name')
                    .find('option')
                    .remove()
                    .end();
                $('#event_inventory_name').val(null).trigger('change');
                // add inventory items to event_inventory_name
                let inventory = results.inventory;
                let nselectOption = new Option('Select', 0, false, false);
                $('#event_inventory_name').append(nselectOption).trigger('change');
                let id = '';
                let text = '';
                $.each(inventory, function (key) {
                    // Fill in values into variables
                    id = inventory[key]['id'] + '|' + inventory[key]['cost']
                    text = inventory[key]['name'] + ' - ' + inventory[key]['cost_formatted']
                    // Add option to menu
                    let newOption = new Option(text, id, false, false);
                    $('#event_inventory_name').append(newOption).trigger('change');
                });

                // add vendors to event_inventory_name

                $('#localOrderEditEventModal').modal('show');
            }
        });
    });

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

    // Save event inventory item
    $('.btn-event-inventory-item-save').on('click', function () {
        let data = {
            eventid: $('input[name="eventid"]').val(),
            eventinventorycategoryid: $('input[name="eventinventorycategoryid"]').val(),
            quantity: $('input[name="quantity"]').val(),
            cost: $('input[name="cost"]').val(),
            description: $('#event_inventory_description').val(),
            inventory_id: $('#event_inventory_name').val(),
            vendorid: $('#event_inventory_vendor').val()
        };
        $.ajax({
            type: "GET",
            url: M.cfg.wwwroot + "/local/order/ajax/insert_event_inventory_item.php",
            data: data,
            dataType: "html",
            success: function (results) {
                $('#event_inventory_accordion').html(results);
                init_event_inventory_items();
                // Expand accordiaon
                $('.btn-collapse-' + data.eventid).attr('aria-expanded', 'true');
                $('#collapse_' + data.eventid).addClass('show');
                get_event_total_cost(data.eventid);
            }
        });

    });
}

/**
 * Update event total cost
 * @param eventId
 */
function get_event_total_cost(eventId) {
        $.ajax({
            type: "GET",
            url: M.cfg.wwwroot + "/local/order/ajax/get_event_total_cost.php?id=" + eventId,
            dataType: "html",
            success: function (results) {
                $('#event_total_cost').html(results);
            }
        });
}