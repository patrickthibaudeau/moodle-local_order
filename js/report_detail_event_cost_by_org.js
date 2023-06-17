$(document).ready(function () {
    let wwwroot = M.cfg.wwwroot;
    $('#local-order-organization').select2({
        theme: 'bootstrap4',
    });

    $('#local-order-organization').on('change', function () {
        let id = $(this).val();
        if (id.length !== 0) {
            $('#export-button-container').show();
            $('#export-to-pdf').hide();
        } else {
            $('#export-button-container').hide();
        }
    });

    // $('#export-to-pdf').click(function () {
    //     let id = $('#local-order-vendor').val();
    //     location.href = M.cfg.wwwroot + '/local/order/export/pdf_org.php?id=' + id;
    // });

    $('#export-to-xls').click(function () {
        let id = $('#local-order-organization').val();
        location.href = M.cfg.wwwroot + '/local/order/export/excel_detail_costs_per_org.php?id=' + id;
    });
});