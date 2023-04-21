$(document).ready(function () {
    let wwwroot = M.cfg.wwwroot;
    $('#local-order-organization').select2({
        theme: 'bootstrap4',
    });

    $('#local-order-organization').on('change', function () {
        let id = $(this).val();
        location.href = M.cfg.wwwroot + '/local/order/export/pdf_event_summary_by_org.php?id=' + id;
    });
});