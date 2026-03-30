<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Claims Summary</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    th {
      background-color: #007bff !important;
      color: white !important;
      font-weight: bold !important;
    }
    body {
      background-color: #E6E9F1;
      padding: 20px;
      font-family: Arial, sans-serif;
    }
    .container {
      max-width: 100%;
      margin: 0;
      padding: 0;
    }
    .row {
      margin-left: 0;
      margin-right: 0;
    }
    .claims-card {
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      padding: 8px 0 8px 8px;
      font-size: 0.7rem;
      box-sizing: border-box;
      margin-left: 0 !important;
      margin-right: 0 !important;
    }

    /* Charts: Small size */
    #myPieChart {
      max-width: 100%;
      max-height: 140px;
      margin: 10px 0 0 0;
      width: 100% !important;
      height: auto !important;
    }

    /* ONLY DONUT CHART: Smaller size */
    #myDonutChart {
      max-width: 100%;
      max-height: 240px !important;
      margin: 10px 0 0 0;
      width: 100% !important;
      height: auto !important;
    }

    /* ONLY BAR CHART: Slightly taller */
    #myBarChart {
      max-width: 100%;
      max-height: 240px !important;
      height: 240px !important;
      margin: 10px 0 0 0;
      width: 100% !important;
    }

    /* BAR CHART CARD: Slightly taller container */
    .row .col-lg-6:first-child .claims-card {
      min-height: 300px !important;
      padding: 12px 0 12px 10px !important;
    }

    .card-title {
      font-size: 16px;
      font-weight: 700;
      color: #333;
      text-align: left;
      margin-bottom: 8px;
      margin-left: 8px;
    }
    .current-year-figure {
      font-weight: bold;
    }
    .totals-box {
      font-size: 14px;
      margin-bottom: 10px;
      color: #333;
      margin-left: 8px;
    }
    .departments {
      display: flex;
      justify-content: space-between;
      flex-wrap: nowrap;
      margin-top: 6px;
      gap: 2px;
      margin-left: 8px;
    }
    .dept-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      flex: 1;
    }
    .dept-name {
      font-weight: normal;
      color: #333;
      font-size: 10px;
    }
    .dept-counts {
      font-size: 9px;
      display: flex;
      flex-direction: column;
      align-items: center;
      font-weight: normal;
    }
    .data-card {
      height: 350px;
      overflow: auto;
      margin-left: 0;
    }
    .data-card th, .data-card td {
      border: 1px solid #ddd;
      padding: 6px;
      text-align: left;
      white-space: nowrap;
      font-family: Arial, sans-serif;
    }
    .data-card th {
      background-color: #f8f9fa;
      position: sticky;
      top: 0;
    }
    .d-flex.justify-content-start {
      margin-left: 8px;
    }
  </style>
</head>
<body>
  <div class="container">
    @php
      $deptNames = [
        '11' => 'Fire',
        '12' => 'Marine',
        '13' => 'Motor',
        '14' => 'Misc',
        '16' => 'Health',
      ];

      // Intimation Data
      $currentYearTotal = $apiData['totals']['CURRENT_YEAR_COUNT'] ?? 0;
      $lastYearTotal = $apiData['totals']['LAST_YEAR_COUNT'] ?? 0;

      $deptLookup = [];
      foreach ($apiData['departments'] ?? [] as $dept) {
        $code = (string) ($dept['PDP_DEPT_CODE'] ?? '');
        if ($code) {
          $deptLookup[$code] = [
            'current' => $dept['CURRENT_YEAR_COUNT'] ?? 0,
            'last' => $dept['LAST_YEAR_COUNT'] ?? 0,
          ];
        }
      }

      $monthwiseData = $apiData['monthwise'] ?? [];
      $currentYearMonthCounts = array_column($monthwiseData, 'CURRENT_YEAR_COUNT', 'MONTH');
      $lastYearMonthCounts = array_column($monthwiseData, 'LAST_YEAR_COUNT', 'MONTH');

      // Settled Data
      $totalsData = $apiStatus['data']['totals'][0] ?? [];
      $currentYearTotalSettled = $totalsData['CURRENT_YEAR_COUNT'] ?? 0;
      $lastYearTotalSettled = $totalsData['LAST_YEAR_COUNT'] ?? 0;

      $deptLookupSettled = [];
      foreach ($apiStatus['data']['departments'] ?? [] as $dept) {
        $code = (string) ($dept['PDP_DEPT_CODE'] ?? '');
        if ($code) {
          $deptLookupSettled[$code] = [
            'current' => $dept['CURRENT_YEAR_COUNT'] ?? 0,
            'last' => $dept['LAST_YEAR_COUNT'] ?? 0,
          ];
        }
      }

      // Survey & Workshop
      $surveyData = $apiData['Surv'][0] ?? ['GPD_PAYEE_AMOUNT' => 0, 'GPD_PAYEE_COUNT' => 0];
      $workshopData = $apiData['Workshop'][0] ?? ['GPD_PAYEE_AMOUNT' => 0, 'GPD_PAYEE_COUNT' => 0];
    @endphp

    <!-- ==================== FIRST SUMMARY ROW ==================== -->
    <div class="row mb-3 g-3">

      <!-- Claim Intimation Card -->
      <div class="col-lg-3 col-md-6">
        <div class="claims-card">
          <div class="card-title">Intimation</div>
          <div class="totals-box">
            <div>
              Current Year:
              <a href="{{ url('/cr6') }}" target="_blank" class="current-year-figure" style="color: inherit; text-decoration: none;">
                {{ number_format($currentYearTotal) }}
              </a>
            </div>
            <div>Last Year: <span>{{ number_format($lastYearTotal) }}</span></div>
          </div>
          <div class="departments">
            @foreach($deptNames as $code => $name)
              @php
                $currentCount = $deptLookup[$code]['current'] ?? 0;
                $lastCount = $deptLookup[$code]['last'] ?? 0;
              @endphp
              <div class="dept-container">
                <span class="dept-name">{{ $name }}</span>
                <div class="dept-counts">
                  <span>{{ number_format($currentCount) }}</span>
                  <span>{{ number_format($lastCount) }}</span>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <!-- Claim Settled Card -->
      <div class="col-lg-3 col-md-6">
        <div class="claims-card">
          <div class="card-title">Settled</div>
          <div class="totals-box">
            <div>
              Current Year:
              <a href="{{ url('/cr7') }}" target="_blank" class="current-year-figure" style="color: inherit; text-decoration: none;">
                {{ number_format($currentYearTotalSettled) }}
              </a>
            </div>
            <div>Last Year: <span>{{ number_format($lastYearTotalSettled) }}</span></div>
          </div>
          <div class="departments">
            @foreach($deptNames as $code => $name)
              @php
                $currentCount = $deptLookupSettled[$code]['current'] ?? 0;
                $lastCount = $deptLookupSettled[$code]['last'] ?? 0;
              @endphp
              <div class="dept-container">
                <span class="dept-name">{{ $name }}</span>
                <div class="dept-counts">
                  <span>{{ number_format($currentCount) }}</span>
                  <span>{{ number_format($lastCount) }}</span>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <!-- Totals Card (Surveyor + Workshop) -->
      <div class="col-lg-2 col-md-6">
        <div class="claims-card" style="height: 140px;">
          <!-- Survey -->
          <div class="row mb-2">
            <div class="col-12 fw-bold">Surveyor</div>
            <div class="col-12">
              Amount:
              <a href="{{ url('/cr9') }}" target="_blank" class="current-year-figure" style="color: inherit; text-decoration: none;">
                {{ number_format($surveyData['GPD_PAYEE_AMOUNT']) }}
              </a>
            </div>
            <div class="col-12">
              Count: <span>{{ number_format($surveyData['GPD_PAYEE_COUNT']) }}</span>
            </div>
          </div>

          <!-- Workshop -->
          <div class="row">
            <div class="col-12 fw-bold">Workshop</div>
            <div class="col-12">
              Amount:
              <a href="{{ url('/cr8') }}" target="_blank" class="current-year-figure" style="color: inherit; text-decoration: none;">
                {{ number_format($workshopData['GPD_PAYEE_AMOUNT']) }}
              </a>
            </div>
            <div class="col-12">
              Count: <span>{{ number_format($workshopData['GPD_PAYEE_COUNT']) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Total OS Card -->
      <div class="col-lg-4 col-md-12">
        <div class="claims-card" style="min-height: 30.6px; padding:6.12px;">
          <div class="card-title mb-1" style="font-size: 0.97em; margin-bottom:6.12px;">
            <strong>Total OS</strong>
          </div>
          <div class="table-responsive" style="max-height: 120.36px; overflow-y: auto;">
            <table class="table table-bordered" style="font-size: 0.918em; width: 100%; margin-bottom: 0;">
              <thead>
                <tr>
                  <th style="white-space: nowrap; padding:6.12px;">O/S</th>
                  <th style="white-space: nowrap; padding:6.12px;">Total</th>
                  <th style="white-space: nowrap; padding:6.12px;">0-7 days</th>
                  <th style="white-space: nowrap; padding:6.12px;">8-15 days</th>
                  <th style="white-space: nowrap; padding:6.12px;">16-30 days</th>
                  <th style="white-space: nowrap; padding:6.12px;">31-60 days</th>
                  <th style="white-space: nowrap; padding:6.12px;">61-90 days</th>
                  <th style="white-space: nowrap; padding:6.12px;">90+ days</th>
                </tr>
              </thead>
              <tbody>
                @foreach(['Surveyor', 'Report', 'Stl'] as $type)
                  @php
                    $days = ['0-7 days', '8-15 days', '16-30 days', '31-60 days', '61-90 days', '90+ days'];
                    $total = 0;
                    foreach ($days as $d) {
                      $total += $combinedData[$type][$d] ?? 0;
                    }
                  @endphp
                  <tr>
                    <td style="padding: 4.08px;">{{ $type }}</td>
                    <td style="padding: 4.08px; text-align: right; font-weight: bold;">
                      {{ number_format($total) }}
                    </td>
                    @foreach ($days as $d)
                      <td style="padding: 4.08px; text-align: right;">
                        {{ number_format($combinedData[$type][$d] ?? 0) }}
                      </td>
                    @endforeach
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
    <!-- ==================== END FIRST SUMMARY ROW ==================== -->

    <!-- ==================== CHARTS + INTIMATION ROW ==================== -->
    <div class="row mb-3 g-3">

      <!-- Bar Chart Card -->
      <div class="col-lg-4 col-md-12">
        <div class="claims-card">
          <div class="card-title">Month-wise Claims</div>
          <canvas id="myBarChart"></canvas>
        </div>
      </div>

      <!-- Donut Chart Card -->
      <div class="col-lg-3 col-md-12">
        <div class="claims-card" style="height: 290px;">
          <div class="card-title">Claim Distribution (Dept)</div>
          <canvas id="myDonutChart"></canvas>
        </div>
      </div>

      <!-- Empty column to fill 12-grid (optional) -->
      <div class="col-lg-1 d-none d-lg-block"></div>

    </div>
    <!-- ==================== END CHARTS + INTIMATION ROW ==================== -->

    <!-- ==================== TABLES ROW ==================== -->
    <div class="row mb-3 g-3">

      <!-- Last Ten (Settled / Intimated) -->
      <div class="col-lg-4 col-md-8">
        <div class="claims-card" style="height: auto;">
          <div class="data-card p-2" style="height: auto;">

            <!-- Settled Table -->
            <div id="last10-settled-table" style="display: block;">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="card-title mb-0">Last Ten (Settled)</div>
                <div>
                  <button class="btn btn-primary btn-sm me-2" onclick="showLastTenTable('last10-settled')">Settled</button>
                  <button class="btn btn-secondary btn-sm" onclick="showLastTenTable('last10-intimated')">Intimated</button>
                </div>
              </div>

              <table class="table table-sm mb-2">
                <thead>
                  <tr>
                    <th>DATE</th>
                    <th>Claim No</th>
                    <th>Insured</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($apiStatus['data']['LAST10'] as $index => $claim)
                    @if($index < 5)
                      <tr>
                        <td class="text-center">{{ $claim['GSH_SETTLEMENTDATE'] ?? '' }}</td>
                        <td>{{ $claim['GSH_DOC_REF_NO'] ?? '' }}</td>
                        <td style="max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $claim['PPS_DESC'] ?? '' }}">
                          {{ $claim['PPS_DESC'] ?? '' }}
                        </td>
                        <td class="text-end">
                          {{ isset($claim['GSH_LOSSCLAIMED']) ? number_format($claim['GSH_LOSSCLAIMED']) : '' }}
                        </td>
                      </tr>
                    @endif
                  @empty
                    <tr><td colspan="4" class="text-center">No data available</td></tr>
                  @endforelse
                </tbody>
              </table>

              <!-- Buttons -->
              <div class="text-center d-flex justify-content-center gap-2">
                <a href="{{ url('/cr9') }}" target="_blank" class="btn btn-outline-primary btn-sm">See More (Settled)</a>
                <a href="{{ url('/cr7') }}" target="_blank" class="btn btn-outline-primary btn-sm">See More (Intimated)</a>
              </div>
            </div>

            <!-- Intimated Table -->
            <div id="last10-intimated-table" style="display: none;">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="card-title mb-0">Last Ten (Intimated)</div>
                <div>
                  <button class="btn btn-primary btn-sm me-2" onclick="showLastTenTable('last10-settled')">Settled</button>
                  <button class="btn btn-secondary btn-sm" onclick="showLastTenTable('last10-intimated')">Intimated</button>
                </div>
              </div>

              <table class="table table-sm mb-2">
                <thead>
                  <tr>
                    <th>DATE</th>
                    <th>Claim No</th>
                    <th>Insured</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($apiData['LAST10'] as $index => $claim)
                    @if($index < 5)
                      <tr>
                        <td class="text-center">{{ $claim['GIH_INTIMATIONDATE'] ?? '' }}</td>
                        <td>{{ $claim['GIH_DOC_REF_NO'] ?? '' }}</td>
                        <td style="max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $claim['PPS_DESC'] ?? '' }}">
                          {{ $claim['PPS_DESC'] ?? '' }}
                        </td>
                        <td class="text-end">
                          {{ isset($claim['GIH_LOSSCLAIMED']) ? number_format($claim['GIH_LOSSCLAIMED']) : '' }}
                        </td>
                      </tr>
                    @endif
                  @empty
                    <tr><td colspan="4" class="text-center">No data available</td></tr>
                  @endforelse
                </tbody>
              </table>

              <!-- Buttons -->
              <div class="text-center d-flex justify-content-center gap-2">
                <a href="{{ url('/cr4') }}" target="_blank" class="btn btn-outline-primary btn-sm">See More (Settled)</a>
                <a href="{{ url('/cr3') }}" target="_blank" class="btn btn-outline-primary btn-sm">See More (Intimated)</a>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Top Ten Amount Wise -->
      <div class="col-lg-4 col-md-8">
        <div class="claims-card" style="height: auto;">
          <div class="data-card p-2" style="height: auto;">

            <!-- Settled Table -->
            <div id="amt10-settled-table" style="display: block;">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="card-title mb-0">Top Ten Amt Wise (Settled)</div>
                <div>
                  <button class="btn btn-primary btn-sm me-2" onclick="showTopTenTable('amt10-settled')">Settled</button>
                  <button class="btn btn-secondary btn-sm" onclick="showTopTenTable('amt10-intimated')">Intimated</button>
                </div>
              </div>

              <table class="table table-sm mb-2">
                <thead>
                  <tr>
                    <th>DATE</th>
                    <th>Claim No</th>
                    <th>Insured</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($apiStatus['data']['AMT10'] as $index => $claim)
                    @if($index < 5)
                      <tr>
                        <td class="text-center">{{ $claim['GSH_SETTLEMENTDATE'] ?? '' }}</td>
                        <td>{{ $claim['GSH_DOC_REF_NO'] ?? '' }}</td>
                        <td style="max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $claim['PPS_DESC'] ?? '' }}">
                          {{ $claim['PPS_DESC'] ?? '' }}
                        </td>
                        <td class="text-end">
                          {{ isset($claim['GSH_LOSSCLAIMED']) ? number_format($claim['GSH_LOSSCLAIMED']) : '' }}
                        </td>
                      </tr>
                    @endif
                  @empty
                    <tr><td colspan="4" class="text-center">No data available</td></tr>
                  @endforelse
                </tbody>
              </table>

              <!-- Buttons -->
              <div class="text-center d-flex justify-content-center gap-2">
                <a href="{{ url('/cr9') }}" target="_blank" class="btn btn-outline-primary btn-sm">See More (Settled)</a>
                <a href="{{ url('/cr6') }}" target="_blank" class="btn btn-outline-primary btn-sm">See More (Intimated)</a>
              </div>
            </div>

            <!-- Intimated Table -->
            <div id="amt10-intimated-table" style="display: none;">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="card-title mb-0">Top Ten Amt Wise (Intimated)</div>
                <div>
                  <button class="btn btn-primary btn-sm me-2" onclick="showTopTenTable('amt10-settled')">Settled</button>
                  <button class="btn btn-secondary btn-sm" onclick="showTopTenTable('amt10-intimated')">Intimated</button>
                </div>
              </div>

              <table class="table table-sm mb-2">
                <thead>
                  <tr>
                    <th>DATE</th>
                    <th>Claim No</th>
                    <th>Insured</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($apiData['AMT10'] as $index => $claim)
                    @if($index < 5)
                      <tr>
                        <td class="text-center">{{ $claim['GIH_INTIMATIONDATE'] ?? '' }}</td>
                        <td>{{ $claim['GIH_DOC_REF_NO'] ?? '' }}</td>
                        <td style="max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $claim['PPS_DESC'] ?? '' }}">
                          {{ $claim['PPS_DESC'] ?? '' }}
                        </td>
                        <td class="text-end">
                          {{ isset($claim['GIH_LOSSCLAIMED']) ? number_format($claim['GIH_LOSSCLAIMED']) : '' }}
                        </td>
                      </tr>
                    @endif
                  @empty
                    <tr><td colspan="4" class="text-center">No data available</td></tr>
                  @endforelse
                </tbody>
              </table>

              <!-- Buttons -->
              <div class="text-center d-flex justify-content-center gap-2">
                <a href="{{ url('/cr9') }}" target="_blank" class="btn btn-outline-primary btn-sm">See More (Settled)</a>
                <a href="{{ url('/cr6') }}" target="_blank" class="btn btn-outline-primary btn-sm">See More (Intimated)</a>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Branch Wise Settled -->
      <div class="col-lg-2 col-md-3">
        <div class="claims-card" style="height: 288px; padding:7px;">
          <div class="card-title mb-2" style="font-size: 0.97em; margin-bottom:6px;">
            <strong>Branch Wise Settled</strong>
          </div>
          <div class="table-responsive" style="height: calc(100% - 35px); overflow-y: auto;">
            <table class="table table-bordered" style="font-size: 0.92em; width: 100%; margin-bottom: 0;">
              <thead style="background-color: #f5f5f5; font-weight: 600;">
                <tr>
                  <th style="white-space: nowrap; padding:7px;">Branch</th>
                  <th style="white-space: nowrap; padding:7px; text-align:right;">Count</th>
                  <th style="white-space: nowrap; padding:7px; text-align:right;">Amount</th>
                </tr>
              </thead>
              <tbody>
                @forelse($apiBIStatus['data']['branch'] ?? [] as $branch)
                  <tr>
                    <td title="{{ $branch['PLC_DESC'] }}" style="white-space: nowrap;">
                      {{ \Illuminate\Support\Str::limit($branch['PLC_DESC'], 8, '...') ?? 'N/A' }}
                    </td>
                    <td style="text-align:right;">{{ number_format($branch['INTI_DOCS'] ?? 0) }}</td>
                    <td style="text-align:right;">{{ number_format($branch['TOT'] ?? 0) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" style="text-align:center; color:red;">No Branch Data Found</td>
                  </tr>
                @endforelse
              </tbody>
              <tfoot style="background-color: #fafafa;">
                @php
                  $totalInti = collect($apiBIStatus['data']['branch'] ?? [])->sum('INTI_DOCS');
                  $tot_amt_br = collect($apiBIStatus['data']['branch'] ?? [])->sum('TOT');
                @endphp
                <tr>
                  <th style="padding: 6px; text-align: right;">Total</th>
                  <th style="padding: 6px; text-align: right; font-weight:bold;">
                    {{ number_format($totalInti) }}
                  </th>
                  <th style="padding: 6px; text-align: right; font-weight:bold;">
                    {{ number_format($tot_amt_br) }}
                  </th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <!-- Insured Wise Settled -->
      <div class="col-lg-2 col-md-3">
        <div class="claims-card" style="height: 288px; padding:7px;">
          <div class="card-title mb-2" style="font-size: 0.97em; margin-bottom:6px;">
            <strong>Insured Wise Settled</strong>
          </div>
          <div class="table-responsive" style="height: calc(100% - 35px); overflow-y: auto;">
            <table class="table table-bordered" style="font-size: 0.92em; width: 100%; margin-bottom: 0;">
              <thead style="background-color: #f5f5f5; font-weight: 600;">
                <tr>
                  <th style="white-space: nowrap; padding:7px;">Insured</th>
                  <th style="white-space: nowrap; padding:7px; text-align:right;">Count</th>
                  <th style="white-space: nowrap; padding:7px; text-align:right;">Amount</th>
                </tr>
              </thead>
              <tbody>
                @foreach($apiBIStatus['data']['insured'] ?? [] as $insured)
                  <tr>
                    <td style="max-width: 80px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $insured['PPS_DESC'] ?? '' }}">
                      {{ $insured['PPS_DESC'] ?? '' }}
                    </td>
                    <td style="padding: 6px; text-align: right;">{{ number_format($insured['CURRENT_YEAR_COUNT'] ?? 0) }}</td>
                    <td style="padding: 6px; text-align: right;">{{ number_format($insured['TOT'] ?? 0) }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot style="background-color: #fafafa;">
                @php
                  $totalIntiInsured = collect($apiBIStatus['data']['insured'] ?? [])->sum('CURRENT_YEAR_COUNT');
                  $tot_amt = collect($apiBIStatus['data']['insured'] ?? [])->sum('TOT');
                @endphp
                <tr>
                  <th style="padding: 6px; text-align: right;">Total</th>
                  <th style="padding: 6px; text-align: right; font-weight:bold;">
                    {{ number_format($totalIntiInsured) }}
                  </th>
                  <th style="padding: 6px; text-align: right; font-weight:bold;">
                    {{ number_format($tot_amt) }}
                  </th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

    </div>
    <!-- ==================== END TABLES ROW ==================== -->

    <script>
      const donutCtx = document.getElementById('myDonutChart').getContext('2d');
      const myDonutChart = new Chart(donutCtx, {
        type: 'doughnut',
        data: {
          labels: ['Fire', 'Marine', 'Motor', 'Misc', 'Health'],
          datasets: [{
            label: 'Current Year Claims',
            data: [
              {{ $deptLookup['11']['current'] ?? 0 }},
              {{ $deptLookup['12']['current'] ?? 0 }},
              {{ $deptLookup['13']['current'] ?? 0 }},
              {{ $deptLookup['14']['current'] ?? 0 }},
              {{ $deptLookup['16']['current'] ?? 0 }}
            ],
            backgroundColor: [
              'rgba(255, 99, 132, 0.6)',
              'rgba(54, 162, 235, 0.6)',
              'rgba(255, 206, 86, 0.6)',
              'rgba(75, 192, 192, 0.6)',
              'rgba(153, 102, 255, 0.6)'
            ]
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                padding: 5,
                font: { size: 10 }, 
                usePointStyle: true,
                pointStyle: 'circle'
              }
            }
          }
        }
      });

      // Bar Chart
      const barCtx = document.getElementById('myBarChart').getContext('2d');
      const myBarChart = new Chart(barCtx, {
        type: 'bar',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
          datasets: [
            {
              label: 'Current Year',
              data: [
                {{ $currentYearMonthCounts['JAN'] ?? 0 }},
                {{ $currentYearMonthCounts['FEB'] ?? 0 }},
                {{ $currentYearMonthCounts['MAR'] ?? 0 }},
                {{ $currentYearMonthCounts['APR'] ?? 0 }},
                {{ $currentYearMonthCounts['MAY'] ?? 0 }},
                {{ $currentYearMonthCounts['JUN'] ?? 0 }},
                {{ $currentYearMonthCounts['JUL'] ?? 0 }},
                {{ $currentYearMonthCounts['AUG'] ?? 0 }},
                {{ $currentYearMonthCounts['SEP'] ?? 0 }}
              ],
              backgroundColor: 'rgba(54, 162, 235, 0.6)'
            },
            {
              label: 'Last Year',
              data: [
                {{ $lastYearMonthCounts['JAN'] ?? 0 }},
                {{ $lastYearMonthCounts['FEB'] ?? 0 }},
                {{ $lastYearMonthCounts['MAR'] ?? 0 }},
                {{ $lastYearMonthCounts['APR'] ?? 0 }},
                {{ $lastYearMonthCounts['MAY'] ?? 0 }},
                {{ $lastYearMonthCounts['JUN'] ?? 0 }},
                {{ $lastYearMonthCounts['JUL'] ?? 0 }},
                {{ $lastYearMonthCounts['AUG'] ?? 0 }},
                {{ $lastYearMonthCounts['SEP'] ?? 0 }}
              ],
              backgroundColor: 'rgba(255, 99, 132, 0.6)'
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: { beginAtZero: true, ticks: { font: { size: 8 }, maxTicksLimit: 5 } },
            x: { ticks: { font: { size: 8 }, maxRotation: 45, minRotation: 45 } }
          },
          plugins: {
            legend: {
              position: 'bottom',
              labels: { font: { size: 9 }, padding: 5 }
            }
          }
        }
      });

      // Toggle Functions
      function showLastTenTable(tableId) {
        document.getElementById('last10-settled-table').style.display = 'none';
        document.getElementById('last10-intimated-table').style.display = 'none';
        document.getElementById(tableId + '-table').style.display = 'block';
      }

      function showTopTenTable(tableId) {
        document.getElementById('amt10-settled-table').style.display = 'none';
        document.getElementById('amt10-intimated-table').style.display = 'none';
        document.getElementById(tableId + '-table').style.display = 'block';
      }
    </script>
  </div>
</body>
</html>