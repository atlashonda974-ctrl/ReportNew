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
        #docTable tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <x-report-header title="Get OS Settlement" />

        <form method="GET" action="{{ url('/cr11') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3 d-flex align-items-center">
                    <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', $start_date) }}">
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', $end_date) }}">
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
                            <option value="{{ $branch->fbracode }}" {{ request('location_category') == $branch->fbracode ? 'selected' : '' }}>
                                {{ $branch->fbradsc }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <label for="business_type" class="form-label me-2" style="white-space: nowrap; width: 100px;">Business Type</label>
                    <select name="business_type" id="business_type" class="form-control">
                        <option value="all" {{ request('business_type', 'all') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="takaful" {{ request('business_type') == 'takaful' ? 'selected' : '' }}>Takaful</option>
                        <option value="conventional" {{ request('business_type') == 'conventional' ? 'selected' : '' }}>Conventional</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <label for="insu" class="form-label me-2" style="white-space: nowrap;">Insurance Type</label>
                    <select name="insu[]" id="insu" class="form-control select2-insurance" multiple>
                        <option value="D">Direct</option>
                        <option value="I">Inward</option>
                        <option value="O">Outward</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-primary me-2" title="Filter">
                        <i class="bi bi-funnel-fill"></i> Filter
                    </button>
                    <a href="{{ url('/cr11') }}" class="btn btn-outline-secondary me-2" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        @php
            $filters = [
                'all' => 'All', '2days' => '2 Days', '5days' => '5 Days',
                '7days' => '7 Days', '10days' => '10 Days', '15days' => '15 Days', '15plus' => '15+ Days'
            ];
        @endphp
        <div class="d-flex justify-content-start mb-3">
            <div class="card time-filter-card">
                <div class="card-body py-2 px-3">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($filters as $key => $label)
                            <a href="{{ request()->fullUrlWithQuery(['time_filter' => $key]) }}"
                               class="time-tab {{ $selected_time_filter == $key ? 'active' : 'bg-light' }}">
                                <div class="fw-bold">{{ $label }}</div>
                                <div class="small">{{ $counts[$key] }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if($data->isEmpty())
            <div class="alert alert-danger">No data available.</div>
        @else
            <table id="reportsTable" class="table table-bordered table-responsive">
                <thead>
                     <tr>
                        <th>Action</th>
                        <th>Remarks</th> 
                        <th>Claim No</th>
                        <th>SET DATE</th>

                        <th>LOSS AMT</th>
                        
                        <th>Insured</th>
                        <th>MOBILE NO</th>
                        <th>EMAIL</th>
                        
                        
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                         <tr>
                            <td>
                                <button class="btn btn-primary btn-sm view-docs" 
                                        data-doc="{{ $record->GSH_DOC_REF_NO ?? '' }}"
                                        data-bs-toggle="modal" data-bs-target="#docModal">
                                    <i class="bi bi-eye"></i> View
                                </button>
                                @if($record->can_approve)
                                    @if($record->button_type == 'ok')
                                        <button class="btn btn-success btn-sm insert-ok" 
                                                data-doc-ref="{{ $record->GSH_DOC_REF_NO ?? '' }}" 
                                                data-loss-claimed="{{ $record->GSH_LOSSADJUSTED ?? 0 }}">
                                            <i class="bi bi-check"></i> OK
                                        </button>
                                    @elseif($record->button_type == 'approve')
                                        <button class="btn btn-success btn-sm insert-record" 
                                                data-doc-ref="{{ $record->GSH_DOC_REF_NO ?? '' }}" 
                                                data-loss-claimed="{{ $record->GSH_LOSSADJUSTED ?? 0 }}">
                                            <i class="bi bi-plus"></i> Approve
                                        </button>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center">  
                                <button class="btn btn-outline-info btn-sm remarks-btn"
                                        data-doc="{{ $record->GSH_DOC_REF_NO ?? '' }}"
                                        data-bs-toggle="modal" data-bs-target="#remarksModal">
                                    Remarks
                                </button>
                            </td>
                            <td>{{ $record->GSH_DOC_REF_NO ?? 'N/A' }}</td>
                            <td>{{ $record->GSH_SETTLEMENTDATE ?? 'N/A' }}</td>
                            <td style="text-align:right">{{ number_format($record->GSH_LOSSADJUSTED) ?? 'N/A' }}</td>
                            <td>{{ $record->PPS_DESC ?? 'N/A' }}</td>
                            <td>{{ $record->PPS_MOBILE_NO ?? 'N/A' }}</td>
                            <td>{{ $record->PPS_EMAIL_ADDRESS ?? 'N/A' }}</td>
                            
                            
                            
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10" style="text-align: right;"><strong>Total:</strong></td>
                        <td style="text-align: right;"><strong></strong></td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>
    
    <div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="remarksModalLabel">Add Remarks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="remarksForm">
                        <input type="hidden" id="remarksDocRef" value=""> <div class="mb-3">
                            <label for="remarksText" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarksText" rows="3" placeholder="Enter your remarks here..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveRemarksBtn">Save Remarks</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="docModal" tabindex="-1" aria-labelledby="docModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="docModalLabel">Documents</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="docTable">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Document</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="2" class="text-center">Click "View" to load documents.</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#location_category').select2({ placeholder: "Select a branch", allowClear: true });
            $('#insu').select2({ placeholder: "Choose type", allowClear: true, width: '150%' });

            // DataTable initialization (unchanged)
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
                    { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-success btn-sm', footer: true },
                    { extend: 'pdf',   text: '<i class="fas fa-file-pdf"></i> PDF',   className: 'btn btn-danger btn-sm',   footer: true, orientation: 'landscape', pageSize: 'A4' }
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var total = api.column(10).data().reduce((a, b) => {
                        return a + (parseFloat(b.replace(/,/g, '')) || 0);
                    }, 0);
                    $(api.column(10).footer()).html('<strong>' + total.toLocaleString() + '</strong>');
                },
                initComplete: function() {
                    this.api().columns.adjust();
                    $('.dataTables_filter input').attr('placeholder', 'Search...');
                    $('.dataTables_filter').css('margin', '0 5px');
                    $('.dt-buttons').css('margin-left', '5px');
                },
                drawCallback: function() {
                    this.api().columns.adjust();
                }
            });

            // View Documents Button (unchanged)
            $(document).on('click', '.view-docs', function() {
                var docRef = $(this).data('doc');
                if (!docRef) {
                    alert('Document reference is missing.');
                    return;
                }

                $('#docModalLabel').text('Documents for: ' + docRef);
                var tbody = $('#docTable tbody');
                tbody.html('<tr><td colspan="2" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');

                var apiUrl = 'http://192.168.170.24/dashboardApi/clm/getDiDocs.php?doc=' + encodeURIComponent(docRef);

                $.ajax({
                    url: apiUrl,
                    method: 'GET',
                    timeout: 30000,
                    success: function(response) {
                        tbody.empty();
                        let files = [];

                        if (typeof response === 'string') {
                            var cleanedResponse = response
                                .replace(/<[^>]*>/g, '')
                                .replace(/File NameDocument/gi, '')
                                .trim();

                            var parts = cleanedResponse.split(/(?<=\.pdf|\.msg|\.doc|\.docx|\.xls|\.xlsx)\s+/i);
                            
                            files = parts
                                .map(filename => filename.trim())
                                .filter(filename => 
                                    filename && 
                                    /\.(pdf|msg|doc|docx|xls|xlsx)$/i.test(filename) &&
                                    !filename.startsWith('.') && 
                                    filename.length > 5
                                );
                        }

                        if (files.length === 0) {
                            tbody.html('<tr><td colspan="2" class="text-center text-muted">No documents found.</td></tr>');
                            return;
                        }

                        files.forEach(function(filename) {
                            var fileUrl = 'http://192.168.170.24/dashboardApi/clm/openDiPdf.php?doc=' + 
                                            encodeURIComponent(docRef) + '&filename=' + encodeURIComponent(filename);
                            tbody.append(`
                                <tr>
                                    <td><code>${filename}</code></td>
                                    <td><a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-arrow-down"></i> View / Download
                                    </a></td>
                                </tr>
                            `);
                        });
                    },
                    error: function(xhr, status, err) {
                        tbody.html('<tr><td colspan="2" class="text-center text-danger">Failed to load documents. Please try again.</td></tr>');
                        console.error('API Error:', err);
                    }
                });
            });
            // End of View Documents Button
        });
    </script>

    <script>
        
        $(document).on('click', '.view-docs', function() {
            var docRef = $(this).data('doc');
            if (!docRef) {
                alert('Document reference is missing.');
                return;
            }

            $('#docModalLabel').text('Documents for: ' + docRef);
            var tbody = $('#docTable tbody');
            tbody.html('<tr><td colspan="2" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');

            var apiUrl = 'http://192.168.170.24/dashboardApi/clm/getDiDocs.php?doc=' + encodeURIComponent(docRef);

            $.ajax({
                url: apiUrl,
                method: 'GET',
                timeout: 30000,
                success: function(response) {
                    tbody.empty();
                    let files = [];

                    if (typeof response === 'string') {
                        // Remove HTML tags
                        var cleanedResponse = response
                            .replace(/<[^>]*>/g, '')
                            .replace(/File NameDocument/gi, '')
                            .trim();

                        console.log('Raw Cleaned Response:', cleanedResponse);

                        var fullPattern = /[A-Z]+~\d{8}_.+?\.(pdf|msg|doc|docx|xls|xlsx|jpg|jpeg|png|gif)(?=\s+[A-Z]+~|\s*$)/gi;
                        var matches = cleanedResponse.match(fullPattern) || [];
                        
                        console.log('Full filename matches:', matches);

                        if (matches.length === 0) {
                            console.log('Trying line-by-line parsing...');
                            
                            var parts = cleanedResponse.split(/\s+(?=[A-Z]+~\d{8}_)/);
                            
                            matches = parts
                                .map(part => part.trim())
                                .filter(part => 
                                    part.match(/[A-Z]+~\d{8}_.+\.(pdf|msg|doc|docx|xls|xlsx|jpg|jpeg|png|gif)$/i)
                                );
                            
                            console.log('Line-by-line matches:', matches);
                        }

                        files = matches
                            .map(f => f.trim())
                            .filter(filename => 
                                filename && 
                                /\.(pdf|msg|doc|docx|xls|xlsx|jpg|jpeg|png|gif)$/i.test(filename) &&
                                filename.length > 20 && 
                                filename.match(/[A-Z]+~\d{8}_/)
                            );

                        console.log('Final Extracted Files:', files);
                    }

                    if (files.length === 0) {
                        tbody.html(`
                            <tr>
                                <td colspan="2" class="text-center text-muted">
                                    No documents found. 
                                    <br><small>Check console for raw response.</small>
                                </td>
                            </tr>
                        `);
                        return;
                    }

                    files.forEach(function(filename) {
                        var fileUrl = 'http://192.168.170.24/dashboardApi/clm/openDiPdf.php?doc=' + 
                                        encodeURIComponent(docRef) + '&filename=' + encodeURIComponent(filename);
                        
                        console.log('Generated URL:', fileUrl);
                        console.log('Filename being sent:', filename);
                        
                        tbody.append(`
                            <tr>
                                <td><code>${filename}</code></td>
                                <td>
                                    <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-arrow-down"></i> View / Download
                                    </a>
                                    <button class="btn btn-sm btn-outline-secondary copy-filename" 
                                            data-filename="${filename}" title="Copy filename">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                },
                error: function(xhr, status, err) {
                    tbody.html('<tr><td colspan="2" class="text-center text-danger">Failed to load documents. Please try again.</td></tr>');
                    console.error('API Error:', err);
                    console.error('Status:', status);
                    console.error('Response Text:', xhr.responseText);
                }
            });
        });

        // 2. Handle the Save Remarks button click
        $('#saveRemarksBtn').on('click', function() {
            const button = $(this);
            const docRef = $('#remarksDocRef').val();
            const remarks = $('#remarksText').val().trim();

            if (!docRef) {
                alert('Document reference is missing.');
                return;
            }

            if (remarks.length === 0) {
                alert('Please enter remarks.');
                return;
            }

            button.prop('disabled', true).text('Saving...');

            // AJAX call to your insertApproval route
            // NOTE: We pass a required dummy value for 'in_range' (0) 
            // since your controller logic relies on it for approval limit checks, 
            // even though we are primarily saving remarks.
            $.post('{{ route('insertApproval') }}', {
                doc: docRef,
                in_range: 0, 
                remarks: remarks,
                _token: '{{ csrf_token() }}'
            })
            .done(function (res) {
                if (res.status) {
                    alert('Remarks saved successfully!');
                    $('#remarksModal').modal('hide');
                    
                    // Update the remarks button to show it was remarked upon
                    const remarksButton = $(`button[data-doc="${docRef}"].remarks-btn`);
                    remarksButton.removeClass('btn-outline-info').addClass('btn-info').text('Remarked');
                    
                } else {
                    alert('Error saving remarks: ' + res.message);
                }
            })
            .fail(function (xhr) {
                const msg = xhr.responseJSON?.message || 'Server error occurred while saving remarks.';
                alert(msg);
            })
            .always(function() {
                button.prop('disabled', false).text('Save Remarks');
            });
        });

        // For "Approve" button (unchanged)
        $(document).on('click', '.insert-record:not(.disabled)', function () {
            const button = $(this);
            const docRef = button.data('doc-ref');
            const amount = parseFloat(button.data('loss-claimed')) || 0;

            if (!docRef) return alert('Document reference missing');

            button.prop('disabled', true)
                  .html('<i class="bi bi-hourglass-split"></i> Processing...');

            // Ensure you include `remarks: ''` here if it's not strictly required, 
            // or pass an empty string to avoid PHP warnings/errors if the field is optional.
            // Since you are using the same route for both approve/ok and remarks, the server expects it.
            $.post('{{ route('insertApproval') }}', {
                doc: docRef,
                in_range: amount,
                remarks: '', // Sending empty remarks for standard approval/OK
                _token: '{{ csrf_token() }}'
            })
            .done(function (res) {
                if (res.status) {
                    alert(res.message);
                    button.html('<i class="bi bi-check-circle"></i> Approved')
                          .removeClass('btn-success')
                          .addClass('btn-secondary disabled');
                    setTimeout(() => button.closest('tr').fadeOut(600), 1200);
                } else {
                    alert(res.message);
                    button.prop('disabled', false).html('<i class="bi bi-plus"></i> Approve');
                }
            })
            .fail(function (xhr) {
                const msg = xhr.responseJSON?.message || 'Server error';
                alert(msg);
                button.prop('disabled', false).html('<i class="bi bi-plus"></i> Approve');
            });
        });

        // For "OK" button (unchanged logic, ensuring remarks is handled)
        $(document).on('click', '.insert-ok:not(.disabled)', function () {
            const button = $(this);
            const docRef = button.data('doc-ref');
            const amount = parseFloat(button.data('loss-claimed')) || 0;

            if (!docRef) return alert('Document reference missing');

            button.prop('disabled', true)
                  .html('<i class="bi bi-hourglass-split"></i> Processing...');

            // Sending empty remarks for standard approval/OK
            $.post('{{ route('insertApproval') }}', {
                doc: docRef,
                in_range: amount,
                remarks: '', 
                _token: '{{ csrf_token() }}'
            })
            .done(function (res) {
                if (res.status) {
                    alert(res.message);
                    button.html('<i class="bi bi-check-circle"></i> OK\'d')
                          .removeClass('btn-success')
                          .addClass('btn-secondary disabled');
                    setTimeout(() => button.closest('tr').fadeOut(600), 1200);
                } else {
                    alert(res.message);
                    button.prop('disabled', false).html('<i class="bi bi-check"></i> OK');
                }
            })
            .fail(function (xhr) {
                const msg = xhr.responseJSON?.message || 'Server error';
                alert(msg);
                button.prop('disabled', false).html('<i class="bi bi-check"></i> OK');
            });
        });
    </script>
</body>
</html>