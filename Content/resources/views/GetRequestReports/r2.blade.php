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
        .summary-card, .aging-card {
            border-radius: 6px;
            font-size: 0.8rem;
        }
        .summary-card .fw-bold, 
        .aging-card .fw-bold {
            font-size: 1rem;
        }
        .summary-card small, 
        .aging-card small {
            font-size: 0.7rem;
        }
        .progress { height: 4px; }
        .card-title { font-size: 0.95rem !important; }
        .time-filter-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .time-tab {
    text-decoration: none !important;   /* remove underline */
    border: none !important;            /* remove any border */
    box-shadow: none;                   /* remove bottom shadow line */
}
        .time-tab {
            flex: 1;
            min-width: 80px;
            max-width: 100px;
            border-radius: 8px;
            padding: 8px 5px;
            text-align: center;
            transition: all 0.2s;
            font-size: 14px;
            color: #000;
        }
        .time-tab:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
        .time-tab.active {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white !important;
        }
        .highlight-red { background-color: #ffcccc !important; }
        .note-tag-btn { 
            font-size: 0.75rem; 
            padding: 2px 7px; 
        }
        .aging-card { cursor: pointer; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <x-report-header title="Get Request Note Report 2" />

        @if(request('uw_doc'))
        <div class="alert alert-info">
            <strong>Debug Info:</strong><br>
            Document Number: {{ request('uw_doc') }}
        </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-6">
                <form method="GET" action="{{ url('/r2') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                            <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', $start_date) }}">
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', $end_date) }}">
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <label for="new_category" class="form-label me-2" style="white-space: nowrap; width: 100px;">Departments</label>
                            <select name="new_category" id="new_category" class="form-control select2">
                                <option value="">All Departments</option>
                                <option value="Fire" {{ request('new_category') == 'Fire' ? 'selected' : '' }}>Fire</option>
                                <option value="Marine" {{ request('new_category') == 'Marine' ? 'selected' : '' }}>Marine</option>
                                <option value="Motor" {{ request('new_category') == 'Motor' ? 'selected' : '' }}>Motor</option>
                                <option value="Miscellaneous" {{ request('new_category') == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                                <option value="Health" {{ request('new_category') == 'Health' ? 'selected' : '' }}>Health</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <button type="submit" class="btn btn-outline-primary me-2"><i class="bi bi-funnel-fill"></i> Filter</button>
                            <a href="{{ url('/r2') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-6">
                <div class="card time-filter-card">
                    <div class="card-body">
                        <h5 class="card-title mb-2" style="font-size: 1.2rem;">Time Filter</h5>
                        <div class="d-flex flex-wrap gap-1 justify-content-between">
                            @foreach (['all','2days','5days','7days','10days','15days','15plus'] as $key)
                                <a href="{{ url('/r2?time_filter=' . $key . '&start_date=' . request('start_date') . '&end_date=' . request('end_date') . '&new_category=' . request('new_category')) }}" 
                                   class="time-tab {{ $selected_time_filter == $key ? 'active' : 'bg-light' }}">
                                    <div class="fw-bold" style="font-size: 0.9rem;">
                                        {{ $key === 'all' ? 'All' : ($key === '15plus' ? '15+' : str_replace('days','d',$key)) }}
                                    </div>
                                    <div class="small" style="font-size: 0.8rem;">{{ $counts[$key] ?? 0 }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(empty($data) || $data->isEmpty())
            <div class="alert alert-danger">No data available.</div>
        @else
            <!-- Summary & Aging -->
            <div class="border border-primary rounded p-2 mb-3">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body p-3">
                        <h6 class="card-title text-primary mb-2">
                            <i class="bi bi-clipboard-data me-1"></i>Posting Status Analysis
                        </h6>
                        <div class="row">
                            <!-- Summary Cards -->
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
                                        </div>
                                        <div class="card border-0 shadow-sm summary-card flex-fill me-2">
                                            <div class="card-body py-2 px-2">
                                                <small class="text-muted">Not Posted</small>
                                                <div class="fw-bold text-danger">{{ $notPostedCount }}</div>
                                                <small class="text-muted">Sum: {{ number_format($notPostedSumInsured) }}</small>
                                            </div>
                                        </div>
                                        <div class="card border-0 shadow-sm summary-card flex-fill">
                                            <div class="card-body py-2 px-2">
                                                <small class="text-muted">Posted %</small>
                                                <div class="fw-bold text-info">
                                                    {{ $totalCount > 0 ? round((($totalCount - $notPostedCount) / $totalCount) * 100) : 0 }}%
                                                </div>
                                                <div class="progress" style="height: 5px;">
                                                    <div class="progress-bar bg-info" style="width: {{ $totalCount > 0 ? round((($totalCount - $notPostedCount) / $totalCount) * 100) : 0 }}%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aging Analysis -->
                            <div class="col-md-8">
                                <h6 class="text-muted mb-2"><i class="bi bi-bar-chart-fill me-1"></i>Aging Analysis</h6>
                                <div class="row g-2">
                                    @php
                                        $groupedCollection = collect($groupedByAging);
                                        $maxCount = $groupedCollection->max(fn($group) => $group->count());
                                        $minCount = $groupedCollection->min(fn($group) => $group->count());
                                    @endphp
                                    @foreach ($groupedByAging as $label => $collection)
                                        @php
                                            $count = $collection->count();
                                            $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100, 2) : 0;
                                            $sumCedSi = $collection->sum(fn($item) => (float)($item->CED_SI ?? 0));
                                            $formattedSum = number_format($sumCedSi);
                                            $colorClass = 'secondary';
                                            $minDays = 0; $maxDays = PHP_INT_MAX;

                                            if (Str::startsWith($label, '0-3')) { $colorClass = 'success'; $minDays = 0; $maxDays = 3; }
                                            elseif (Str::startsWith($label, '4-7')) { $colorClass = 'warning'; $minDays = 4; $maxDays = 7; }
                                            elseif (Str::startsWith($label, '8-10')) { $colorClass = 'warning'; $minDays = 8; $maxDays = 10; }
                                            elseif (Str::startsWith($label, '11-15')) { $colorClass = 'info'; $minDays = 11; $maxDays = 15; }
                                            elseif (Str::startsWith($label, '16-20')) { $colorClass = 'primary'; $minDays = 16; $maxDays = 20; }
                                            elseif (Str::startsWith($label, '20+')) { $colorClass = 'dark'; $minDays = 21; $maxDays = PHP_INT_MAX; }

                                            if ($count === $maxCount && $maxCount > 0) $colorClass = 'danger';
                                            elseif ($count === $minCount && $minCount > 0) $colorClass = 'info';
                                        @endphp
                                        <div class="col-6 col-sm-4">
                                            <div class="card aging-card border-{{ $colorClass }} shadow-sm h-100"
                                                 data-label="{{ $label }}"
                                                 data-min-days="{{ $minDays }}"
                                                 data-max-days="{{ $maxDays }}">
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

            <!-- Main Table with Action Column -->
            <table id="reportsTable" class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Action</th>
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
                        <th data-field="conv_takaful">Conventional/Takaful</th>
                        <th data-field="posted">Posted</th>
                        <th data-field="user_name">User Name</th>
                        <th data-field="acceptance_date">Acceptance Date</th>
                        <th data-field="warranty_period">Warranty Period</th>
                        <th data-field="commission_percent">Commission Percent</th>
                        <th data-field="commission_amount">Commission Amount</th>
                        <th data-field="acceptance_no">Acceptance No</th>
                        <th data-field="plc_loc_code">PLC LOC CODE</th>
                        <th data-field="pbc_busiclass_code">PBC BUSICLASS CODE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                        @php
                            $formattedDate = isset($record->GRH_DOCUMENTDATE) 
                                ? \Carbon\Carbon::createFromFormat('d-M-y', $record->GRH_DOCUMENTDATE)->format('d-m-Y') 
                                : 'N/A';
                            $categoryMapping = [11=>'Fire',12=>'Marine',13=>'Motor',14=>'Miscellaneous',16=>'Health'];
                            $deptCode = $record->PDP_DEPT_CODE ?? null;
                            $isHighlighted = isset($record->days_old) && $record->days_old >= 15;
                        @endphp
                        <tr {{ $isHighlighted ? 'class=highlight-red' : '' }}>
                            <td>
                                <button class="btn btn-sm note-tag-btn"
                                        style="background:#fff3cd; border:1px solid #ffc107; color:#856404;"
                                        data-req-note="{{ $record->GRH_REFERENCE_NO ?? '' }}"
                                        title="Add Note Tag">
                                    <i class="fas fa-sticky-note"></i> Note
                                </button>
                            </td>
                            <td data-field="request_note">{{ $record->GRH_REFERENCE_NO ?? 'N/A' }}</td>
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
                            <td data-field="total_sum_ins" class="text-end">{{ is_numeric($record->TOT_SI) ? number_format($record->TOT_SI) : 'N/A' }}</td>
                            <td data-field="ri_sum_ins" class="text-end">{{ is_numeric($record->CED_SI) ? number_format($record->CED_SI) : 'N/A' }}</td>
                            <td data-field="share" class="text-end">{{ is_numeric($record->GRH_CEDEDSISHARE) ? number_format($record->GRH_CEDEDSISHARE, 2) . '%' : 'N/A' }}</td>
                            <td data-field="total_premium" class="text-end">{{ is_numeric($record->TOT_PRE) ? number_format($record->TOT_PRE) : 'N/A' }}</td>
                            <td data-field="ri_premium" class="text-end">{{ is_numeric($record->CED_PRE) ? number_format($record->CED_PRE) : 'N/A' }}</td>
                            <td data-field="comm_date">{{ $record->GRH_COMMDATE ? \Carbon\Carbon::parse($record->GRH_COMMDATE)->format('d-m-Y') : 'N/A' }}</td>
                            <td data-field="expiry_date">{{ $record->GRH_EXPIRYDATE ? \Carbon\Carbon::parse($record->GRH_EXPIRYDATE)->format('d-m-Y') : 'N/A' }}</td>
                            <td data-field="conv_takaful">{{ $record->INSU_TYPE ?? 'N/A' }}</td>
                            <td data-field="posted">{{ $record->GRH_POSTINGTAG ?? 'N/A' }}</td>
                            <td data-field="user_name">{{ $record->CREATED_BY ?? 'N/A' }}</td>
                            <td data-field="acceptance_date">{{ $record->GRH_ACCEPTEDDATE ? \Carbon\Carbon::parse($record->GRH_ACCEPTEDDATE)->format('d-m-Y') : 'N/A' }}</td>
                            <td data-field="warranty_period">
                                @if(isset($record->GRH_ACCEPTEDDATE))
                                    {{ \Carbon\Carbon::parse($record->GRH_ACCEPTEDDATE)->addDays(30)->format('d-m-Y') }}
                                @else N/A @endif
                            </td>
                            <td data-field="commission_percent" class="text-end">{{ is_numeric($record->GRH_COMMISSIONRATE) ? number_format($record->GRH_COMMISSIONRATE, 2) : 'N/A' }}</td>
                            <td data-field="commission_amount" class="text-end">{{ is_numeric($record->COMMISSIONAMT) ? number_format($record->COMMISSIONAMT) : 'N/A' }}</td>
                            <td data-field="acceptance_no">{{ $record->GRH_REINS_REF_NO ?? 'N/A' }}</td>
                            <td data-field="plc_loc_code">{{ $record->PLC_LOC_CODE ?? 'N/A' }}</td>
                            <td data-field="pbc_busiclass_code">{{ $record->PBC_BUSICLASS_CODE ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <td colspan="7"></td>
                        <td class="text-end fw-bold">Total RI Sum Insured:</td>
                        <td class="fw-bold text-end footer-value">0</td>
                        <td colspan="15"></td>
                    </tr>
                </tfoot>
            </table>
        @endif>

        <!-- Note Tag Modal -->
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
                            <small class="text-muted d-block">REQUEST NOTE #</small>
                            <strong id="ntRefNo" class="text-dark" style="font-size:0.95rem;">—</strong>
                        </div>

                        <input type="hidden" id="ntHiddenRef">
                        <input type="hidden" id="ntReportName" value="OSCP">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Action <span class="text-danger">*</span></label>
                            <select class="form-select" id="ntAction">
                                <option value="">— Select Action —</option>
                                <option value="decline">Decline</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Remarks</label>
                            <textarea class="form-control" id="ntRemarks" rows="4" maxlength="2000" placeholder="Enter remarks (optional)…"></textarea>
                            <div class="text-end mt-1">
                                <small class="text-muted"><span id="ntCharCount">0</span>/2000</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <span class="text-danger small d-none" id="ntSubmitError">
                            <i class="bi bi-exclamation-circle me-1"></i> <span id="ntSubmitErrorMsg"></span>
                        </span>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-warning fw-bold text-dark" id="ntSubmitBtn">
                            <i class="fas fa-save me-1"></i> Save Note
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtered Records Modal (Action column removed) -->
        <div class="modal fade" id="filteredRecordsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filteredRecordsTitle">Filtered Records</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="modalSearch" class="form-control" placeholder="Search records...">
                            <button class="btn btn-outline-secondary" id="clearModalSearch">Clear</button>
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
                                        <th>Conventional/Takaful</th>
                                        <th>Posted</th>
                                        <th>User Name</th>
                                        <th>Acceptance Date</th>
                                        <th>Warranty Period</th>
                                        <th>Commission Percent</th>
                                        <th>Commission Amount</th>
                                        <th>Acceptance No</th>
                                        <th>PLC LOC CODE</th>
                                        <th>PBC BUSICLASS CODE</th>
                                    </tr>
                                </thead>
                                <tbody id="filteredRecordsBody"></tbody>
                                <tfoot>
                                    <tr class="table-active">
                                        <td colspan="6"></td>
                                        <td class="text-end fw-bold">Total RI Sum Insured:</td>
                                        <td class="fw-bold text-end footer-value">0</td>
                                        <td colspan="15"></td>
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
    </div>

    <!-- Scripts -->
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
        var noteTargetRow = null;

        function calculateDaysDiff(docDate) {
            try {
                const parts = docDate.split('-');
                if (parts.length !== 3) return 0;
                const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                const date = new Date(formattedDate);
                if (isNaN(date)) return 0;
                const today = new Date();
                return Math.floor((today - date) / (1000 * 60 * 60 * 24));
            } catch (e) {
                return 0;
            }
        }

        $(document).ready(function () {
            $('.select2').select2({ placeholder: "Select an option", allowClear: true, width: '100%' });

            var table = $('#reportsTable').DataTable({
                paging: false,
                searching: true,
                ordering: true,
                info: true,
                scrollX: true,
                scrollY: '500px',
                fixedHeader: { header: true, footer: true },
                autoWidth: true,
                dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
                buttons: [
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-success btn-sm', title: 'Outstanding Report R2' },
                    { extend: 'pdfHtml5',  text: '<i class="fas fa-file-pdf"></i> PDF',  className: 'btn btn-danger btn-sm', title: 'Outstanding Report R2', orientation: 'landscape', pageSize: 'A4' }
                ],
                footerCallback: function () {
                    var api = this.api();
                    var intVal = i => typeof i === 'string' ? parseFloat(i.replace(/[^\d.-]/g, '')) || 0 : (typeof i === 'number' ? i : 0);
                    var total = api.column(8, {page: 'current'}).data().reduce((a,b) => intVal(a) + intVal(b), 0);
                    $(api.column(8).footer()).html(total.toLocaleString('en-US'));
                },
                drawCallback: function () { this.api().columns.adjust(); }
            });

            // Note Tag Functionality
            $(document).on('click', '.note-tag-btn', function () {
                noteTargetRow = $(this).closest('tr')[0];
                var reqNote = $(this).data('req-note');

                $('#ntHiddenRef').val(reqNote);
                $('#ntRefNo').text(reqNote || '—');
                $('#ntAction').val('').removeClass('is-invalid');
                $('#ntRemarks').val('');
                $('#ntCharCount').text('0');
                $('#ntSubmitError').addClass('d-none');

                $('#noteTagModal').modal('show');
            });

            $('#ntRemarks').on('input', function () {
                $('#ntCharCount').text($(this).val().length);
            });

            $('#ntSubmitBtn').on('click', function () {
                var refNo = $('#ntHiddenRef').val();
                var action = $('#ntAction').val();
                var remarks = $('#ntRemarks').val().trim();
                var reportName = $('#ntReportName').val();

                if (!action) {
                    $('#ntAction').addClass('is-invalid');
                    $('#ntSubmitError').removeClass('d-none').find('#ntSubmitErrorMsg').text('Please select an action.');
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

                $.ajax({
                    url: "{{ route('reins.tag.store') }}",
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: { GRH_REFERENCE_NO: refNo, report_name: reportName, tag_action: action, remarks: remarks },
                    success: function (response) {
                       
                        if (response.success) {
    $('#noteTagModal').modal('hide');
    location.reload(); 
} else {
                            $('#ntSubmitError').removeClass('d-none').find('#ntSubmitErrorMsg').text(response.message || 'Failed to save note.');
                            $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Note');
                        }
                    },
                    error: function () {
                        $('#ntSubmitError').removeClass('d-none').find('#ntSubmitErrorMsg').text('Server error');
                        $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Note');
                    }
                });
            });

            $('#noteTagModal').on('hidden.bs.modal', function () {
                $('#ntSubmitBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Note');
                noteTargetRow = null;
            });

            // ====================== AGING ANALYSIS MODAL ======================
            const filteredModal = new bootstrap.Modal(document.getElementById('filteredRecordsModal'));
            let allFilteredRows = [];

            $('.aging-card').on('click', function () {
                const minDays = parseInt($(this).data('min-days'));
                const maxDays = parseInt($(this).data('max-days'));
                const label = $(this).data('label');

                $('#filteredRecordsBody').empty();
                allFilteredRows = [];

                let count = 0, totalRiSumIns = 0;

                $('#reportsTable tbody tr').each(function () {
                    const $row = $(this);
                    const docDate = $row.find('td[data-field="doc_date"]').text().trim();
                    const riSumIns = parseFloat($row.find('td[data-field="ri_sum_ins"]').text().replace(/,/g, '')) || 0;
                    const daysOld = calculateDaysDiff(docDate);

                    if (daysOld >= minDays && daysOld <= maxDays) {
                        const $cloned = $row.clone();

                        // IMPORTANT: Remove the first column (Action button) so modal starts from Request Note
                        $cloned.find('td:first').remove();

                        $('#filteredRecordsBody').append($cloned);
                        allFilteredRows.push({ element: $cloned, riSumIns: riSumIns });
                        count++;
                        totalRiSumIns += riSumIns;
                    }
                });

                $('#filteredRecordsTitle').text(`${label} - ${count} records`);
                $('#modalRecordCount').text(`Showing ${count} records`);
                $('#filteredRecordsTable tfoot .footer-value').text(totalRiSumIns.toLocaleString('en-US'));

                filteredModal.show();
            });

            // Modal Search
            $('#modalSearch').on('keyup', function () {
                const val = $(this).val().toLowerCase();
                let visible = 0, total = 0;

                allFilteredRows.forEach(row => {
                    const show = row.element.text().toLowerCase().indexOf(val) > -1;
                    row.element.toggle(show);
                    if (show) { visible++; total += row.riSumIns; }
                });

                $('#filteredRecordsTable tfoot .footer-value').text(total.toLocaleString('en-US'));
                $('#modalRecordCount').text(`Showing ${visible} filtered records`);
            });

            $('#clearModalSearch').on('click', function () {
                $('#modalSearch').val('');
                let total = 0;
                allFilteredRows.forEach(row => {
                    row.element.show();
                    total += row.riSumIns;
                });
                $('#filteredRecordsTable tfoot .footer-value').text(total.toLocaleString('en-US'));
                $('#modalRecordCount').text(`Showing ${allFilteredRows.length} records`);
            });
        });
    </script>
</body>
</html>