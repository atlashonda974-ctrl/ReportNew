<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.master_titles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <x-datatable-styles />
</head>
<body>
    <div class="container mt-5">
        <x-report-header title="Get Request Note Report 2" />

        <form method="GET" action="{{ url('/getlast') }}" class="mb-4">
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
                        <option value="">All Departments</option>
                        <option value="Fire" {{ request('new_category') == 'Fire' ? 'selected' : '' }}>Fire</option>
                        <option value="Marine" {{ request('new_category') == 'Marine' ? 'selected' : '' }}>Marine</option>
                        <option value="Motor" {{ request('new_category') == 'Motor' ? 'selected' : '' }}>Motor</option>
                        <option value="Miscellaneous" {{ request('new_category') == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                        <option value="Health" {{ request('new_category') == 'Health' ? 'selected' : '' }}>Health</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-primary me-2" title="Filter">
                        <i class="bi bi-funnel-fill"></i> Filter
                    </button>
                    <a href="{{ url('/getlast') }}" class="btn btn-outline-secondary me-2" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        @if(empty($data))
            <div class="alert alert-danger">No data available.</div>
        @else
            <table id="reportsTable" class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>Reference No</th>
                        <th>Department Code</th>
                        <th>Serial No</th>
                        <th>Issue Date</th>
                        <th>Commencement Date</th>
                        <th>Expiry Date</th>
                        <th>Reinsurer</th>
                        <th>Reissue Date</th>
                        <th>Recommended Date</th>
                        <th>Reexpiry Date</th>
                        <th>Total SI</th>
                        <th>Total Premium</th>
                        <th>Reinsurance SI</th>
                        <th>Reinsurance Premium</th>
                        <th>Commission Amount</th>
                        <th>Posting Tag</th>
                        <th>Cancellation Tag</th>
                        <th>Posted By</th>
                        <th>Their Reference No</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                        <tr data-record='@json($record)'>
                            <td>
                                <button class="btn btn-sm btn-primary send-email-btn" title="Email">
                                    <i class="fas fa-envelope"></i>
                                </button>
                                <button class="btn btn-sm btn-info upload-btn" title="Upload File">
                                    <i class="fas fa-upload"></i>
                                </button>
                                <button class="btn btn-sm btn-success verify-btn" title="Verify">
                                    <i class="fas fa-check"></i>
                                </button>
                                <input type="file" class="file-input d-none" accept=".pdf" />
                                <span class="file-name text-muted small d-block"></span>
                            </td>
                            <td>{{ $record->GCP_DOC_REFERENCENO ?? 'N/A' }}</td>
@php
    $categoryMapping = [
        11 => 'Fire',
        12 => 'Marine',
        13 => 'Motor',
        14 => 'Miscellaneous',
        16 => 'Health',
    ];
    
    // Get the department code whether it's an array or object
    $deptCode = is_array($record) ? ($record['PDP_DEPT_CODE'] ?? null) : ($record->PDP_DEPT_CODE ?? null);
@endphp

<td>{{ $categoryMapping[$deptCode] ?? 'N/A' }}</td>

                            <td>{{ $record->GCP_SERIALNO ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_ISSUEDATE ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_COMMDATE ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_EXPIRYDATE ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_REINSURER ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_REISSUEDATE ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_RECOMMDATE ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_REEXPIRYDATE ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_COTOTALSI ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_COTOTALPREM ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_REINSI ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_REINPREM ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_COMMAMOUNT ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_POSTINGTAG ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_CANCELLATIONTAG ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_POST_USER ?? 'N/A' }}</td>
                            <td>{{ $record->GCT_THEIR_REF_NO ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Email Modal -->
        <div id="emailModal" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Send Email</h5>
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
                        <button type="button" class="btn btn-primary" id="sendEmailBtn">Send Email</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
    $('.select2').select2({
        placeholder: "Select a branch",
        allowClear: true,
        width: '69%'
    });

    var table = $('#reportsTable').DataTable({
        "paging": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "scrollX": true,
        "scrollY": "500px",
        "scrollCollapse": false,
        "fixedHeader": {
            header: true,
            footer: true
        },
        "autoWidth": true,
        dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Outstanding Report',
                footer: true,
                exportOptions: {
                    columns: ':visible:not(:first-child)', // Exclude the first column (Actions)
                    format: {
                        body: function(data, row, column, node) {
                            return data;
                        }
                    },
                    modifier: {
                        page: 'current'
                    }
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Outstanding Report',
                orientation: 'landscape',
                pageSize: 'A4',
                footer: true,
                exportOptions: {
                    columns: ':visible:not(:first-child)', // Exclude the first column (Actions)
                    format: {
                        body: function(data, row, column, node) {
                            return data;
                        }
                    },
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
        },
    });

    // Global variable to store current row data
    let currentRowData = null;
    let currentRow = null;

    // Email button click handler
    $(document).on('click', '.send-email-btn', function() {
        currentRow = $(this).closest('tr');
        currentRowData = currentRow.data('record');

        // Populate email modal
        $('#to').val('');
        $('#cc').val('');
        $('#subject').val('Reinsurance Request Note: ' + (currentRowData.GCP_DOC_REFERENCENO || 'N/A'));
        $('#body').val('Dear Sir/Madam,\n\n' +
            'Please find below details for Request Note: ' + (currentRowData.GCP_DOC_REFERENCENO || 'N/A') + '\n\n' +
            'Reinsurer: ' + (currentRowData.GCP_REINSURER || 'N/A') + '\n' +
            'Department: ' + (currentRowData.PDP_DEPT_CODE || 'N/A') + '\n' +
            'Total Sum Insured: ' + (currentRowData.GCP_COTOTALSI || 'N/A') + '\n\n' +
            'Please find the attached PDF document for detailed information.\n\n' +
            'Regards,\n\n');

        $('#emailModal').modal('show');
    });

    // Send email function with PDF attachment and VerifyLog storage
    $('#sendEmailBtn').on('click', function() {
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

        // Map data to match VerifyLog fields
        const emailData = {
            _token: csrfToken,
            to: to,
            cc: cc,
            subject: subject,
            body: body,
            reportName: 'Last Reinsurance Request Note',
            record: {
                referenceNo: currentRowData.GCP_DOC_REFERENCENO ?? null,
                departmentCode: currentRowData.PDP_DEPT_CODE ?? null,
                serialNo: currentRowData.GCP_SERIALNO ?? null,
                issueDate: currentRowData.GCP_ISSUEDATE ?? null,
                commencementDate: currentRowData.GCP_COMMDATE ?? null,
                expiryDate: currentRowData.GCP_EXPIRYDATE ?? null,
                reinsurer: currentRowData.GCP_REINSURER ?? null,
                reissueDate: currentRowData.GCP_REISSUEDATE ?? null,
                recommendedDate: currentRowData.GCP_RECOMMDATE ?? null,
                reexpiryDate: currentRowData.GCP_REEXPIRYDATE ?? null,
                totalSi: currentRowData.GCP_COTOTALSI ?? null,
                totalPremium: currentRowData.GCP_COTOTALPREM ?? null,
                reinsuranceSi: currentRowData.GCP_REINSI ?? null,
                reinsurancePremium: currentRowData.GCP_REINPREM ?? null,
                commissionAmount: currentRowData.GCP_COMMAMOUNT ?? null,
                postingTag: currentRowData.GCP_POSTINGTAG ?? null,
                cancellationTag: currentRowData.GCP_CANCELLATIONTAG ?? null,
                postedBy: currentRowData.GCP_POST_USER ?? null,
                theirReferenceNo: currentRowData.GCT_THEIR_REF_NO ?? null
            }
        };

        // Generate PDF
        try {
            const docDefinition = createPdfDefinition(currentRowData);
            const filename = `Request_Note_${currentRowData.GCP_DOC_REFERENCENO || 'Document'}.pdf`;
            
            pdfMake.createPdf(docDefinition).getBase64((pdfBase64) => {
                emailData.pdf_data = pdfBase64;
                emailData.pdf_filename = filename;
                
                sendEmailRequest(emailData, $btn, currentRow);
            });
        } catch (error) {
            console.error('Error generating PDF:', error);
            sendEmailRequest(emailData, $btn, currentRow);
        }
    });

    function sendEmailRequest(emailData, $btn, $row) {
        $.ajax({
            url: "{{ route('send.email') }}",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': emailData._token
            },
            data: emailData,
            success: function(response) {
                if (response.success) {
                    alert('Email sent successfully with PDF attachment!');
                    $('#emailModal').modal('hide');
                    $row.remove();
                    $('#reportsTable').DataTable().draw();
                } else {
                    alert('Error: ' + (response.message || 'Failed to send email'));
                }
            },
            error: function(xhr) {
                let errorMsg = 'Failed to send email';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            },
            complete: function() {
                $btn.prop('disabled', false).text('Send Email');
            }
        });
    }

    // PDF definition for email attachment
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
                    text: `${rowData.PDP_DEPT_CODE || 'Fire'} Reinsurance Request Note`,
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
                                            ['Date:', rowData.GCP_ISSUEDATE || 'N/A'],
                                            ['Request Note#:', rowData.GCP_DOC_REFERENCENO || 'N/A'],
                                            ['Base Request Note#:', rowData.GCP_SERIALNO || 'N/A']
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
                                                    ['REINSURER', rowData.GCP_REINSURER || 'N/A'],
                                                    ['SUM INSURED', rowData.GCP_COTOTALSI || 'N/A'],
                                                    ['REINSURANCE SUM INSURED', rowData.GCP_REINSI || 'N/A'],
                                                    ['PREMIUM', rowData.GCP_COTOTALPREM || 'N/A'],
                                                    ['REINSURANCE PREMIUM', rowData.GCP_REINPREM || 'N/A'],
                                                    ['COMMISSION AMOUNT', rowData.GCP_COMMAMOUNT || 'N/A'],
                                                    ['COMMENCEMENT DATE', rowData.GCP_COMMDATE || 'N/A'],
                                                    ['EXPIRY DATE', rowData.GCP_EXPIRYDATE || 'N/A'],
                                                    ['REISSUE DATE', rowData.GCP_REISSUEDATE || 'N/A'],
                                                    ['RECOMMENDED DATE', rowData.GCP_RECOMMDATE || 'N/A'],
                                                    ['REEXPIRY DATE', rowData.GCP_REEXPIRYDATE || 'N/A'],
                                                    ['POSTING TAG', rowData.GCP_POSTINGTAG || 'N/A'],
                                                    ['CANCELLATION TAG', rowData.GCP_CANCELLATIONTAG || 'N/A'],
                                                    ['POSTED BY', rowData.GCP_POST_USER || 'N/A'],
                                                    ['THEIR REFERENCE NO', rowData.GCT_THEIR_REF_NO || 'N/A']
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

    // Upload button click handler
    $(document).on('click', '.upload-btn', function() {
        $(this).closest('td').find('.file-input').click();
    });

    // File input change handler
    $(document).on('change', '.file-input', function() {
        const file = this.files[0];
        const $row = $(this).closest('td');
        const $fileNameSpan = $row.find('.file-name');

        if (file) {
            const fileSize = file.size / 1024; // Size in KB
            if (fileSize > 2048) {
                alert('File size exceeds 2MB limit.');
                this.value = '';
                $fileNameSpan.text('');
                return;
            }
            if (!file.type.includes('pdf')) {
                alert('Only PDF files are allowed.');
                this.value = '';
                $fileNameSpan.text('');
                return;
            }
            // Display the selected file name
            $fileNameSpan.text(`Selected: ${file.name}`);
        } else {
            $fileNameSpan.text('');
        }
    });

    // Verify button click handler
    $(document).on('click', '.verify-btn', function() {
        const $row = $(this).closest('tr');
        const record = $row.data('record');
        const fileInput = $row.find('.file-input')[0];
        const file = fileInput.files[0];

        const formData = new FormData();
        formData.append('record', JSON.stringify({
            referenceNo: record.GCP_DOC_REFERENCENO ?? null,
            departmentCode: record.PDP_DEPT_CODE ?? null,
            serialNo: record.GCP_SERIALNO ?? null,
            issueDate: record.GCP_ISSUEDATE ?? null,
            commencementDate: record.GCP_COMMDATE ?? null,
            expiryDate: record.GCP_EXPIRYDATE ?? null,
            reinsurer: record.GCP_REINSURER ?? null,
            reissueDate: record.GCP_REISSUEDATE ?? null,
            recommendedDate: record.GCP_RECOMMDATE ?? null,
            reexpiryDate: record.GCP_REEXPIRYDATE ?? null,
            totalSi: record.GCP_COTOTALSI ?? null,
            totalPremium: record.GCP_COTOTALPREM ?? null,
            reinsuranceSi: record.GCP_REINSI ?? null,
            reinsurancePremium: record.GCP_REINPREM ?? null,
            commissionAmount: record.GCP_COMMAMOUNT ?? null,
            postingTag: record.GCP_POSTINGTAG ?? null,
            cancellationTag: record.GCP_CANCELLATIONTAG ?? null,
            postedBy: record.GCP_POST_USER ?? null,
            theirReferenceNo: record.GCT_THEIR_REF_NO ?? null
        }));
        if (file) {
            formData.append('file', file);
        }

        $.ajax({
            url: '{{ url("/verify-record") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    table.row($row).remove().draw();
                    alert(response.message);
                    fileInput.value = ''; // Clear file input
                    $row.find('.file-name').text(''); // Clear file name display
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to verify record'));
            }
        });
    });

    $('a[title="Reset"]').on('click', function() {
        setTimeout(function() {
            table.draw();
        }, 100);
    });
});
    </script>
</body>
</html>