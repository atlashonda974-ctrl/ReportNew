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

        /* Note Tag badge on rows */
        .note-tag-btn { font-size: 0.75rem; padding: 2px 7px; }
    </style>
</head>
<body>
<div class="container mt-5">
    <x-report-header title="Get Request Note Report 1" />

    @if(request('uw_doc'))
        <div class="alert alert-info">
            <strong>Debug Info:</strong><br>
            Document Number: {{ request('uw_doc') }}<br>
        </div>
    @endif

    {{-- ================================================================ --}}
    {{-- FILTER FORM                                                       --}}
    {{-- ================================================================ --}}
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

    {{-- ================================================================ --}}
    {{-- SUMMARY + AGING ANALYSIS                                         --}}
    {{-- ================================================================ --}}
    @if(empty($data) || $data->isEmpty())
        <div class="alert alert-danger">No data available.</div>
    @else
        <div class="border border-primary rounded p-2 mb-3">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-3">
                    <h6 class="card-title text-primary mb-2">
                        <i class="bi bi-clipboard-data me-1"></i>Posting Status Analysis
                    </h6>
                    <div class="row">
                        {{-- Summary --}}
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
                                        $count      = $collection->count();
                                        $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100) : 0;
                                        $sumCedSi   = $collection->sum(fn($item) => (float)($item->CED_SI ?? 0));
                                        $formattedSum = number_format($sumCedSi);
                                        $colorClass = 'secondary';
                                        $minDays    = 0; $maxDays = PHP_INT_MAX;
                                        if      (Str::startsWith($label, '0-3'))  { $colorClass = 'success'; $minDays = 0;  $maxDays = 3; }
                                        elseif  (Str::startsWith($label, '4-7'))  { $colorClass = 'warning'; $minDays = 4;  $maxDays = 7; }
                                        elseif  (Str::startsWith($label, '8-10')) { $colorClass = 'orange';  $minDays = 8;  $maxDays = 10; }
                                        elseif  (Str::startsWith($label, '11-15')){ $colorClass = 'info';    $minDays = 11; $maxDays = 15; }
                                        elseif  (Str::startsWith($label, '16-20')){ $colorClass = 'primary'; $minDays = 16; $maxDays = 20; }
                                        elseif  (Str::startsWith($label, '20+'))  { $colorClass = 'dark';    $minDays = 21; $maxDays = PHP_INT_MAX; }
                                        if      ($count === $maxCount)            { $colorClass = 'danger'; }
                                        elseif  ($count === $minCount)            { $colorClass = 'info'; }
                                    @endphp
                                    <div class="col-6 col-sm-4">
                                        <div class="card aging-card border-{{ $colorClass }} shadow-sm h-100"
                                             data-bs-toggle="modal" data-bs-target="#filteredRecordsModal"
                                             data-label="{{ $label }}" data-count="{{ $count }}"
                                             data-percentage="{{ $percentage }}"
                                             data-min-days="{{ $minDays }}" data-max-days="{{ $maxDays }}"
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
    @endif

    {{-- ================================================================ --}}
    {{-- MAIN TABLE                                                        --}}
    {{-- ================================================================ --}}
    @if(empty($data))
        <div class="alert alert-danger">No data available.</div>
    @else

        <div class="bulk-bar">
            <button type="button" id="bulkSendEmailBtn" class="btn btn-success btn-sm" disabled>
                <i class="bi bi-envelope-fill"></i>
                Send Email to Selected &nbsp;<span class="badge bg-light text-success" id="selectedCount">0</span>
            </button>
            <span class="sending-progress d-none" id="sendingProgress"></span>
        </div>

        <table id="reportsTable" class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th data-field="action" style="white-space:nowrap;">
                        <input type="checkbox" id="selectAllCheckbox" title="Select / Deselect All">
                        &nbsp;Action
                    </th>
                    <th data-field="pdf">PDF</th>
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
                        {{-- ── ACTION COLUMN (checkbox + Note button) ── --}}
                        <td data-field="action" style="white-space:nowrap;">
                            <input type="checkbox" class="row-checkbox" style="margin-right:5px;">
                            <button
                                class="btn btn-sm note-tag-btn ms-1"
                                style="background:#fff3cd; border:1px solid #ffc107; color:#856404;"
                                data-req-note="{{ $record->GRH_REFERENCE_NO }}"
                                data-insured="{{ $record->INSURED_DESC ?? 'N/A' }}"
                                data-dept="{{ $categoryMapping[$deptCode] ?? 'N/A' }}"
                                data-doc-date="{{ $formattedDate }}"
                                title="Add Note Tag">
                                <i class="fas fa-sticky-note"></i> Note
                            </button>
                        </td>

                        {{-- ── PDF COLUMN ── --}}
                        <td data-field="pdf">
                            <button class="btn btn-primary btn-sm preview-pdf-btn me-1"
                                    data-req-note="{{ $record->GRH_REFERENCE_NO }}"
                                    data-doc-date="{{ $formattedDate }}"
                                    data-dept="{{ $categoryMapping[$deptCode] ?? 'N/A' }}"
                                    data-business-desc="{{ $record->PBC_DESC ?? 'N/A' }}"
                                    data-insured="{{ $record->INSURED_DESC ?? 'N/A' }}"
                                    data-reins-party="{{ $record->RE_COMP_DESC ?? 'N/A' }}"
                                    data-total-si="{{ is_numeric($record->TOT_SI ?? null) ? number_format($record->TOT_SI) : 'N/A' }}"
                                    data-total-pre="{{ is_numeric($record->TOT_PRE ?? null) ? number_format($record->TOT_PRE) : 'N/A' }}"
                                    data-share="{{ is_numeric($record->GRH_CEDEDSISHARE ?? null) ? number_format($record->GRH_CEDEDSISHARE, 2) . '%' : 'N/A' }}"
                                    data-ri-si="{{ is_numeric($record->CED_SI ?? null) ? number_format($record->CED_SI) : 'N/A' }}"
                                    data-ri-pre="{{ is_numeric($record->CED_PRE ?? null) ? number_format($record->CED_PRE) : 'N/A' }}"
                                    data-comm-date="{{ $record->GRH_COMMDATE ? \Carbon\Carbon::parse($record->GRH_COMMDATE)->format('d-m-Y') : 'N/A' }}"
                                    data-expiry-date="{{ $record->GRH_EXPIRYDATE ? \Carbon\Carbon::parse($record->GRH_EXPIRYDATE)->format('d-m-Y') : 'N/A' }}"
                                    data-cp="{{ $record->CP_STS ?? 'N/A' }}"
                                    data-insu-type="{{ $record->INSU_TYPE ?? 'N/A' }}"
                                    data-posted="{{ $record->GRH_POSTINGTAG ?? 'N/A' }}"
                                    data-created-by="{{ $record->CREATED_BY ?? 'N/A' }}"
                                    data-accepted-date="{{ $record->GRH_ACCEPTEDDATE ? \Carbon\Carbon::parse($record->GRH_ACCEPTEDDATE)->format('d-m-Y') : 'N/A' }}"
                                    data-warranty-period="{{ isset($record->GRH_ACCEPTEDDATE) ? \Carbon\Carbon::parse($record->GRH_ACCEPTEDDATE)->addDays(30)->format('d-m-Y') : 'N/A' }}"
                                    data-comm-percent="{{ is_numeric($record->GRH_COMMISSIONRATE ?? null) ? number_format($record->GRH_COMMISSIONRATE, 2) : 'N/A' }}"
                                    data-comm-amount="{{ is_numeric($record->COMMISSIONAMT ?? null) ? number_format($record->COMMISSIONAMT) : 'N/A' }}"
                                    data-acceptance-no="{{ $record->GRH_REINS_REF_NO ?? 'N/A' }}">
                                <i class="fas fa-eye"></i> Preview
                            </button>
                            <button class="btn btn-danger btn-sm download-pdf-btn"
                                    data-req-note="{{ $record->GRH_REFERENCE_NO }}"
                                    data-doc-date="{{ $formattedDate }}"
                                    data-dept="{{ $categoryMapping[$deptCode] ?? 'N/A' }}"
                                    data-business-desc="{{ $record->PBC_DESC ?? 'N/A' }}"
                                    data-insured="{{ $record->INSURED_DESC ?? 'N/A' }}"
                                    data-reins-party="{{ $record->RE_COMP_DESC ?? 'N/A' }}"
                                    data-total-si="{{ is_numeric($record->TOT_SI ?? null) ? number_format($record->TOT_SI) : 'N/A' }}"
                                    data-total-pre="{{ is_numeric($record->TOT_PRE ?? null) ? number_format($record->TOT_PRE) : 'N/A' }}"
                                    data-share="{{ is_numeric($record->GRH_CEDEDSISHARE ?? null) ? number_format($record->GRH_CEDEDSISHARE, 2) . '%' : 'N/A' }}"
                                    data-ri-si="{{ is_numeric($record->CED_SI ?? null) ? number_format($record->CED_SI) : 'N/A' }}"
                                    data-ri-pre="{{ is_numeric($record->CED_PRE ?? null) ? number_format($record->CED_PRE) : 'N/A' }}"
                                    data-comm-date="{{ $record->GRH_COMMDATE ? \Carbon\Carbon::parse($record->GRH_COMMDATE)->format('d-m-Y') : 'N/A' }}"
                                    data-expiry-date="{{ $record->GRH_EXPIRYDATE ? \Carbon\Carbon::parse($record->GRH_EXPIRYDATE)->format('d-m-Y') : 'N/A' }}"
                                    data-cp="{{ $record->CP_STS ?? 'N/A' }}"
                                    data-insu-type="{{ $record->INSU_TYPE ?? 'N/A' }}"
                                    data-posted="{{ $record->GRH_POSTINGTAG ?? 'N/A' }}"
                                    data-created-by="{{ $record->CREATED_BY ?? 'N/A' }}"
                                    data-accepted-date="{{ $record->GRH_ACCEPTEDDATE ? \Carbon\Carbon::parse($record->GRH_ACCEPTEDDATE)->format('d-m-Y') : 'N/A' }}"
                                    data-warranty-period="{{ isset($record->GRH_ACCEPTEDDATE) ? \Carbon\Carbon::parse($record->GRH_ACCEPTEDDATE)->addDays(30)->format('d-m-Y') : 'N/A' }}"
                                    data-comm-percent="{{ is_numeric($record->GRH_COMMISSIONRATE ?? null) ? number_format($record->GRH_COMMISSIONRATE, 2) : 'N/A' }}"
                                    data-comm-amount="{{ is_numeric($record->COMMISSIONAMT ?? null) ? number_format($record->COMMISSIONAMT) : 'N/A' }}"
                                    data-acceptance-no="{{ $record->GRH_REINS_REF_NO ?? 'N/A' }}">
                                <i class="fas fa-file-pdf"></i> Download
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
                        <td data-field="total_sum_ins" class="numeric" style="text-align:right;">
                            {{ is_numeric($record->TOT_SI ?? null) ? number_format($record->TOT_SI) : 'N/A' }}
                        </td>
                        <td data-field="ri_sum_ins" class="numeric" style="text-align:right;">
                            {{ is_numeric($record->CED_SI ?? null) ? number_format($record->CED_SI) : 'N/A' }}
                        </td>
                        <td data-field="share" class="numeric" style="text-align:right;">
                            {{ is_numeric($record->GRH_CEDEDSISHARE ?? null) ? number_format($record->GRH_CEDEDSISHARE, 2) . '%' : 'N/A' }}
                        </td>
                        <td data-field="total_premium" class="numeric" style="text-align:right;">
                            {{ is_numeric($record->TOT_PRE ?? null) ? number_format($record->TOT_PRE) : 'N/A' }}
                        </td>
                        <td data-field="ri_premium" class="numeric" style="text-align:right;">
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
                            @else N/A @endif
                        </td>
                        <td data-field="commission_percent" class="numeric" style="text-align:right;">
                            {{ is_numeric($record->GRH_COMMISSIONRATE ?? null) ? number_format($record->GRH_COMMISSIONRATE, 2) : 'N/A' }}
                        </td>
                        <td data-field="commission_amount" class="numeric" style="text-align:right;">
                            {{ is_numeric($record->COMMISSIONAMT ?? null) ? number_format($record->COMMISSIONAMT) : 'N/A' }}
                        </td>
                        <td data-field="acceptance_no">{{ $record->GRH_REINS_REF_NO ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-active">
                    <td colspan="9" class="text-end fw-bold">Total RI Sum Insured:</td>
                    <td class="fw-bold" id="totalRiSumIns">0</td>
                    <td colspan="14"></td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- ================================================================ --}}
    {{-- NOTE TAG MODAL                                                    --}}
    {{-- ================================================================ --}}
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
                    {{-- Record Info Banner --}}
                    <div class="rounded p-3 mb-4" style="background:#f8f9fa; border-left:4px solid #ffc107;">
                        <div class="row g-1">
                            <div class="col-6">
                                <small class="text-muted d-block" style="font-size:0.7rem;">REQUEST NOTE #</small>
                                <strong id="ntRefNo" class="text-dark" style="font-size:0.95rem;">—</strong>
                            </div>

                        </div>
                    </div>

                    {{-- Hidden field --}}
                    <input type="hidden" id="ntHiddenRef">

                    {{-- Action dropdown --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Action <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="ntAction">
                            <option value="">— Select Action —</option>
                            <option value="revise"> Revise</option>
                            <option value="cancel"> Cancel</option>
                        </select>
                        <div class="invalid-feedback" id="ntActionError">Please select an action.</div>
                    </div>

                    {{-- Remarks textarea --}}
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Remarks</label>
                        <textarea
                            class="form-control"
                            id="ntRemarks"
                            rows="4"
                            maxlength="2000"
                            placeholder="Enter remarks (optional)…"
                            style="resize:vertical;"></textarea>
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

    {{-- ================================================================ --}}
    {{-- REQUEST NOTE DETAILS MODAL                                        --}}
    {{-- ================================================================ --}}
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

    {{-- ================================================================ --}}
    {{-- BULK EMAIL MODAL                                                  --}}
    {{-- ================================================================ --}}
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
                        Each will receive its own email with an individual PDF attached.
                    </div>
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
                    <div class="mb-3">
                        <label class="form-label">To: <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="bulkTo" placeholder="recipient@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CC:</label>
                        <input type="email" class="form-control" id="bulkCc" placeholder="cc@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject Prefix: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="bulkSubject" value="Reinsurance Request Note:" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Body: <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="bulkBody" rows="7" required>Dear Sir/Madam,

Please find below details for Request Note.
Please find the attached PDF document for detailed information.

Regards,</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="me-auto text-muted small fw-semibold" id="bulkModalStatus"></div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="confirmBulkSendBtn">
                        <i class="bi bi-send-fill me-1"></i>Send All Emails
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- SINGLE EMAIL MODAL                                                --}}
    {{-- ================================================================ --}}
    <div id="emailModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">To:</label>
                        <input type="email" class="form-control" id="to" placeholder="Recipient's email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CC:</label>
                        <input type="email" class="form-control" id="cc" placeholder="CC email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject:</label>
                        <input type="text" class="form-control" id="subject" placeholder="Email subject" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Body:</label>
                        <textarea class="form-control" id="body" rows="4" placeholder="Email body" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="sendEmailBtn">Send Email</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- FILTERED RECORDS MODAL (Aging Cards)                             --}}
    {{-- ================================================================ --}}
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

    {{-- ================================================================ --}}
    {{-- PDF PREVIEW MODAL                                                 --}}
    {{-- ================================================================ --}}
    <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">PDF Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfPreviewFrame" style="width:100%; height:600px; border:none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="downloadFromPreview">Download</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- SCRIPTS                                                           --}}
    {{-- ================================================================ --}}
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
    let currentRowDataForEmail = null;
    let currentRow             = null;
    let selectedRowsData       = [];

    $(document).ready(function () {

        $('.select2').select2({ placeholder: "Select a branch", allowClear: true, width: '69%' });

        // ── DataTable ───────────────────────────────────────────────────
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
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
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
                        let sum = data.body.reduce((acc, row) => acc + intVal(row[7]), 0);
                        totals[7] = sum.toLocaleString('en-US');
                        totals[0] = 'Total RI Sum Insured:';
                        data.body.push(totals);
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
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
                        let sum = doc.content[1].table.body.slice(1).reduce((acc, row) => acc + intVal(row[7].text), 0);
                        totals[7] = { text: sum.toLocaleString('en-US'), bold: true };
                        totals[0] = 'Total RI Sum Insured:';
                        doc.content[1].table.body.push(totals);
                    }
                }
            ],
            footerCallback: function (row, data, start, end, display) {
                var api    = this.api();
                var intVal = i => typeof i === 'string' ? parseFloat(i.replace(/[^\d.-]/g, '')) || 0 : typeof i === 'number' ? i : 0;
                var total  = api.column(9, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                $(api.column(9).footer()).html(total.toLocaleString('en-US'));
            },
            initComplete: function () {
                this.api().draw();
                this.api().columns.adjust();
                $('.dataTables_filter input').attr('placeholder', 'Search...');
                $('.dataTables_filter').css({ 'margin-left': '5px', 'margin-right': '5px' });
                $('.dt-buttons').css('margin-left', '5px');
            },
            drawCallback: function () { this.api().columns.adjust(); }
        });

        $('a[title="Reset"]').on('click', function () { setTimeout(() => table.draw(), 100); });

        // ── Checkboxes ──────────────────────────────────────────────────
        $('#selectAllCheckbox').on('change', function () {
            const checked = $(this).prop('checked');
            $('#reportsTable tbody tr').each(function () {
                $(this).find('.row-checkbox').prop('checked', checked);
            });
            updateBulkBar();
        });

        $(document).on('change', '.row-checkbox', function () {
            updateBulkBar();
            const total   = $('#reportsTable tbody tr').length;
            const checked = $('#reportsTable tbody .row-checkbox:checked').length;
            $('#selectAllCheckbox')
                .prop('indeterminate', checked > 0 && checked < total)
                .prop('checked', total > 0 && checked === total);
        });

        function updateBulkBar () {
            const count = $('#reportsTable tbody .row-checkbox:checked').length;
            $('#selectedCount').text(count);
            $('#bulkSendEmailBtn').prop('disabled', count === 0);
            count > 0 ? $('#bulkHint').addClass('d-none') : $('#bulkHint').removeClass('d-none');
        }

        // ── Collect checked rows ─────────────────────────────────────────
        function collectSelectedRows () {
            const rows = [];
            $('#reportsTable tbody tr').each(function () {
                if (!$(this).find('.row-checkbox').prop('checked')) return;
                const $r = $(this), $pBtn = $r.find('.preview-pdf-btn');
                rows.push({
                    reqNote:           $r.find('td[data-field="request_note"]').text().trim(),
                    docDate:           $r.find('td[data-field="doc_date"]').text().trim(),
                    dept:              $r.find('td[data-field="dept"]').text().trim(),
                    businessDesc:      $r.find('td[data-field="business_desc"] .truncate-text').attr('title') || $r.find('td[data-field="business_desc"]').text().trim(),
                    insured:           $r.find('td[data-field="insured"] .truncate-text').attr('title') || $r.find('td[data-field="insured"]').text().trim(),
                    reinsParty:        $r.find('td[data-field="reins_party"] .truncate-text').attr('title') || $r.find('td[data-field="reins_party"]').text().trim(),
                    totalSumIns:       $r.find('td[data-field="total_sum_ins"]').text().trim(),
                    riSumIns:          $r.find('td[data-field="ri_sum_ins"]').text().trim(),
                    share:             $r.find('td[data-field="share"]').text().trim(),
                    totalPremium:      $r.find('td[data-field="total_premium"]').text().trim(),
                    riPremium:         $r.find('td[data-field="ri_premium"]').text().trim(),
                    commDate:          $r.find('td[data-field="comm_date"]').text().trim(),
                    expiryDate:        $r.find('td[data-field="expiry_date"]').text().trim(),
                    cp:                $r.find('td[data-field="cp"]').text().trim(),
                    convTakaful:       $r.find('td[data-field="conv_takaful"]').text().trim(),
                    posted:            $r.find('td[data-field="posted"]').text().trim(),
                    userName:          $r.find('td[data-field="user_name"]').text().trim(),
                    acceptanceDate:    $r.find('td[data-field="acceptance_date"]').text().trim(),
                    warrantyPeriod:    $r.find('td[data-field="warranty_period"]').text().trim(),
                    commissionPercent: $r.find('td[data-field="commission_percent"]').text().trim(),
                    commissionAmount:  $r.find('td[data-field="commission_amount"]').text().trim(),
                    acceptanceNo:      $r.find('td[data-field="acceptance_no"]').text().trim(),
                    totalSi:  $pBtn.data('total-si'),
                    riSi:     $pBtn.data('ri-si'),
                    riPre:    $pBtn.data('ri-pre'),
                    totalPre: $pBtn.data('total-pre'),
                });
            });
            return rows;
        }

        // ── Bulk email modal ─────────────────────────────────────────────
        $('#bulkSendEmailBtn').on('click', function () {
            selectedRowsData = collectSelectedRows();
            $('#bulkSelectedCountDisplay').text(selectedRowsData.length);
            const $tbody = $('#bulkPreviewBody').empty();
            selectedRowsData.forEach((r, i) => {
                $tbody.append(`<tr><td>${i+1}</td><td><strong>${r.reqNote}</strong></td><td>${r.insured}</td><td>${r.dept}</td><td>${r.docDate}</td></tr>`);
            });
            $('#bulkModalStatus').text('');
            $('#confirmBulkSendBtn').prop('disabled', false).html('<i class="bi bi-send-fill me-1"></i>Email Click');
            $('#bulkEmailModal').modal('show');
        });

        $('#confirmBulkSendBtn').on('click', async function () {
            const to      = $('#bulkTo').val().trim();
            const cc      = $('#bulkCc').val().trim();
            const subject = $('#bulkSubject').val().trim();
            const body    = $('#bulkBody').val().trim();

            if (!to)      { alert('Please enter a recipient email address.'); return; }
            if (!subject) { alert('Please enter a subject prefix.'); return; }
            if (!body)    { alert('Please enter an email body.'); return; }

            const $btn      = $(this);
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Generating PDFs…');
            $('#bulkModalStatus').html('<i class="bi bi-hourglass-split me-1"></i> Generating PDFs, please wait…');

            try {
                const pdfBase64Array = await Promise.all(
                    selectedRowsData.map(record =>
                        new Promise((resolve, reject) => {
                            try {
                                pdfMake.createPdf(createPdfDefinition(record)).getBase64(resolve);
                            } catch (e) { reject(e); }
                        })
                    )
                );

                const notesList   = selectedRowsData.map(r => r.reqNote).join(', ');
                const fullSubject = `${subject} [${notesList}]`;

                $btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Sending email…');
                $('#bulkModalStatus').html('<i class="bi bi-hourglass-split me-1"></i> Sending one email with all PDFs…');

                const recordsWithPdf = selectedRowsData.map((record, i) => ({
                    ...record,
                    pdfBase64:   pdfBase64Array[i],
                    pdfFilename: `Request_Note_${record.reqNote}.pdf`
                }));

                const response = await $.ajax({
                    url: "{{ route('send.email') }}",
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    traditional: false,
                    data: { _token: csrfToken, to, cc, subject: fullSubject, body, records: recordsWithPdf, pdfs: pdfBase64Array }
                });

                $btn.prop('disabled', false).html('<i class="bi bi-send-fill me-1"></i>Email Click');
                $('#bulkModalStatus').text('');
                $('#bulkEmailModal').modal('hide');
                $('.row-checkbox, #selectAllCheckbox').prop('checked', false).prop('indeterminate', false);
                updateBulkBar();

                if (response.success) { alert('Email sent!'); location.reload(); }
                else                  { alert('❌ Error: ' + response.message); }

            } catch (err) {
                $btn.prop('disabled', false).html('<i class="bi bi-send-fill me-1"></i>Email Click');
                $('#bulkModalStatus').text('');
                alert('❌ Failed: ' + (err.responseJSON?.message || err.message || 'Unknown error'));
            }
        });

        function sendEmailRequest (emailData, $btn) {
            $.ajax({
                url: "{{ route('send.email') }}", method: 'POST',
                headers: { 'X-CSRF-TOKEN': emailData._token },
                data: emailData,
                success: function (response) {
                    if (response.success) {
                        alert('Email sent successfully with PDF attachment!');
                        $('#emailModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr) { alert(xhr.responseJSON?.message || 'Failed to send email'); },
                complete: function () { $btn.prop('disabled', false).text('Send Email'); }
            });
        }

        // ── PDF buttons ──────────────────────────────────────────────────
        $(document).on('click', '.preview-pdf-btn', function () {
            pdfMake.createPdf(createPdfDefinition($(this).data())).open();
        });

        $(document).on('click', '.download-pdf-btn', function () {
            const d = $(this).data();
            pdfMake.createPdf(createPdfDefinition(d)).download(`Request_Note_${d.reqNote || 'document'}.pdf`);
        });

        function createPdfDefinition (rowData) {
            return {
                content: [
                    { text: 'Atlas Insurance Ltd.', style: 'header', alignment: 'center', margin: [0, 5, 0, 5] },
                    { text: `${rowData.dept || 'Fire'} Reinsurance Request Note`, style: 'header', alignment: 'center', margin: [0, 0, 0, 10] },
                    {
                        table: { widths: ['*', '*', '*'], body: [[ '', '', {
                            table: { widths: ['auto', '*'], body: [
                                ['Date:', rowData.docDate || '10/01/2025'],
                                ['Request Note#:', rowData.reqNote || 'N/A'],
                                ['Base Request Note#:', '-']
                            ]}, layout: 'noBorders'
                        }]]}, layout: 'noBorders', margin: [0, 0, 0, 10]
                    },
                    {
                        table: { widths: ['*'], body: [[{ stack: [{
                            table: { widths: ['auto', '*'], body: [
                                ['CLASS OF BUSINESS', rowData.businessDesc || 'N/A'],
                                ['DESCRIPTION', 'Co-Insurers Panel:\n   IGI............. 30%\n   Askari.......... 10%\n   Habib........... 10%\n   Atlas........... 20%\n   Shaheen ........ 10%\n   TPL............. 20%\n   -----------------------\n   Total........... 100%\n\nDeductible: 5% of loss amount minimum Rs. 250,000/- on each & every loss.\nPolicy issued by cancelling & replacing Cover Note # 2024ISB-IIFCMIIT00177'],
                                ['INSURED NAME', rowData.insured || 'N/A'],
                                ['NTN Number', '4505300-8'],
                                ['C NOTE/POLICY#', '2025ISB-IIFCMIIP00002'],
                                ['SITUATION', 'Plot no.A-19 & A-20, M-3 Industrial City Road, M-3, Industrial City, Faisalabad.'],
                                ['CONSTRUCTION CLASS', '1st Class'],
                                ['RI Reference', 'null'],
                                ['PERIOD OF INSURANCE', rowData.commDate],
                                ['PERIOD OF RE-INSURANCE', rowData.expiryDate],
                                ['SUM INSURED (Our Share)', rowData.totalSi ? rowData.totalSi.toLocaleString() : 'N/A'],
                                ['ATLAS SHARE', rowData.share ? parseFloat(rowData.share).toFixed(2) + '%' : 'N/A'],
                                ['GROSS PREMIUM RATE', rowData.grossPremiumRate ? parseFloat(rowData.grossPremiumRate).toFixed(4) + '%' : '0.0872%'],
                                ['RI COMMISSION', rowData.commissionPercent ? parseFloat(rowData.commissionPercent).toFixed(2) + '%' : '25.00%']
                            ]}, layout: 'noBorders', margin: [0, 0, 0, 15]
                        }]}]]},
                        layout: { hLineWidth: () => 1, vLineWidth: () => 1, hLineColor: () => 'black', vLineColor: () => 'black', paddingLeft: () => 6, paddingRight: () => 6, paddingTop: () => 6, paddingBottom: () => 6 }
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
                        ], margin: [0, 10, 0, 10]
                    },
                    {
                        table: { widths: ['*', 50, 110, '*'], body: [
                            [
                                { text: 'REINSURER',          style: 'tableHeader', border: [true,true,true,true] },
                                { text: 'FAC RI OFFERED',     style: 'tableHeader', colSpan: 2, alignment: 'center', border: [true,true,true,true] },
                                {},
                                { text: 'SIGNATURE OF ACCEPTANCE', style: 'tableHeader', border: [true,true,true,true] }
                            ],
                            [
                                { text: '', border: [true,true,true,false] },
                                { text: '%',      style: 'tableHeader', border: [true,true,true,true] },
                                { text: 'AMOUNT', alignment: 'right', style: 'tableHeader', border: [true,true,true,true] },
                                { text: '', border: [true,true,true,false] }
                            ],
                            [
                                { text: rowData.reinsParty, alignment: 'center', border: [true,false,true,true] },
                                { text: rowData.share || '14.70%', alignment: 'center', border: [true,false,true,true] },
                                { text: rowData.riSi || '239,478,694', alignment: 'right',  border: [true,false,true,true] },
                                { text: '', border: [true,false,true,true] }
                            ]
                        ]},
                        layout: {
                            hLineWidth: (i, node) => (i === 0 || i === 1 || i === 2 || i === node.table.body.length) ? 1 : 0,
                            vLineWidth: () => 1, hLineColor: () => 'black', vLineColor: () => 'black',
                            paddingLeft: () => 5, paddingRight: () => 5, paddingTop: () => 5, paddingBottom: () => 5
                        }, margin: [0, 0, 0, 10]
                    },
                    { text: 'For And On Behalf Of\nATLAS INSURANCE LTD.', alignment: 'right', margin: [0, 0, 0, 5] },
                    { text: 'Page 1 of 1', alignment: 'center', margin: [0, 5, 0, 0] }
                ],
                styles: {
                    header:      { fontSize: 12, bold: true },
                    tableHeader: { bold: true, fontSize: 10, fillColor: '#f0f0f0', alignment: 'center' }
                },
                defaultStyle: { fontSize: 10 }
            };
        }

        // ================================================================
        // NOTE TAG MODAL — open, char counter, submit via AJAX
        // ================================================================

        // Open modal and populate banner
        $(document).on('click', '.note-tag-btn', function () {
            const $btn = $(this);
            $('#ntHiddenRef').val($btn.data('req-note'));
            $('#ntRefNo').text($btn.data('req-note'));


            // Reset form state
            $('#ntAction').val('').removeClass('is-invalid');
            $('#ntRemarks').val('');
            $('#ntCharCount').text('0');
            $('#ntSubmitError').addClass('d-none');
            $('#ntSubmitErrorMsg').text('');
            $('#ntSubmitBtn')
                .prop('disabled', false)
                .html('<i class="fas fa-save me-1"></i>Save Note');

            $('#noteTagModal').modal('show');
        });

        // Live character counter
        $('#ntRemarks').on('input', function () {
            $('#ntCharCount').text($(this).val().length);
        });

        // Clear validation state when action changes
        $('#ntAction').on('change', function () {
            $(this).removeClass('is-invalid');
            $('#ntSubmitError').addClass('d-none');
        });

        // Submit Note Tag via AJAX

          $('#ntSubmitBtn').on('click', function () {
    const refNo   = $('#ntHiddenRef').val();
    const action  = $('#ntAction').val();
    const remarks = $('#ntRemarks').val().trim();

    if (!action) {
        $('#ntAction').addClass('is-invalid');
        $('#ntSubmitError').removeClass('d-none');
        $('#ntSubmitErrorMsg').text('Please select an action (Revise or Cancel).');
        return;
    }

    const $btn = $(this);
    $btn.prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span>Saving…');
    $('#ntSubmitError').addClass('d-none');

    $.ajax({
        url:     "{{ route('reins.tag.store') }}",
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: {
            GRH_REFERENCE_NO: refNo,
            tag_action:       action,
            remarks:          remarks
        },
        success: function (response) {
            if (response.success) {
                $('#noteTagModal').modal('hide');
                location.reload();
            } else {
                $('#ntSubmitError').removeClass('d-none');
                $('#ntSubmitErrorMsg').text(response.message || 'Failed to save note.');
                $btn.prop('disabled', false)
                    .html('<i class="fas fa-save me-1"></i>Save Note');
            }
        },
        error: function (xhr) {
            const msg = xhr.responseJSON?.errors
                ? Object.values(xhr.responseJSON.errors).flat().join(' ')
                : (xhr.responseJSON?.message || 'Server error. Please try again.');
            $('#ntSubmitError').removeClass('d-none');
            $('#ntSubmitErrorMsg').text(msg);
            $btn.prop('disabled', false)
                .html('<i class="fas fa-save me-1"></i>Save Note');
        }
    });
});

        // ── Aging card modal ─────────────────────────────────────────────
        const filteredRecordsModal = new bootstrap.Modal(
            document.getElementById('filteredRecordsModal'),
            { backdrop: 'static', keyboard: false }
        );
        let allFilteredRows = [];

        function calculateDaysDiff (docDate) {
            try {
                if (!docDate || docDate === 'N/A' || docDate === 'Invalid Date') return -1;
                const [day, month, year] = docDate.split('-').map(Number);
                const parsed = new Date(year, month - 1, day);
                if (isNaN(parsed.getTime())) return -1;
                const diff = Math.floor((new Date() - parsed) / 86400000);
                return diff >= 0 ? diff : -1;
            } catch (e) { return -1; }
        }

        $('.aging-card').on('click', function () {
            const minDays = parseInt($(this).data('min-days'));
            const maxDays = parseInt($(this).data('max-days'));
            const label   = $(this).data('label');

            $('#filteredRecordsBody').empty();
            allFilteredRows = [];
            let riTotal = 0;

            $('#reportsTable tbody tr').each(function () {
                const $row    = $(this);
                const docDate = $row.find('td[data-field="doc_date"]').text().trim();
                const riText  = $row.find('td[data-field="ri_sum_ins"]').text().trim();
                const ri      = riText === 'N/A' ? 0 : (parseFloat(riText.replace(/,/g, '')) || 0);
                const days    = calculateDaysDiff(docDate);

                if (days >= minDays && days <= maxDays) {
                    const reqNote     = $row.find('td[data-field="request_note"]').text().trim();
                    const dept        = $row.find('td[data-field="dept"]').text().trim();
                    const bizDescFull = $row.find('td[data-field="business_desc"] .truncate-text').attr('title') || $row.find('td[data-field="business_desc"]').text().trim();
                    const insuredFull = $row.find('td[data-field="insured"] .truncate-text').attr('title')       || $row.find('td[data-field="insured"]').text().trim();
                    const reinsFull   = $row.find('td[data-field="reins_party"] .truncate-text').attr('title')   || $row.find('td[data-field="reins_party"]').text().trim();
                    const totSi       = $row.find('td[data-field="total_sum_ins"]').text().trim();
                    const riSi        = $row.find('td[data-field="ri_sum_ins"]').text().trim();
                    const share       = $row.find('td[data-field="share"]').text().trim();
                    const totPre      = $row.find('td[data-field="total_premium"]').text().trim();
                    const riPre       = $row.find('td[data-field="ri_premium"]').text().trim();
                    const commDate    = $row.find('td[data-field="comm_date"]').text().trim();
                    const expiryDate  = $row.find('td[data-field="expiry_date"]').text().trim();
                    const cp          = $row.find('td[data-field="cp"]').text().trim();
                    const convTak     = $row.find('td[data-field="conv_takaful"]').text().trim();
                    const posted      = $row.find('td[data-field="posted"]').text().trim();
                    const userName    = $row.find('td[data-field="user_name"]').text().trim();
                    const accDate     = $row.find('td[data-field="acceptance_date"]').text().trim();
                    const warPeriod   = $row.find('td[data-field="warranty_period"]').text().trim();
                    const commissionPercent     = $row.find('td[data-field="commission_percent"]').text().trim();
                    const commAmt     = $row.find('td[data-field="commission_amount"]').text().trim();
                    const accNo       = $row.find('td[data-field="acceptance_no"]').text().trim();

                    const $newRow = $(`<tr>
                        <td>${reqNote}</td><td>${docDate}</td><td>${dept}</td>
                        <td><span title="${bizDescFull}">${bizDescFull}</span></td>
                        <td><span title="${insuredFull}">${insuredFull}</span></td>
                        <td><span title="${reinsFull}">${reinsFull}</span></td>
                        <td class="text-end">${totSi}</td><td class="text-end">${riSi}</td>
                        <td class="text-end">${share}</td><td class="text-end">${totPre}</td>
                        <td class="text-end">${riPre}</td><td>${commDate}</td><td>${expiryDate}</td>
                        <td>${cp}</td><td>${convTak}</td><td>${posted}</td><td>${userName}</td>
                        <td>${accDate}</td><td>${warPeriod}</td>
                        <td class="text-end">${commissionPercent}</td><td class="text-end">${commAmt}</td>
                        <td>${accNo}</td>
                    </tr>`);

                    $('#filteredRecordsBody').append($newRow);
                    allFilteredRows.push({ element: $newRow, riSumIns: ri });
                    riTotal += ri;
                }
            });

            $('#filteredRecordsTable tfoot').remove();
            $('#filteredRecordsTable').append(`
                <tfoot>
                    <tr class="table-active">
                        <td colspan="7" class="text-end fw-bold">Total RI Sum Insured:</td>
                        <td class="fw-bold">${riTotal.toLocaleString('en-US')}</td>
                        <td colspan="14"></td>
                    </tr>
                </tfoot>`);

            $('#filteredRecordsTitle').text(`${label} — ${allFilteredRows.length} records`);
            $('#modalRecordCount').text(`Showing ${allFilteredRows.length} records`);
            filteredRecordsModal.show();
        });

        $('#modalSearch').on('keyup', function () {
            const val = $(this).val().toLowerCase();
            let visible = 0, riTotal = 0;
            allFilteredRows.forEach(r => {
                const show = r.element.text().toLowerCase().includes(val);
                r.element.toggle(show);
                if (show) { visible++; riTotal += r.riSumIns; }
            });
            $('#filteredRecordsTable tfoot td.fw-bold').last().text(riTotal.toLocaleString('en-US'));
            $('#modalRecordCount').text(`Showing ${visible} filtered records`);
        });

        $('#clearModalSearch').on('click', function () {
            $('#modalSearch').val('');
            let riTotal = 0;
            allFilteredRows.forEach(r => { r.element.show(); riTotal += r.riSumIns; });
            $('#filteredRecordsTable tfoot td.fw-bold').last().text(riTotal.toLocaleString('en-US'));
            $('#modalRecordCount').text(`Showing ${allFilteredRows.length} records`);
        });

        // ── Request Note click → detailsModal ───────────────────────────
        $(document).on('click', '.open-modal', function (e) {
            e.preventDefault();
            const reqNote = $(this).data('req-note');
            $('#modalBody').html('<p class="text-center text-muted py-4">Loading...</p>');
            $('#detailsModal').modal('show');

            $.ajax({
                url: 'http://192.168.170.24/dashboardApi/reins/rqn/get_notes_dtl.php',
                method: 'GET',
                data: { req_note: reqNote },
                dataType: 'json',
                success: function (data) {
                    if (!data || data.length === 0) {
                        $('#modalBody').html('<p class="text-center text-muted">No records found.</p>');
                        return;
                    }
                    let html = '<div class="table-responsive"><table class="table table-bordered table-striped table-sm"><thead class="table-light"><tr>';
                    const headers = [
                        'Doc No','Insured','Reinsurer','Share','Commission','Accepted Date',
                        'Comm. Date','Expiry Date','Issue Date','Ceded SI','Ceded Premium',
                        'SI','Premium','CP No','CP Date','Ref No'
                    ];
                    headers.forEach(h => html += `<th>${h}</th>`);
                    html += '</tr></thead><tbody>';
                    data.forEach(function (item) {
                        const r = typeof item === 'string' ? JSON.parse(item) : item;
                        html += `<tr>
                            <td>${r.GRD_CEDINGDOCNO || ''}</td>
                            <td>${r.INSURED_DESC || ''}</td>
                            <td>${r.RE_COMP_DESC || ''}</td>
                            <td>${r.GRH_CEDEDSISHARE || ''}%</td>
                            <td>${r.GRH_COMMISSIONRATE || ''}%</td>
                            <td>${r.GRH_ACCEPTEDDATE || ''}</td>
                            <td>${r.GDH_COMMDATE || ''}</td>
                            <td>${r.GDH_EXPIRYDATE || ''}</td>
                            <td>${r.GDH_ISSUEDATE || ''}</td>
                            <td class="text-end">${formatNumber(r['SUM(GRS_CEDEDSI)'])}</td>
                            <td class="text-end">${formatNumber(r['SUM(GRS_CEDEDPREM)'])}</td>
                            <td class="text-end">${formatNumber(r['SUM(GRS_TOTALSI)'])}</td>
                            <td class="text-end">${formatNumber(r['SUM(GRS_TOTALPREM)'])}</td>
                            <td>${r.GCP_DOC_REFERENCENO || ''}</td>
                            <td>${r.CREATE_DATE || ''}</td>
                            <td>${r.GCT_THEIR_REF_NO || ''}</td>
                        </tr>`;
                    });
                    html += '</tbody></table></div>';
                    $('#modalBody').html(html);
                },
                error: function () {
                    $('#modalBody').html('<p class="text-center text-danger">Error loading data. Please try again.</p>');
                }
            });
        });

    }); // end document.ready

    function formatNumber(num) {
        if (!num) return '0';
        return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    </script>
</div>
</body>
</html>