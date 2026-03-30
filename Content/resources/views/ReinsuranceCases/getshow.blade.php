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
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    .highlight-red {
        background-color: #ffcccc !important;
    }
</style>
</head>
<body>
@php
    // Define the helper function once, outside the loop
    function formatNumber($value) {
        if (is_numeric($value)) {
            return number_format((float)$value, 2);
        }
        return 'N/A';
    }
@endphp

<div class="container mt-4">
    <h2 class="text-center mb-4">Reinsurance</h2>
    <div class="row mb-4">
    <div class="col-6">
        <div class="card time-filter-card">
            <div class="card-body">
                <h5 class="card-title mb-2" style="font-size: 1.2rem;">Time Filter</h5>
                <div class="d-flex flex-wrap gap-1 justify-content-between">
                    <a href="{{ url('/getshow?time_filter=all') }}" 
                       class="time-tab {{ $selected_time_filter == 'all' ? 'active' : 'bg-light' }}" style="padding: 0.5rem 0.75rem;">
                        <div class="fw-bold" style="font-size: 0.9rem;">All</div>
                        <div class="small" style="font-size: 0.8rem;">{{ $counts['all'] }}</div>
                    </a>
                    <a href="{{ url('/getshow?time_filter=2days') }}" 
                       class="time-tab {{ $selected_time_filter == '2days' ? 'active' : 'bg-light' }}" style="padding: 0.5rem 0.75rem;">
                        <div class="fw-bold" style="font-size: 0.9rem;">2d</div>
                        <div class="small" style="font-size: 0.8rem;">{{ $counts['2days'] }}</div>
                    </a>
                    <a href="{{ url('/getshow?time_filter=5days') }}" 
                       class="time-tab {{ $selected_time_filter == '5days' ? 'active' : 'bg-light' }}" style="padding: 0.5rem 0.75rem;">
                        <div class="fw-bold" style="font-size: 0.9rem;">5d</div>
                        <div class="small" style="font-size: 0.8rem;">{{ $counts['5days'] }}</div>
                    </a>
                    <a href="{{ url('/getshow?time_filter=7days') }}" 
                       class="time-tab {{ $selected_time_filter == '7days' ? 'active' : 'bg-light' }}" style="padding: 0.5rem 0.75rem;">
                        <div class="fw-bold" style="font-size: 0.9rem;">7d</div>
                        <div class="small" style="font-size: 0.8rem;">{{ $counts['7days'] }}</div>
                    </a>
                    <a href="{{ url('/getshow?time_filter=10days') }}" 
                       class="time-tab {{ $selected_time_filter == '10days' ? 'active' : 'bg-light' }}" style="padding: 0.5rem 0.75rem;">
                        <div class="fw-bold" style="font-size: 0.9rem;">10d</div>
                        <div class="small" style="font-size: 0.8rem;">{{ $counts['10days'] }}</div>
                    </a>
                    <a href="{{ url('/getshow?time_filter=15days') }}" 
                       class="time-tab {{ $selected_time_filter == '15days' ? 'active' : 'bg-light' }}" style="padding: 0.5rem 0.75rem;">
                        <div class="fw-bold" style="font-size: 0.9rem;">15d</div>
                        <div class="small" style="font-size: 0.8rem;">{{ $counts['15days'] }}</div>
                    </a>
                    <a href="{{ url('/getshow?time_filter=15plus') }}" 
                       class="time-tab {{ $selected_time_filter == '15plus' ? 'active' : 'bg-light' }}" style="padding: 0.5rem 0.75rem;">
                        <div class="fw-bold" style="font-size: 0.9rem;">15+</div>
                        <div class="small" style="font-size: 0.8rem;">{{ $counts['15plus'] }}</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Data Table -->
    <div class="row mt-4">
        <div class="col-12">
            @if($records->isEmpty())
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle me-2"></i> 
                    No data available for the selected filters.
                </div>
            @else
                <div class="table-responsive">
                    <table id="reportsTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Email Log</th>
                                <th>Req Note</th>
                                <th>Rep Name</th>
                                <th>Created By</th>
                                <th>Doc Date</th>
                                <th>Dept</th>
                                <th>Business Desc</th>
                                <th>Insured</th>
                                <th>Reins Party</th>
                                <th>Total Sum Ins</th>
                                <th>RI Sum Ins</th>
                                <th>Share</th>
                                <th>Total Premium</th>
                                <th>RI Premium</th>
                                <th>Comm Date</th>
                                <th>Expiry Date</th>
                                <th>CP</th>
                                <th>Conv Takaful</th>
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
                            @foreach($records as $record)
                                <tr data-record='@json(array_merge($record->toArray(), ["email_count" => $record->email_count]))'>
                                    <td data-field="action">
                                        <button class="btn btn-info btn-sm send-email-btn"
                                                data-req-note="{{ $record->reqnote }}"
                                                data-repname="{{ $record->repname ?? 'N/A' }}"
                                                data-created-by="{{ $record->created_by ?? 'N/A' }}"
                                                data-doc-date="{{ $record->doc_date ?? 'N/A' }}"
                                                data-dept="{{ $record->dept ?? 'N/A' }}"
                                                data-business-desc="{{ $record->business_desc ?? 'N/A' }}"
                                                data-insured="{{ $record->insured ?? 'N/A' }}"
                                                data-reins-party="{{ $record->reins_party ?? 'N/A' }}"
                                                data-total-sum-ins="{{ $record->total_sum_ins ?? 'N/A' }}"
                                                data-ri-sum-ins="{{ $record->ri_sum_ins ?? 'N/A' }}"
                                                data-share="{{ $record->share ?? 'N/A' }}"
                                                data-total-premium="{{ $record->total_premium ?? 'N/A' }}"
                                                data-ri-premium="{{ $record->ri_premium ?? 'N/A' }}"
                                                data-comm-date="{{ $record->comm_date ?? 'N/A' }}"
                                                data-expiry-date="{{ $record->expiry_date ?? 'N/A' }}"
                                                data-cp="{{ $record->cp ?? 'N/A' }}"
                                                data-conv-takaful="{{ $record->conv_takaful ? '1' : '0' }}"
                                                data-posted="{{ $record->posted ? '1' : '0' }}"
                                                data-user-name="{{ $record->user_name ?? 'N/A' }}"
                                                data-acceptance-date="{{ $record->acceptance_date ?? 'N/A' }}"
                                                data-warranty-period="{{ $record->warranty_period ?? 'N/A' }}"
                                                data-commission-percent="{{ $record->commission_percent ?? 'N/A' }}"
                                                data-commission-amount="{{ $record->commission_amount ?? 'N/A' }}"
                                                data-acceptance-no="{{ $record->acceptance_no ?? 'N/A' }}"
                                                data-datetime="{{ $record->datetime ?? 'N/A' }}">
                                            Resend Email
                                        </button>
                                        <button class="btn btn-primary btn-sm preview-pdf-btn me-1"
                                                data-req-note="{{ $record->reqnote }}"
                                                data-repname="{{ $record->repname ?? 'N/A' }}"
                                                data-created-by="{{ $record->created_by ?? 'N/A' }}"
                                                data-doc-date="{{ $record->doc_date ?? 'N/A' }}"
                                                data-dept="{{ $record->dept ?? 'N/A' }}"
                                                data-business-desc="{{ $record->business_desc ?? 'N/A' }}"
                                                data-insured="{{ $record->insured ?? 'N/A' }}"
                                                data-reins-party="{{ $record->reins_party ?? 'N/A' }}"
                                                data-total-sum-ins="{{ $record->total_sum_ins ?? 'N/A' }}"
                                                data-ri-sum-ins="{{ $record->ri_sum_ins ?? 'N/A' }}"
                                                data-share="{{ $record->share ?? 'N/A' }}"
                                                data-total-premium="{{ $record->total_premium ?? 'N/A' }}"
                                                data-ri-premium="{{ $record->ri_premium ?? 'N/A' }}"
                                                data-comm-date="{{ $record->comm_date ?? 'N/A' }}"
                                                data-expiry-date="{{ $record->expiry_date ?? 'N/A' }}"
                                                data-cp="{{ $record->cp ?? 'N/A' }}"
                                                data-conv-takaful="{{ $record->conv_takaful ? '1' : '0' }}"
                                                data-posted="{{ $record->posted ? '1' : '0' }}"
                                                data-user-name="{{ $record->user_name ?? 'N/A' }}"
                                                data-acceptance-date="{{ $record->acceptance_date ?? 'N/A' }}"
                                                data-warranty-period="{{ $record->warranty_period ?? 'N/A' }}"
                                                data-commission-percent="{{ $record->commission_percent ?? 'N/A' }}"
                                                data-commission-amount="{{ $record->commission_amount ?? 'N/A' }}"
                                                data-acceptance-no="{{ $record->acceptance_no ?? 'N/A' }}"
                                                data-datetime="{{ $record->datetime ?? 'N/A' }}">
                                            <i class="fas fa-eye"></i> Preview
                                        </button>
                                        <button class="btn btn-danger btn-sm download-pdf-btn"
                                                data-req-note="{{ $record->reqnote }}"
                                                data-repname="{{ $record->repname ?? 'N/A' }}"
                                                data-created-by="{{ $record->created_by ?? 'N/A' }}"
                                                data-doc-date="{{ $record->doc_date ?? 'N/A' }}"
                                                data-dept="{{ $record->dept ?? 'N/A' }}"
                                                data-business-desc="{{ $record->business_desc ?? 'N/A' }}"
                                                data-insured="{{ $record->insured ?? 'N/A' }}"
                                                data-reins-party="{{ $record->reins_party ?? 'N/A' }}"
                                                data-total-sum-ins="{{ $record->total_sum_ins ?? 'N/A' }}"
                                                data-ri-sum-ins="{{ $record->ri_sum_ins ?? 'N/A' }}"
                                                data-share="{{ $record->share ?? 'N/A' }}"
                                                data-total-premium="{{ $record->total_premium ?? 'N/A' }}"
                                                data-ri-premium="{{ $record->ri_premium ?? 'N/A' }}"
                                                data-comm-date="{{ $record->comm_date ?? 'N/A' }}"
                                                data-expiry-date="{{ $record->expiry_date ?? 'N/A' }}"
                                                data-cp="{{ $record->cp ?? 'N/A' }}"
                                                data-conv-takaful="{{ $record->conv_takaful ? '1' : '0' }}"
                                                data-posted="{{ $record->posted ? '1' : '0' }}"
                                                data-user-name="{{ $record->user_name ?? 'N/A' }}"
                                                data-acceptance-date="{{ $record->acceptance_date ?? 'N/A' }}"
                                                data-warranty-period="{{ $record->warranty_period ?? 'N/A' }}"
                                                data-commission-percent="{{ $record->commission_percent ?? 'N/A' }}"
                                                data-commission-amount="{{ $record->commission_amount ?? 'N/A' }}"
                                                data-acceptance-no="{{ $record->acceptance_no ?? 'N/A' }}"
                                                data-datetime="{{ $record->datetime ?? 'N/A' }}">
                                            <i class="fas fa-file-pdf"></i> Download
                                        </button>
                                    </td>
                                    <td data-field="email_log">
                                        <a href="#" class="email-log-link" data-req-note="{{ $record->reqnote }}" data-datetime="{{ $record->datetime ?? 'N/A' }}">
                                            {{ $record->email_count }}
                                        </a>
                                    </td>
                                    <td data-field="request_note">{{ $record->reqnote ?? 'N/A' }}</td>
                                    <td>{{ $record->repname ?? 'N/A' }}</td>
                                    <td>{{ $record->created_by ?? 'N/A' }}</td>
                                    <td>{{ $record->doc_date ?? 'N/A' }}</td>
                                    <td>{{ $record->dept ?? 'N/A' }}</td>
                                    <td>{{ $record->business_desc ?? 'N/A' }}</td>
                                    <td>{{ $record->insured ?? 'N/A' }}</td>
                                    <td>{{ $record->reins_party ?? 'N/A' }}</td>
                                    <td>{{ formatNumber($record->total_sum_ins) }}</td>
                                    <td>{{ formatNumber($record->ri_sum_ins) }}</td>
                                    <td>{{ formatNumber($record->share) }}</td>
                                    <td>{{ formatNumber($record->total_premium) }}</td>
                                    <td>{{ formatNumber($record->ri_premium) }}</td>
                                    <td>{{ $record->comm_date ?? 'N/A' }}</td>
                                    <td>{{ $record->expiry_date ?? 'N/A' }}</td>
                                    <td>{{ $record->cp ?? 'N/A' }}</td>
                                    <td>{{ $record->conv_takaful ? 'Yes' : 'No' }}</td>
                                    <td>{{ $record->posted ? 'Yes' : 'No' }}</td>
                                    <td>{{ $record->user_name ?? 'N/A' }}</td>
                                    <td>{{ $record->acceptance_date ?? 'N/A' }}</td>
                                    <td>{{ $record->warranty_period ?? 'N/A' }}</td>
                                    <td>{{ formatNumber($record->commission_percent) }}</td>
                                    <td>{{ formatNumber($record->commission_amount) }}</td>
                                    <td>{{ $record->acceptance_no ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Email Modal -->
<div id="emailModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel">Resend Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="emailForm">
                    <div class="mb-3">
                        <label for="to" class="form-label">To:</label>
                        <input type="email" class="form-control" id="to" placeholder="Recipient's email" required>
                    </div>
                    <div class="mb-3">
                        <label for="cc" class="form-label">CC:</label>
                        <input type="email" class="form-control" id="cc" placeholder="CC email">
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject:</label>
                        <input type="text" class="form-control" id="subject" placeholder="Email subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="body" class="form-label">Body:</label>
                        <textarea class="form-control" id="body" rows="4" placeholder="Email body" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="sendEmailBtn">Resend Email</button>
            </div>
        </div>
    </div>
</div>

<!-- Email Log Modal -->
<div id="emailLogModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="emailLogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailLogModalLabel">Email Log for Request Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Total Emails Sent: <span id="emailLogCount">0</span></h6>
                <div class="table-responsive">
                    <table id="emailLogTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sent At</th>
                                <th>To</th>
                                <th>CC</th>
                                <th>Subject</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- PDF Preview Modal -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">PDF Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdfPreviewFrame" style="width: 100%; height: 600px; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadFromPreview">Download</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.3.2/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

// Global variables
let currentRow = null;
let currentDocDefinition = null;

$(document).ready(function() {
    // Initialize DataTable
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
        autoWidth: true,
        dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel custom-icon"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Renewal Report',
                footer: true,
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf custom-icon"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Renewal Report',
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
        initComplete: function() {
            this.api().columns.adjust();
            $('.dataTables_filter').css('margin-left', '5px').css('margin-right', '5px');
            $('.dt-buttons').css('margin-left', '5px');
            console.log('DataTable initialized with database email counts');
        },
        drawCallback: function() {
            this.api().columns.adjust();
        }
    });

    // Email modal functionality
    $(document).on('click', '.send-email-btn', function() {
        console.log('Send Email button clicked');
        currentRow = $(this).closest('tr');
        var rowData = $(this).data();
        console.log('Row data:', rowData);

        // Populate email modal
        $('#to').val('');
        $('#cc').val('');
        $('#subject').val('Reinsurance Request Note: ' + rowData.reqNote);
        $('#body').val('Dear Sir/Madam,\n\n' +
                       'Please find below details for Request Note: ' + rowData.reqNote + '\n\n' +
                       'Insured: ' + (rowData.insured || 'N/A') + '\n' +
                       'Reinsurance Party: ' + (rowData.reinsParty || 'N/A') + '\n' +
                       'Business Description: ' + (rowData.businessDesc || 'N/A') + '\n\n' +
                       'Please find the attached PDF document for detailed information.\n\n' +
                       'Regards,\n\nAtlas Insurance Ltd.');

        $('#emailModal').modal('show');
    });

    // Send email function
    $('#sendEmailBtn').on('click', function() {
        console.log('Send Email button in modal clicked');
        const to = $('#to').val();
        const cc = $('#cc').val();
        const subject = $('#subject').val();
        const body = $('#body').val();
        const $btn = $(this);

        if (!to) {
            alert('Please enter a valid email address in the "To" field.');
            return;
        }

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');

        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const rowData = $(currentRow).find('.send-email-btn').data();

        // Prepare full row data for backend
        const fullRowData = {
            reqNote: rowData.reqNote,
            repname: rowData.repname || 'N/A',
            createdBy: rowData.createdBy || 'N/A',
            docDate: rowData.docDate || 'N/A',
            dept: rowData.dept || 'N/A',
            businessDesc: rowData.businessDesc || 'N/A',
            insured: rowData.insured || 'N/A',
            reinsParty: rowData.reinsParty || 'N/A',
            totalSumIns: rowData.totalSumIns || 'N/A',
            riSumIns: rowData.riSumIns || 'N/A',
            share: rowData.share || 'N/A',
            totalPremium: rowData.totalPremium || 'N/A',
            riPremium: rowData.riPremium || 'N/A',
            commDate: rowData.commDate || 'N/A',
            expiryDate: rowData.expiryDate || 'N/A',
            cp: rowData.cp || 'N/A',
            convTakaful: rowData.convTakaful || 'N/A',
            posted: rowData.posted || 'N/A',
            userName: rowData.userName || 'N/A',
            acceptanceDate: rowData.acceptanceDate || 'N/A',
            warrantyPeriod: rowData.warrantyPeriod || 'N/A',
            commissionPercent: rowData.commissionPercent || 'N/A',
            commissionAmount: rowData.commissionAmount || 'N/A',
            acceptanceNo: rowData.acceptanceNo || 'N/A',
            datetime: rowData.datetime || 'N/A'
        };

        console.log('Sending email with data:', fullRowData);

        const docDefinition = createPdfDefinition(fullRowData);
        const filename = `Request_Note_${fullRowData.reqNote || 'Document'}.pdf`;

        pdfMake.createPdf(docDefinition).getBase64((pdfBase64) => {
            $.ajax({
                url: "{{ route('send.email') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: {
                    _token: csrfToken,
                    to: to,
                    cc: cc,
                    subject: subject,
                    body: body,
                    pdf_data: pdfBase64,
                    pdf_filename: filename,
                    record: fullRowData
                },
                success: function(response) {
                    console.log('AJAX success response:', response);
                    if (response.success) {
                        alert('Email sent successfully with PDF attachment!');
                        $('#emailModal').modal('hide');

                        // Update the email count in the UI
                        const currentCountElement = $(currentRow).find('td[data-field="email_log"] a');
                        const currentCount = parseInt(currentCountElement.text()) || 0;
                        currentCountElement.text(currentCount + 1);
                        console.log(`Updated email count for ${fullRowData.reqNote} to ${currentCount + 1}`);
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr) {
                    console.error('AJAX error:', xhr);
                    let errorMsg = 'Failed to send email';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Resend Email');
                    currentRow = null;
                }
            });
        });
    });

    // Email log modal functionality
    $(document).on('click', '.email-log-link', function(e) {
        e.preventDefault();
        const reqNote = $(this).data('req-note');
        const datetime = $(this).data('datetime');
        console.log('Email log link clicked for reqNote:', reqNote, 'datetime:', datetime);

        $('#emailLogModalLabel').text(`Email Log for Request Note: ${reqNote}`);

        // Show loading state
        const tbody = $('#emailLogTable tbody');
        tbody.html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
        $('#emailLogCount').text('Loading...');
        $('#emailLogModal').modal('show');

        // Fetch email logs from database
        $.ajax({
            url: "{{ route('get.email.logs') }}",
            method: 'GET',
            data: {
                reqnote: reqNote,
                datetime: datetime
            },
            success: function(response) {
                console.log('Email logs response:', response);
                if (response.success) {
                    const emailLogs = response.logs;
                    $('#emailLogCount').text(emailLogs.length);

                    tbody.empty();
                    if (emailLogs.length === 0) {
                        tbody.append('<tr><td colspan="4" class="text-center">No email logs found.</td></tr>');
                    } else {
                        emailLogs.forEach(log => {
                            tbody.append(`
                                <tr>
                                    <td>${new Date(log.datetime).toLocaleString()}</td>
                                    <td>${log.sent_to || 'N/A'}</td>
                                    <td>${log.sent_cc || 'N/A'}</td>
                                    <td>${log.subject || 'N/A'}</td>
                                </tr>
                            `);
                        });
                    }
                } else {
                    tbody.html('<tr><td colspan="4" class="text-center text-danger">Error loading email logs</td></tr>');
                    $('#emailLogCount').text('Error');
                }
            },
            error: function(xhr) {
                console.error('Email logs AJAX error:', xhr);
                tbody.html('<tr><td colspan="4" class="text-center text-danger">Failed to load email logs</td></tr>');
                $('#emailLogCount').text('Error');
            }
        });
    });

    // PDF Generation - Preview
    $(document).on('click', '.preview-pdf-btn', function() {
        const rowData = $(this).data();
        console.log('Preview PDF data:', rowData);
        const docDefinition = createPdfDefinition(rowData);
        pdfMake.createPdf(docDefinition).open();
    });

    // PDF Generation - Download
    $(document).on('click', '.download-pdf-btn', function() {
        const rowData = $(this).data();
        console.log('Download PDF data:', rowData);
        const docDefinition = createPdfDefinition(rowData);
        pdfMake.createPdf(docDefinition).download(`Request_Note_${rowData.reqNote || 'Document'}.pdf`);
    });

    // Download from Preview Modal
    $(document).on('click', '#downloadFromPreview', function() {
        if (currentDocDefinition) {
            const filename = `Request_Note_${currentDocDefinition.content[1].text.split(':')[1].trim() || 'Document'}.pdf`;
            pdfMake.createPdf(currentDocDefinition).download(filename);
        }
    });

    // PDF Definition Function
    function createPdfDefinition(rowData) {
        return {
            content: [
                {
                    text: 'Atlas Insurance Ltd.',
                    style: 'header',
                    alignment: 'center',
                    margin: [0, 5, 0, 5]
                },
                {
                    text: `${rowData.dept || 'Fire'} Reinsurance Request Note`,
                    style: 'header',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                },
                {
                    table: {
                        widths: ['*', '*', '*'],
                        body: [
                            [
                                '',
                                '',
                                {
                                    table: {
                                        widths: ['auto', '*'],
                                        body: [
                                            ['Date:', rowData.docDate || '10/01/2025'],
                                            ['Request Note#:', rowData.reqNote || '2025REFCMIIR0007'],
                                            ['Base Request Note#:', '-']
                                        ]
                                    },
                                    layout: 'noBorders'
                                }
                            ]
                        ]
                    },
                    layout: 'noBorders',
                    margin: [0, 0, 0, 10]
                },
                {
                    table: {
                        widths: ['*'],
                        body: [
                            [
                                {
                                    stack: [
                                        {
                                            table: {
                                                widths: ['auto', '*'],
                                                body: [
                                                    ['CLASS OF BUSINESS', rowData.businessDesc || 'Comprehensive Machinery Insurance'],
                                                    ['DESCRIPTION', 'Co-Insurers Panel:\n' +
                                                        '   IGI............. 30%\n' +
                                                        '   Askari.......... 10%\n' +
                                                        '   Habib........... 10%\n' +
                                                        '   Atlas........... 20%\n' +
                                                        '   Shaheen ........ 10%\n' +
                                                        '   TPL............. 20%\n' +
                                                        '   -----------------------\n' +
                                                        '   Total........... 100%\n\n' +
                                                        'Deductible: 5% of loss amount minimum Rs. 250,000/- on each & every loss.\n' +
                                                        'Policy issued by cancelling & replacing Cover Note # 2024ISB-IIFCMIIT00177'
                                                    ],
                                                    ['INSURED NAME', rowData.insured || 'COASTAL JADE TECHNOLOGY (PRIVATE) LTD,'],
                                                    ['NTN Number', '4505300-8'],
                                                    ['C NOTE/POLICY#', '2025ISB-IIFCMIIP00002'],
                                                    ['SITUATION', 'Plot no.A-19 & A-20, M-3 Industrial City Road, M-3, Industrial City, Faisalabad.'],
                                                    ['CONSTRUCTION CLASS', '1st Class'],
                                                    ['RI Reference', 'null'],
                                                    ['PERIOD OF INSURANCE', 'From 01/11/2024 To 31/10/2025'],
                                                    ['PERIOD OF RE-INSURANCE', 'From 01/11/2024 To 31/10/2025'],
                                                    ['SUM INSURED (Our Share)', rowData.totalSumIns ? rowData.totalSumIns.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '1,628,957,386'],
                                                    ['ATLAS SHARE', rowData.share ? rowData.share : '20.00%'],
                                                    ['GROSS PREMIUM RATE', rowData.totalPremium && rowData.totalSumIns ? 
                                                        ((parseFloat(rowData.totalPremium.toString().replace(/,/g, '')) / parseFloat(rowData.totalSumIns.toString().replace(/,/g, '')) * 100).toFixed(4) + '%') : '0.0872%'],
                                                    ['RI COMMISSION', rowData.commissionPercent ? rowData.commissionPercent : '25.00%']
                                                ]
                                            },
                                            layout: 'noBorders',
                                            margin: [0, 0, 0, 15]
                                        }
                                    ]
                                }
                            ]
                        ]
                    },
                    layout: {
                        hLineWidth: () => 1,
                        vLineWidth: () => 1,
                        hLineColor: () => 'black',
                        vLineColor: () => 'black',
                        paddingLeft: () => 6,
                        paddingRight: () => 6,
                        paddingTop: () => 6,
                        paddingBottom: () => 6
                    }
                },
                {
                    text: [
                        'DETAILS:\n',
                        '1. Closing Particular submission (60) days.\n',
                        '2. Simultaneous Payment Clause\n',
                        '3. The sixty (60) days period for the Closing Particulars shall commence on the first day of the Risk Period.\n',
                        '4. 20% Automatic Escalation in Sum Insured\n',
                        '5. Automatic Period extension clause\n',
                        'All other details, terms, conditions, warranties, subjectivities and exclusions as per original policy.'
                    ],
                    margin: [0, 10, 0, 10]
                },
                {
                    table: {
                        widths: ['*', 50, 110, '*'],
                        body: [
                            [
                                {
                                    text: 'REINSURER',
                                    style: 'tableHeader',
                                    border: [true, true, true, true]
                                },
                                {
                                    text: 'FAC RI OFFERED',
                                    style: 'tableHeader',
                                    colSpan: 2,
                                    alignment: 'center',
                                    border: [true, true, true, true]
                                },
                                {},
                                {
                                    text: 'SIGNATURE OF ACCEPTANCE',
                                    style: 'tableHeader',
                                    border: [true, true, true, true]
                                }
                            ],
                            [
                                {
                                    text: '',
                                    border: [true, true, true, false]
                                },
                                {
                                    text: '%',
                                    style: 'tableHeader',
                                    border: [true, true, true, true]
                                },
                                {
                                    text: 'AMOUNT',
                                    alignment: 'right',
                                    style: 'tableHeader',
                                    border: [true, true, true, true]
                                },
                                {
                                    text: '',
                                    border: [true, true, true, false]
                                }
                            ],
                            [
                                {
                                    text: rowData.reinsParty || 'Premier Insurance Co. Ltd.',
                                    alignment: 'center',
                                    margin: [0, 0],
                                    border: [true, false, true, true],
                                    padding: [0, 0]
                                },
                                {
                                    text: rowData.share ? (rowData.share.toString().includes('%') ? rowData.share : rowData.share + '%') : '14.70%',
                                    alignment: 'center',
                                    border: [true, false, true, true]
                                },
                                {
                                    text: rowData.riSumIns || '239,478,694',
                                    alignment: 'right',
                                    border: [true, false, true, true]
                                },
                                {
                                    text: '',
                                    border: [true, false, true, true]
                                }
                            ]
                        ]
                    },
                    layout: {
                        hLineWidth: (i, node) => {
                            if (i === 0) return 1;
                            if (i === 1) return 1;
                            if (i === node.table.body.length) return 1;
                            if (i === 2) return 1;
                            return 0;
                        },
                        vLineWidth: () => 1,
                        hLineColor: () => 'black',
                        vLineColor: () => 'black',
                        paddingLeft: () => 5,
                        paddingRight: () => 5,
                        paddingTop: () => 5,
                        paddingBottom: () => 5
                    },
                    margin: [0, 0, 0, 10]
                },
                {
                    text: 'For And On Behalf Of\nATLAS INSURANCE LTD.',
                    alignment: 'right',
                    margin: [0, 0, 0, 5]
                },
                {
                    text: 'Page 1 of 1',
                    alignment: 'center',
                    margin: [0, 5, 0, 0]
                }
            ],
            styles: {
                header: {
                    fontSize: 12,
                    bold: true
                },
                tableHeader: {
                    bold: true,
                    fontSize: 10,
                    fillColor: '#f0f0f0',
                    alignment: 'center'
                }
            },
            defaultStyle: {
                fontSize: 10
            }
        };
    }
});
</script>
</body>
</html>