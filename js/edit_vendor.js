$(document).ready(function () {
    let wwwroot = M.cfg.wwwroot;

    $('.btn-delete-contact').on('click', function () {
        let id = $(this).data('id');
        console.log(id);

        if (confirm('Are you sure you want to delete this team member?')) {
            $.ajax({
                type: "POST",
                url: M.cfg.wwwroot + "/local/order/ajax/delete.php?id=" + id + "&action=vendor_contact",
                dataType: "html",
                success: function (resultData) {
                    $('#row' + id).remove();
                    return false;
                }
            });
            return false;
        } else {
            return false;
        }
    });

    $('#userid').select2({
        theme: 'bootstrap4',
        placeholder: 'Select User',
    });

    // Add new event
    $('.btn-add-new').on('click', function(){
        $('.user-form').show();
    });
});