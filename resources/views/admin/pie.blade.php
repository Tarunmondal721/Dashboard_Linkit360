
<div class="card shadow-sm graphs">
    <div class="card-header">
        <button class="btn btn-link pie-btn" style="text-decoration:none"><strong>+ Pie</strong></button>
    </div>
    <div class="card-body" style="display:none">
        <div class="row">

            <div class="col-md-4">
                <!-- Right side: Pie Chart -->
                <strong class="size">Business Type</strong>
                <div class="col-md-12 text-center">
                    <div id="pieChartContainer" class="right-pie pieChartContainer"></div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Right side: Pie Chart -->
                <strong class="size">Country</strong>
                <div class="col-md-12 text-center">
                    <div id="pieChartContainer" class="right-pie pieChartContainer"></div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Right side: Pie Chart -->
                <strong class="size">Company</strong>
                <div class="col-md-12 text-center">
                    <div id="pieChartContainer" class="right-pie pieChartContainer"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <!-- Left side: Pie Chart -->
                <div class="p-3 header">
                    <strong class="size1">Pie Chart Data</strong>
                    <ul id="graphDataList1" class="left-labels"></ul>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Left side: Pie Chart -->
                <div class="p-3 header">
                    <strong class="size1">Pie Chart Data</strong>
                    <ul id="graphDataList2" class="left-labels"></ul>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Left side: Pie Chart -->
                <div class="p-3 header">
                    <strong class="size1">Pie Chart Data</strong>
                    <ul id="graphDataList3" class="left-labels"></ul>
                </div>
            </div>
        </div>

        <div class="separator-container">
            <div class="separator-row">
                <div class="separator-label">
                    <div class="separator-box revenue-box"></div>
                    <label>Revenue</label>
                </div>
                <div class="separator-label">
                    <div class="separator-box gross-revenue-box"></div>
                    <label>Gross Revenue</label>
                </div>
                <div class="separator-label">
                    <div class="separator-box net-revenue-box"></div>
                    <label>Net Revenue</label>
                </div>
                <div class="separator-label">
                    <div class="separator-box total-mo-box"></div>
                    <label>Total Mo</label>
                </div>
                <div class="separator-label">
                    <div class="separator-box cost-box"></div>
                    <label>Cost</label>
                </div>
                <div class="separator-label">
                    <div class="separator-box gp-box"></div>
                    <label>GP</label>
                </div>
            </div>
        </div>

    </div>
</div>


@push('script')
    <script src="{{ asset('assets/js/pie.js') }}"></script>
@endpush
