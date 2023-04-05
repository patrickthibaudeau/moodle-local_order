$(document).ready(function () {
    init();
    save_contact();
});

function init() {
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
                    init();
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

    $('.btn-add-new').on('click', function(e){
        e.preventDefault();
        $('.user-form').show();
    });
}

function save_contact() {
    let wwwroot = M.cfg.wwwroot;
    $('#btn-save-contact').on('click', function(){
        let vendorid = $(this).data('vendorid');
        let userid = $('#userid').val();
        let userInfo = $('#userid option:selected').text();
        console.log(userInfo);
        let userInfoArr = userInfo.split(',');
        let otherUserInfoArray   = userInfoArr[1].split(' - ');
        let firstname = otherUserInfoArray[0];
        let lastname = userInfoArr[0];
        let email = otherUserInfoArray[1];


        $.ajax({
            type: "POST",
            url: M.cfg.wwwroot + "/local/order/ajax/save.php?action=vendor_contact&vendorid=" + vendorid + "&userid=" + userid,
            dataType: "html",
            success: function (resultData) {
                $('#tbody').append(`<tr id="row${resultData}">
          <td>
                ${lastname}
           <td>
            ${firstname}
            </td>
            <td>
            ${email}
            </td>
            <td>
            <button class="btn btn-outline-danger btn-sm btn-delete-contact" title="Delete team member" data-id="${resultData}">
                            <i class="fa fa-trash"></i>
                </button>
           </tr>`);

                init();
            }
        })
    });
}