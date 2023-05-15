$(document).ready(function () {
    let wwwroot = M.cfg.wwwroot;
    $('#local-order-organization').select2({
        theme: 'bootstrap4',
    });

    $('#local-order-organization').on('change', function () {
        let id = $(this).val();
        console.log(id);
        if (id.length !== 0) {
            $('#export-button-container').show();
            if (id == 0) {
                $('#export-to-pdf').hide();
            } else {
                $('#export-to-pdf').show();
            }
        } else {
            $('#export-button-container').hide();
        }
    });

    $('#export-to-pdf').click(function () {
        let id = $('#local-order-organization').val();
        location.href = M.cfg.wwwroot + '/local/order/export/pdf_event_summary_by_org.php?id=' + id;
    });

    $('#export-to-xls').click(function () {
        let id = $('#local-order-organization').val();
        location.href = M.cfg.wwwroot + '/local/order/export/excel_event_summary_by_org.php?id=' + id;
    });
});