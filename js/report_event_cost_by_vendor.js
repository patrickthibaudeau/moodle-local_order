$(document).ready(function () {
    let wwwroot = M.cfg.wwwroot;
    $('#local-order-vendor').select2({
        theme: 'bootstrap4',
    });

    $('#local-order-vendor').on('change', function () {
        let id = $(this).val();
        if (id.length !== 0) {
            $('#export-button-container').show();
        } else {
            $('#export-button-container').hide();
        }
    });

    // $('#export-to-pdf').click(function () {
    //     let id = $('#local-order-vendor').val();
    //     location.href = M.cfg.wwwroot + '/local/order/export/pdf_org.php?id=' + id;
    // });

    $('#export-to-xls').click(function () {
        let id = $('#local-order-vendor').val();
        location.href = M.cfg.wwwroot + '/local/order/export/excel_costs_per_vendor.php?id=' + id;
    });
});