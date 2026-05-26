@extends('corporate.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert dark alert-icon alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert dark alert-icon alert-error alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
                    <i class="icon fa-warning" aria-hidden="true"></i>{{ session('error') }}
                </div>
            @endif
            @if ($pending_invoice > 0 && !empty($pending_invoice_msg))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ $pending_invoice_msg }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            {{-- ══════════════════════════════════════════════════
                 SECTION 1 — EXECUTIVE SUMMARY (merged with usage filters)
            ══════════════════════════════════════════════════ --}}
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <h3 class="panel-title text-center font-weight-700"
                        style="font-size:1.4rem; letter-spacing:1px; text-transform:uppercase;">
                        @lang("$string_file.executive_summary")
                    </h3>
                </header>
                <div class="panel-body">

                    {{-- KPI row --}}
                    <div class="row mb-20">
                        <div class="col-md-6">
                            <span class="font-weight-600">@lang("$string_file.active_users")</span>
                            <span class="ml-15 font-size-18 font-weight-400" id="kpi_active_users">{{ $active_users }}</span>
                        </div>
                        <div class="col-md-6 text-right">
                            <span class="font-weight-600">@lang("$string_file.average_ride_amount")</span>
                            <span class="ml-15 font-size-18 font-weight-400" id="kpi_avg_ride">
                                {{ $currency_symbol }} {{ number_format($avg_ride_amount, 2) }}
                            </span>
                        </div>
                    </div>

                    <hr style="margin:10px 0 20px;">

                    {{-- Filters row --}}
                    <div class="row mb-20 align-items-end">
                        <div class="col-md-3">
                            <label class="font-weight-600">@lang("$string_file.department")</label>
                            <select id="filter_department" class="form-control select2" style="width:100%;">
                                <option value="">-- @lang("$string_file.all") --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="font-weight-600">@lang("$string_file.employee")</label>
                            <select id="filter_employee" class="form-control select2" style="width:100%;">
                                <option value="">-- @lang("$string_file.employee") --</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <button id="execGetBtn" class="btn btn-primary btn-block">
                                @lang("$string_file.search")
                            </button>
                        </div>
                    </div>

                    {{-- Charts --}}
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="execTripsChart" height="120"></canvas>
                        </div>
                        <div class="col-md-6">
                            <canvas id="execSpendChart" height="120"></canvas>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══════════════════════════════════════════════════
                 SECTION 2 — INVOICING AND BILLING
            ══════════════════════════════════════════════════ --}}
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <h3 class="panel-title text-center font-weight-700"
                        style="font-size:1.4rem; letter-spacing:1px; text-transform:uppercase;">
                        @lang("$string_file.invoice_and_billing")
                    </h3>
                </header>
                <div class="panel-body">

                    {{-- KPI row --}}
                    <div class="row mb-20">
                        <div class="col-md-6">
                            <span class="font-weight-600">@lang("$string_file.credit_limit")</span>
                            <span class="ml-15 font-size-18 font-weight-400">
                                {{ $currency_symbol }} {{ number_format($credit_limit, 2) }}
                            </span>
                        </div>
                        <div class="col-md-6 text-right">
                            <span class="font-weight-600">@lang("$string_file.current_outstanding")</span>
                            <span class="ml-15 font-size-18 font-weight-400">
                                {{ $currency_symbol }} {{ number_format($current_outstanding, 2) }}
                            </span>
                        </div>
                    </div>

                    <hr style="margin:10px 0 20px;">

                    {{-- Year filter --}}
                    <div class="row mb-20 align-items-end">
                        <div class="col-md-3">
                            <label class="font-weight-600">@lang("$string_file.year")</label>
                            <select id="billing_year" class="form-control select2" style="width:100%;">
                                @foreach($invoice_years as $yr)
                                    <option value="{{ $yr }}" {{ $yr == $billing_year ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button id="billingGetBtn" class="btn btn-primary btn-block">
                                @lang("$string_file.search")
                            </button>
                        </div>
                    </div>

                    {{-- Chart --}}
                    <div class="row">
                        <div class="col-md-12">
                            <canvas id="billingChart" height="60"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /.page-content --}}
    </div>{{-- /.page --}}
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const currency    = '{{ $currency_symbol }}';
        const currentYear = '{{ $current_year }}';

        // ── Combo chart builder (bar + line) ──────────────────────────
        function buildComboChart(canvasId, title, barLabel, lineLabel, labels, barData, lineData, isCurrency) {

            const existing = Chart.getChart(canvasId);
            if (existing) existing.destroy();

            const barTopPlugin = {
                id: 'barTopLabel_' + canvasId,
                afterDatasetsDraw(chart) {
                    const { ctx, data } = chart;
                    chart.getDatasetMeta(0).data.forEach((bar, i) => {
                        const val = data.datasets[0].data[i];
                        if (!val) return;
                        ctx.save();
                        ctx.fillStyle = '#444';
                        ctx.font = 'bold 10px sans-serif';
                        ctx.textAlign = 'center';
                        ctx.fillText(val, bar.x, bar.y - 5);
                        ctx.restore();
                    });
                }
            };

            return new Chart(document.getElementById(canvasId), {
                type: 'bar',
                plugins: [barTopPlugin],
                data: {
                    labels: labels,
                    datasets: [
                        {
                            type: 'bar',
                            label: barLabel,
                            data: barData,
                            backgroundColor: 'rgba(54, 116, 181, 0.85)',
                            borderColor: 'rgb(54, 116, 181)',
                            borderWidth: 1,
                            yAxisID: 'y',
                            order: 2,
                        },
                        {
                            type: 'line',
                            label: lineLabel,
                            data: lineData,
                            borderColor: 'rgb(255, 140, 0)',
                            backgroundColor: 'transparent',
                            borderWidth: 2.5,
                            pointRadius: 3,
                            fill: false,
                            tension: 0.3,
                            yAxisID: 'y1',
                            order: 1,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        title: { display: true, text: title, font: { size: 13, weight: '600' } },
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label(ctx) {
                                    const v = ctx.parsed.y ?? 0;
                                    return isCurrency
                                        ? ` ${ctx.dataset.label}: ${currency} ${v.toLocaleString()}`
                                        : ` ${ctx.dataset.label}: ${v}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y:  { beginAtZero: true, position: 'left',  title: { display: true, text: barLabel } },
                        y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: lineLabel } }
                    }
                }
            });
        }

        // ── Load executive charts (with optional filters) ─────────────
        function loadExecCharts() {
            const params = {
                department_id : $('#filter_department').val() || '',
                user_id       : $('#filter_employee').val()   || '',
                category_id   : $('#filter_category').val()   || '',
                year          : currentYear,
            };

            $.get('{{ route("corporate.dashboard.usage") }}', params, function(data) {

                buildComboChart(
                    'execTripsChart',
                    'Total Trips (Monthly / YTD)',
                    'Trip', 'YTD',
                    data.labels, data.trip_data, data.ytd_trips,
                    false
                );
                buildComboChart(
                    'execSpendChart',
                    'Total Spend (Monthly / YTD)',
                    'Trip', 'YTD',
                    data.labels, data.spend_data, data.ytd_spend,
                    true
                );

                // Update KPIs if the AJAX response returns them
                if (data.active_users !== undefined) {
                    $('#kpi_active_users').text(data.active_users);
                }
                if (data.avg_ride_amount !== undefined) {
                    $('#kpi_avg_ride').text(currency + ' ' + parseFloat(data.avg_ride_amount).toFixed(2));
                }
            });
        }

        // Load on page open with no filters
        loadExecCharts();

        $('#execGetBtn').on('click', function() {
            loadExecCharts();
        });

        // ── Billing chart ─────────────────────────────────────────────
        function buildBillingChart(data, yr) {
            const existing = Chart.getChart('billingChart');
            if (existing) existing.destroy();

            const labelPlugin = {
                id: 'billingBarLabel',
                afterDatasetsDraw(chart) {
                    const { ctx: c, data } = chart;
                    data.datasets.forEach((ds, di) => {
                        chart.getDatasetMeta(di).data.forEach((bar, i) => {
                            const val = ds.data[i];
                            if (!val) return;
                            c.save();
                            c.fillStyle = '#333';
                            c.font = 'bold 10px sans-serif';
                            c.textAlign = 'center';
                            c.fillText(val.toLocaleString(), bar.x, bar.y - 5);
                            c.restore();
                        });
                    });
                }
            };

            new Chart(document.getElementById('billingChart'), {
                type: 'bar',
                plugins: [labelPlugin],
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Invoice',
                            data: data.invoice_data,
                            backgroundColor: 'rgba(54, 116, 181, 0.85)',
                            borderColor: 'rgb(54, 116, 181)',
                            borderWidth: 1,
                        },
                        {
                            label: 'Settled Amount',
                            data: data.settled_data,
                            backgroundColor: 'rgba(255, 140, 0, 0.85)',
                            borderColor: 'rgb(255, 140, 0)',
                            borderWidth: 1,
                        },
                        {
                            label: 'Unsettled Amount',
                            data: data.unsettled_data,
                            backgroundColor: 'rgba(160, 160, 160, 0.85)',
                            borderColor: 'rgb(160, 160, 160)',
                            borderWidth: 1,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: { display: true, text: String(yr), font: { size: 14, weight: '600' } },
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label(ctx) {
                                    return ` ${ctx.dataset.label}: ${currency} ${(ctx.parsed.y ?? 0).toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // Initial render from server-side data
        buildBillingChart(@json($billing_chart), {{ $billing_year }});

        $('#billingGetBtn').on('click', function() {
            const yr = $('#billing_year').val();
            $.get('{{ route("corporate.dashboard.billing") }}', { year: yr }, function(data) {
                buildBillingChart(data, yr);
            });
        });
    </script>

    <script>
        // ── Populate employees by department ──────────────────────────
        function getDepartmentUsers(dept_id) {
            if (!dept_id) {
                $('#filter_employee').html('<option value="">-- @lang("$string_file.employee") --</option>');
                if ($('#filter_employee').hasClass('select2-hidden-accessible')) {
                    $('#filter_employee').select2('destroy').select2();
                }
                return;
            }

            $('#filter_employee').html('<option value="">Loading...</option>');

            $.ajax({
                url  : '{{ route("corporate.getDepartmentUsers") }}',
                type : 'GET',
                data : { department_id: dept_id },
                success: function(response) {
                    let options = '<option value="">-- @lang("$string_file.employee") --</option>';

                    if (response.users && response.users.length > 0) {
                        response.users.forEach(function(user) {
                            options += `<option value="${user.id}">${user.first_name} ${user.last_name ?? ''} (${user.UserPhone})</option>`;
                        });
                    } else {
                        options = '<option value="">No users found</option>';
                    }

                    $('#filter_employee').html(options);

                    if ($('#filter_employee').hasClass('select2-hidden-accessible')) {
                        $('#filter_employee').select2('destroy').select2();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching users:', error);
                    $('#filter_employee').html('<option value="">Error loading users</option>');
                }
            });
        }

        $(document).ready(function() {

            // ── Fire AJAX whenever department changes ──────────────────
            $('#filter_department').on('change', function() {
                getDepartmentUsers($(this).val());
            });

            // ── On page load: restore previously selected dept/user ────
            const selectedDept = $('#filter_department').val();
            if (selectedDept) {
                getDepartmentUsers(selectedDept);

                const selectedUserId = '{{ request("user_id") ?? (isset($arr_search["user_id"]) ? $arr_search["user_id"] : "") }}';
                if (selectedUserId) {
                    // Wait for AJAX to populate the dropdown first
                    setTimeout(function() {
                        $('#filter_employee').val(selectedUserId);
                        if ($('#filter_employee').hasClass('select2-hidden-accessible')) {
                            $('#filter_employee').trigger('change');
                        }
                    }, 600);
                }
            }
        });
    </script>
@endsection