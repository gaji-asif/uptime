@extends('layouts.app')

@section('title', 'Dashboard')

@push('stylesheets')
  <link href="{{ asset('css/daterangepicker.css') }}" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">

  <style>
    .chart-wrapper .card-body {
      height: 192px;
      min-width: 100%;
    }
    .chart-wrapper .card {
      overflow-x: auto;
      overflow-y:hidden;
    }
    .chart-wrapper .custom-chart-legend {
      overflow-y: auto;
      padding-right: 15px;
    }
    .custom-chart-legend ul {
      list-style: none;
    }
    .custom-chart-legend ul li {
      position: relative;
      margin-bottom: 10px;
      font-weight: 400;
    }
    .custom-chart-legend ul li span{
      display: inline-block;
      width: 20px;
      height: 10px;
      padding: 5px;
      margin-right: 10px;
    }
    .card-title .mdi {
      padding: 4px 8px;
      border: 2px solid #2a82a0;
      border-radius: 50%;
      color: #2a82a0;
      font-size: 24px;
    }

    /* Overwrite the default to keep the scrollbar always visible */
    .content-wrapper ::-webkit-scrollbar {
      -webkit-appearance: none;
      width: 7px;
    }
    .content-wrapper ::-webkit-scrollbar-thumb {
      border-radius: 4px;
      background-color: rgba(0,0,0,.5);
      -webkit-box-shadow: 0 0 1px rgba(255,255,255,.5);
    }

    @media (max-width: 410px) {
      .navbar-brand-wrapper {
        display: none!important;
      }

      #dashboard-filter {
        display: none;
      }

      #dashboard-filter.toggled {
        display: block;
        position: absolute;
        top: 70px;
        left: 0;
        right: 0;
      }

      .daterangepicker, .daterangepicker .drp-calendar {
        width: 100%;
        max-width: 100%;
      }
    }
  </style>
@endpush

@section('header')
  <button id="dashboard-filter-toggler" type="button" class="d-lg-none btn btn-outline-info mr-3">
    <i class="mdi mdi-filter"></i>
  </button>
  <ul id="dashboard-filter" class="navbar-nav navbar-nav-right ml-0">
    <form class="form-inline" action="" method="get">
      <select name="store" id="" class="form-control mr-2" onchange="this.form.submit()">
        <option value="" readonly="">All Stores</option>
        @foreach ($stores as $store)
          <option value="{{ $store->id }}" @if (request('store') == $store->id) {{ 'selected' }} @endif>
            {{ $store->industry_name }}
          </option>
        @endforeach
      </select>
      <select class="form-control mr-2" name="access_level" onchange="this.form.submit()">
        <option value="" @if ( (string) request('access_level') == '') {{ 'selected' }} @endif>All Access Level</option>
        <option value="0" @if (request('access_level') == '0') {{ 'selected' }} @endif>0 - Rep</option>
        <option value="1" @if (request('access_level') == '1')) {{ 'selected' }} @endif>1 - Managers</option>
        <option value="2" @if (request('access_level') == '2')) {{ 'selected' }} @endif>2 - District Leaders / Regional</option>
      </select>
      <input type="hidden" name="from" id="date-from" value="{{ request('from') }}" onchange="this.form.submit()">
      <input type="hidden" name="to" id="date-to" value="{{ request('to') }}" onchange="this.form.submit()">
      <input type="text" id="date-filter" class="form-control" value="" placeholder="Filter by date" autocomplete="off">
    </form>
  </ul>
  <ul class="navbar-nav navbar-nav-right mr-3">
    <form action="{{ route('export') }}" method="post">
      @csrf
      <input type="hidden" name="guard" value="admin">
      <input type="hidden" name="url" value="{{ request()->fullUrl() }}">
      <button type="submit" class="btn btn-outline-info">
        <i class="mdi mdi-printer"></i>
      </button>
    </form>
  </ul>
@endsection

@section('content')
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-6">
        <div class="row row-cols-1">
          <div class="col-md-12 mb-4 chart-wrapper">
            <div class="d-flex justify-content-between align-items-center pb-2">
              <h5 class="font-weight-bold text-muted mb-0">Challenges by <span id="challenges-type">{{ empty(request('store')) ? 'Store' : 'Category' }}</span></h5>
              <div class="{{ !empty(request('store')) ? 'd-none' : '' }}">
                <small class="text-muted">Switch to <span id="toggle-type">Categories</span></small>
                <input type="checkbox" id="toggle-challenges-type" checked data-toggle="toggle" data-size="xs" data-on="Stores" data-off="Categories" data-onstyle="info" data-offstyle="info" style="display:none;">
              </div>
            </div>
            <div class="card">
              <div class="card-body p-3">
                @if (!empty($challengesByStore))
                  <canvas id="challenges-stores"></canvas>
                @else
                  <div class="text-center h-100 d-flex justify-content-center align-items-center">
                    <h1 class="font-weight-bold">N/A</h1>
                  </div>
                @endif
              </div>
              <div class="card-body p-3" style="display: none">
                @if (!empty($challengesByCategory))
                  <canvas id="challenges-categories"></canvas>
                @else
                  <div class="text-center h-100 d-flex justify-content-center align-items-center">
                    <h1 class="font-weight-bold">N/A</h1>
                  </div>
                @endif
              </div>
            </div>
          </div>
          <div class="col-md-12 mb-4 chart-wrapper">
            <div class="d-flex justify-content-between align-items-center pb-2">
              <h5 class="font-weight-bold text-muted mb-0">Challenges by Employee</h5>
              <select name="" id="select-challenges-category" class="form-control form-control-sm w-auto">
                <option value="">All Categories</option>
                @foreach ($challengesByEmployeePerCategory as $category => $categoryGroup)
                  <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
              </select>
            </div>
            <div class="card">
              <div class="card-body p-3">
                @if (!empty($challengesByEmployeePerCategory))
                  <canvas id="challenges-employees"></canvas>
                @else
                  <div class="text-center h-100 d-flex justify-content-center align-items-center">
                    <h1 class="font-weight-bold">N/A</h1>
                  </div>
                @endif
              </div>
            </div>
          </div>
          <div class="col-md-12 mb-4 chart-wrapper">
            <div class="d-flex justify-content-between align-items-center pb-2">
              <h5 class="font-weight-bold text-muted mb-0">Categories by Employee</h5>
              <select name="" id="select-category-employee" class="form-control form-control-sm w-auto">
                <option value="">All Employees</option>
                @foreach ($categoriesByEmployeePerEmployee as $employee => $employeeGroup)
                  <option value="{{ $employee }}">{{ $employee }}</option>
                @endforeach
              </select>
            </div>
            <div class="card">
              <div class="card-body p-3 d-flex">
                @if (!empty($categoriesByEmployeePerEmployee))
                  <div style="min-width: 60%; max-width: 65%">
                    <canvas id="categories-employees"></canvas>
                  </div>
                  <div id="categories-employee-legend" class="custom-chart-legend"></div>
                @else
                  <div class="text-center h-100 w-100 d-flex justify-content-center align-items-center">
                    <h1 class="font-weight-bold">N/A</h1>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="row row-cols-1 row-cols-md-2">
          <div class="col-md-6 mb-4">
            <div class="card">
              <div class="card-body p-3">
                <div class="card-title text-center">
                  <span class="mdi mdi-flag-checkered"></span>
                  <h6 class="font-weight-bold text-muted mt-2">
                    Challenges Completed
                  </h6>
                </div>
                <div class="text-center p-4">
                  <h1 class="font-weight-bold">{{ $metrics['challengesCompleted'] }}</h1>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <div class="card">
              <div class="card-body p-3">
                <div class="card-title text-center">
                  <span class="mdi mdi-arrange-send-backward"></span>
                  <h6 class="font-weight-bold text-muted mt-2">
                    Categories Approved
                  </h6>
                </div>
                <div class="text-center p-4">
                  <h1 class="font-weight-bold">{{ $metrics['categoriesApproved'] }}</h1>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <div class="card">
              <div class="card-body p-3">
                <div class="card-title text-center">
                  <span class="mdi mdi-trophy-award"></span>
                  <h6 class="font-weight-bold text-muted mt-2">
                    Points by Employee
                  </h6>
                </div>
                <div class="mt-3" style="height: 152px; overflow-y: auto;">
                  @if (count($metrics['topEmployees']) == 0)
                    <div class="text-center p-4">
                      <h1 class="font-weight-bold">N/A</h1>
                    </div>
                  @endif
                  <ul class="list-group list-group-flush">
                    @foreach ($metrics['topEmployees'] as $index => $employee)
                      <li class="list-group-item bg-light">
                        <span class="font-weight-bold">{{ $employee->point }}</span>
                        <span>-</span>
                        <span>{{ $employee->full_name }}</span>
                      </li>
                    @endforeach
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <div class="card">
              <div class="card-body p-3">
                <div class="card-title text-center">
                  <span class="mdi mdi-account-card-details"></span>
                  <h6 class="font-weight-bold text-muted mt-2">
                    Total Employees
                  </h6>
                </div>
                <div class="text-center p-5">
                  <h1 class="font-weight-bold">{{ $metrics['totalEmployees'] }}</h1>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <div class="card">
              <div class="card-body p-3">
                <div class="card-title text-center">
                  <span class="mdi mdi-bullhorn"></span>
                  <h6 class="font-weight-bold text-muted mt-2">
                    Total Announcements
                  </h6>
                </div>
                <div class="text-center p-4">
                  <h1 class="font-weight-bold">{{ $metrics['totalAnnouncements'] }}</h1>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <div class="card">
              <div class="card-body p-3">
                <div class="card-title text-center">
                  <span class="mdi mdi-star-circle"></span>
                  <h6 class="font-weight-bold text-muted mt-2">
                    Rewards Purchased
                  </h6>
                </div>
                <div class="text-center p-4">
                  <h1 class="font-weight-bold">{{ $metrics['rewardsPurchased'] }}</h1>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card {{ request('print') ? 'd-block' : 'd-none' }} ">
      <p id="error" class="text-danger"></p>
    </div>
  </div>
@endsection

@push('scripts')
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.15/lodash.min.js"></script>

  <script>
    var colorsPallete = [
      '#FF6633', '#FFB399', '#FF33FF', '#FFFF99', '#00B3E6',
      '#E6B333', '#3366E6', '#999966', '#99FF99', '#B34D4D',
      '#80B300', '#809900', '#E6B3B3', '#6680B3', '#66991A',
      '#FF99E6', '#CCFF1A', '#FF1A66', '#E6331A', '#33FFCC',
      '#66994D', '#B366CC', '#4D8000', '#B33300', '#CC80CC',
      '#66664D', '#991AFF', '#E666FF', '#4DB3FF', '#1AB399',
      '#E666B3', '#33991A', '#CC9999', '#B3B31A', '#00E680',
      '#4D8066', '#809980', '#E6FF80', '#1AFF33', '#999933',
      '#FF3380', '#CCCC00', '#66E64D', '#4D80CC', '#9900B3',
      '#E64D66', '#4DB380', '#FF4D4D', '#99E6E6', '#6666FF'
    ];

    var themeColor = '#2a82a0';

    var chartOptions = {
      maintainAspectRatio: false,
      animation: {
        duration: 3000,
      },
      legend: {
        display: false,
      },
      scales: {
        xAxes: [{
          gridLines: {
            display: false,
          },
          ticks: {
            fontSize: 12,
            fontWeight: 'bold',
            beginAtZero: true,
            autoSkip: false,
            maxRotation: 0,
            minRotation: 0,
          }
        }],
        yAxes: [{
          gridLines: {
            display: true,
          },
          ticks: {
            fontSize: 12,
            fontWeight: 'bold',
            beginAtZero: true,
            suggestedMin: 0,
            precision: 0, // Available in Chart.js 2.7.3 and above
            callback: function(value, index, values) {
              if (Math.floor(value) === value) {
                return value;
              }
            },
            // suggestedMax: 200,
            // stepSize: 5,
          }
        }]
      },
      tooltips: {
        callbacks: {
          title: function(tooltipItem, data) {
            if (tooltipItem[0].label !== '') {
              return tooltipItem[0].label;
            }

            return data.labels[tooltipItem[0].index];
          },
          label: function(tooltipItem, data) {
            const { label, value, index } = tooltipItem;
            const { datasets: [dataset] } = data;

            let counts = [];

            const tooltipLabel = label === ''
              ? Object.keys(dataset.challengesBySubcategory)[index]
              : label;

            const tooltipValue = value === ''
              ? _.flatten(Object.values(dataset.challengesBySubcategory[tooltipLabel])).length
              : value;

            if (dataset.challengesBySubcategory) {
              let subcategories = Object.keys(dataset.challengesBySubcategory[tooltipLabel]);

              for (let i = 0; i < subcategories.length; i++) {
                if (i >= 8) {
                  counts.push(`+${subcategories.length - i + 1} More Subcategories...`);
                  break;
                }

                let subcategory = subcategories[i];

                let count = dataset.challengesBySubcategory[tooltipLabel][subcategory].length;

                if (typeof subcategory === 'string' && typeof count !== 'undefined') {
                  counts.push(subcategory + ": " + count);
                }
              }
            }

            let i, j, lines = [], chunk = 3;
            for (i = 0, j = counts.length; i < j; i += chunk) {
              lines.push(counts.slice(i,i+chunk).join(', '));
            }

            lines.unshift("Total: " + tooltipValue)

            return lines;
          }
        }
      }
    };

    function initChart({ canvas, type, dataset, options = null, showSubCategory = false }) {
      if (!canvas) {
        return;
      }

      let labels = Object.keys(dataset);

      let dataPoints = labels.map(function (item, index) {
        if (showSubCategory) {
          return _.flatten(Object.values(dataset[item])).length;
        }
        return dataset[item].length;
      });

      let colors = labels.map(function (item, index) {
        return colorsPallete[index];
      });

      if (type === 'bar') {
        // Set the chart width
        setBarChartWidth(canvas, labels.length);
      }

      if (type === 'pie') {
        labels = labels.map((label, index) => label + ' (' + dataPoints[index] + ')')
      }

      let data = {
        labels: labels,
        datasets: [
          {
            backgroundColor: colors,
            borderColor: colors,
            borderWidth: 2,
            hoverBackgroundColor: themeColor,
            hoverBorderColor: themeColor,
            data: dataPoints,
            showSubCategory: showSubCategory, // Flag to display sub-categories on hover
            challengesBySubcategory: dataset, // Set reference to original data (to be accessed on hover)
          }
        ]
      };

      return new Chart(canvas, {
        type: type,
        data: data,
        options: options || chartOptions,
      });
    }

    function setBarChartWidth(element, bars) {
      let currentWidth = $(element).parent('.card-body').css('width');
      currentWidth = parseInt(currentWidth.substring(0, currentWidth.length - 2));

      let calculatedWith = bars * 150;

      if (currentWidth < calculatedWith) {
        $(element).parent('.card-body').css('width', calculatedWith + 'px');
      }
    }

    function initChallengesByStoreChart() {
      let canvas = document.getElementById('challenges-stores');
      let challengesByStore = @json($challengesByStore, JSON_PRETTY_PRINT);
      {{--let challengesByStoreWithSubCategory = @json($challengesByStoreWithSubCategory, JSON_PRETTY_PRINT);--}}

      initChart({
        canvas: canvas,
        type: 'bar',
        dataset: challengesByStore,
        showSubCategory: true,
      })
    }

    function initChallengesByCategoryChart() {
      let canvas = document.getElementById('challenges-categories');
      let challengesByCategory = @json($challengesByCategory, JSON_PRETTY_PRINT);
      {{--let challengesBySubCategory = @json($challengesBySubCategory, JSON_PRETTY_PRINT);--}}

      initChart({
        canvas: canvas,
        type: 'bar',
        dataset: challengesByCategory,
        showSubCategory: true,
      });
    }

    function initChallengesByEmployeeChart() {
      let canvas = document.getElementById('challenges-employees');
      let challengesByEmployee = @json($challengesByEmployee, JSON_PRETTY_PRINT);
      let challengesByEmployeePerCategory = @json($challengesByEmployeePerCategory, JSON_PRETTY_PRINT);

      let chart = initChart({
        canvas: canvas,
        type: 'bar',
        dataset: challengesByEmployee,
        showSubCategory: true,
        options: {
          ...chartOptions,
          onClick: selectEmployeeOnChart,
        }
      });

      $('#select-challenges-category').change(function () {
        console.log($(this).val());
        let category = $(this).val();
        let dataset = challengesByEmployee;

        if (category != '') {
          dataset = challengesByEmployeePerCategory[category];
        }

        // Destroy the current chart
        chart.destroy();

        // Reinitialize chart with selected dataset
        chart = initChart({
          canvas: canvas,
          type: 'bar',
          dataset: dataset,
          showSubCategory: true,
          options: {
            ...chartOptions,
            onClick: selectEmployeeOnChart,
          }
        });
      });
    }

    function initCategoryByEmployeeChart() {
      let canvas = document.getElementById('categories-employees');
      let categoriesByEmployee = @json($categoriesByEmployee, JSON_PRETTY_PRINT);
      let categoriesByEmployeePerEmployee = @json($categoriesByEmployeePerEmployee, JSON_PRETTY_PRINT);

      let chart = initChart({
        canvas: canvas,
        type: 'pie',
        dataset: categoriesByEmployee,
        showSubCategory: true,
        options: {
          maintainAspectRatio: false,
          tooltips: chartOptions.tooltips,
          legend: {
            display: false,
          },
          // legendCallback: function (chart) {
            // Return the HTML string here.
            // console.log('legend', chart.data.datasets);
            // var text = [];
            // text.push('<ul class="' + chart.id + '-legend">');
            // for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
            //   text.push('<li><span id="legend-' + i + '-item" style="background-color:' + chart.data.datasets[0].backgroundColor[i] + '"   onclick="updateDataset(event, ' + '\'' + i + '\'' + ')">');
            //   if (chart.data.labels[i]) {
            //     text.push(chart.data.labels[i]);
            //   }
            //   text.push('</span></li>');
            // }
            // text.push('</ul>');
            // return text.join("");
          // },
        }
      });

      if (chart) {
        // Generate legends in the custom legend area
        document.getElementById('categories-employee-legend').innerHTML = chart.generateLegend();
        setPieChartLegendsCount(categoriesByEmployee);
      }

      $('#select-category-employee').change(function () {
        console.log($(this).val());
        let employee = $(this).val();
        let dataset = categoriesByEmployee;

        if (employee != '') {
          dataset = categoriesByEmployeePerEmployee[employee];
        }

        // Destroy the current chart
        chart.destroy();

        // Reinitialize chart with selected dataset
        chart = initChart({
          canvas: canvas,
          type: 'pie',
          dataset: dataset,
          showSubCategory: true,
          options: {
            maintainAspectRatio: false,
            tooltips: chartOptions.tooltips,
            legend: {
              display: false,
            },
          }
        });

        if (chart) {
          // Generate legends in the custom legend area
          document.getElementById('categories-employee-legend').innerHTML = chart.generateLegend();
          setPieChartLegendsCount(dataset);
        }
      });
    }

    function selectEmployeeOnChart(event) {
      let bar = this.getElementsAtEvent(event)[0];
      if (bar) {
        let employee = bar._model;
        $('#select-category-employee').val(employee.label).change();
      }
    }

    function setPieChartLegendsCount(dataset) {

    }

    $(document).ready(function () {
      try {
        $('#dashboard-filter-toggler').click(function () {
          $('#dashboard-filter').toggleClass('toggled');
        });
        /*
       Initialize charts
       */
        initChallengesByStoreChart();
        initChallengesByCategoryChart();
        initChallengesByEmployeeChart();
        initCategoryByEmployeeChart();

        /*
        Handle date filter
        */

        // Set start/end date
        let startDate = {!! 'moment("' . request()->input('from', now()->startOfMonth()->toDateString()) . '")' !!};
        let endDate = {!! 'moment("' . request()->input('to', now()->toDateString()) . '")' !!};

        if (startDate.isValid() && endDate.isValid()) {
          $('#date-filter').val(startDate.format('MM/DD/YYYY') + ' - ' + endDate.format('MM/DD/YYYY'));
        }

        // Initialize the date picker
        $('#date-filter').daterangepicker({
          autoUpdateInput: false,
          showDropdowns: true,
          showCustomRangeLabel: true,
          minDate: '1900-01-01',
          startDate: startDate.isValid() ? startDate : undefined,
          endDate: endDate.isValid() ? endDate : undefined,
          locale: {
            cancelLabel: 'Clear',
            format: 'YYYY-MM-DD',
          },
          ranges: {
            // 'Today': [moment(), moment()],
            // 'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            // 'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            // 'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment()],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Last 3 Months': [moment().subtract(3, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Last 6 Months': [moment().subtract(6, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('month')],
          },
        });

        // Apply and submit selected date range
        $('#date-filter').on('apply.daterangepicker', function(ev, picker) {
          $('#date-from').val(picker.startDate.format('YYYY-MM-DD'));
          $('#date-to').val(picker.endDate.format('YYYY-MM-DD'));

          this.form.submit();
        });

        // Clear out selected date filter
        $('#date-filter').on('cancel.daterangepicker', function(ev, picker) {
          $('#date-filter').val('');
          $('#date-from').val('');
          $('#date-to').val('');

          this.form.submit();
        });

        /*
        Handle challenges type toggle
        */

        $('#toggle-challenges-type').change(function () {
          if (this.checked) {
            $('#challenges-type').text('Store');
            $('#toggle-type').text('Categories');

            $('#challenges-stores').parent('.card-body').show();
            $('#challenges-categories').parent('.card-body').hide();
          } else {
            $('#challenges-type').text('Category');
            $('#toggle-type').text('Stores');

            $('#challenges-stores').parent('.card-body').hide();
            $('#challenges-categories').parent('.card-body').show();
          }
        });

        $('#toggle-challenges-type').prop('checked', Boolean({{ empty(request('store')) }}));
        $('#toggle-challenges-type').change();
        $('#toggle-challenges-type').show();
      } catch (e) {
        console.log(e);

        // Print the error on browser if print/export is requested.
        @if (request('print'))
          $('#error').append((e));
        @endif
      }
    });
  </script>
@endpush