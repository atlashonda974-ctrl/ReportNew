<!-- JS dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Custom DataTable setup -->
<script>
    $(document).ready(function () {
        $('.select2').select2({
            placeholder: "Select a branch",
            allowClear: true,
            width: '100%'
        });

        var table = $('#reportsTable').DataTable({
            paging: false,
            searching: true,
            ordering: true,
            info: true,
            scrollX: true,
            scrollY: "500px",
            scrollCollapse: false,
            fixedHeader: {
                header: true,
                footer: true
            },
            autoWidth: false,
            dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Assets Addition Report',
                    footer: true,
                    exportOptions: {
                        modifier: {
                            page: 'current'
                        }
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Assets Addition Report',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    footer: true,
                    exportOptions: {
                        columns: ':visible',
                        modifier: {
                            page: 'current'
                        }
                    }
                }
            ],
            initComplete: function () {
                this.api().columns.adjust();
                $('.dataTables_filter').css('margin-left', '5px').css('margin-right', '5px');
                $('.dt-buttons').css('margin-left', '5px');
            },
            drawCallback: function () {
                this.api().columns.adjust();
            }
        });

        $(window).on('resize', function () {
            table.columns.adjust();
        });
    });
</script>
