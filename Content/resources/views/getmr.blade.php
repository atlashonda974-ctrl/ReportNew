@extends('layouts.master')

@push('styles')
    @include('layouts.master_titles')

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <style>
        .dataTables_wrapper .dt-buttons,
        .dataTables_wrapper .dataTables_filter {
            position: relative;
            z-index: 10;
        }

        .dt-controls-header {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .text-nowrap th, .text-nowrap td {
            white-space: nowrap !important;
        }

        .card .table-responsive {
            margin-top: 10px;
        }
    </style>
@endpush

@section('content')
<div class="content-body">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">

                <x-report-header title="Get Medical Report" />

                @if(empty($employees))
                    <div class="alert alert-danger">
                        No medical reimbursement claims submitted yet.
                    </div>
                @else
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="reportsTable"
                                       class="table table-bordered table-striped table-hover table-sm text-center align-middle w-100 text-nowrap">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>SR NO</th>
                                            <th>Folio No</th>
                                            <th>Health Card No</th>
                                            <th>Plan</th>
                                            <th>Gender</th>
                                            <th>Name</th>
                                            <th>CNIC</th>
                                            <th>Age</th>
                                            <th>Relation</th>
                                            <th>Maternity</th>

                                            <th>Hosp. Limit</th>
                                            <th>Hosp. Utilization</th>
                                            <th>Hosp. Balance</th>

                                            <th>ND Limit</th>
                                            <th>ND Utilization</th>
                                            <th>ND Balance</th>

                                            <th>C-SEC Limit</th>
                                            <th>C-SEC Utilization</th>
                                            <th>C-SEC Balance</th>

                                            <th>DD Limit</th>
                                            <th>DD Utilization</th>
                                            <th>DD Balance</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($employees as $index => $claim)
                                            @php
                                                $srNo = $index + 1;
                                                $planVal = (int)($claim['SERIAL_NUMBER'] ?? 0);
                                                $planLetter = ($planVal > 0 && $planVal <= 26)
                                                    ? chr(64 + $planVal)
                                                    : ($claim['PRL_CODE'] ?? '-');

                                                $planData = $plans[$planVal - 1] ?? [];

                                                // PLAN LIMITS
                                                $hospLimit = (int) ($planData['HOSP_L'] ?? 0);
                                                $ndLimit   = (int) ($planData['MAT_L'] ?? 0);
                                                $csecLimit = (int) ($planData['CSEC_L'] ?? 0);
                                                $ddLimit   = (int) ($planData['DD_L'] ?? 0);

                                                // UTILIZATION FROM EMPLOYEE API
                                                $hospUsed = (int) ($claim['HOSP_L'] ?? 0);
                                                $ndUsed   = (int) ($claim['MAT_L'] ?? 0);
                                                $csecUsed = (int) ($claim['CSEC_L'] ?? 0);
                                                $ddUsed   = (int) ($claim['DD_L'] ?? 0);

                                                // BALANCES
                                                $hospBal = max($hospLimit - $hospUsed, 0);
                                                $ndBal   = max($ndLimit - $ndUsed, 0);
                                                $csecBal = max($csecLimit - $csecUsed, 0);
                                                $ddBal   = max($ddLimit - $ddUsed, 0);
                                            @endphp

                                            <tr>
                                                <td>{{ $srNo }}</td>
                                                <td>{{ $claim['HMB_CODE'] ?? '-' }}</td>
                                                <td>{{ $claim['HMB_HEALTHCARD_NO'] ?? '-' }}</td>
                                                <td>{{ $planLetter }}</td>
                                                <td>{{ $claim['GIH_GENDER'] ?? '-' }}</td>
                                                <td>{{ $claim['GIH_NAME'] ?? '-' }}</td>
                                                <td>{{ $claim['GID_NIC_NO'] ?? '-' }}</td>
                                                <td>{{ $claim['GIH_AGE'] ?? '-' }}</td>
                                                <td>{{ $claim['PRL_DESC'] ?? '-' }}</td>
                                                <td>{{ $claim['MATERNITY'] ?? 'No' }}</td>

                                                {{-- Hospital --}}
                                                <td class="text-end">Rs. {{ number_format($hospLimit) }}</td>
                                                <td class="text-end">Rs. {{ number_format($hospUsed) }}</td>
                                                <td class="text-end fw-bold">Rs. {{ number_format($hospBal) }}</td>

                                                {{-- ND --}}
                                                <td class="text-end">Rs. {{ number_format($ndLimit) }}</td>
                                                <td class="text-end">Rs. {{ number_format($ndUsed) }}</td>
                                                <td class="text-end fw-bold">Rs. {{ number_format($ndBal) }}</td>

                                                {{-- C-SEC --}}
                                                <td class="text-end">Rs. {{ number_format($csecLimit) }}</td>
                                                <td class="text-end">Rs. {{ number_format($csecUsed) }}</td>
                                                <td class="text-end fw-bold">Rs. {{ number_format($csecBal) }}</td>

                                                {{-- DD --}}
                                                <td class="text-end">Rs. {{ number_format($ddLimit) }}</td>
                                                <td class="text-end">Rs. {{ number_format($ddUsed) }}</td>
                                                <td class="text-end fw-bold">Rs. {{ number_format($ddBal) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        $(function () {
            $('#reportsTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                scrollX: true,
                autoWidth: false,
                dom: '<"dt-controls-header"fB>rt<"row mt-2"<"col-md-6"i><"col-md-6"p>>',
                buttons: [
                    {
                        extend: 'excel',
                        className: 'btn btn-success btn-sm',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        title: 'Medical Report'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        title: 'Medical Report',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    }
                ]
            });
        });
    </script>
@endpush
