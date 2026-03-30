 <style>
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

        /* FORCE FOOTER TO SCROLL - NO FIXED POSITIONING */
        .dataTables_wrapper {
            position: relative !important;
        }

        .dataTables_scroll {
            position: relative !important;
        }

        .dataTables_scrollBody {
            overflow: auto !important;
        }

        .dataTables_scrollFoot {
            overflow: hidden !important;
            border: none !important;
            position: relative !important;
        }

        .dataTables_scrollFootInner {
            overflow: hidden !important;
            padding: 0 !important;
            border: none !important;
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

        /* Custom icon color */
        .custom-icon {
            color: black !important;
        }

        /* Ensure numeric columns are right-aligned */
        .numeric {
            text-align: right !important;
        }

        /* Truncate text styling */
        .truncate-text {
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
        }
        /* Reduce horizontal scrollbar width */
::-webkit-scrollbar {
    height: 8px !important;  /* Makes the scrollbar thinner */
    width: 8px !important;
}

::-webkit-scrollbar-track {
    background: #f1f1f1 !important;
}

::-webkit-scrollbar-thumb {
    background: #888 !important;
    border-radius: 4px !important;
}

::-webkit-scrollbar-thumb:hover {
    background: #555 !important;
}

/* For Firefox */
* {
    scrollbar-width: thin !important;
    scrollbar-color: #888 #f1f1f1 !important;
}



    </style>