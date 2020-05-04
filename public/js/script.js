$(document).ready(function () {
    $('#tasks-table').DataTable({
        "columnDefs": [
            {
                "targets": 2,
                "orderable": false
            }
        ],
        "searching": false,
        "lengthChange": false,
        "iDisplayLength": 3,
    });
});
  