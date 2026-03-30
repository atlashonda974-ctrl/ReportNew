<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.master_titles')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <x-datatable-styles />

    <style>
        .time-filter-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .time-tab {
            flex: 1;
            min-width: 80px;   
            max-width: 100px; 
            border-radius: 8px;
            padding: 8px 5px;
            text-align: center;
            transition: all 0.2s;
            text-decoration: none;
            font-size: 14px;
        }
        .time-tab:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .time-tab.active {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white !important;
        }
        .dt-ellipsis {
            max-width: 100px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            cursor: pointer;
        }
        #emailHistoryContent .card {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        #emailHistoryContent .card-header {
            font-size: 14px;
        }
        #emailHistoryContent .card-body {
            font-size: 13px;
        }
        .view-email-history-btn {
            transition: all 0.2s;
        }
        .view-email-history-btn:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <x-report-header title="Get O/S Surveyor Appointment" />

    <!-- Filter Form -->
    <form method="GET" action="{{ url('/cr2') }}" class="mb-4">
        <div class="row g-3">
            <div class="col-md-3 d-flex align-items-center">
                <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" 
                       value="{{ request('start_date', $start_date) }}">
            </div>

            <div class="col-md-3 d-flex align-items-center">
                <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" 
                       value="{{ request('end_date', $end_date) }}">
            </div>

            <div class="col-md-3 d-flex align-items-center">
                <label for="new_category" class="form-label me-2" style="white-space: nowrap; width: 100px;">Departments</label>
                <select name="new_category" id="new_category" class="form-control">
                    <option value="" {{ $selected_category == '' ? 'selected' : '' }}>All Departments</option>
                    <option value="Fire" {{ $selected_category == 'Fire' ? 'selected' : '' }}>Fire</option>
                    <option value="Marine" {{ $selected_category == 'Marine' ? 'selected' : '' }}>Marine</option>
                    <option value="Motor" {{ $selected_category == 'Motor' ? 'selected' : '' }}>Motor</option>
                    <option value="Miscellaneous" {{ $selected_category == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                    <option value="Health" {{ $selected_category == 'Health' ? 'selected' : '' }}>Health</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-center">
                <label for="location_category" class="form-label me-2" style="white-space: nowrap;">Branches</label>
                <select name="location_category" id="location_category" class="form-control select2">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->fbracode }}" 
                                {{ request('location_category') == $branch->fbracode ? 'selected' : '' }}>
                            {{ $branch->fbradsc }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-center">
                <label for="insu" class="form-label me-2" style="white-space: nowrap;">Insurance Type</label>
                <select name="insu[]" id="insu" class="form-control select2-insurance" multiple>
                    <option value="D" {{ in_array('D', request('insu', ['D','O'])) ? 'selected' : '' }}>Direct</option>
                    <option value="I" {{ in_array('I', request('insu', ['D','O'])) ? 'selected' : '' }}>Inward</option>
                    <option value="O" {{ in_array('O', request('insu', ['D','O'])) ? 'selected' : '' }}>Outward</option>
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-center">
                <button type="submit" class="btn btn-outline-primary me-2" title="Filter">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                <a href="{{ url('/cr2') }}" class="btn btn-outline-secondary me-2" title="Reset">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Time Filter Tabs -->
    @php
        $baseQuery = request()->except('time_filter');
    @endphp

    <div class="d-flex justify-content-start mb-3">
        <div class="card time-filter-card">
            <div class="card-body py-2 px-3">
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $tabs = [
                            'all'     => 'All',
                            '2days'   => '2 Days',
                            '5days'   => '5 Days',
                            '7days'   => '7 Days',
                            '10days'  => '10 Days',
                            '15days'  => '15 Days',
                            '15plus'  => '15+ Days',
                        ];
                    @endphp

                    @foreach($tabs as $key => $label)
                        <a href="{{ url('/cr2') . '?' . http_build_query(array_merge($baseQuery, ['time_filter' => $key])) }}"
                           class="time-tab {{ $selected_time_filter === $key ? 'active' : 'bg-light' }}">
                            <div class="fw-bold">{{ $label }}</div>
                            <div class="small">{{ $counts[$key] ?? 0 }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if(empty($data))
        <div class="alert alert-danger">No data available.</div>
    @else
        <div class="table-responsive">
            <table id="reportsTable" class="table table-bordered">
    <thead>
        <tr>
            <th>Action</th>
            <th>Email Logs</th>
            <th>Dept.</th>
            <th>Loss city</th>
            <th>Claim No</th>
            <th>Int Date</th>
            <th>Policy NO</th>
            <th>Insured</th>
            <th>Claimant</th>
            <th>Cause of loss</th>
            <th>Claim Amt</th>
            <th>Sum Insured</th>
            <th>MOBILE NO</th>
            <th>Branch Name</th>
            <th>Insured type</th>
        </tr>
    </thead>

    <tbody>
        @foreach($data as $record)
            @php
                $intimationDate = $record->GIH_INTIMATIONDATE ?? null;
                $days = $intimationDate
                    ? \Carbon\Carbon::parse($intimationDate)->diffInDays(now())
                    : 0;

                $rowClass = ($days > 2) ? 'table-warning' : '';

                $categoryMapping = [
                    11 => 'Fire',
                    12 => 'Marine',
                    13 => 'Motor',
                    14 => 'Miscellaneous',
                    16 => 'Health',
                ];

                $department = $categoryMapping[$record->PDP_DEPT_CODE ?? null] ?? 'N/A';

                $insuranceTypeMapping = [
                    'D' => 'Direct',
                    'I' => 'Inward',
                    'O' => 'Outward',
                ];

                $insuranceType = $insuranceTypeMapping[$record->PIY_INSUTYPE ?? null]
                    ?? $record->PIY_INSUTYPE
                    ?? 'N/A';

                $emailCount = $record->email_send_count ?? 0;
            @endphp

            <tr class="{{ $rowClass }}">
                <td>
                    <button type="button"
                            class="btn btn-info btn-sm send-surveyor-email-btn"
                            data-claim-no="{{ $record->GIH_DOC_REF_NO ?? 'N/A' }}"
                            data-insured="{{ $record->PPS_DESC ?? 'N/A' }}"
                            data-claimant="{{ $record->GIH_CLAIMANT_INFO ?? 'N/A' }}"
                            data-mobile="{{ $record->PPS_MOBILE_NO ?? 'N/A' }}"
                            data-intimation="{{ $intimationDate ? \Carbon\Carbon::parse($intimationDate)->format('d-m-Y') : 'N/A' }}"
                            data-policy="{{ $record->GID_BASEDOCUMENTNO ?? 'N/A' }}"
                            data-loss-desc="{{ $record->POC_LOSSDESC ?? 'N/A' }}"
                            data-city="{{ $record->PCO_DESC ?? 'N/A' }}"
                            data-dept="{{ $department }}"
                            data-si="{{ $record->GDH_TOTALSI ?? 0 }}"
                            data-claimed="{{ $record->GIH_LOSSCLAIMED ?? 0 }}">
                        <i class="bi bi-envelope"></i> Send
                    </button>
                </td>

                <td class="text-center">
                    @if($emailCount > 0)
                        <button type="button"
                                class="btn btn-sm btn-outline-primary view-email-history-btn"
                                data-claim-no="{{ $record->GIH_DOC_REF_NO ?? 'N/A' }}"
                                data-history="{{ json_encode($record->email_history ?? []) }}">
                            <i class="bi bi-envelope-open"></i>
                            <strong>{{ $emailCount }}</strong>
                        </button>
                    @else
                        <span class="text-muted">0</span>
                    @endif
                </td>

                <td>{{ $department }}</td>
                <td>{{ $record->PCO_DESC ?? 'N/A' }}</td>
                <td>{{ $record->GIH_DOC_REF_NO ?? 'N/A' }}</td>
                <td>{{ $intimationDate ? \Carbon\Carbon::parse($intimationDate)->format('d-m-Y') : 'N/A' }}</td>
                <td>{{ $record->GID_BASEDOCUMENTNO ?? 'N/A' }}</td>

                <td>
                    <span class="dt-ellipsis" title="{{ $record->PPS_DESC ?? 'N/A' }}">
                        {{ $record->PPS_DESC ?? 'N/A' }}
                    </span>
                </td>

                <td>
                    <span class="dt-ellipsis" title="{{ $record->GIH_CLAIMANT_INFO ?? 'N/A' }}">
                        {{ $record->GIH_CLAIMANT_INFO ?? 'N/A' }}
                    </span>
                </td>

                <td>{{ $record->POC_LOSSDESC ?? 'N/A' }}</td>

                <td style="text-align:right">
                    {{ $record->GIH_LOSSCLAIMED
                        ? number_format($record->GIH_LOSSCLAIMED, 2)
                        : 'N/A' }}
                </td>

                <td style="text-align:right">
                    {{ $record->GDH_TOTALSI
                        ? number_format($record->GDH_TOTALSI, 2)
                        : 'N/A' }}
                </td>

                <td>{{ $record->PPS_MOBILE_NO ?? 'N/A' }}</td>

                <td>
                    <span class="dt-ellipsis" title="{{ $record->branch_description ?? 'N/A' }}">
                        {{ $record->branch_description ?? 'N/A' }}
                    </span>
                </td>

                <td>{{ $insuranceType }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="10" class="text-end"><strong>Total:</strong></td>
            <td id="totalClaimAmt">0</td>
            <td id="totalSI">0</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>

        </div>
    @endif
</div>

<!-- Email Sending Modal -->
<div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendEmailModalLabel">Send Email here</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="emailSurveyorForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">To:</label>
                            <input type="email" class="form-control" id="emailTo" required placeholder="Branch@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CC (optional):</label>
                            <input type="text" class="form-control" id="emailCc" placeholder="manager@example.com, another@domain.com">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Subject:</label>
                        <input type="text" class="form-control" id="emailSubject" required>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Message:</label>
                        <textarea class="form-control" id="emailBody" rows="10" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSendSurveyorEmail">
                    <i class="bi bi-send-fill"></i> Send Email
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Email History Modal -->
<div class="modal fade" id="emailHistoryModal" tabindex="-1" aria-labelledby="emailHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="emailHistoryModalLabel">
                    <i class="bi bi-clock-history"></i> Email History
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="emailHistoryContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.3.2/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Select2
    $('#location_category').select2({
        placeholder: "Select a branch",
        allowClear: true,
        width: '100%'
    });

    $('#insu').select2({
        placeholder: "Choose type",
        allowClear: true,
        width: '100%'
    });

    // DataTable
    var table = $('#reportsTable').DataTable({
        paging: false,
        searching: true,
        ordering: true,
        info: true,
        scrollX: true,
        scrollY: "500px",
        scrollCollapse: false,
        fixedHeader: { header: true, footer: true },
        autoWidth: true,
        dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Outstanding Surveyor Appointments',
                footer: true
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Outstanding Surveyor Appointments',
                orientation: 'landscape',
                pageSize: 'A4',
                footer: true
            }
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            var intVal = function (i) {
                return typeof i === 'string' ? parseFloat(i.replace(/[^\d.-]/g, '')) || 0 : (typeof i === 'number' ? i : 0);
            };

            var totalClaimAmt = api.column(10, {page: 'current'}).data().reduce((a, b) => intVal(a) + intVal(b), 0);
            var totalSI = api.column(11, {page: 'current'}).data().reduce((a, b) => intVal(a) + intVal(b), 0);

            $('#totalClaimAmt').html('<strong>' + totalClaimAmt.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong>');
            $('#totalSI').html('<strong>' + totalSI.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong>');
        },
       
        initComplete: function() {
            this.api().columns.adjust();
            $('.dataTables_filter input').attr('placeholder', 'Search...');
            $('.dataTables_filter').css('margin-left', '5px').css('margin-right', '5px').attr('placeholder', 'Search...');
            $('.dt-buttons').css('margin-left', '5px');
        },
        drawCallback: function() {
            this.api().columns.adjust();
        }
    });

    // Email functionality
    let currentClaimData = null;

    // Send Email Button Click
    $(document).on('click', '.send-surveyor-email-btn', function() {
        currentClaimData = {
            claimNo: $(this).data('claim-no'),
            insured: $(this).data('insured'),
            mobile: $(this).data('mobile'),
            intimation: $(this).data('intimation'),
            policy: $(this).data('policy'),
            lossDesc: $(this).data('loss-desc'),
            city: $(this).data('city'),
            dept: $(this).data('dept'),
            si: $(this).data('si'),
            claimed: $(this).data('claimed')
        };

        const claimNo = currentClaimData.claimNo;
        const insured = currentClaimData.insured;
        const policy = currentClaimData.policy;
        const intimation = currentClaimData.intimation;

        $('#emailSubject').val(`Claim No: ${claimNo}`);

        const defaultBody =
`Dear Sir/Madam,
Kindly assign a surveyor to assess the extent of the damage for the following claim:

Claim Number        : ${claimNo}
Policy Number       : ${policy}
Type of Loss        : ${currentClaimData.lossDesc || 'N/A'}
Contact Number      : ${currentClaimData.mobile || 'N/A'}

The initial loss report and photographs are attached for your reference.
Please confirm once a surveyor has been assigned and provide their contact details.

Thank you,
Claims Department
Atlas Insurance Limited`;

        $('#emailBody').val(defaultBody);
        $('#emailTo').val('');
        $('#emailCc').val('');

        $('#sendEmailModal').modal('show');
    });

    // Send Email Button
    $('#btnSendSurveyorEmail').on('click', function() {
        const to = $('#emailTo').val().trim();
        const cc = $('#emailCc').val().trim();
        const subject = $('#emailSubject').val().trim();
        const body = $('#emailBody').val().trim();

        if (!to) {
            alert("Please enter recipient email address!");
            return;
        }
        if (!subject || !body) {
            alert("Subject and message body are required!");
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Sending...');

        $.ajax({
            url: "{{ route('send.surveyor.email') }}",
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                to: to,
                cc: cc,
                subject: subject,
                body: body,
                claim_data: currentClaimData
            },
            success: function(response) {
                if (response.success) {
                    alert('Email sent successfully!');
                    $('#sendEmailModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + (response.message || 'Failed to send email'));
                }
            },
            error: function(xhr) {
                alert('Failed to send email. Server error occurred.');
                console.error(xhr.responseText);
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="bi bi-send-fill"></i> Send Email');
            }
        });
    });

    // Email History View Button Click
    $(document).on('click', '.view-email-history-btn', function() {
        const claimNo = $(this).data('claim-no');
        const history = $(this).data('history');
        
        $('#emailHistoryModalLabel').html(`<i class="bi bi-clock-history"></i> Email History - Claim: ${claimNo}`);
        
        let historyHtml = '';
        
        if (history && history.length > 0) {
            history.forEach((email, index) => {
                const datetime = email.datetime || 'N/A';
                const createdBy = email.created_by || 'N/A';
                const sentTo = email.sent_to || 'N/A';
                const sentCc = email.sent_cc || '';
                const subject = email.subject || 'N/A';
                const body = email.body || 'N/A';
                
                historyHtml += `
                    <div class="card mb-3 ${index === 0 ? 'border-primary' : ''}">
                        <div class="card-header ${index === 0 ? 'bg-primary text-white' : 'bg-light'}">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>${index === 0 ? '📧 Latest Email' : `Email #${history.length - index}`}</strong>
                                <span class="badge ${index === 0 ? 'bg-warning text-dark' : 'bg-secondary'}">
                                    ${datetime}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <strong>From:</strong> ${createdBy}
                                </div>
                                <div class="col-md-6">
                                    <strong>To:</strong> ${sentTo}
                                </div>
                            </div>
                            ${sentCc ? `
                            <div class="row mb-2">
                                <div class="col-12">
                                    <strong>CC:</strong> ${sentCc}
                                </div>
                            </div>
                            ` : ''}
                            <div class="row mb-2">
                                <div class="col-12">
                                    <strong>Subject:</strong> ${subject}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <strong>Message:</strong>
                                    <div class="mt-2 p-3 bg-light rounded" style="white-space: pre-wrap;">${body}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            historyHtml = '<div class="alert alert-info">No email history found for this claim.</div>';
        }
        
        $('#emailHistoryContent').html(historyHtml);
        $('#emailHistoryModal').modal('show');
    });
});
</script>
</body>
</html>