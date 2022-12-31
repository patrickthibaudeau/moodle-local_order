$(document).ready(function () {
    // Hide rooms select menu
    $('#id_room').select2({
        theme: 'bootstrap4',
        width: '150px'
    })

    const building = $('#id_building').select2({
        theme: 'bootstrap4',
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
});