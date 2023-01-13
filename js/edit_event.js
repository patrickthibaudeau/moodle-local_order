$(document).ready(function () {
    $('#id_starttime_calendar').hide();
    $('#id_endtime_calendar').hide();
    // Initiate select2 elements
    $('#event_inventory_name').select2({
        theme: 'bootstrap4',
        placeholder: 'Inventory item',
        dropdownParent: $('#localOrderEditEventModal')
    });
    $('#event_inventory_vendor').select2({
        theme: 'bootstrap4',
        placeholder: 'Vendor',
        dropdownParent: $('#localOrderEditEventModal')
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
                let nselectOption = new Option('Select', 0, true, true);
                $('#event_inventory_name').append(nselectOption).trigger('change');
                let id = '';
                let text = '';
                $.each(inventory, function (key) {
                    // Fill in values into variables
                    id = inventory[key]['id'] + '|' + inventory[key]['cost']
                    text = inventory[key]['name'] + ' - ' + inventory[key]['cost_formatted']
                    // Add option to menu
                    let newOption = new Option(text, id, true, false);
                    $('#event_inventory_name').append(newOption).trigger('change');
                });

                // add vendors to event_inventory_name

                $('#localOrderEditEventModal').modal('show');
            }
        });
    });

    // Edit event inventory item
    $('.btn-edit-event-inventory-item').off();
    $('.btn-edit-event-inventory-item').on('click', function () {
        let inventoryid = $(this).data('id');
        let eventInventoryCategoryId = $(this).data('eventinventorycategoryid');
        let eventId = $(this).data('eventid');
        $('#event_inventory_cost').val(500);
        $.ajax({
            type: "GET",
            url: M.cfg.wwwroot + "/local/order/ajax/edit_event_inventory_form.php?id=" + inventoryid +
                "&eicid=" + eventInventoryCategoryId + '&eventid=' + eventId,
            dataType: "json",
            success: function (results) {
                // Add values to hidden fields
                $('input[name="inventoryid"]').val(inventoryid);
                $('input[name="eventid"]').val(eventId);
                $('input[name="eventinventorycategoryid"]').val(eventInventoryCategoryId);
                $('#event_inventory_description').val(results.description);
                $('#event_inventory_quantity').val(results.quantity);
                $('#event_inventory_quantity').removeAttr('disabled');
                $('#event_inventory_cost').val(results.cost);

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
                let selected = false;
                let selectedId = 0;
                $.each(inventory, function (key) {
                    // Fill in values into variables
                    id = inventory[key]['id'] + '|' + inventory[key]['cost']
                    text = inventory[key]['name'] + ' - ' + inventory[key]['cost_formatted']

                    if (inventory[key]['selected'] == 'selected') {
                        selected = true;
                        selectedId = id;
                    } else {
                        selected = false;
                    }
                    // Add option to menu
                    let newOption = new Option(text, id, false, false);
                    $('#event_inventory_name').append(newOption).trigger('change');
                    $('#event_inventory_name').val(selectedId);
                });

                // add vendors to event_inventory_name
               let modal =  $('#localOrderEditEventModal').modal('show');
               modal.on('shown.bs.modal', function(){
                   console.log('hello');
                   selectSelects();
               });
            }
        });
    });

    // Save event inventory item
    $('.btn-event-inventory-item-save').off();
    $('.btn-event-inventory-item-save').on('click', function () {
        let data = {
            id: $('input[name="inventoryid"]').val(),
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
                $('#event_inventory_description').val('');
                $('#event_inventory_quantity').val('');
                $('#event_inventory_quantity').attr('disabled', true);
                $('#event_inventory_quantity').prop('disabled', true);
                $('#event_inventory_cost').val(0);
                init_event_inventory_items();
                // Expand accordiaon
                $('.btn-collapse-' + data.eventinventorycategoryid).attr('aria-expanded', 'true');
                $('#collapse_' + data.eventinventorycategoryid).addClass('show');
                get_event_total_cost(data.eventid);
                // $('.btn-event-inventory-item-save').off();
                // $('.btn-edit-event-inventory-item').off();
                $('#localOrderEditEventModal').modal('hide');
            }
        });
    });

    $('.btn-delete-event-inventory-item').off();
    $('.btn-delete-event-inventory-item').on('click', function () {
        let data = {
            id: $(this).data('id'),
            eventid: $(this).data('eventid'),
            eventInventoryCategoryid: $(this).data('eventinventorycategoryid')
        };
        if (confirm("are you sure you want to delete this item?")) {
            $.ajax({
                type: "GET",
                url: M.cfg.wwwroot + "/local/order/ajax/delete_event_inventory_item.php",
                data: data,
                dataType: "html",
                success: function (results) {
                    $('#event_inventory_accordion').html(results);
                    init_event_inventory_items();
                    // Expand accordiaon
                    $('.btn-collapse-' + data.eventInventoryCategoryid).attr('aria-expanded', 'true');
                    $('#collapse_' + data.eventInventoryCategoryid).addClass('show');
                    get_event_total_cost(data.eventid);
                }
            });
        }
    });

    // Make quantity availabe only when an inventory package is selected
    $('#event_inventory_name').off();
    $('#event_inventory_name').on('select2:select', function () {
        if ($('#event_inventory_name').val() != 0) {
            $('#event_inventory_quantity').removeAttr('disabled');
            let quantity = $('#event_inventory_quantity').val();
            let itemCostArray = $('#event_inventory_name').val().split('|');
            let itemCost = Number(itemCostArray[1]);
            if (isNaN(itemCost)) {
                itemCost = 0
            }
            calculate_cost(quantity, itemCost);
        }
    });
    // Adjust cost once quantity changed
    $('#event_inventory_quantity').on('change', function () {
        let quantity = Number($('#event_inventory_quantity').val());
        let itemCostArray = $('#event_inventory_name').val().split('|');
        if (isNaN(quantity)) {
            alert('You must enter an integer');
        } else {
            let itemCost = Number(itemCostArray[1]);
            if (isNaN(itemCost)) {
                itemCost = 0
            }
            calculate_cost(quantity, itemCost);
        }
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

/**
 * Calulate cost
 * @param quantity
 * @param itemCost
 */
function calculate_cost(quantity, itemCost) {
    let cost = quantity * itemCost;
    $('#event_inventory_cost').val(cost);
}

function selectSelects(){
    $('.select2-element').trigger('change');
}