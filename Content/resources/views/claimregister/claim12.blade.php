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
        .nav-tabs .nav-link {font-weight:600;color:#495057;}
        .nav-tabs .nav-link.active{background:linear-gradient(135deg,#007bff,#0056b3);color:white!important;border-color:#007bff;}
        .tab-pane{padding-top:20px;}
        body{background-color:#f8f9fa;}
        .dataTables_wrapper .dataTables_scrollBody{background-color:white;}
        tfoot{font-weight:bold;background-color:#f1f3f5;}
        .buttons-excel,.buttons-pdf{margin-left:5px!important;}
    </style>
</head>
<body>
<div class="container mt-5">
    <x-report-header title="Get Reports (R12)" />

    {{-- ==== FILTER FORM ==== --}}
   <form method="GET" action="{{ url('/cr12') }}" class="row g-3 mb-4 align-items-end">
    <div class="col-md-3 d-flex align-items-center">
        <label for="start_date" class="form-label me-2" style="white-space:nowrap;width:100px;">From Date</label>
        <input type="date" name="start_date" id="start_date" class="form-control"
               value="{{ request('start_date', $start_date) }}">
    </div>

    <div class="col-md-3 d-flex align-items-center">
        <label for="end_date" class="form-label me-2" style="white-space:nowrap;width:100px;">To Date</label>
        <input type="date" name="end_date" id="end_date" class="form-control"
               value="{{ request('end_date', $end_date) }}">
    </div>
     <div class="col-md-4 d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-primary me-2" title="Filter">
                        <i class="bi bi-funnel-fill"></i> Filter
                    </button>
                    <a href="{{ url('/cr12') }}" class="btn btn-outline-secondary me-2" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
</form>


    {{-- ==== ERROR / NO DATA ==== --}}
    @if(isset($error_message))
        <div class="alert alert-danger">{{ $error_message }}</div>
    @elseif($uw_collection->isEmpty() && $claim->isEmpty() && $gl_exp->isEmpty())
        <div class="alert alert-info">No data available for the selected filters.</div>
    @else
        {{-- ==== TABS ==== --}}
        <ul class="nav nav-tabs mb-4" id="claimTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="uw-tab" data-bs-toggle="tab" data-bs-target="#uw-pane">
                    Underwriting <span class="badge bg-light text-dark ms-2">{{ $uw_count }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="claim-tab" data-bs-toggle="tab" data-bs-target="#claim-pane">
                    Claims <span class="badge bg-light text-dark ms-2">{{ $claim_count }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="gl-tab" data-bs-toggle="tab" data-bs-target="#gl-pane">
                    General Ledger <span class="badge bg-light text-dark ms-2">{{ $gl_exp_count }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="claimTabContent">
            {{-- ==== UNDERWRITING ==== --}}
            <div class="tab-pane fade show active" id="uw-pane" role="tabpanel">
    @if($uw_collection->isNotEmpty())
        <table id="uwTable" class="table table-bordered table-hover table-striped display nowrap" style="width:100%">
            <thead class="table-primary">
                <tr>
                    <th>Location</th><th>Location Desc</th><th>GIAS2</th><th>Business Class</th><th>Doc Type</th>
                    <th>Dept Code</th><th>Doc Ref No</th><th>Base Doc No</th><th>Party Code</th><th>Party Name</th>
                    <th>Division</th><th>User</th><th>Entry Date</th><th>Issue Date</th><th>Comm. Date</th>
                    <th>Expiry Date</th><th>Gross Premium</th><th>Net Premium</th><th>Total SI</th><th>Broker</th>
                </tr>
            </thead>
            <tbody>
                @foreach($uw_collection as $r)
                    @if(isset($r->message))
                        <tr>
                            <td colspan="20" class="text-center text-danger">{{ $r->message }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>{{ $r->PLC_LOC_CODE ?? 'N/A' }}</td>
                            <td>{{ $r->PLC_LOCADESC ?? 'N/A' }}</td>
                            <td>{{ $r->PLC_LOC_GIAS2 ?? 'N/A' }}</td>
                            <td>{{ $r->PBC_BUSICLASS_CODE ?? 'N/A' }}</td>
                            <td>{{ $r->PDT_DOCTYPE ?? 'N/A' }}</td>
                            <td>{{ $r->PDP_DEPT_CODE ?? 'N/A' }}</td>
                            <td>{{ $r->GDH_DOC_REFERENCE_NO ?? 'N/A' }}</td>
                            <td>{{ $r->GDH_BASEDOCUMENTNO ?? 'N/A' }}</td>
                            <td>{{ $r->PPS_PARTY_CODE ?? 'N/A' }}</td>
                            <td>{{ $r->PPS_DESC ?? 'N/A' }}</td>
                            <td>{{ $r->PDO_DEVOFFDESC ?? 'N/A' }}</td>
                            <td>{{ $r->GDH_CREATEUSER ?? 'N/A' }}</td>
                            <td>{{ $r->GDH_DOC_ENTRY_DATE ?? 'N/A' }}</td>
                            <td>{{ $r->GDH_ISSUEDATE ?? 'N/A' }}</td>
                            <td>{{ $r->GDH_COMMDATE ?? 'N/A' }}</td>
                            <td>{{ $r->GDH_EXPIRYDATE ?? 'N/A' }}</td>
                            <td style="text-align:right" data-order="{{ $r->GDH_GROSSPREMIUM ?? 0 }}">{{ number_format($r->GDH_GROSSPREMIUM ?? 0) }}</td>
                            <td style="text-align:right">{{ number_format($r->GDH_NETPREMIUM ?? 0) }}</td>
                            <td style="text-align:right">{{ number_format($r->GDH_TOTALSI ?? 0) }}</td>
                            <td>{{ $r->BROKER ?? 'N/A' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #cfe2ff;">
                    <th colspan="16" style="text-align: right;">Total Gross Premium:</th>
                    <th style="text-align: right;" id="uwTotal">0.00</th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="alert alert-warning">No Underwriting records found.</div>
    @endif
</div>


            {{-- ==== CLAIMS ==== --}}
            <div class="tab-pane fade" id="claim-pane" role="tabpanel">
                @if($claim->isNotEmpty())
                    <table id="claimTable" class="table table-bordered table-hover table-striped display nowrap" style="width:100%">
                        <thead class="table-success">
                            <tr>
                                <th>Location</th><th>Dept Code</th><th>Claim Doc No</th><th>Party Code</th><th>Party Name</th>
                                <th>Mobile</th><th>Email</th><th>Intimation Date</th><th>Entry No</th><th>Ref No</th>
                                <th>Base Doc No</th><th>Claim Amount</th><th>Ins Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($claim as $r)
                                <tr>
                                    <td>{{ $r->PLC_LOC_CODE ?? 'N/A' }}</td>
                                    <td>{{ $r->PDP_DEPT_CODE ?? 'N/A' }}</td>
                                    <td>{{ $r->GIH_DOCUMENTNO ?? 'N/A' }}</td>
                                    <td>{{ $r->PPS_PARTY_CODE ?? 'N/A' }}</td>
                                    <td>{{ $r->PPS_DESC ?? 'N/A' }}</td>
                                    <td>{{ $r->PPS_MOBILE_NO ?? 'N/A' }}</td>
                                    <td>{{ $r->PPS_EMAIL_ADDRESS ?? 'N/A' }}</td>
                                    <td>{{ $r->GIH_INTIMATIONDATE ?? 'N/A' }}</td>
                                    <td>{{ $r->GIH_INTI_ENTRYNO ?? 'N/A' }}</td>
                                    <td>{{ $r->GIH_DOC_REF_NO ?? 'N/A' }}</td>
                                    <td>{{ $r->GID_BASEDOCUMENTNO ?? 'N/A' }}</td>
                                    <td style="text-align:right" data-order="{{ $r->GIH_LOSSCLAIMED ?? 0 }}">
                                        {{ number_format($r->GIH_LOSSCLAIMED ?? 0, 2) }}
                                    </td>
                                    <td>
                                        @php
                                            $map = ['D'=>'Direct','I'=>'Inward','O'=>'Outward'];
                                            echo $map[$r->PIY_INSUTYPE ?? ''] ?? $r->PIY_INSUTYPE ?? 'N/A';
                                        @endphp
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background-color:#d4edda;">
                                <th colspan="11" style="text-align:right;">Total Claim Amount:</th>
                                <th style="text-align:right;" id="claimTotal">0.00</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    <div class="alert alert-warning">No Claim records found.</div>
                @endif
            </div>

            {{-- ==== GL ==== --}}
            <div class="tab-pane fade" id="gl-pane" role="tabpanel">
                @if($gl_exp->isNotEmpty())
                    <table id="glTable" class="table table-bordered table-hover table-striped display nowrap" style="width:100%">
                        <thead class="table-warning">
                            <tr>
                                <th>Voucher Type</th><th>Voucher No</th><th>Voucher Date</th><th>Voucher Narration</th>
                                <th>Detail Narration</th><th>GL Account Code</th><th>Debit Amount</th><th>Credit Amount</th>
                                <th>Document Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gl_exp as $record)
                                <tr>
                                    <td>{{ $record->PVT_VCHTTYPE ?? 'N/A' }}</td>
                                    <td>{{ $record->LVH_VCHDNO ?? 'N/A' }}</td>
                                    <td>{{ $record->LVH_VCHDDATE ?? 'N/A' }}</td>
                                    <td>{{ $record->LVH_VCHDNARRATION ?? 'N/A' }}</td>
                                    <td>{{ $record->LVD_VCDTNARRATION1 ?? 'N/A' }}</td>
                                    <td>{{ $record->PCA_GLACCODE ?? 'N/A' }}</td>
                                   
                                   
                                    <td>{{ $record->LVD_VCDTDOCREF ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background-color:#fff3cd;">
                                <th colspan="6" style="text-align:right;">Total:</th>
                                <th style="text-align:right;" id="glDebitTotal">0.00</th>
                                <th style="text-align:right;" id="glCreditTotal">0.00</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    <div class="alert alert-warning">No GL Expense records found.</div>
                @endif
            </div>
        </div>
    @endif
</div>

{{-- ==== SCRIPTS ==== --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.3.2/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('#location_category').select2({placeholder: "Select a branch", allowClear: true});

    const commonConfig = {
        paging: false,
        searching: true,
        ordering: true,
        info: true,
        scrollX: true,
        scrollY: "65vh",
        scrollCollapse: true,
        fixedHeader: {header: true, footer: true},
        autoWidth: true,
        dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
        buttons: [
            {extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-success btn-sm', footer: true},
            {extend: 'pdf',   text: '<i class="fas fa-file-pdf"></i> PDF',   className: 'btn btn-danger btn-sm',   footer: true, orientation: 'landscape', pageSize: 'A4'}
        ]
    };

    const initTable = (id, colIdx = null, totalId = null, title = 'Report') => {
        if ($.fn.DataTable.isDataTable('#' + id)) return;

        $('#' + id).DataTable({
            ...commonConfig,
            buttons: [
                {extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-success btn-sm', title, footer: true},
                {extend: 'pdf',   text: '<i class="fas fa-file-pdf"></i> PDF',   className: 'btn btn-danger btn-sm',   title, footer: true, orientation: 'landscape', pageSize: 'A4'}
            ],
            footerCallback: function () {
                const api = this.api();

                if (id === 'glTable') {
                    const debit  = api.column(6).data().reduce((a, v) => a + parseFloat(v) ,0);
                    const credit = api.column(7).data().reduce((a, v) => a + parseFloat(v) ,0);
                    $('#glDebitTotal').html('<strong>' + debit.toLocaleString('en-US', {minimumFractionDigits:2}) + '</strong>');
                    $('#glCreditTotal').html('<strong>' + credit.toLocaleString('en-US', {minimumFractionDigits:2}) + '</strong>');
                } else if (colIdx !== null && totalId !== null) {
                    const total = api.column(colIdx).data().reduce((a, v) => a + parseFloat(v) ,0);
                    $(totalId).html('<strong>' + total.toLocaleString('en-US', {minimumFractionDigits:2}) + '</strong>');
                }
            },
            initComplete: function () {
                this.api().columns.adjust();
                $('.dataTables_filter input').attr('placeholder', 'Search...');
            },
            drawCallback: function () {
                this.api().columns.adjust();
            }
        });
    };

    // UW table (always visible on load)
    if ($('#uwTable tbody tr').length) initTable('uwTable', 16, '#uwTotal', 'Underwriting Report');

    // Claim & GL tables – initialise only when their tab is shown
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const pane = $(e.target).data('bs-target');
        if (pane === '#claim-pane' && $('#claimTable tbody tr').length) {
            initTable('claimTable', 11, '#claimTotal', 'Claims Report');
        }
        if (pane === '#gl-pane' && $('#glTable tbody tr').length) {
            initTable('glTable', null, null, 'GL Expense Report');
        }
    });
});
</script>
</body>
</html>