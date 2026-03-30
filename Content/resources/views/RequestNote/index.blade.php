<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.master_titles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <x-datatable-styles />
    <style>
        .modal-dialog {
            max-width: 80%;
        }
        .modal-content {
            height: auto;
            max-height: 90vh;
            overflow-y: auto;
        }
        .truncate-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            max-width: 150px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <x-report-header title="Request Note Report" />

        @if(request('uw_doc'))
            <div class="alert alert-info">
                <strong>Debug Info:</strong><br>
                Document Number: {{ request('uw_doc') }}<br>
            </div>
        @endif

        <form method="GET" action="{{ url('/getnote') }}" class="mb-4">
            <!-- Row 1: From Date, To Date, Document Number -->
            <div class="row g-3">
                <!-- From Date -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" 
                           value="{{ request('start_date', $start_date) }}">
                </div>

                <!-- To Date -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" 
                           value="{{ request('end_date', $end_date) }}">
                </div>

                <!-- Document Number -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="uw_doc" class="form-label me-2" style="white-space: nowrap; width: 100px;">Doc Number</label>
                    <input type="text" name="uw_doc" id="uw_doc" class="form-control" 
                           placeholder="e.g.2025HYDAPCDP00005"
                           value="{{ request('uw_doc') }}">
                </div>
            </div>

            <!-- Row 2: Category, Re-Ins Party, CP STS, Buttons -->
            <div class="row g-3 mt-2">
                <!-- Category -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="new_category" class="form-label me-2" style="white-space: nowrap; width: 100px;">Category</label>
                    <select name="new_category" id="new_category" class="form-control">
                        <option value="">All Categories</option>
                        <option value="Fire" {{ request('new_category') == 'Fire' ? 'selected' : '' }}>Fire</option>
                        <option value="Marine" {{ request('new_category') == 'Marine' ? 'selected' : '' }}>Marine</option>
                        <option value="Motor" {{ request('new_category') == 'Motor' ? 'selected' : '' }}>Motor</option>
                        <option value="Miscellaneous" {{ request('new_category') == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                        <option value="Health" {{ request('new_category') == 'Health' ? 'selected' : '' }}>Health</option>
                    </select>
                </div>

                <!-- Re-Ins Party -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="reins_party" class="form-label me-2" style="white-space: nowrap; width: 100px;">Re-Ins Party</label>
                    <select name="reins_party" id="reins_party" class="form-control select2">
                        <option value="">All Parties</option>
                        @foreach($reinsParties as $party)
                            <option value="{{ $party }}" {{ request('reins_party') == $party ? 'selected' : '' }}>{{ $party }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- CP STS -->
                <div class="col-md-2 d-flex align-items-center">
                    <label for="cp_sts" class="form-label me-2" style="white-space: nowrap; width: 80px;">CP STS</label>
                    <select name="cp_sts" id="cp_sts" class="form-control">
                        <option value="">All</option>
                        <option value="Yes" {{ request('cp_sts') == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ request('cp_sts') == 'No' ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-4 d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-primary me-2" title="Filter">
                        <i class="bi bi-funnel-fill"></i> Filter
                    </button>
                    <a href="{{ url('/getnote') }}" class="btn btn-outline-secondary me-2" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                    <span class="text-muted ms-3">
                        Showing {{ $filtered_records }} of {{ $total_records }} records
                    </span>
                </div>
            </div>
        </form>

        <!-- Modal Structure -->
        <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsModalLabel">Request Note Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

        <table id="reportsTable" class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th>Request Note #</th>
                    <th>Doc Date</th>
                    <th>Dept.</th>
                    <th>Insured</th>
                    <th>Re-Ins Party</th>
                    <th>Total Sum Ins</th>
                    <th>Total Premium</th>
                    <th>Share</th>
                    <th>RI Sum Ins</th>
                    <th>RI Premium</th>
                    <th>Comm. Date</th>
                    <th>Expiry Date</th>
                    <th>CP</th>
                    <th>Conventional/Takaful</th>
                    <th>Posted</th>
                    <th>User Name</th>
                    <th>Acceptance Date</th>
                    <th>Warranty Period</th>
                    <th>Commission Percent</th>
                    <th>Commission Amount</th>
                    <th>Acceptance No</th>
                </tr>
            </thead>

            <tbody>
                @foreach($data as $record)
                    <tr>
                        <td>
                            <a href="#" class="open-modal" data-req-note="{{ $record['GRH_REFERENCE_NO'] }}">
                                {{ $record['GRH_REFERENCE_NO'] ?? 'N/A' }}
                            </a>
                        </td>
                        @php
                            try {
                                $formattedDate = isset($record['GRH_DOCUMENTDATE']) 
                                    ? \Carbon\Carbon::createFromFormat('d-M-y', $record['GRH_DOCUMENTDATE'])->format('d-m-Y')
                                    : 'N/A';
                            } catch (\Exception $e) {
                                $formattedDate = 'Invalid Date';
                            }
                        @endphp
                        <td>{{ $formattedDate }}</td>

                        @php
                            $categoryMapping = [
                                11 => 'Fire',
                                12 => 'Marine',
                                13 => 'Motor',
                                14 => 'Miscellaneous',
                                16 => 'Health',
                            ];
                            $deptCode = $record['PDP_DEPT_CODE'] ?? null;
                        @endphp
                        <td>{{ $categoryMapping[$deptCode] ?? 'N/A' }}</td>
                        <td>
                            <span class="truncate-text" title="{{ $record['INSURED_DESC'] ?? 'N/A' }}">
                                {{ Str::limit($record['INSURED_DESC'] ?? 'N/A', 5, '...') }}
                            </span>
                        </td>
                        <td>
                            <span class="truncate-text" title="{{ $record['RE_COMP_DESC'] ?? 'N/A' }}">
                                {{ Str::limit($record['RE_COMP_DESC'] ?? 'N/A', 8, '...') }}
                            </span>
                        </td>
                        <td class="numeric" style="text-align: right;">
                            {{ is_numeric($record['TOT_SI'] ?? null) ? number_format($record['TOT_SI']) : 'N/A' }}
                        </td>
                        <td class="numeric" style="text-align: right;">
                            {{ is_numeric($record['TOT_PRE'] ?? null) ? number_format($record['TOT_PRE']) : 'N/A' }}
                        </td>
                        <td class="numeric" style="text-align: right;">
                            {{ is_numeric($record['GRH_CEDEDSISHARE'] ?? null) ? number_format($record['GRH_CEDEDSISHARE'], 2) . '%' : 'N/A' }}
                        </td>
                        <td class="numeric" style="text-align: right;">
                            {{ is_numeric($record['CED_SI'] ?? null) ? number_format($record['CED_SI']) : 'N/A' }}
                        </td>
                        <td class="numeric" style="text-align: right;">
                            {{ is_numeric($record['CED_PRE'] ?? null) ? number_format($record['CED_PRE']) : 'N/A' }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($record['GRH_COMMDATE'] ?? null)->format('d-m-Y') ?? 'N/A' }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($record['GRH_EXPIRYDATE'] ?? null)->format('d-m-Y') ?? 'N/A' }}
                        </td>
                        <td>{{ $record['CP_STS'] ?? 'N/A' }}</td>
                        <td>{{ $record['INSU_TYPE'] ?? 'N/A' }}</td>
                        <td>{{ $record['GRH_POSTINGTAG'] ?? 'N/A' }}</td>
                        <td>{{ $record['CREATED_BY'] ?? 'N/A' }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($record['GRH_ACCEPTEDDATE'] ?? null)->format('d-m-Y') ?? 'N/A' }}
                        </td>
                        <td>
                            @if(isset($record['GRH_ACCEPTEDDATE']))
                                {{ \Carbon\Carbon::parse($record['GRH_ACCEPTEDDATE'])->addDays(30)->format('d-m-Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="numeric" style="text-align: right;">
    {{ is_numeric($record['GRH_COMMISSIONRATE'] ?? null) ? number_format($record['GRH_COMMISSIONRATE'], 2) : 'N/A' }}
</td>

                        <td class="numeric" style="text-align: right;">
                            {{ is_numeric($record['COMMISSIONAMT'] ?? null) ? number_format($record['COMMISSIONAMT']) : 'N/A' }}
                        </td>
                        <td>{{ $record['GRH_REINS_REF_NO'] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <th colspan="5" style="text-align: right;">Totals:</th>
                    <th id="totalSumInsured" style="text-align: right;"></th>
                    <th id="totalPremium" style="text-align: right;"></th>
                    <th></th> <!-- Share column (no total) -->
                    <th id="totalRISumIns" style="text-align: right;"></th>
                    <th id="totalRIPremium" style="text-align: right;"></th>
                    <th></th> <!-- Comm. Date -->
                    <th></th> <!-- Expiry Date -->
                    <th></th> <!-- CP -->
                    <th></th> <!-- Conventional/Takaful -->
                    <th></th> <!-- Posted -->
                    <th></th> <!-- User Name -->
                    <th></th> <!-- Acceptance Date -->
                    <th></th> <!-- Warranty Period -->
                    <th id="totalCommissionRate" style="text-align: right;"></th>
                    <th id="totalCommissionPercent" style="text-align: right;"></th>
                    <th></th> <!-- Acceptance No -->
                </tr>
            </tfoot>
        </table>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        $('.select2').select2({
            placeholder: "Choose a Re-Ins Party",
            allowClear: true,
            width: '90%'
        });

        var table = $('#reportsTable').DataTable({
            paging: false,
            searching: true,
            ordering: false,
            info: true,
            scrollX: true,
            scrollY: "500px",
            scrollCollapse: true,
            autoWidth: false,
            dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
            buttons: [
    {
        extend: 'excelHtml5',
        text: '<i class="fas fa-file-excel"></i> Excel',
        className: 'btn btn-success btn-sm',
        title: 'Request Note Report',
        exportOptions: {
            columns: ':visible',
            format: {
                body: function (data, row, column, node) {
                    if (column === 19) { // Commission amount column
                        const value = parseFloat($(node).text().replace(/,/g, ''));
                        return isNaN(value) ? '0' : value.toLocaleString('en-US');
                    }
                    if ([5, 6, 8, 9, 18].includes(column)) {
                        return parseFloat($(node).text().replace(/,/g, '')).toLocaleString('en-US');
                    }
                    if (column === 3 || column === 4) {
                        return $(node).find('.truncate-text').attr('title') || $(node).text().trim();
                    }
                    return $(node).text().trim();
                }
            }
        },
        customizeData: function (data) {
            const intVal = i => typeof i === 'string' ? i.replace(/[^\d.-]/g, '') * 1 : (typeof i === 'number' ? i : 0);
            const totalCols = [5, 6, 8, 9, 19];
            let totals = new Array(data.body[0].length).fill('');

            totalCols.forEach(col => {
                let sum = data.body.reduce((acc, row) => acc + intVal(row[col]), 0);
                totals[col] = sum.toLocaleString('en-US');
            });

            totals[0] = 'Totals:';
            data.body.push(totals);
        }
    },
    {
        extend: 'pdfHtml5',
        text: '<i class="fas fa-file-pdf"></i> PDF',
        className: 'btn btn-danger btn-sm',
        title: 'Request Note Report',
        orientation: 'landscape',
        pageSize: 'A4',
        exportOptions: {
            columns: ':visible',
            format: {
                body: function (data, row, column, node) {
                    if (column === 19) { // Commission amount column
                        const value = parseFloat(data.replace(/,/g, ''));
                        return isNaN(value) ? '0' : value.toLocaleString('en-US');
                    }
                    if ([5, 6, 8, 9, 18].includes(column)) {
                        return parseFloat(data.replace(/,/g, '')).toLocaleString('en-US');
                    }
                    if (column === 0 || column === 3 || column === 4) {
                        return $(node).text().trim() || data;
                    }
                    return data;
                }
            }
        },
        customize: function (doc) {
            const intVal = i => typeof i === 'string' ? i.replace(/[^\d.-]/g, '') * 1 : (typeof i === 'number' ? i : 0);
            const totalCols = [5, 6, 8, 9, 19];
            let totals = new Array(doc.content[1].table.body[0].length).fill('');
            totals[0] = 'Totals:';

            totalCols.forEach(col => {
                let sum = doc.content[1].table.body.slice(1).reduce((acc, row) => acc + intVal(row[col].text), 0);
                totals[col] = { text: sum.toLocaleString('en-US'), bold: true };
            });

            doc.content[1].table.body.push(totals);
            doc.pageMargins = [5, 10, 5, 10];
            doc.defaultStyle.fontSize = 5;
            doc.styles.tableHeader.fontSize = 5;
            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length).fill('auto');

            doc.content[1].table.body.forEach(row => {
                row.forEach(cell => {
                    if (cell.text && typeof cell.text === 'string') {
                        cell.text = cell.text.match(/.{1,20}/g).join('\n');
                    }
                });
            });

            doc.content[1].layout = {
                hLineWidth: function(i, node) { return 0.5; },
                vLineWidth: function(i, node) { return 0.5; },
                hLineColor: function(i, node) { return '#aaa'; },
                vLineColor: function(i, node) { return '#aaa'; },
                paddingLeft: function(i, node) { return 2; },
                paddingRight: function(i, node) { return 2; },
                paddingTop: function(i, node) { return 2; },
                paddingBottom: function(i, node) { return 2; }
            };

            doc.content[1]._columnWidths = new Array(doc.content[1].table.body[0].length).fill('*');
        }
    }
],
            columnDefs: [
                {
                    targets: [3, 4],
                    render: function (data, type, row) {
                        if (type === 'filter' || type === 'sort') {
                            return $(data).attr('title') || data;
                        }
                        return data;
                    }
                }
            ],
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();
                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[^\d.-]/g, '') * 1 :
                        typeof i === 'number' ? i : 0;
                };

                var format = val => val.toLocaleString('en-US');

                var totalSumInsured = api.column(5, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                var totalPremium = api.column(6, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                var totalRISumIns = api.column(8, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                var totalRIPremium = api.column(9, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                var totalCommissionPercent = api.column(19, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0);

                $('#totalSumInsured').html(format(totalSumInsured));
                $('#totalPremium').html(format(totalPremium));
                $('#totalRISumIns').html(format(totalRISumIns));
                $('#totalRIPremium').html(format(totalRIPremium));
                $('#totalCommissionPercent').html(format(totalCommissionPercent));
            },
            initComplete: function () {
                this.api().columns.adjust();
                $('.dataTables_filter').css('margin-left', '5px').css('margin-right', '5px');
                $('.dt-buttons').css('margin-left', '5px');

                $('.dataTables_scrollBody').on('scroll', function () {
                    var scrollLeft = $(this).scrollLeft();
                    $('.dataTables_scrollFoot').scrollLeft(scrollLeft);
                });
            },
            drawCallback: function () {
                this.api().columns.adjust();
                $('.dataTables_scrollBody').off('scroll.footerSync').on('scroll.footerSync', function () {
                    var scrollLeft = $(this).scrollLeft();
                    $('.dataTables_scrollFoot').scrollLeft(scrollLeft);
                });
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.open-modal').on('click', function(e) {
            e.preventDefault();
            const reqNote = $(this).data('req-note');

            $.ajax({
                url: `http://192.168.170.24/dashboardApi/reins/rqn/get_notes_dtl.php?req_note=${reqNote}`,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data && data.length > 0) {
                        let tableHtml = `
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Doc No</th>
                                            <th>Insured</th>
                                            <th>Reinsurer</th>
                                            <th>Share</th>
                                            <th>Commission</th>
                                            <th>Accepted Date</th>
                                            <th>Commencement Date</th>
                                            <th>Expiry Date</th>
                                            <th>Issue Date</th>
                                            <th>Ceded SI</th>
                                            <th>Ceded Premium</th>
                                            <th>SI</th>
                                            <th>Premium</th>
                                            <th>Closing Particular Number</th>
                                            <th>Closing Particular Date</th>
                                            <th>Reference Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                        
                        data.forEach(item => {
                            let rowData = typeof item === 'string' ? JSON.parse(item) : item;
                            
                            tableHtml += `
                                <tr>
                                    <td>${rowData.GRD_CEDINGDOCNO || ''}</td>
                                    <td>${rowData.INSURED_DESC || ''}</td>
                                    <td>${rowData.RE_COMP_DESC || ''}</td>
                                    <td>${rowData.GRH_CEDEDSISHARE || ''}%</td>
                                    <td>${rowData.GRH_COMMISSIONRATE || ''}%</td>
                                    <td>${rowData.GRH_ACCEPTEDDATE || ''}</td>
                                    <td>${rowData.GDH_COMMDATE || ''}</td>
                                    <td>${rowData.GDH_EXPIRYDATE || ''}</td>
                                    <td>${rowData.GDH_ISSUEDATE || ''}</td>
                                    <td>${formatNumber(rowData['SUM(GRS_CEDEDSI)']) || '0'}</td>
                                    <td>${formatNumber(rowData['SUM(GRS_CEDEDPREM)']) || '0'}</td>
                                    <td>${formatNumber(rowData['SUM(GRS_TOTALSI)']) || '0'}</td>
                                    <td>${formatNumber(rowData['SUM(GRS_TOTALPREM)']) || '0'}</td>
                                    <td>${rowData.GCP_DOC_REFERENCENO || ''}</td>
                                    <td>${rowData.CREATE_DATE || ''}</td>
                                    <td>${rowData.GCT_THEIR_REF_NO|| ''}</td>
                                </tr>`;
                        });
                        
                        tableHtml += `
                                    </tbody>
                                </table>
                            </div>`;
                        
                        $('#modalBody').html(tableHtml);
                        $('#detailsModal').modal('show');
                    } else {
                        $('#modalBody').html('<p>No data found for this request note.</p>');
                        $('#detailsModal').modal('show');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching details:", error);
                    $('#modalBody').html('<p>Error fetching details. Please try again.</p>');
                    $('#detailsModal').modal('show');
                }
            });
        });
    });

    function formatNumber(num) {
        if (!num) return '0';
        return parseFloat(num).toLocaleString('en-US', {
            maximumFractionDigits: 2,
            minimumFractionDigits: 2
        });
    }
</script>