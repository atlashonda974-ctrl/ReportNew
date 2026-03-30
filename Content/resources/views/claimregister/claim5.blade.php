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
    </style>
</head>
<body>
    <div class="container mt-5">
        <x-report-header title="Settled Screen" />
        <form method="GET" action="{{ url('/cr5') }}" class="mb-4">
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
                            <option 
                                value="{{ $branch->fbracode }}" 
                                {{ request('location_category') == $branch->fbracode ? 'selected' : '' }}
                            >
                                {{ $branch->fbradsc }}
                            </option>
                        @endforeach
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
                    <a href="{{ url('/cr5') }}" class="btn btn-outline-secondary me-2" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        @php
            $insuValues = request('insu', []);
            $insuQuery = is_array($insuValues) ? implode(',', $insuValues) : $insuValues;
        @endphp

        @php
            $filters = [
                'all'     => 'All',
                '2days'   => '2 Days',
                '5days'   => '5 Days',
                '7days'   => '7 Days',
                '10days'  => '10 Days',
                '15days'  => '15 Days',
                '15plus'  => '15+ Days',
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
                        <th>Remarks</th>
                        <th>Action</th>
                        <th>PLC_LOC_CODE</th>
                        <th>PDP_DEPT_CODE</th>
                        <th>PPS_PARTY_CODE</th>
                        <th>PPS_DESC</th>
                        <th>PPS_MOBILE_NO</th>
                        <th>PPS_EMAIL_ADDRESS</th>
                        <th>GIH_INTIMATIONDATE</th>
                        <th>MAX(CLM_INTHD.GIH_INTI_ENTRYNO)</th>
                        <th>GIH_DOC_REF_NO</th>
                        <th>GID_BASEDOCUMENTNO</th>
                        <th>GIH_LOSSCLAIMED</th>
                        <th>GUD_REPORT_DATE</th>
                        <th>GSH_SETTLEMENTDATE</th>
                        <th>PIY_INSUTYPE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                        <tr data-record='@json($record)'>
                            <td>
                                <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#feedbackModal"
                                    data-id="{{ $record->GIH_DOC_REF_NO }}"
                                    data-uw-doc="{{ $record->GIH_DOC_REF_NO }}">
                                    <i class="bi bi-chat-text"></i> Remarks
                                </button>
                            </td>
                            <td>
                                @php
                                    $hasFeedback = \App\Models\Feedback::where('uw_doc', $record->GIH_DOC_REF_NO)->exists();
                                @endphp
                                <button class="btn btn-success btn-sm action-btn" 
                                    data-id="{{ $record->GIH_DOC_REF_NO }}"
                                    {{ $hasFeedback ? '' : 'disabled' }}
                                    title="{{ $hasFeedback ? 'Complete Action' : 'Submit remarks first' }}">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </td>
                            <td>{{ $record->PLC_LOC_CODE ?? 'N/A' }}</td>
                            <td>{{ $record->PDP_DEPT_CODE ?? 'N/A' }}</td>
                            <td>{{ $record->PPS_PARTY_CODE ?? 'N/A' }}</td>
                            <td>{{ $record->PPS_DESC ?? 'N/A' }}</td>
                            <td>{{ $record->PPS_MOBILE_NO ?? 'N/A' }}</td>
                            <td>{{ $record->PPS_EMAIL_ADDRESS ?? 'N/A' }}</td>
                            <td>{{ $record->GIH_INTIMATIONDATE ?? 'N/A' }}</td>
                            <td>{{ $record->{"MAX(CLM_INTHD.GIH_INTI_ENTRYNO)"} ?? 'N/A' }}</td>
                            <td>{{ $record->GIH_DOC_REF_NO ?? 'N/A' }}</td>
                            <td>{{ $record->GID_BASEDOCUMENTNO ?? 'N/A' }}</td>
                            <td style="text-align: right;">
                                {{ $record->GIH_LOSSCLAIMED ? number_format($record->GIH_LOSSCLAIMED) : 'N/A' }}
                            </td>
                            <td>{{ $record->GUD_REPORT_DATE ?? 'N/A' }}</td>
                            <td>{{ $record->GSH_SETTLEMENTDATE ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $insuranceTypeMapping = [
                                        'D' => 'Direct',
                                        'I' => 'Inward',
                                        'O' => 'Outward',
                                    ];
                                    $code = $record->PIY_INSUTYPE ?? null;
                                    $insuranceType = $insuranceTypeMapping[$code] ?? $code;
                                @endphp
                                {{ $insuranceType }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="12" style="text-align: right;">Total GIH_LOSSCLAIMED</th>
                        <th style="text-align: right;">
                            {{ number_format($data->sum('GIH_LOSSCLAIMED')) }}
                        </th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        @endif

        <!-- Feedback Modal -->
        <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feedbackModalLabel">Feedback Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="feedbackForm" method="POST" action="{{ url('/cr5/feedback') }}">
                    @csrf
                    <input type="hidden" name="uw_doc" id="uw_doc">
                    
                    <!-- Surveyor Professionalism -->
                    <div class="mb-3">
                        <label class="form-label">Surveyor Professionalism</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_prof" value="1" id="surv_prof_1">
                                <label class="form-check-label" for="surv_prof_1">1</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_prof" value="2" id="surv_prof_2">
                                <label class="form-check-label" for="surv_prof_2">2</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_prof" value="3" id="surv_prof_3">
                                <label class="form-check-label" for="surv_prof_3">3</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_prof" value="4" id="surv_prof_4">
                                <label class="form-check-label" for="surv_prof_4">4</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="surv_prof" value="5" id="surv_prof_5">
                                <label class="form-check-label" for="surv_prof_5">5</label>
                            </div>
                        </div>
                    </div>

                    <!-- Repeat similar structure for other fields -->
                    <div class="mb-3">
                        <label class="form-label">Surveyor Responsiveness</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_resp" value="1" id="surv_resp_1">
                                <label class="form-check-label" for="surv_resp_1">1</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_resp" value="2" id="surv_resp_2">
                                <label class="form-check-label" for="surv_resp_2">2</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_resp" value="3" id="surv_resp_3">
                                <label class="form-check-label" for="surv_resp_3">3</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_resp" value="4" id="surv_resp_4">
                                <label class="form-check-label" for="surv_resp_4">4</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="surv_resp" value="5" id="surv_resp_5">
                                <label class="form-check-label" for="surv_resp_5">5</label>
                            </div>
                        </div>
                    </div>

                    <!-- Continue similarly for other categories -->
                    <!-- Surveyor Accuracy -->
                    <div class="mb-3">
                        <label class="form-label">Surveyor Accuracy</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_acc" value="1" id="surv_acc_1">
                                <label class="form-check-label" for="surv_acc_1">1</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_acc" value="2" id="surv_acc_2">
                                <label class="form-check-label" for="surv_acc_2">2</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_acc" value="3" id="surv_acc_3">
                                <label class="form-check-label" for="surv_acc_3">3</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_acc" value="4" id="surv_acc_4">
                                <label class="form-check-label" for="surv_acc_4">4</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="surv_acc" value="5" id="surv_acc_5">
                                <label class="form-check-label" for="surv_acc_5">5</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Surveyor Overall</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_overall" value="1" id="surv_overall_1">
                                <label class="form-check-label" for="surv_overall_1">1</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_overall" value="2" id="surv_overall_2">
                                <label class="form-check-label" for="surv_overall_2">2</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_overall" value="3" id="surv_overall_3">
                                <label class="form-check-label" for="surv_overall_3">3</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="surv_overall" value="4" id="surv_overall_4">
                                <label class="form-check-label" for="surv_overall_4">4</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="surv_overall" value="5" id="surv_overall_5">
                                <label class="form-check-label" for="surv_overall_5">5</label>
                            </div>
                        </div>
                    </div>

                    <!-- Client Requirements -->
                    <div class="mb-3">
                        <label class="form-label">Client Requirements</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_req" value="1" id="clt_req_1">
                                <label class="form-check-label" for="clt_req_1">1</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_req" value="2" id="clt_req_2">
                                <label class="form-check-label" for="clt_req_2">2</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_req" value="3" id="clt_req_3">
                                <label class="form-check-label" for="clt_req_3">3</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_req" value="4" id="clt_req_4">
                                <label class="form-check-label" for="clt_req_4">4</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="clt_req" value="5" id="clt_req_5">
                                <label class="form-check-label" for="clt_req_5">5</label>
                            </div>
                        </div>
                    </div>

                    <!-- Client Information -->
                    <div class="mb-3">
                        <label class="form-label">Client Information</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_info" value="1" id="clt_info_1">
                                <label class="form-check-label" for="clt_info_1">1</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_info" value="2" id="clt_info_2">
                                <label class="form-check-label" for="clt_info_2">2</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_info" value="3" id="clt_info_3">
                                <label class="form-check-label" for="clt_info_3">3</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_info" value="4" id="clt_info_4">
                                <label class="form-check-label" for="clt_info_4">4</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="clt_info" value="5" id="clt_info_5">
                                <label class="form-check-label" for="clt_info_5">5</label>
                            </div>
                        </div>
                    </div>

                    <!-- Client Cooperation -->
                    <div class="mb-3">
                        <label class="form-label">Client Cooperation</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_coop" value="1" id="clt_coop_1">
                                <label class="form-check-label" for="clt_coop_1">1</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_coop" value="2" id="clt_coop_2">
                                <label class="form-check-label" for="clt_coop_2">2</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_coop" value="3" id="clt_coop_3">
                                <label class="form-check-label" for="clt_coop_3">3</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_coop" value="4" id="clt_coop_4">
                                <label class="form-check-label" for="clt_coop_4">4</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="clt_coop" value="5" id="clt_coop_5">
                                <label class="form-check-label" for="clt_coop_5">5</label>
                            </div>
                        </div>
                    </div>

                    <!-- Client Overall -->
                    <div class="mb-3">
                        <label class="form-label">Client Overall</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_overall" value="1" id="clt_overall_1">
                                <label class="form-check-label" for="clt_overall_1">1</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_overall" value="2" id="clt_overall_2">
                                <label class="form-check-label" for="clt_overall_2">2</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_overall" value="3" id="clt_overall_3">
                                <label class="form-check-label" for="clt_overall_3">3</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="clt_overall" value="4" id="clt_overall_4">
                                <label class="form-check-label" for="clt_overall_4">4</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="clt_overall" value="5" id="clt_overall_5">
                                <label class="form-check-label" for="clt_overall_5">5</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                </form>
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
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('#location_category').select2({
                    placeholder: "Select a branch",
                    allowClear: true
                });
                
                $('#insu').select2({
                    placeholder: "Choose type",
                    allowClear: true,
                    width: '150%'
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
                                columns: ':visible:not(:first-child, :nth-child(2))',
                                format: {
                                    body: function(data, row, column, node) {
                                        return data;
                                    }
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
                                columns: ':visible:not(:first-child, :nth-child(2))',
                                format: {
                                    body: function(data, row, column, node) {
                                        return data;
                                    }
                                }
                            }
                        }
                    ],
                    "footerCallback": function (row, data, start, end, display) {
                        var api = this.api();
                        var intVal = function (i) {
                            return typeof i === 'string'
                                ? parseFloat(i.replace(/[^\d.-]/g, '')) || 0
                                : typeof i === 'number'
                                    ? i
                                    : 0;
                        };
                        var totalLossClaimed = api
                            .column(12, { page: 'current' })
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                        $(api.column(12).footer()).html(
                            '<strong>' + totalLossClaimed.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) + '</strong>'
                        );
                    },
                    "initComplete": function() {
                        this.api().columns.adjust();
                        $('.dataTables_filter input').attr('placeholder', 'Search...');
                        $('.dataTables_filter').css('margin-left', '5px').css('margin-right', '5px');
                        $('.dt-buttons').css('margin-left', '5px');
                    },
                    "drawCallback": function() {
                        this.api().columns.adjust();
                    }
                });

                $('#reportsTable').on('click', 'button[data-bs-toggle="modal"]', function() {
                    var uwDoc = $(this).data('uw-doc');
                    $('#uw_doc').val(uwDoc);
                    $('#feedbackForm')[0].reset();
                    $('#uw_doc').val(uwDoc);
                });

                $('#feedbackForm').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#feedbackModal').modal('hide');
                                alert('Feedback submitted successfully.');
                                var uwDoc = $('#uw_doc').val();
                                var row = $('#reportsTable').find(`button[data-id="${uwDoc}"]`).closest('tr');
                                row.find('.action-btn').prop('disabled', false).attr('title', 'Complete Action');
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            alert('Error submitting feedback: ' + (xhr.responseJSON?.message || 'Unknown error'));
                        }
                    });
                });

                $('#reportsTable').on('click', '.action-btn', function() {
                    var id = $(this).data('id');
                    var row = $(this).closest('tr');
                    var hasFeedback = $(this).prop('disabled') === false;

                    if (!hasFeedback) {
                        alert('Please submit remarks first.');
                        return;
                    }

                    if (confirm('Are you sure you want to complete this action?')) {
                        $.ajax({
                            url: '{{ url("/cr5/complete") }}/' + id,
                            method: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    table.row(row).remove().draw();
                                    alert('Action completed successfully.');
                                } else {
                                    alert('Error: ' + response.message);
                                }
                            },
                            error: function(xhr) {
                                alert('Error completing action: ' + (xhr.responseJSON?.message || 'Unknown error'));
                            }
                        });
                    }
                });

                $('a[title="Reset"]').on('click', function() {
                    setTimeout(function() {
                        table.draw();
                    }, 100);
                });
            });
        </script>
    </div>
</body>
</html>