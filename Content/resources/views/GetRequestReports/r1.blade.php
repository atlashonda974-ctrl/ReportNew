<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.master_titles')
    
    {{-- Styles --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-datatable-styles />
    
    <style>
        .summary-card, .aging-card { border-radius: 6px; font-size: 0.8rem; }
        .summary-card .fw-bold, .aging-card .fw-bold { font-size: 1rem; }
        .summary-card small, .aging-card small { font-size: 0.7rem; }
        .progress { height: 4px; }
        .card-title { font-size: 0.95rem !important; }
        .row-checkbox { width: 16px; height: 16px; cursor: pointer; accent-color: #0d6efd; }
        #selectAllCheckbox { width: 16px; height: 16px; cursor: pointer; accent-color: #0d6efd; }
        .bulk-bar {
            background: #f0f7ff; border: 1px solid #b6d4fe; border-radius: 6px;
            padding: 8px 14px; display: flex; align-items: center; gap: 12px; margin-bottom: 10px;
        }
        #bulkSendEmailBtn:disabled { opacity: 0.55; }
        .sending-progress { font-size: 0.85rem; color: #0d6efd; }
        .note-tag-btn { font-size: 0.75rem; padding: 2px 7px; }
        .truncate-text { cursor: help; }
        .numeric { text-align: right; }
        
        /* Fix for footer */
        tfoot td:first-child {
            text-align: right !important;
            font-weight: bold;
        }
        tfoot td:nth-child(2) {
            font-weight: bold;
        }

        /* File badges */
        .file-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: #6c757d; color: #fff;
            border-radius: 4px; padding: 3px 8px;
            font-size: 0.8rem; margin: 3px 3px 0 0;
        }
        .file-badge .remove-file {
            background: none; border: none; color: #fff;
            font-size: 0.7rem; cursor: pointer; padding: 0; line-height: 1;
        }
        #attachmentPreview { flex-wrap: wrap; }
    </style>
</head>
<body>
<div class="container mt-5">
    
    @if(request('uw_doc'))
        <div class="alert alert-info">
            <strong>Debug Info:</strong><br>
            Document Number: {{ request('uw_doc') }}
        </div>
    @endif

    {{-- Filter Form --}}
    <form method="GET" action="{{ url('/r1') }}" class="mb-4">
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
                <button type="submit" class="btn btn-outline-primary me-2" title="Filter">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                <a href="{{ url('/r1') }}" class="btn btn-outline-secondary me-2" title="Reset">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </div>
    </form>

    @if(empty($data) || $data->isEmpty())
        <div class="alert alert-danger">No data available.</div>
    @else
        {{-- Dashboard Summary --}}
        <div class="border border-primary rounded p-2 mb-3">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-3">
                    <h6 class="card-title text-primary mb-2">
                        <i class="bi bi-clipboard-data me-1"></i>Posting Status Analysis
                    </h6>
                    <div class="row">
                        {{-- Summary Cards --}}
                        <div class="col-md-4 border-end bg-light">
                            <div class="px-2 py-2">
                                <h6 class="text-muted mb-2">
                                    <i class="bi bi-bar-chart-fill me-1 text-primary"></i>Summary
                                </h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="card border-0 shadow-sm summary-card flex-fill me-2">
                                        <div class="card-body py-2 px-2">
                                            <small class="text-muted">Total Records</small>
                                            <div class="fw-bold">{{ $totalCount }}</div>
                                            <small class="text-muted">Sum: {{ number_format($totalSumInsured) }}</small>
                                        </div>
                                        <i class="bi bi-database text-primary"></i>
                                    </div>
                                    <div class="card border-0 shadow-sm summary-card flex-fill me-2">
                                        <div class="card-body py-2 px-2">
                                            <small class="text-muted">Not Posted</small>
                                            <div class="fw-bold text-danger">{{ $notPostedCount }}</div>
                                            <small class="text-muted">Sum: {{ number_format($notPostedSumInsured) }}</small>
                                        </div>
                                        <i class="bi bi-exclamation-triangle text-danger"></i>
                                    </div>
                                    <div class="card border-0 shadow-sm summary-card flex-fill">
                                        <div class="card-body py-2 px-2">
                                            <small class="text-muted">Percentage of Posted Records</small>
                                            <div class="fw-bold text-info">
                                                {{ $totalCount > 0 ? round((($totalCount - $notPostedCount) / $totalCount) * 100) : 0 }}%
                                            </div>
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar bg-info"
                                                     style="width: {{ $totalCount > 0 ? round((($totalCount - $notPostedCount) / $totalCount) * 100) : 0 }}%;"></div>
                                            </div>
                                        </div>
                                        <i class="bi bi-percent text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Aging Analysis --}}
                        @php
                            $groupedCollection = collect($groupedByAging);
                            $maxCount = $groupedCollection->max(fn($group) => $group->count());
                            $minCount = $groupedCollection->min(fn($group) => $group->count());
                        @endphp
                        <div class="col-md-8">
                            <h6 class="text-muted mb-2">📊 Aging Analysis</h6>
                            <div class="row g-2">
                                @foreach ($groupedByAging as $label => $collection)
                                    @php
                                        $count = $collection->count();
                                        $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100) : 0;
                                        $sumCedSi = $collection->sum(fn($item) => (float)($item->CED_SI ?? 0));
                                        $formattedSum = number_format($sumCedSi);
                                        $colorClass = 'secondary';
                                        $minDays = 0;
                                        $maxDays = PHP_INT_MAX;
                                        
                                        if (Str::startsWith($label, '0-3')) { 
                                            $colorClass = 'success'; 
                                            $minDays = 0;  
                                            $maxDays = 3; 
                                        } elseif (Str::startsWith($label, '4-7')) { 
                                            $colorClass = 'warning'; 
                                            $minDays = 4;  
                                            $maxDays = 7; 
                                        } elseif (Str::startsWith($label, '8-10')) { 
                                            $colorClass = 'warning'; 
                                            $minDays = 8;  
                                            $maxDays = 10; 
                                        } elseif (Str::startsWith($label, '11-15')) { 
                                            $colorClass = 'info';    
                                            $minDays = 11; 
                                            $maxDays = 15; 
                                        } elseif (Str::startsWith($label, '16-20')) { 
                                            $colorClass = 'primary'; 
                                            $minDays = 16; 
                                            $maxDays = 20; 
                                        } elseif (Str::startsWith($label, '20+')) { 
                                            $colorClass = 'dark';    
                                            $minDays = 21; 
                                            $maxDays = PHP_INT_MAX; 
                                        }
                                        
                                        if ($count === $maxCount && $maxCount > 0) { 
                                            $colorClass = 'danger'; 
                                        } elseif ($count === $minCount && $minCount > 0) { 
                                            $colorClass = 'info'; 
                                        }
                                    @endphp
                                    <div class="col-6 col-sm-4">
                                        <div class="card aging-card border-{{ $colorClass }} shadow-sm h-100"
                                             data-bs-toggle="modal" data-bs-target="#filteredRecordsModal"
                                             data-label="{{ $label }}"
                                             data-count="{{ $count }}"
                                             data-percentage="{{ $percentage }}"
                                             data-min-days="{{ $minDays }}"
                                             data-max-days="{{ $maxDays }}"
                                             style="cursor: pointer;">
                                            <div class="card-body py-2 px-2">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-{{ $colorClass }}">
                                                        <i class="bi bi-circle-fill me-1 small"></i>{{ $label }}
                                                    </span>
                                                    <span class="fw-bold">{{ $count }}</span>
                                                </div>
                                                <div class="progress" style="height: 4px;">
                                                    <div class="progress-bar bg-{{ $colorClass }}" style="width: {{ $percentage }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $percentage }}%</small>
                                                <small class="text-primary fw-bold d-block">Sum: {{ $formattedSum }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bulk Action Bar --}}
        <div class="bulk-bar">
            <button type="button" id="bulkSendEmailBtn" class="btn btn-success btn-sm" disabled>
                <i class="bi bi-envelope-fill"></i>
                Send Email to Selected <span class="badge bg-light text-success" id="selectedCount">0</span>
            </button>
            <span class="sending-progress d-none" id="sendingProgress"></span>
        </div>

        {{-- Main Table --}}
        <table id="reportsTable" class="table table-bordered table-responsive" style="width:100%">
            <thead>
                <tr>
                    <th data-field="select" style="white-space:nowrap; width:80px;">
                        <input type="checkbox" id="selectAllCheckbox" title="Select / Deselect All">
                    </th>
                    <th data-field="request_note">Request Note #</th>
                    <th data-field="doc_date">Doc Date</th>
                    <th data-field="dept">Dept.</th>
                    <th data-field="business_desc">Business Description</th>
                    <th data-field="insured">Insured</th>
                    <th data-field="reins_party">Re-Ins Party</th>
                    <th data-field="total_sum_ins">Total Sum Ins</th>
                    <th data-field="ri_sum_ins">RI Sum Ins</th>
                    <th data-field="share">Share</th>
                    <th data-field="total_premium">Total Premium</th>
                    <th data-field="ri_premium">RI Premium</th>
                    <th data-field="comm_date">Comm. Date</th>
                    <th data-field="expiry_date">Expiry Date</th>
                    <th data-field="cp">CP</th>
                    <th data-field="conv_takaful">Conventional/Takaful</th>
                    <th data-field="posted">Posted</th>
                    <th data-field="user_name">User Name</th>
                    <th data-field="acceptance_date">Acceptance Date</th>
                    <th data-field="warranty_period">Warranty Period</th>
                    <th data-field="commission_percent">Commission Percent</th>
                    <th data-field="commission_amount">Commission Amount</th>
                    <th data-field="acceptance_no">Acceptance No</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $record)
                    @php
                        try {
                            $formattedDate = isset($record->GRH_DOCUMENTDATE)
                                ? \Carbon\Carbon::createFromFormat('d-M-y', $record->GRH_DOCUMENTDATE)->format('d-m-Y')
                                : 'N/A';
                        } catch (\Exception $e) {
                            $formattedDate = 'Invalid Date';
                            \Log::error("Invalid date format for GRH_DOCUMENTDATE: {$record->GRH_DOCUMENTDATE}");
                        }
                        $deptCode = $record->PDP_DEPT_CODE ?? null;
                        $categoryMapping = [11 => 'Fire', 12 => 'Marine', 13 => 'Motor', 14 => 'Miscellaneous', 16 => 'Health'];
                    @endphp
                    <tr>
                        <td data-field="select" style="white-space:nowrap;">
                            <input type="checkbox" class="row-checkbox" style="margin-right:5px;">
                            <button class="btn btn-sm note-tag-btn ms-1"
                                    style="background:#fff3cd; border:1px solid #ffc107; color:#856404;"
                                    data-req-note="{{ $record->GRH_REFERENCE_NO }}"
                                    title="Add Note Tag">
                                <i class="fas fa-sticky-note"></i> Note
                            </button>
                        </td>
                        <td data-field="request_note">
                            <a href="#" class="open-modal" data-req-note="{{ $record->GRH_REFERENCE_NO }}">
                                {{ $record->GRH_REFERENCE_NO ?? 'N/A' }}
                            </a>
                        </td>
                        <td data-field="doc_date">{{ $formattedDate }}</td>
                        <td data-field="dept">{{ $categoryMapping[$deptCode] ?? 'N/A' }}</td>
                        <td data-field="business_desc">
                            <span class="truncate-text" title="{{ $record->PBC_DESC ?? 'N/A' }}">
                                {{ \Illuminate\Support\Str::limit($record->PBC_DESC ?? 'N/A', 15, '...') }}
                            </span>
                        </td>
                        <td data-field="insured">
                            <span class="truncate-text" title="{{ $record->INSURED_DESC ?? 'N/A' }}">
                                {{ \Illuminate\Support\Str::limit($record->INSURED_DESC ?? 'N/A', 5, '...') }}
                            </span>
                        </td>
                        <td data-field="reins_party">
                            <span class="truncate-text" title="{{ $record->RE_COMP_DESC ?? 'N/A' }}">
                                {{ \Illuminate\Support\Str::limit($record->RE_COMP_DESC ?? 'N/A', 8, '...') }}
                            </span>
                        </td>
                        <td data-field="total_sum_ins" class="numeric">
                            {{ is_numeric($record->TOT_SI ?? null) ? number_format($record->TOT_SI) : 'N/A' }}
                        </td>
                        <td data-field="ri_sum_ins" class="numeric">
                            {{ is_numeric($record->CED_SI ?? null) ? number_format($record->CED_SI) : 'N/A' }}
                        </td>
                        <td data-field="share" class="numeric">
                            {{ is_numeric($record->GRH_CEDEDSISHARE ?? null) ? number_format($record->GRH_CEDEDSISHARE, 2) . '%' : 'N/A' }}
                        </td>
                        <td data-field="total_premium" class="numeric">
                            {{ is_numeric($record->TOT_PRE ?? null) ? number_format($record->TOT_PRE) : 'N/A' }}
                        </td>
                        <td data-field="ri_premium" class="numeric">
                            {{ is_numeric($record->CED_PRE ?? null) ? number_format($record->CED_PRE) : 'N/A' }}
                        </td>
                        <td data-field="comm_date">
                            {{ $record->GRH_COMMDATE ? \Carbon\Carbon::parse($record->GRH_COMMDATE)->format('d-m-Y') : 'N/A' }}
                        </td>
                        <td data-field="expiry_date">
                            {{ $record->GRH_EXPIRYDATE ? \Carbon\Carbon::parse($record->GRH_EXPIRYDATE)->format('d-m-Y') : 'N/A' }}
                        </td>
                        <td data-field="cp">{{ $record->CP_STS ?? 'N/A' }}</td>
                        <td data-field="conv_takaful">{{ $record->INSU_TYPE ?? 'N/A' }}</td>
                        <td data-field="posted">{{ $record->GRH_POSTINGTAG ?? 'N/A' }}</td>
                        <td data-field="user_name">{{ $record->CREATED_BY ?? 'N/A' }}</td>
                        <td data-field="acceptance_date">
                            {{ $record->GRH_ACCEPTEDDATE ? \Carbon\Carbon::parse($record->GRH_ACCEPTEDDATE)->format('d-m-Y') : 'N/A' }}
                        </td>
                        <td data-field="warranty_period">
                            @if(isset($record->GRH_ACCEPTEDDATE))
                                {{ \Carbon\Carbon::parse($record->GRH_ACCEPTEDDATE)->addDays(30)->format('d-m-Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td data-field="commission_percent" class="numeric">
                            {{ is_numeric($record->GRH_COMMISSIONRATE ?? null) ? number_format($record->GRH_COMMISSIONRATE, 2) : 'N/A' }}
                        </td>
                        <td data-field="commission_amount" class="numeric">
                            {{ is_numeric($record->COMMISSIONAMT ?? null) ? number_format($record->COMMISSIONAMT) : 'N/A' }}
                        </td>
                        <td data-field="acceptance_no">{{ $record->GRH_REINS_REF_NO ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8" style="text-align:right; font-weight:bold;">Total RI Sum Insured:</td>
                    <td style="font-weight:bold;" class="numeric total-ri-sum">0</td>
                    <td colspan="14"></td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- Note Tag Modal --}}
    <div class="modal fade" id="noteTagModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header" style="background:#fff3cd; border-bottom:1px solid #ffc107;">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fas fa-sticky-note me-2 text-warning"></i>Add Note Tag
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="rounded p-3 mb-4" style="background:#f8f9fa; border-left:4px solid #ffc107;">
                        <div class="row g-1">
                            <div class="col-6">
                                <small class="text-muted d-block" style="font-size:0.7rem;">REQUEST NOTE #</small>
                                <strong id="ntRefNo" class="text-dark" style="font-size:0.95rem;">—</strong>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="ntHiddenRef">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Action <span class="text-danger">*</span></label>
                        <select class="form-select" id="ntAction">
                            <option value="">— Select Action —</option>
                            <option value="revise">Revise</option>
                            <option value="cancel">Cancel</option>
                        </select>
                        <div class="invalid-feedback" id="ntActionError">Please select an action.</div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Remarks</label>
                        <textarea class="form-control" id="ntRemarks" rows="4" maxlength="2000"
                                  placeholder="Enter remarks (optional)…" style="resize:vertical;"></textarea>
                        <div class="d-flex justify-content-end mt-1">
                            <small class="text-muted"><span id="ntCharCount">0</span>/2000</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background:#fafafa;">
                    <div class="me-auto">
                        <span class="text-danger small d-none" id="ntSubmitError">
                            <i class="bi bi-exclamation-circle me-1"></i><span id="ntSubmitErrorMsg"></span>
                        </span>
                    </div>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Close
                    </button>
                    <button type="button" class="btn btn-warning fw-bold text-dark" id="ntSubmitBtn">
                        <i class="fas fa-save me-1"></i>Save Note
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Request Note Details Modal --}}
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Note Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <p class="text-center text-muted">Loading...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Email Modal --}}
    <div id="bulkEmailModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-envelope-fill me-2"></i>Send Email</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3 py-2">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Sending to <strong id="bulkSelectedCountDisplay">0</strong> selected record(s).
                    </div>

                    {{-- Selected Records Preview --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">Selected Records:</label>
                        <div style="max-height:130px; overflow-y:auto; border:1px solid #dee2e6; border-radius:4px;">
                            <table class="table table-sm table-bordered mb-0" id="bulkPreviewTable">
                                <thead class="table-light sticky-top">
                                    <tr><th>#</th><th>Request Note</th><th>Insured</th><th>Dept</th><th>Doc Date</th></tr>
                                </thead>
                                <tbody id="bulkPreviewBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <hr class="my-3">

                    {{--
                        NOTE: type="text" is used for To and CC so that comma-separated
                        multiple email addresses can be entered freely.
                        Validation is handled server-side via parseEmails().
                    --}}
                    <div class="mb-3">
                        <label class="form-label">To: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="bulkTo"
                               placeholder="recipient@example.com">
                        <div class="form-text text-muted">Separate multiple addresses with a comma.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CC:</label>
                        <input type="text" class="form-control" id="bulkCc"
                               placeholder="cc@example.com, another@example.com">
                        <div class="form-text text-muted">Separate multiple addresses with a comma.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject:</label>
                        <input type="text" class="form-control" id="bulkSubject"
                               value="Reinsurance Request Note Details" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Body: <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="bulkBody" rows="6" required>Dear Sir/Madam,

Please find below details for the Request Note(s).

Regards,</textarea>
                    </div>

                    {{-- Multiple File Attachment --}}
                    <div class="mb-2">
                        <label class="form-label">
                            Attachments <span class="text-muted small">(optional — one or more)</span>:
                        </label>

                        {{--
                            "multiple" allows selecting several files at once.
                            Selecting again ADDS to the list (handled in JS via selectedFiles array).
                        --}}
                        <input type="file" class="form-control" id="bulkAttachmentFile"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip"
                               multiple>
                        <div class="form-text text-muted">
                            Max 10MB per file &mdash; PDF, Word, Excel, Image, ZIP.
                            You can select multiple files at once.
                        </div>

                        {{-- Dynamic per-file badge list --}}
                        <div id="attachmentPreview" class="mt-2 d-flex flex-wrap gap-1"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="me-auto text-muted small fw-semibold" id="bulkModalStatus"></div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="confirmBulkSendBtn">
                        <i class="bi bi-send-fill me-1"></i>Send Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtered Records Modal --}}
    <div class="modal fade" id="filteredRecordsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filteredRecordsTitle">Filtered Records</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="modalSearch" class="form-control" placeholder="Search records...">
                            <button class="btn btn-outline-secondary" type="button" id="clearModalSearch">Clear</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="filteredRecordsTable">
                            <thead>
                                <tr>
                                    <th>Request Note #</th>
                                    <th>Doc Date</th>
                                    <th>Dept.</th>
                                    <th>Business Description</th>
                                    <th>Insured</th>
                                    <th>Re-Ins Party</th>
                                    <th>Total Sum Ins</th>
                                    <th>RI Sum Ins</th>
                                    <th>Share</th>
                                    <th>Total Premium</th>
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
                            <tbody id="filteredRecordsBody"></tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="7" class="text-end fw-bold">Total RI Sum Insured:</td>
                                    <td class="fw-bold" id="modalTotalRiSumIns">0</td>
                                    <td colspan="14"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="me-auto" id="modalRecordCount">Showing 0 records</div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

  
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>

    // ── Selected files list (maintained independently of the file input) ──────
    // We use a plain array so the user can remove individual files and
    // then re-open the picker to add more without losing what they already picked.
    var selectedFiles = [];

    /**
     * Rebuild the preview badges from the selectedFiles array.
     */
    function renderFileBadges() {
        var $preview = $('#attachmentPreview').empty();
        if (selectedFiles.length === 0) return;

        selectedFiles.forEach(function(file, index) {
            var $badge = $(
                '<span class="file-badge">' +
                    '<i class="bi bi-paperclip"></i>' +
                    '<span>' + file.name + '</span>' +
                    '<button type="button" class="remove-file" data-index="' + index + '" title="Remove">' +
                        '&times;' +
                    '</button>' +
                '</span>'
            );
            $preview.append($badge);
        });
    }

    // When the user picks file(s) — APPEND to selectedFiles, don't replace
    $(document).on('change', '#bulkAttachmentFile', function() {
        var newFiles = Array.from(this.files);

        newFiles.forEach(function(newFile) {
            // Avoid exact duplicates (same name + size)
            var isDuplicate = selectedFiles.some(function(f) {
                return f.name === newFile.name && f.size === newFile.size;
            });
            if (!isDuplicate) {
                selectedFiles.push(newFile);
            }
        });

        // Reset the input so the same file can be re-added after removal if needed
        $(this).val('');
        renderFileBadges();
    });

    // Remove individual file badge
    $(document).on('click', '.remove-file', function() {
        var index = parseInt($(this).data('index'));
        selectedFiles.splice(index, 1);
        renderFileBadges();
    });

    // Reset everything when the email modal closes
    $('#bulkEmailModal').on('hidden.bs.modal', function() {
        selectedFiles = [];
        $('#attachmentPreview').empty();
        $('#bulkAttachmentFile').val('');
        $('#bulkModalStatus').html('');
    });

    $(document).ready(function() {

        // ── DataTable initialization ──────────────────────────────────────────
        var table = $('#reportsTable').DataTable({
            paging:         false,
            searching:      true,
            ordering:       true,
            info:           true,
            scrollX:        true,
            scrollY:        "500px",
            scrollCollapse: false,
            fixedHeader:    { header: true, footer: true },
            autoWidth:      true,
            dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-sm border',
                    title: 'Get Request Note',
                    exportOptions: {
                        columns: ':visible:not(:first-child):not(:nth-child(2))',
                        format: {
                            body: function (data, row, column, node) {
                                if ([3, 4, 5].includes(column))
                                    return $(node).find('.truncate-text').attr('title') || $(node).text().trim();
                                if ([6, 7, 9, 10, 20].includes(column)) {
                                    const v = parseFloat($(node).text().replace(/,/g, ''));
                                    return isNaN(v) ? '0' : v.toLocaleString('en-US');
                                }
                                return $(node).text().trim();
                            }
                        }
                    },
                    customizeData: function (data) {
                        const intVal = i => typeof i === 'string' ? i.replace(/[^\d.-]/g, '') * 1 : (typeof i === 'number' ? i : 0);
                        let totals = new Array(data.body[0].length).fill('');
                        let sum    = data.body.reduce((acc, row) => acc + intVal(row[7]), 0);
                        totals[7]  = sum.toLocaleString('en-US');
                        totals[0]  = 'Total RI Sum Insured:';
                        data.body.push(totals);
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-sm border',
                    title: 'Get Request Note',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible:not(:first-child):not(:nth-child(2))',
                        format: {
                            body: function (data, row, column, node) {
                                if ([3, 4, 5].includes(column))
                                    return $(node).find('.truncate-text').attr('title') || $(node).text().trim();
                                if ([6, 7, 9, 10, 20].includes(column)) {
                                    const v = parseFloat(data.replace(/,/g, ''));
                                    return isNaN(v) ? '0' : v.toLocaleString('en-US');
                                }
                                return $(node).text().trim();
                            }
                        }
                    },
                    customize: function (doc) {
                        const intVal = i => typeof i === 'string' ? i.replace(/[^\d.-]/g, '') * 1 : (typeof i === 'number' ? i : 0);
                        let totals = new Array(doc.content[1].table.body[0].length).fill('');
                        let sum    = doc.content[1].table.body.slice(1).reduce((acc, row) => acc + intVal(row[7].text), 0);
                        totals[7]  = { text: sum.toLocaleString('en-US'), bold: true };
                        totals[0]  = 'Total RI Sum Insured:';
                        doc.content[1].table.body.push(totals);
                    }
                }
            ],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var intVal = function(i) {
                    if (typeof i === 'string') {
                        i = i.replace(/[^\d.-]/g, '');
                        return parseFloat(i) || 0;
                    }
                    return typeof i === 'number' ? i : 0;
                };
                var total = api.column(8).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                $(api.column(8).footer()).html(total.toLocaleString('en-US'));
            },
            initComplete: function () {
                this.api().draw();
                this.api().columns.adjust();
                $('.dataTables_filter input').attr('placeholder', 'Search...');
                $('.dataTables_filter').css({ 'margin-left': '5px', 'margin-right': '5px' });
                $('.dt-buttons').css('margin-left', '5px');
            },
            drawCallback: function() {
                this.api().columns.adjust();
                updateFooterTotal();
            }
        });

        // Reset button handler
        $('a[title="Reset"]').on('click', function() {
            setTimeout(function() {
                table.draw();
                updateFooterTotal();
            }, 100);
        });

        // ── Checkbox functionality ────────────────────────────────────────────
        $('#selectAllCheckbox').on('change', function() {
            const checked = $(this).prop('checked');
            $('#reportsTable tbody tr').each(function() {
                $(this).find('.row-checkbox').prop('checked', checked);
            });
            updateBulkBar();
        });

        $(document).on('change', '.row-checkbox', function() {
            updateBulkBar();
            const total   = $('#reportsTable tbody tr').length;
            const checked = $('#reportsTable tbody .row-checkbox:checked').length;
            $('#selectAllCheckbox')
                .prop('indeterminate', checked > 0 && checked < total)
                .prop('checked', total > 0 && checked === total);
        });

        function updateBulkBar() {
            const count = $('#reportsTable tbody .row-checkbox:checked').length;
            $('#selectedCount').text(count);
            $('#bulkSendEmailBtn').prop('disabled', count === 0);
        }

        // Collect selected rows data
        function collectSelectedRows() {
            const rows = [];
            $('#reportsTable tbody tr').each(function() {
                if (!$(this).find('.row-checkbox').prop('checked')) return;
                const $r = $(this);
                rows.push({
                    reqNote:      $r.find('td[data-field="request_note"]').text().trim(),
                    docDate:      $r.find('td[data-field="doc_date"]').text().trim(),
                    dept:         $r.find('td[data-field="dept"]').text().trim(),
                    insured:      $r.find('td[data-field="insured"] .truncate-text').attr('title') || $r.find('td[data-field="insured"]').text().trim(),
                    businessDesc: $r.find('td[data-field="business_desc"] .truncate-text').attr('title') || $r.find('td[data-field="business_desc"]').text().trim(),
                    riSumIns:     $r.find('td[data-field="ri_sum_ins"]').text().trim()
                });
            });
            return rows;
        }

        // ── Bulk Email — open modal ───────────────────────────────────────────
        $('#bulkSendEmailBtn').on('click', function() {
            const selectedRows = collectSelectedRows();
            if (selectedRows.length === 0) {
                alert('Please select at least one record.');
                return;
            }
            $('#bulkSelectedCountDisplay').text(selectedRows.length);
            const $tbody = $('#bulkPreviewBody').empty();
            selectedRows.forEach(function(r, i) {
                $tbody.append(
                    '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td><strong>' + r.reqNote + '</strong></td>' +
                        '<td>' + r.insured + '</td>' +
                        '<td>' + r.dept + '</td>' +
                        '<td>' + r.docDate + '</td>' +
                    '</tr>'
                );
            });
            $('#bulkEmailModal').modal('show');
        });

        // ── Bulk Email — send ─────────────────────────────────────────────────
        $('#confirmBulkSendBtn').on('click', function() {
            const to      = $('#bulkTo').val().trim();
            const cc      = $('#bulkCc').val().trim();
            const subject = $('#bulkSubject').val().trim();
            const body    = $('#bulkBody').val().trim();

            if (!to)      { alert('Please enter recipient email address.'); return; }
            if (!subject) { alert('Please enter email subject.'); return; }
            if (!body)    { alert('Please enter email body.'); return; }

            const $btn         = $(this);
            const csrfToken    = $('meta[name="csrf-token"]').attr('content');
            const selectedRows = collectSelectedRows();

            // Build FormData
            const formData = new FormData();
            formData.append('_token',  csrfToken);
            formData.append('to',      to);
            formData.append('cc',      cc);
            formData.append('subject', subject);
            formData.append('body',    body);
            formData.append('records', JSON.stringify(selectedRows));

            // Append ALL selected files as upload_files[]
            selectedFiles.forEach(function(file) {
                formData.append('upload_files[]', file);
            });

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Sending...');
            $('#bulkModalStatus').html('<i class="bi bi-hourglass-split me-1"></i> Sending email...');

            $.ajax({
                url:         "{{ route('send.email') }}",
                method:      'POST',
                headers:     { 'X-CSRF-TOKEN': csrfToken },
                data:        formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#bulkEmailModal').modal('hide');

                        // Dim sent rows
                        $('#reportsTable tbody .row-checkbox:checked').each(function() {
                            const row = $(this).closest('tr');
                            row.css({ 'background': '#e9ecef', 'opacity': '0.6' });
                            row.find('input,button').prop('disabled', true);
                        });

                        setTimeout(function() {
                            alert('Email sent successfully!');
                            location.reload();
                        }, 300);
                    } else {
                        alert('Error: ' + response.message);
                        $btn.prop('disabled', false).html('<i class="bi bi-send-fill me-1"></i>Send Email');
                        $('#bulkModalStatus').html('');
                    }
                },
                error: function(xhr) {
                    const msg = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Unknown error';
                    alert('Failed to send email: ' + msg);
                    $btn.prop('disabled', false).html('<i class="bi bi-send-fill me-1"></i>Send Email');
                    $('#bulkModalStatus').html('');
                }
            });
        });

        // ── Note Tag Modal ────────────────────────────────────────────────────
        $(document).on('click', '.note-tag-btn', function() {
            const $btn = $(this);
            $('#ntHiddenRef').val($btn.data('req-note'));
            $('#ntRefNo').text($btn.data('req-note'));
            $('#ntAction').val('').removeClass('is-invalid');
            $('#ntRemarks').val('');
            $('#ntCharCount').text('0');
            $('#ntSubmitError').addClass('d-none');
            $('#noteTagModal').modal('show');
        });

        $('#ntRemarks').on('input', function() {
            $('#ntCharCount').text($(this).val().length);
        });

        $('#ntAction').on('change', function() {
            $(this).removeClass('is-invalid');
            $('#ntSubmitError').addClass('d-none');
        });

        $('#ntSubmitBtn').on('click', function() {
            const refNo   = $('#ntHiddenRef').val();
            const action  = $('#ntAction').val();
            const remarks = $('#ntRemarks').val().trim();

            if (!action) {
                $('#ntAction').addClass('is-invalid');
                $('#ntSubmitError').removeClass('d-none').find('#ntSubmitErrorMsg').text('Please select an action.');
                return;
            }

            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

            $.ajax({
                url:     "{{ route('reins.tag.store') }}",
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data:    { GRH_REFERENCE_NO: refNo, tag_action: action, remarks: remarks },
                success: function(response) {
                    if (response.success) {
                        $('#noteTagModal').modal('hide');
                        location.reload();
                    } else {
                        $('#ntSubmitError').removeClass('d-none').find('#ntSubmitErrorMsg').text(response.message || 'Failed to save note.');
                        $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Note');
                    }
                },
                error: function(xhr) {
                    const msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error))
                        ? (xhr.responseJSON.message || xhr.responseJSON.error)
                        : 'Server error. Please try again.';
                    $('#ntSubmitError').removeClass('d-none').find('#ntSubmitErrorMsg').text(msg);
                    $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Note');
                }
            });
        });

        // ── Request Note details modal ────────────────────────────────────────
        $(document).on('click', '.open-modal', function(e) {
            e.preventDefault();
            const reqNote = $(this).data('req-note');
            $('#modalBody').html('<p class="text-center text-muted py-4">Loading...</p>');
            $('#detailsModal').modal('show');

            $.ajax({
                url:      'http://192.168.170.24/dashboardApi/reins/rqn/get_notes_dtl.php',
                method:   'GET',
                data:     { req_note: reqNote },
                dataType: 'json',
                timeout:  10000,
                success: function(data) {
                    if (!data || data.length === 0) {
                        $('#modalBody').html('<p class="text-center text-muted">No records found.</p>');
                        return;
                    }
                    let html = '<div class="table-responsive"><table class="table table-bordered table-striped table-sm"><thead class="table-light"><tr>';
                    const headers = ['Doc No','Insured','Reinsurer','Share','Commission','Accepted Date','Comm. Date','Expiry Date','Issue Date','Ceded SI','Ceded Premium','SI','Premium','CP No','CP Date','Ref No'];
                    headers.forEach(function(h) { html += '<th>' + h + '</th>'; });
                    html += '</tr></thead><tbody>';
                    data.forEach(function(item) {
                        const r = typeof item === 'string' ? JSON.parse(item) : item;
                        html += '<tr>' +
                            '<td>' + (r.GRD_CEDINGDOCNO || '') + '</td>' +
                            '<td>' + (r.INSURED_DESC || '') + '</td>' +
                            '<td>' + (r.RE_COMP_DESC || '') + '</td>' +
                            '<td>' + (r.GRH_CEDEDSISHARE || '') + '%</td>' +
                            '<td>' + (r.GRH_COMMISSIONRATE || '') + '%</td>' +
                            '<td>' + (r.GRH_ACCEPTEDDATE || '') + '</td>' +
                            '<td>' + (r.GDH_COMMDATE || '') + '</td>' +
                            '<td>' + (r.GDH_EXPIRYDATE || '') + '</td>' +
                            '<td>' + (r.GDH_ISSUEDATE || '') + '</td>' +
                            '<td class="text-end">' + formatNumber(r['SUM(GRS_CEDEDSI)']) + '</td>' +
                            '<td class="text-end">' + formatNumber(r['SUM(GRS_CEDEDPREM)']) + '</td>' +
                            '<td class="text-end">' + formatNumber(r['SUM(GRS_TOTALSI)']) + '</td>' +
                            '<td class="text-end">' + formatNumber(r['SUM(GRS_TOTALPREM)']) + '</td>' +
                            '<td>' + (r.GCP_DOC_REFERENCENO || '') + '</td>' +
                            '<td>' + (r.CREATE_DATE || '') + '</td>' +
                            '<td>' + (r.GCT_THEIR_REF_NO || '') + '</td>' +
                        '</tr>';
                    });
                    html += '</tbody></table></div>';
                    $('#modalBody').html(html);
                },
                error: function() {
                    $('#modalBody').html('<p class="text-center text-danger">Error loading data. Please try again.</p>');
                }
            });
        });

        // ── Aging cards modal ─────────────────────────────────────────────────
        const filteredRecordsModal = new bootstrap.Modal(document.getElementById('filteredRecordsModal'));
        let allFilteredRows = [];

        function calculateDaysDiff(docDate) {
            try {
                if (!docDate || docDate === 'N/A' || docDate === 'Invalid Date') return -1;
                const parts  = docDate.split('-').map(Number);
                const parsed = new Date(parts[2], parts[1] - 1, parts[0]);
                if (isNaN(parsed.getTime())) return -1;
                const diff = Math.floor((new Date() - parsed) / 86400000);
                return diff >= 0 ? diff : -1;
            } catch (e) {
                return -1;
            }
        }

        $('.aging-card').on('click', function() {
            const minDays = parseInt($(this).data('min-days'));
            const maxDays = parseInt($(this).data('max-days'));
            const label   = $(this).data('label');

            if (isNaN(minDays) || isNaN(maxDays)) return;

            $('#filteredRecordsBody').empty();
            allFilteredRows = [];
            let riTotal = 0;

            $('#reportsTable tbody tr').each(function() {
                const $row   = $(this);
                const docDate = $row.find('td[data-field="doc_date"]').text().trim();
                const riText  = $row.find('td[data-field="ri_sum_ins"]').text().trim();
                const ri      = riText === 'N/A' ? 0 : (parseFloat(riText.replace(/,/g, '')) || 0);
                const days    = calculateDaysDiff(docDate);

                if (days >= minDays && days <= maxDays) {
                    const rowData = {
                        reqNote:           $row.find('td[data-field="request_note"]').text().trim(),
                        docDate:           docDate,
                        dept:              $row.find('td[data-field="dept"]').text().trim(),
                        businessDesc:      $row.find('td[data-field="business_desc"] .truncate-text').attr('title') || $row.find('td[data-field="business_desc"]').text().trim(),
                        insured:           $row.find('td[data-field="insured"] .truncate-text').attr('title') || $row.find('td[data-field="insured"]').text().trim(),
                        reinsParty:        $row.find('td[data-field="reins_party"] .truncate-text').attr('title') || $row.find('td[data-field="reins_party"]').text().trim(),
                        totalSumIns:       $row.find('td[data-field="total_sum_ins"]').text().trim(),
                        riSumIns:          riText,
                        share:             $row.find('td[data-field="share"]').text().trim(),
                        totalPremium:      $row.find('td[data-field="total_premium"]').text().trim(),
                        riPremium:         $row.find('td[data-field="ri_premium"]').text().trim(),
                        commDate:          $row.find('td[data-field="comm_date"]').text().trim(),
                        expiryDate:        $row.find('td[data-field="expiry_date"]').text().trim(),
                        cp:                $row.find('td[data-field="cp"]').text().trim(),
                        convTakaful:       $row.find('td[data-field="conv_takaful"]').text().trim(),
                        posted:            $row.find('td[data-field="posted"]').text().trim(),
                        userName:          $row.find('td[data-field="user_name"]').text().trim(),
                        acceptanceDate:    $row.find('td[data-field="acceptance_date"]').text().trim(),
                        warrantyPeriod:    $row.find('td[data-field="warranty_period"]').text().trim(),
                        commissionPercent: $row.find('td[data-field="commission_percent"]').text().trim(),
                        commissionAmount:  $row.find('td[data-field="commission_amount"]').text().trim(),
                        acceptanceNo:      $row.find('td[data-field="acceptance_no"]').text().trim()
                    };

                    const $newRow = $(
                        '<tr>' +
                            '<td>' + rowData.reqNote + '</td>' +
                            '<td>' + rowData.docDate + '</td>' +
                            '<td>' + rowData.dept + '</td>' +
                            '<td><span title="' + rowData.businessDesc.replace(/"/g, '&quot;') + '">' + rowData.businessDesc + '</span></td>' +
                            '<td><span title="' + rowData.insured.replace(/"/g, '&quot;') + '">' + rowData.insured + '</span></td>' +
                            '<td><span title="' + rowData.reinsParty.replace(/"/g, '&quot;') + '">' + rowData.reinsParty + '</span></td>' +
                            '<td class="text-end">' + rowData.totalSumIns + '</td>' +
                            '<td class="text-end">' + rowData.riSumIns + '</td>' +
                            '<td class="text-end">' + rowData.share + '</td>' +
                            '<td class="text-end">' + rowData.totalPremium + '</td>' +
                            '<td class="text-end">' + rowData.riPremium + '</td>' +
                            '<td>' + rowData.commDate + '</td>' +
                            '<td>' + rowData.expiryDate + '</td>' +
                            '<td>' + rowData.cp + '</td>' +
                            '<td>' + rowData.convTakaful + '</td>' +
                            '<td>' + rowData.posted + '</td>' +
                            '<td>' + rowData.userName + '</td>' +
                            '<td>' + rowData.acceptanceDate + '</td>' +
                            '<td>' + rowData.warrantyPeriod + '</td>' +
                            '<td class="text-end">' + rowData.commissionPercent + '</td>' +
                            '<td class="text-end">' + rowData.commissionAmount + '</td>' +
                            '<td>' + rowData.acceptanceNo + '</td>' +
                        '</tr>'
                    );

                    $('#filteredRecordsBody').append($newRow);
                    allFilteredRows.push({ element: $newRow, riSumIns: ri });
                    riTotal += ri;
                }
            });

            $('#modalTotalRiSumIns').text(riTotal.toLocaleString('en-US'));
            $('#filteredRecordsTitle').text(label + ' — ' + allFilteredRows.length + ' records');
            $('#modalRecordCount').text('Showing ' + allFilteredRows.length + ' records');
            filteredRecordsModal.show();
        });

        $('#modalSearch').on('keyup', function() {
            const val = $(this).val().toLowerCase();
            let visible = 0, riTotal = 0;
            allFilteredRows.forEach(function(r) {
                const show = r.element.text().toLowerCase().includes(val);
                r.element.toggle(show);
                if (show) { visible++; riTotal += r.riSumIns; }
            });
            $('#modalTotalRiSumIns').text(riTotal.toLocaleString('en-US'));
            $('#modalRecordCount').text('Showing ' + visible + ' filtered records');
        });

        $('#clearModalSearch').on('click', function() {
            $('#modalSearch').val('');
            let riTotal = 0;
            allFilteredRows.forEach(function(r) { r.element.show(); riTotal += r.riSumIns; });
            $('#modalTotalRiSumIns').text(riTotal.toLocaleString('en-US'));
            $('#modalRecordCount').text('Showing ' + allFilteredRows.length + ' records');
        });

    }); // end document.ready

    // ── Helpers ───────────────────────────────────────────────────────────────
    function calculateTotalRISum() {
        let total = 0;
        $('#reportsTable tbody tr').each(function() {
            const riText = $(this).find('td[data-field="ri_sum_ins"]').text().trim();
            if (riText !== 'N/A') {
                const value = parseFloat(riText.replace(/,/g, ''));
                if (!isNaN(value)) total += value;
            }
        });
        return total;
    }

    function updateFooterTotal() {
        const total = calculateTotalRISum();
        $('#reportsTable tfoot .total-ri-sum').text(total.toLocaleString('en-US'));
    }

    function formatNumber(num) {
        if (!num) return '0';
        const parsed = parseFloat(num);
        return isNaN(parsed) ? '0' : parsed.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    </script>
</body>
</html>