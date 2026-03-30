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
        .progress { 
            height: 4px; 
        }
        .card-title { 
            font-size: 0.95rem !important; 
        }
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

        <form method="GET" action="{{ url('/r1') }}" class="mb-4">
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
            <div class="border border-primary rounded p-2 mb-3">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body p-3">
                        <h6 class="card-title text-primary mb-2">
                            <i class="bi bi-clipboard-data me-1"></i>Posting Status Analysis
                        </h6>

                        <div class="row">
                            <!-- LEFT SUMMARY -->
                            <div class="col-md-4 border-end bg-light">
                                <div class="px-2 py-2">
                                    <h6 class="text-muted mb-2">
                                        <i class="bi bi-bar-chart-fill me-1 text-primary"></i>Summary
                                    </h6>

                                    <div class="d-flex justify-content-between mb-2">
                                        <!-- Total Records -->
                                        <div class="card border-0 shadow-sm summary-card flex-fill me-2">
                                            <div class="card-body py-2 px-2">
                                                <small class="text-muted">Total Records</small>
                                                <div class="fw-bold">{{ $totalCount }}</div>
                                                <small class="text-muted">Sum: {{ number_format($totalSumInsured) }}</small>
                                            </div>
                                            <i class="bi bi-database text-primary"></i>
                                        </div>

                                        <!-- Not Posted -->
                                        <div class="card border-0 shadow-sm summary-card flex-fill me-2">
                                            <div class="card-body py-2 px-2">
                                                <small class="text-muted">Not Posted</small>
                                                <div class="fw-bold text-danger">{{ $notPostedCount }}</div>
                                                <small class="text-muted">Sum: {{ number_format($notPostedSumInsured) }}</small>
                                            </div>
                                            <i class="bi bi-exclamation-triangle text-danger"></i>
                                        </div>

                                        <!-- Percentage -->
                                        <div class="card border-0 shadow-sm summary-card flex-fill">
                                            <div class="card-body py-2 px-2">
                                                <small class="text-muted">Percentage of Posted Records</small>
                                                <div class="fw-bold text-info">
                                                    {{ $totalCount > 0 ? round((($totalCount - $notPostedCount) / $totalCount) * 100) : 0 }}%
                                                </div>
                                                <div class="progress" style="height: 5px;">
                                                    <div class="progress-bar bg-info" style="width: {{ $totalCount > 0 ? round((($totalCount - $notPostedCount) / $totalCount) * 100) : 0 }}%;"></div>
                                                </div>
                                            </div>
                                            <i class="bi bi-percent text-info"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT AGING ANALYSIS -->
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
                                                $colorClass = 'orange';
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

                                            if ($count === $maxCount) {
                                                $colorClass = 'danger';
                                            } elseif ($count === $minCount) {
                                                $colorClass = 'info';
                                            }
                                        @endphp

                                        <div class="col-6 col-sm-4">
                                            <div class="card aging-card border-{{ $colorClass }} shadow-sm h-100"
                                                 data-bs-toggle="modal"
                                                 data-bs-target="#filteredRecordsModal"
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
        @endif

        @if(empty($data))
            <div class="alert alert-danger">No data available.</div>
        @else
            <table id="reportsTable" class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th data-field="action">Action</th>
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
                            }

                            $deptCode = $record->PDP_DEPT_CODE ?? null;
                            $categoryMapping = [
                                11 => 'Fire',
                                12 => 'Marine',
                                13 => 'Motor',
                                14 => 'Miscellaneous',
                                16 => 'Health',
                            ];
                        @endphp
                        <tr>
                            <td data-field="action">
                                <button class="btn btn-info btn-sm send-email-btn">
                                    Send Email
                                </button>
                            </td>
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
                            <td data-field="total_sum_ins" class="numeric" style="text-align: right;">
                                {{ is_numeric($record->TOT_SI ?? null) ? number_format($record->TOT_SI) : 'N/A' }}
                            </td>
                            <td data-field="ri_sum_ins" class="numeric" style="text-align: right;">
                                {{ is_numeric($record->CED_SI ?? null) ? number_format($record->CED_SI) : 'N/A' }}
                            </td>
                            <td data-field="share" class="numeric" style="text-align: right;">
                                {{ is_numeric($record->GRH_CEDEDSISHARE ?? null) ? number_format($record->GRH_CEDEDSISHARE, 2) . '%' : 'N/A' }}
                            </td>
                            <td data-field="total_premium" class="numeric" style="text-align: right;">
                                {{ is_numeric($record->TOT_PRE ?? null) ? number_format($record->TOT_PRE) : 'N/A' }}
                            </td>
                            <td data-field="ri_premium" class="numeric" style="text-align: right;">
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
                            <td data-field="commission_percent" class="numeric" style="text-align: right;">
                                {{ is_numeric($record->GRH_COMMISSIONRATE ?? null) ? number_format($record->GRH_COMMISSIONRATE, 2) : 'N/A' }}
                            </td>
                            <td data-field="commission_amount" class="numeric" style="text-align: right;">
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

        <!-- Modal for filtered records -->
        <div class="modal fade" id="filteredRecordsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filteredRecordsTitle">Filtered Records</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                        <th>Action</th>
                                        <th>PDF</th>
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
                                <tbody id="filteredRecordsBody">
                                    <!-- Records will be inserted here by JavaScript -->
                                </tbody>
                                <tfoot>
                                    <tr class="table-active">
                                        <td colspan="9" class="text-end fw-bold">Total RI Sum Insured:</td>
                                        <td class="fw-bold" id="totalRiSumIns">0</td>
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
            // Global variable to store current row data for email
            let currentRowDataForEmail = null;
            let currentRow = null;

            $(document).ready(function() {
                // Initialize select2
                $('.select2').select2({
                    placeholder: "Select a branch",
                    allowClear: true,
                    width: '69%'
                });

                // Initialize DataTable
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
        extend: 'excelHtml5',
        text: '<i class="fas fa-file-excel"></i> Excel',
        className: 'btn btn-success btn-sm',
        title: 'Outstanding Report',
        exportOptions: {
            columns: ':visible:not(:first-child):not(:nth-child(2))', // Skip Action and PDF columns
            format: {
                body: function (data, row, column, node) {
                    // For truncated text columns (business_desc, insured, reins_party)
                    if ([3, 4, 5].includes(column)) {
                        return $(node).find('.truncate-text').attr('title') || $(node).text().trim();
                    }
                    // For numeric columns 
                    if ([6, 7, 9, 10, 20].includes(column)) {
                        const value = parseFloat($(node).text().replace(/,/g, ''));
                        return isNaN(value) ? '0' : value.toLocaleString('en-US');
                    }
                    return $(node).text().trim();
                }
            }
        },
        customizeData: function (data) {
            const intVal = i => typeof i === 'string'
                ? i.replace(/[^\d.-]/g, '') * 1
                : (typeof i === 'number' ? i : 0);

            const totalCols = [7]; // Only RI Sum Insured column (column 9 becomes 7 after skipping 2)
            let totals = new Array(data.body[0].length).fill('');

            totalCols.forEach(col => {
                let sum = data.body.reduce((acc, row) => acc + intVal(row[col]), 0);
                totals[col] = sum.toLocaleString('en-US');
            });

            totals[0] = 'Total RI Sum Insured:'; // First column label
            data.body.push(totals);
        }
    },
    {
        extend: 'pdfHtml5',
        text: '<i class="fas fa-file-pdf"></i> PDF',
        className: 'btn btn-danger btn-sm',
        title: 'Outstanding Report',
        orientation: 'landscape',
        pageSize: 'A4',
        exportOptions: {
            columns: ':visible:not(:first-child):not(:nth-child(2))', // Skip Action and PDF columns
            format: {
                body: function (data, row, column, node) {
                    // For truncated text columns
                    if ([3, 4, 5].includes(column)) {
                        return $(node).find('.truncate-text').attr('title') || $(node).text().trim();
                    }
                    // For numeric columns
                    if ([6, 7, 9, 10, 20].includes(column)) {
                        const value = parseFloat(data.replace(/,/g, ''));
                        return isNaN(value) ? '0' : value.toLocaleString('en-US');
                    }
                    return $(node).text().trim();
                }
            }
        },
        customize: function (doc) {
            const intVal = i => typeof i === 'string'
                ? i.replace(/[^\d.-]/g, '') * 1
                : (typeof i === 'number' ? i : 0);

            const totalCols = [7]; // Only RI Sum Insured column
            let totals = new Array(doc.content[1].table.body[0].length).fill('');

            totalCols.forEach(col => {
                let sum = doc.content[1].table.body.slice(1).reduce((acc, row) => acc + intVal(row[col].text), 0);
                totals[col] = { text: sum.toLocaleString('en-US'), bold: true };
            });

            totals[0] = 'Total RI Sum Insured:'; // First column label
            data.body.push(totals);
        }
    }
],
                    "footerCallback": function (row, data, start, end, display) {
                        var api = this.api();

                        var intVal = function (i) {
                            return typeof i === 'string' ? 
                                parseFloat(i.replace(/[^\d.-]/g, '')) || 0 : 
                                typeof i === 'number' ? i : 0;
                        };

                        var riSumInsColumnIndex = 9;
                        var totalSum = api.column(riSumInsColumnIndex, { page: 'current' }).data().reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                        $(api.column(riSumInsColumnIndex).footer()).html(totalSum.toLocaleString('en-US'));
                    },
                    "initComplete": function() {
                        $('#totalSum').html('0');
                        this.api().draw();
                        this.api().columns.adjust();
                        $('.dataTables_filter input').attr('placeholder', 'Search...');
                        $('.dataTables_filter').css('margin-left', '5px').css('margin-right', '5px');
                        $('.dt-buttons').css('margin-left', '5px');
                    },
                    "drawCallback": function() {
                        this.api().columns.adjust();
                    }
                });

                // Reset button functionality
                $('a[title="Reset"]').on('click', function() {
                    setTimeout(function() {
                        table.draw();
                    }, 100);
                });

                // Email modal functionality
                $(document).on('click', '.send-email-btn', function() {
                    currentRow = $(this).closest('tr');
                    
                    // Collect full row data for backend
                    currentRowDataForEmail = {
                        reqNote: currentRow.find('td[data-field="request_note"]').text().trim(),
                        docDate: currentRow.find('td[data-field="doc_date"]').text().trim(),
                        dept: currentRow.find('td[data-field="dept"]').text().trim(),
                        businessDesc: currentRow.find('td[data-field="business_desc"] .truncate-text').attr('title') || 
                                     currentRow.find('td[data-field="business_desc"]').text().trim(),
                        insured: currentRow.find('td[data-field="insured"] .truncate-text').attr('title') || 
                                currentRow.find('td[data-field="insured"]').text().trim(),
                        reinsParty: currentRow.find('td[data-field="reins_party"] .truncate-text').attr('title') || 
                                   currentRow.find('td[data-field="reins_party"]').text().trim(),
                        totalSumIns: currentRow.find('td[data-field="total_sum_ins"]').text().trim(),
                        riSumIns: currentRow.find('td[data-field="ri_sum_ins"]').text().trim(),
                        share: currentRow.find('td[data-field="share"]').text().trim(),
                        totalPremium: currentRow.find('td[data-field="total_premium"]').text().trim(),
                        riPremium: currentRow.find('td[data-field="ri_premium"]').text().trim(),
                        commDate: currentRow.find('td[data-field="comm_date"]').text().trim(),
                        expiryDate: currentRow.find('td[data-field="expiry_date"]').text().trim(),
                        cp: currentRow.find('td[data-field="cp"]').text().trim(),
                        convTakaful: currentRow.find('td[data-field="conv_takaful"]').text().trim(),
                        posted: currentRow.find('td[data-field="posted"]').text().trim(),
                        userName: currentRow.find('td[data-field="user_name"]').text().trim(),
                        acceptanceDate: currentRow.find('td[data-field="acceptance_date"]').text().trim(),
                        warrantyPeriod: currentRow.find('td[data-field="warranty_period"]').text().trim(),
                        commissionPercent: currentRow.find('td[data-field="commission_percent"]').text().trim(),
                        commissionAmount: currentRow.find('td[data-field="commission_amount"]').text().trim(),
                        acceptanceNo: currentRow.find('td[data-field="acceptance_no"]').text().trim()
                    };
                    
                    // Populate email modal exactly as original
                    var requestNote = currentRow.find('td[data-field="request_note"]').text().trim();
                    var insured = currentRow.find('td[data-field="insured"] .truncate-text').attr('title') || 
                                 currentRow.find('td[data-field="insured"]').text().trim();
                    var reinsParty = currentRow.find('td[data-field="reins_party"] .truncate-text').attr('title') || 
                                    currentRow.find('td[data-field="reins_party"]').text().trim();
                    var businessDesc = currentRow.find('td[data-field="business_desc"] .truncate-text').attr('title') || 
                                      currentRow.find('td[data-field="business_desc"]').text().trim();

                    $('#to').val('');
                    $('#cc').val('');
                    $('#subject').val('Reinsurance Request Note: ' + requestNote);
                    $('#body').val('Dear Sir/Madam,\n\n' +
                                  'Please find below details for Request Note: ' + requestNote + '\n\n' +
                                  'Insured: ' + insured + '\n' +
                                  'Reinsurance Party: ' + reinsParty + '\n' +
                                  'Business Description: ' + businessDesc + '\n\n' +
                                  'Please find the attached PDF document for detailed information.\n\n' +
                                  'Regards,\n\n');
                    
                    $('#emailModal').modal('show');
                });

                // Send email function with PDF attachment and row removal
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

                    let emailData = {
                        _token: csrfToken,
                        to: to,
                        cc: cc,
                        subject: subject,
                        body: body,
                        record: currentRowDataForEmail // Send full record data
                    };

                    if (currentRowDataForEmail) {
                        try {
                            const docDefinition = createPdfDefinition(currentRowDataForEmail);
                            const filename = `Request_Note_${currentRowDataForEmail.reqNote || 'Document'}.pdf`;
                            
                            pdfMake.createPdf(docDefinition).getBase64((pdfBase64) => {
                                emailData.pdf_data = pdfBase64;
                                emailData.pdf_filename = filename;
                                
                                sendEmailRequest(emailData, $btn, currentRow);
                            });
                        } catch (error) {
                            console.error('Error generating PDF:', error);
                            sendEmailRequest(emailData, $btn, currentRow);
                        }
                    } else {
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
                                    location.reload(); // This will refresh the page and show the new data from the server.
                                } else {
                                    alert('Error: ' + response.message);
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

                // PDF Generation - Preview
                $(document).on('click', '.preview-pdf-btn', function() {
                    const rowData = $(this).data();
                    const docDefinition = createPdfDefinition(rowData);
                    pdfMake.createPdf(docDefinition).open();
                });

                // PDF Generation - Download
                $(document).on('click', '.download-pdf-btn', function() {
                    const rowData = $(this).data();
                    const docDefinition = createPdfDefinition(rowData);
                    pdfMake.createPdf(docDefinition).download(`Request_Note_${rowData.reqNote || '2025REFCMIIR0007'}.pdf`);
                });

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
                                                                ['SUM INSURED (Our Share)', rowData.totalSi ? rowData.totalSi.toLocaleString() : '1,628,957,386'],
                                                                ['ATLAS SHARE', rowData.share ? parseFloat(rowData.share).toFixed(2) + '%' : '20.00%'],
                                                                ['GROSS PREMIUM RATE', rowData.grossPremiumRate ? parseFloat(rowData.grossPremiumRate).toFixed(4) + '%' : '0.0872%'],
                                                                ['RI COMMISSION', rowData.riCommission ? parseFloat(rowData.riCommission).toFixed(2) + '%' : '25.00%']
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
                                                text: 'Premier Insurance Co. Ltd.',
                                                alignment: 'center',
                                                margin: [0, 0],
                                                border: [true, false, true, true],
                                                padding: [0, 0]
                                            },
                                            {
                                                text: rowData.percentage || '14.70%',
                                                alignment: 'center',
                                                border: [true, false, true, true]
                                            },
                                            {
                                                text: rowData.amount || '239,478,694',
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

                // Initialize the filtered records modal
                const filteredRecordsModal = new bootstrap.Modal(document.getElementById('filteredRecordsModal'));
                let allFilteredRows = [];

                // Click handler for aging cards
                $('.aging-card').on('click', function() {
                    const minDays = parseInt($(this).data('min-days'));
                    const maxDays = parseInt($(this).data('max-days'));
                    const label = $(this).data('label');

                    $('#filteredRecordsBody').empty();
                    allFilteredRows = [];

                    let count = 0;
                    let totalRiSumIns = 0;

                    $('#reportsTable tbody tr').each(function() {
                        const $row = $(this);
                        const docDate = $row.find('td[data-field="doc_date"]').text().trim();
                        const riSumIns = parseFloat($row.find('td[data-field="ri_sum_ins"]').text().replace(/,/g, '')) || 0;
                        
                        const daysOld = calculateDaysDiff(docDate);
                        
                        if (daysOld >= minDays && daysOld <= maxDays) {
                            const $clonedRow = $row.clone();
                            $('#filteredRecordsBody').append($clonedRow);
                            allFilteredRows.push({
                                element: $clonedRow,
                                riSumIns: riSumIns
                            });
                            count++;
                            totalRiSumIns += riSumIns;
                        }
                    });

                    $('#filteredRecordsTable tfoot').remove();
                    $('#filteredRecordsTable').append(`
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="9" class="text-end fw-bold">Total RI Sum Insured:</td>
                                <td class="fw-bold">${totalRiSumIns.toLocaleString('en-US')}</td>
                                <td colspan="14"></td>
                            </tr>
                        </tfoot>
                    `);

                    $('#filteredRecordsTitle').text(`${label} - ${count} records`);
                    $('#modalRecordCount').text(`Showing ${count} records`);
                    filteredRecordsModal.show();
                });

                // Search functionality for modal with total recalculation
                $('#modalSearch').on('keyup', function() {
                    const value = $(this).val().toLowerCase();
                    let visibleCount = 0;
                    let totalRiSumIns = 0;

                    allFilteredRows.forEach(row => {
                        const $row = row.element;
                        const shouldShow = $row.text().toLowerCase().indexOf(value) > -1;
                        $row.toggle(shouldShow);
                        
                        if (shouldShow) {
                            visibleCount++;
                            totalRiSumIns += row.riSumIns;
                        }
                    });

                    $('#filteredRecordsTable tfoot td.fw-bold').last().text(totalRiSumIns.toLocaleString('en-US'));
                    $('#modalRecordCount').text(`Showing ${visibleCount} filtered records`);
                });

                // Clear search
                $('#clearModalSearch').on('click', function() {
                    $('#modalSearch').val('');
                    let totalRiSumIns = 0;
                    
                    allFilteredRows.forEach(row => {
                        row.element.show();
                        totalRiSumIns += row.riSumIns;
                    });

                    $('#filteredRecordsTable tfoot td.fw-bold').last().text(totalRiSumIns.toLocaleString('en-US'));
                    $('#modalRecordCount').text(`Showing ${allFilteredRows.length} records`);
                });

 function calculateDaysDiff(docDate) {
    try {
        const parts = docDate.split('-');
        if(parts.length !== 3) return 0;
        // Convert 'dd-mm-yyyy' to 'yyyy-mm-dd' for Date constructor
        const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
        const date = new Date(formattedDate);
        if (isNaN(date)) return 0;
        const today = new Date();
        const diffTime = today - date;
        return Math.floor(diffTime / (1000 * 60 * 60 * 24));
    } catch (e) {
        return 0;
    }
}

                
            });
        </script>
    </body>
</html>
now