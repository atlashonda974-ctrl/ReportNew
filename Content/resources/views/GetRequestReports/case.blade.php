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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-multiselect@1.1.2/dist/css/bootstrap-multiselect.min.css">
    <x-datatable-styles />

    <style>
        .multiselect-container { min-width: 200px; }
        .multiselect.dropdown-toggle {
            width: 100%; background-color: #fff; border: 1px solid #ced4da;
            border-radius: 0.375rem; text-align: left; padding: 0.375rem 0.75rem;
            font-size: 0.875rem; color: #212529;
        }
        .multiselect-container .dropdown-item { padding: 4px 12px; }
        .multiselect-container label { font-weight: normal; margin-bottom: 0; }

        tfoot tr.total-row td {
            font-weight: 700; background-color: #e9f0fb;
            border-top: 2px solid #4a6fa5; font-size: 0.85rem;
        }
        tfoot tr.total-row td.total-label { text-align: right; color: #4a6fa5; letter-spacing: 0.04em; }
        tfoot tr.total-row td.total-value  { text-align: right; color: #1a3a5c; }

        .bulk-bar { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; flex-wrap: wrap; }

        /* Locked row: faded, nothing clickable */
        tr.row-locked { opacity: 0.5; pointer-events: none; }
    </style>
</head>
<body>
<div class="container mt-5">
    <x-report-header title="Reinsurance Case" />

    <form method="GET" action="{{ url('/c') }}" class="mb-4" id="filterForm">
        <div class="row g-3">

            <div class="col-md-3 d-flex align-items-center">
                <label for="start_date" class="form-label me-2" style="white-space:nowrap; width:100px;">From Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $start_date }}">
            </div>

            <div class="col-md-3 d-flex align-items-center">
                <label for="end_date" class="form-label me-2" style="white-space:nowrap; width:100px;">To Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $end_date }}">
            </div>

            <div class="col-md-4 d-flex align-items-center">
                <label for="new_category" class="form-label me-2" style="white-space:nowrap; width:100px;">Dept</label>
                @php
                    $selectedCategories = request()->has('new_category')
                        ? (array) request('new_category')
                        : ['Fire','Motor','Miscellaneous'];
                @endphp
                <select name="new_category[]" id="new_category" class="form-control" multiple="multiple" style="width:100%;">
                    @foreach (['Fire','Marine','Motor','Miscellaneous','Health'] as $category)
                        <option value="{{ $category }}" {{ in_array($category,$selectedCategories) ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-center">
                <label for="location_category" class="form-label me-2" style="white-space:nowrap; width:100px;">Branches</label>
                <select name="location_category" id="location_category" class="form-control select2">
                    <option value="All" {{ $location_category == 'All' ? 'selected' : '' }}>ALL</option>
                    @foreach($Branches as $branch)
                        <option value="{{ $branch['fbracode'] }}" {{ $location_category == $branch['fbracode'] ? 'selected' : '' }}>{{ $branch['fbradsc'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-center">
                <label for="broker_code" class="form-label me-2" style="white-space:nowrap;">Select Broker</label>
                <select name="broker_code" id="broker_code" class="form-control select2">
                    <option value="All" {{ request('broker_code') == 'All' ? 'selected' : '' }}>All</option>
                    @foreach($brokers as $broker)
                        <option value="{{ $broker['PPS_PARTY_CODE'] }}" {{ request('broker_code') == $broker['PPS_PARTY_CODE'] ? 'selected' : '' }}>{{ $broker['PPS_PARTY_NAME'] ?? $broker['PPS_DESC'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-center">
                <label for="client_type" class="form-label me-2" style="white-space:nowrap; width:100px;">Client Type</label>
                <select name="client_type" id="client_type" class="form-control">
                    <option value="All" {{ request('client_type') == 'All' ? 'selected' : '' }}>ALL</option>
                    <option value="new"  {{ request('client_type') == 'new'  ? 'selected' : '' }}>New</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-center">
                <label for="insu_type" class="form-label me-2" style="white-space:nowrap; width:100px;">Insurance Type</label>
                <select name="insu_type" id="insu_type" class="form-control">
                    <option value="Conv"    {{ request('insu_type','takaful') === 'Conv'    ? 'selected' : '' }}>Conventional</option>
                    <option value="takaful" {{ request('insu_type','takaful') === 'takaful' ? 'selected' : '' }}>Takaful</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-center">
                <label for="posted" class="form-label me-2" style="white-space:nowrap; width:100px;">Posted</label>
                <select name="posted" id="posted" class="form-control">
                    <option value="Y" {{ request('posted','Y') === 'Y' ? 'selected' : '' }}>Posted</option>
                    <option value="N" {{ request('posted','Y') === 'N' ? 'selected' : '' }}>Not Posted</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-center">
                <label for="doc_type" class="form-label me-2" style="white-space:nowrap; width:100px;">Doc Type</label>
                <select name="doc_type" id="doc_type" class="form-control">
                    <option value="P" {{ request('doc_type','P') === 'P' ? 'selected' : '' }}>Policy</option>
                    <option value="E" {{ request('doc_type','P') === 'E' ? 'selected' : '' }}>Endorsement</option>
                    <option value="C" {{ request('doc_type','P') === 'C' ? 'selected' : '' }}>Certificate</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-center">
                <label for="sum" class="form-label me-2" style="white-space:nowrap; width:100px;">Sum</label>
                <input type="number" name="sum" id="sum" class="form-control" value="{{ request('sum',10000000) }}">
            </div>

            <div class="col-md-3 d-flex align-items-center">
                <button type="submit" class="btn btn-outline-primary me-1"><i class="bi bi-funnel-fill"></i></button>
                <a href="{{ url('/c') }}" class="btn btn-outline-secondary me-1"><i class="bi bi-arrow-clockwise"></i></a>
            </div>

        </div>
    </form>

    @if($data->isEmpty())
        <div class="alert alert-danger">No data available.</div>
    @else

    @php
        $parseDate = function ($raw) {
            if (empty(trim($raw ?? ''))) return null;
            foreach (['d-M-y','j-M-y','d-M-Y','j-M-Y'] as $fmt) {
                try { return \Carbon\Carbon::createFromFormat($fmt, $raw)->format('d-m-Y'); } catch (\Exception $e) {}
            }
            return null;
        };
        $categoryMap = [11=>'Fire',12=>'Marine',13=>'Motor',14=>'Miscellaneous',16=>'Health'];
    @endphp

    {{-- BULK BAR --}}
    <div class="bulk-bar">
        <button type="button" id="bulkAcceptBtn" class="btn btn-success btn-sm" disabled>
            <i class="fas fa-check me-1"></i> Accept Selected (<span id="selCount">0</span>)
        </button>
        <button type="button" id="bulkRejectBtn" class="btn btn-danger btn-sm" disabled>
            <i class="fas fa-xmark me-1"></i> Reject Selected (<span id="selCount2">0</span>)
        </button>
    </div>

    <table id="reportsTable" class="table table-bordered table-responsive">
        <thead>
            <tr>
                <th style="width:30px; text-align:center;">
                    <input type="checkbox" id="chkAll" style="width:15px;height:15px;cursor:pointer;" title="Select All">
                </th>
                <th>Sr#</th>
                <th>Actions</th>
                <th>Document Ref No</th>
                <th>Base Doc</th>
                <th>Req Note Status</th>
                <th>Department</th>
                <th>Issue</th>
                <th>Comm.</th>
                <th>Exp</th>
                <th>Posting</th>
                <th>Posting Tag</th>
                <th>Insured</th>
                <th>IAP</th>
                <th>Sum Insured</th>
                <th>Gross Premium</th>
                <th>Branch</th>
            </tr>
        </thead>
        <tbody>
            @php $counter = 1; @endphp
            @foreach($data as $record)
                @php
                    $dept      = $categoryMap[$record->PDP_DEPT_CODE ?? null] ?? 'N/A';
                    $si        = trim($record->GDH_TOTALSI ?? '');
                    $gp        = trim($record->GDH_GROSSPREMIUM ?? '');
                    $siDisplay = ($si===''||$si==='&nbsp;') ? 'N/A' : (is_numeric($si) ? number_format((float)$si) : $si);
                    $gpDisplay = ($gp===''||$gp==='&nbsp;') ? 'N/A' : (is_numeric($gp) ? number_format((float)$gp) : $gp);
                @endphp
                <tr>
                    <td style="text-align:center; vertical-align:middle;">
                        <input type="checkbox" class="rowChk" style="width:15px;height:15px;cursor:pointer;"
                            data-uw-doc="{{ $record->GDH_DOC_REFERENCE_NO ?? '' }}"
                            data-dept="{{ $record->PDP_DEPT_CODE ?? '' }}"
                            data-issue-date="{{ $record->GDH_ISSUEDATE ?? '' }}"
                            data-comm-date="{{ $record->GDH_COMMDATE ?? '' }}"
                            data-expiry-date="{{ $record->GDH_EXPIRYDATE ?? '' }}"
                            data-insured="{{ urlencode($record->PPS_DESC ?? 'N/A') }}"
                            data-location="{{ urlencode($record->PLC_LOCADESC ?? 'N/A') }}"
                            data-business-class="{{ urlencode($record->GDH_POSTING_DATE ?? 'N/A') }}"
                            data-sum-insured="{{ $record->GDH_TOTALSI ?? '' }}"
                            data-gross-premium="{{ $record->GDH_GROSSPREMIUM ?? '' }}"
                            data-base-doc="{{ $record->GDH_BASEDOCUMENTNO ?? '' }}"
                            data-pii-desc="{{ urlencode($record->PII_DESC ?? '') }}"
                            data-rqn-sts="{{ $record->RQN_STS ?? '' }}">
                    </td>
                    <td>{{ $counter }}</td>
                    <td>
                        <button type="button" class="btn btn-success btn-sm reins-action me-1" data-status="Y"
                            data-uw-doc="{{ $record->GDH_DOC_REFERENCE_NO ?? '' }}"
                            data-dept="{{ $record->PDP_DEPT_CODE ?? '' }}"
                            data-issue-date="{{ $record->GDH_ISSUEDATE ?? '' }}"
                            data-comm-date="{{ $record->GDH_COMMDATE ?? '' }}"
                            data-expiry-date="{{ $record->GDH_EXPIRYDATE ?? '' }}"
                            data-insured="{{ urlencode($record->PPS_DESC ?? 'N/A') }}"
                            data-location="{{ urlencode($record->PLC_LOCADESC ?? 'N/A') }}"
                            data-business-class="{{ urlencode($record->GDH_POSTING_DATE ?? 'N/A') }}"
                            data-sum-insured="{{ $record->GDH_TOTALSI ?? '' }}"
                            data-gross-premium="{{ $record->GDH_GROSSPREMIUM ?? '' }}"
                            data-base-doc="{{ $record->GDH_BASEDOCUMENTNO ?? '' }}"
                            data-pii-desc="{{ urlencode($record->PII_DESC ?? '') }}"
                            data-rqn-sts="{{ $record->RQN_STS ?? '' }}">
                            <i class="fas fa-check"></i> Accept
                        </button>
                        <button type="button" class="btn btn-danger btn-sm reins-action" data-status="N"
                            data-uw-doc="{{ $record->GDH_DOC_REFERENCE_NO ?? '' }}"
                            data-dept="{{ $record->PDP_DEPT_CODE ?? '' }}"
                            data-issue-date="{{ $record->GDH_ISSUEDATE ?? '' }}"
                            data-comm-date="{{ $record->GDH_COMMDATE ?? '' }}"
                            data-expiry-date="{{ $record->GDH_EXPIRYDATE ?? '' }}"
                            data-insured="{{ urlencode($record->PPS_DESC ?? 'N/A') }}"
                            data-location="{{ urlencode($record->PLC_LOCADESC ?? 'N/A') }}"
                            data-business-class="{{ urlencode($record->GDH_POSTING_DATE ?? 'N/A') }}"
                            data-sum-insured="{{ $record->GDH_TOTALSI ?? '' }}"
                            data-gross-premium="{{ $record->GDH_GROSSPREMIUM ?? '' }}"
                            data-base-doc="{{ $record->GDH_BASEDOCUMENTNO ?? '' }}"
                            data-pii-desc="{{ urlencode($record->PII_DESC ?? '') }}"
                            data-rqn-sts="{{ $record->RQN_STS ?? '' }}">
                            <i class="fas fa-xmark"></i> Reject
                        </button>
                    </td>
                    <td>{{ $record->GDH_DOC_REFERENCE_NO ?? 'N/A' }}</td>
                    <td>{{ $record->GDH_BASEDOCUMENTNO ?? 'N/A' }}</td>
                    <td>{{ $record->RQN_STS ?? 'N/A' }}</td>
                    <td>{{ $dept }}</td>
                    <td>{{ $parseDate($record->GDH_ISSUEDATE  ?? '') ?? 'N/A' }}</td>
                    <td>{{ $parseDate($record->GDH_COMMDATE   ?? '') ?? 'N/A' }}</td>
                    <td>{{ $parseDate($record->GDH_EXPIRYDATE ?? '') ?? 'N/A' }}</td>
                    <td>{{ $parseDate($record->GDH_POSTING_DATE ?? '') ?? 'N/A' }}</td>
                    <td>{{ $record->GDH_POSTING_TAG ?? 'N/A' }}</td>
                    <td title="{{ $record->PPS_DESC ?? 'N/A' }}">{{ \Illuminate\Support\Str::limit($record->PPS_DESC ?? 'N/A', 20, '...') }}</td>
                    <td title="{{ $record->PII_DESC ?? 'N/A' }}">{{ \Illuminate\Support\Str::limit($record->PII_DESC ?? 'N/A', 20, '...') }}</td>
                    <td class="numeric" style="text-align:right;">{{ $siDisplay }}</td>
                    <td class="numeric" style="text-align:right;">{{ $gpDisplay }}</td>
                    <td title="{{ $record->PLC_LOCADESC ?? 'N/A' }}">{{ \Illuminate\Support\Str::limit($record->PLC_LOCADESC ?? 'N/A', 30, '...') }}</td>
                </tr>
                @php $counter++; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="14" class="total-label">TOTAL</td>
                <td id="totalSumInsured" class="total-value">0</td>
                <td id="totalGrossPrem"  class="total-value">0</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.3.2/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-multiselect@1.1.2/dist/js/bootstrap-multiselect.min.js"></script>

<script>
$(document).ready(function () {

    $('.select2').select2({ placeholder: "Select a location", allowClear: true, width: '69%' });

    $('#new_category').multiselect({
        includeSelectAllOption: true, selectAllText: 'Select All',
        allSelectedText: 'All Departments', numberDisplayed: 3,
        buttonWidth: '100%', buttonClass: 'btn btn-light border',
        templates: {
            button: '<button type="button" class="multiselect dropdown-toggle btn btn-light border" data-bs-toggle="dropdown" data-toggle="dropdown"><span class="multiselect-selected-text"></span> <b class="caret"></b></button>',
        },
        onChange: function () {}
    });

    @if(!$data->isEmpty())

    var table = $('#reportsTable').DataTable({
        paging: false, searching: true, ordering: true, info: true,
        scrollX: true, scrollY: '500px', scrollCollapse: false,
        fixedHeader: { header: true, footer: true }, autoWidth: true,
        columnDefs: [
            { orderable: false, targets: 0 },
            { orderable: false, targets: 2 }
        ],
        dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
        buttons: [
            {
                extend: 'excel', text: '<i class="fas fa-file-excel custom-icon"></i> Excel',
                className: 'btn btn-success btn-sm', title: 'Reinsurance Case Report', footer: true,
                exportOptions: { columns: ':visible:not(:nth-child(1)):not(:nth-child(3))' }
            },
            {
                extend: 'pdf', text: '<i class="fas fa-file-pdf custom-icon"></i> PDF',
                className: 'btn btn-danger btn-sm', title: 'Reinsurance Case Report',
                orientation: 'landscape', pageSize: 'A4', footer: true,
                exportOptions: {
                    columns: ':visible:not(:nth-child(1)):not(:nth-child(3))',
                    format: {
                        body: function (data, row, column, node) {
                            if (column === 9 || column === 10) return $(node).attr('title') || $(node).text().trim();
                            return $(node).text().trim();
                        }
                    }
                }
            }
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            var intVal = function (i) {
                if (typeof i === 'string') { var c = i.replace(/,/g,'').replace(/[^\d.-]/g,''); return c===''?0:parseFloat(c); }
                return typeof i === 'number' ? i : 0;
            };
            var si = api.column(14, {page:'current'}).data().reduce(function(a,b){return intVal(a)+intVal(b);},0);
            var gp = api.column(15, {page:'current'}).data().reduce(function(a,b){return intVal(a)+intVal(b);},0);
            $('#totalSumInsured').html(si.toLocaleString('en-US'));
            $('#totalGrossPrem').html(gp.toLocaleString('en-US'));
        },
        initComplete: function () {
            this.api().columns.adjust();
            $('.dataTables_filter').css({'margin-left':'5px','margin-right':'5px'});
            $('.dt-buttons').css('margin-left','5px');
        },
        drawCallback: function () { this.api().columns.adjust(); }
    });

    // ── Checkbox logic ──────────────────────────────────────────────

    $(document).on('change', '#chkAll', function () {
        $(table.rows().nodes()).find('.rowChk').prop('checked', this.checked);
        updateBar();
    });

    $(document).on('change', '.rowChk', function () {
        updateBar();
        var total   = table.rows().count();
        var checked = $(table.rows().nodes()).find('.rowChk:checked').length;
        var hdr = document.getElementById('chkAll');
        hdr.indeterminate = (checked > 0 && checked < total);
        hdr.checked = (checked === total && total > 0);
    });

    function updateBar() {
        var n = $(table.rows().nodes()).find('.rowChk:checked').length;
        $('#selCount, #selCount2').text(n);
        $('#bulkAcceptBtn, #bulkRejectBtn').prop('disabled', n === 0);
    }

    // ── Per-row action — plain $.ajax, no async/await ────────────────

    $(document).on('click', '.reins-action', function () {
        var $btn   = $(this);
        var $row   = $btn.closest('tr');
        var status = $btn.data('status');

        // Immediately lock the whole row: fades it out + blocks all clicks
        $row.addClass('row-locked');

        // Show spinner on the clicked button
        $btn.html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' +
            (status === 'Y' ? 'Accepting...' : 'Rejecting...')
        );

        $.ajax({
            url:      "{{ route('fetch.reinsurance.data') }}",
            method:   'POST',
            dataType: 'json',
            data: {
                _token:         "{{ csrf_token() }}",
                status:         status,
                uw_doc:         $btn.data('uw-doc'),
                dept:           $btn.data('dept'),
                issue_date:     $btn.data('issue-date'),
                comm_date:      $btn.data('comm-date'),
                expiry_date:    $btn.data('expiry-date'),
                insured:        $btn.data('insured'),
                location:       $btn.data('location'),
                business_class: $btn.data('business-class'),
                sum_insured:    $btn.data('sum-insured'),
                gross_premium:  $btn.data('gross-premium'),
                base_doc:       $btn.data('base-doc'),
                pii_desc:       $btn.data('pii-desc'),
                rqn_sts:        $btn.data('rqn-sts')
            },
            success: function () {
                // Replace the actions cell with a done badge
                $row.find('td').eq(2).html(
                    status === 'Y'
                    ? '<span class="badge bg-success px-2 py-1"><i class="fas fa-check me-1"></i>Accepted</span>'
                    : '<span class="badge bg-danger  px-2 py-1"><i class="fas fa-xmark me-1"></i>Rejected</span>'
                );
            },
            error: function () {
                // Unlock so the user can retry
                $row.removeClass('row-locked');
                $btn.html(
                    status === 'Y'
                    ? '<i class="fas fa-check"></i> Accept'
                    : '<i class="fas fa-xmark"></i> Reject'
                );
                alert('Action failed. Please try again.');
            }
        });
    });

    // ── Bulk send — sequential plain $.ajax via recursive callback ────
    // No async/await — each request starts only after the previous one completes

    $('#bulkAcceptBtn').on('click', function () { bulkSend('Y'); });
    $('#bulkRejectBtn').on('click', function () { bulkSend('N'); });

    function bulkSend(status) {
        var rows = [];
        $(table.rows().nodes()).find('.rowChk:checked').each(function () {
            var $c = $(this);
            rows.push({
                uw_doc:         $c.data('uw-doc'),
                dept:           $c.data('dept'),
                issue_date:     $c.data('issue-date'),
                comm_date:      $c.data('comm-date'),
                expiry_date:    $c.data('expiry-date'),
                insured:        $c.data('insured'),
                location:       $c.data('location'),
                business_class: $c.data('business-class'),
                sum_insured:    $c.data('sum-insured'),
                gross_premium:  $c.data('gross-premium'),
                base_doc:       $c.data('base-doc'),
                pii_desc:       $c.data('pii-desc'),
                rqn_sts:        $c.data('rqn-sts')
            });
        });

        if (!rows.length) return;

        // Lock bulk buttons + show spinner on clicked one
        $('#bulkAcceptBtn, #bulkRejectBtn').prop('disabled', true);
        var $clickedBtn = status === 'Y' ? $('#bulkAcceptBtn') : $('#bulkRejectBtn');
        $clickedBtn.html('<span class="spinner-border spinner-border-sm" role="status"></span> Processing...');

        // Lock ALL checkboxes so user cannot change selection
        $('#chkAll').prop('disabled', true).css({ opacity: 0.4, cursor: 'not-allowed', 'pointer-events': 'none' });
        $(table.rows().nodes()).find('.rowChk').prop('disabled', true).css({ opacity: 0.4, cursor: 'not-allowed', 'pointer-events': 'none' });

        // Lock all per-row action buttons too
        $(table.rows().nodes()).find('.reins-action').prop('disabled', true).css({ opacity: 0.4, cursor: 'not-allowed' });

        var csrf  = "{{ csrf_token() }}";
        var index = 0;

        function sendNext() {
            if (index >= rows.length) {
                location.reload();
                return;
            }
            var row = rows[index];
            index++;
            $.ajax({
                url:      "{{ route('fetch.reinsurance.data') }}",
                method:   'POST',
                dataType: 'json',
                data:     $.extend({ _token: csrf, status: status }, row),
                complete: function () {
                    // success or error — always move to next
                    sendNext();
                }
            });
        }

        sendNext(); 
    }

    @endif
});
</script>

</body>
</html>