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
            url: M.cfg.wwwroot + "/local/order/ajax/get_rooms.php?id=" + id ,
            dataType: "json",
            success: function (results) {
                $.each(results, function(value, text){
                    $('#id_room').append($('<option>', {
                        value: value,
                        text : text
                    }));
                });

                // console.log(results);
            }
        });
    });

    /**
     * Edit inventory items
     */
    $('.btn-inventory-edit').on('click', function(){
        let id = $(this).data('id');
        let event = $(this).data('event');

        $.ajax({
            type: "GET",
            url: M.cfg.wwwroot + "/local/order/ajax/get_inventory_details.php?id=" + id + '&event=' + event ,
            dataType: "html",
            success: function (results) {
                $('#local_order_inventory_edit_container').html(results);

                $('#localOrderEditEventModal').modal('show');
            }
        });
    });
});