<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/report.css') }}"> 
    <style>
        /* Your existing styles from the first template */
        /* Main table fixes */
        table.dataTable {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        /* Column styling */
        th, td {
            font-size: 14px !important;
            font-family: Calibri, sans-serif !important;
            padding: 2px !important;
            border-collapse: collapse !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Header styling */
        th {
            text-align: center !important;
            border: 1px solid blue !important;
            background-color: blue !important;
            color: white !important;
            font-weight: bold !important;
        }

        /* Cell styling */
        td {
            border: 1px solid black !important;
            vertical-align: top !important;
            text-align: left;
        }

        /* Footer styling */
        tfoot th, tfoot td {
            border: 1px solid black !important;
            background-color: blue !important;
            color: white !important;
            text-align: center !important;
            box-sizing: border-box; 
        }

        /* Ensure the footer spans the full width (for scrollable tables) */
        .dataTables_scrollFoot {
            width: 100% !important;
        }
        .dataTables_scrollFootInner {
            width: 100% !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            th, td {
                font-size: 12px !important;
            }
        }

        /* DataTables wrapper adjustments */
        .dataTables_wrapper {
            margin-top: 0px;
            margin-left: 0;
            width: 100%;
            padding: 0;
        }

        /* Search container */
        .dataTables_filter {
            margin-bottom: 0px;
            width: 100%;
            padding: 0;
        }

        /* Search input styling */
        .dataTables_filter input {
            width: 250px !important;
            font-size: 12px !important;
            border: 2px solid #007bff !important;
            border-radius: 5px !important;
            padding: 5px !important;
            margin-left: 0 !important;
        }

        /* Buttons container */
        .dt-buttons {
            margin-bottom: -5px !important;
            padding: 0 !important;
        }

        /* Button styling */
        .dt-buttons .btn {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            margin-right: 3px !important;
        }

        /* Top container */
        .dataTables_wrapper .top {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 10px;
            margin-bottom: 2px;
            padding: 0;
            flex-wrap: wrap;
        }

        /* Breadcrumb styling */
        .breadcrumb {
            background-color: #f8f9fa;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 14px;
        }

        /* Select2 styling */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .select2-container .select2-dropdown {
            border-color: #ced4da;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Custom icon color */
        .custom-icon {
            color: black !important;
        }
    </style>
    @stack('styles') <!-- For additional styles from child views -->
</head>
<body>
    @yield('content')
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('scripts') <!-- For additional scripts from child views -->
</body>
</html>