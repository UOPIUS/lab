// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable({"lengthMenu": [
    [10, 50, 500, 1000, -1],
    [10, 50, 500, 1000, "All"]
]});
});
