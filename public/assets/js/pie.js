



$(function () {
    var current_fs, next_fs, previous_fs;
    var path = window.location.pathname;
    if (path == "/" || path == "/home" || path == "/dashboard/operator" || path == "/dashboard/country" || path == "/dashboard/company" || path == "/dashboard/business") {
        mixedGraph();
    }

});

function mixedGraph() {

    var graph_data = {};

    setTimeout(function () {
        $(document).find('#dtbl:eq(0) tbody tr').each(function () {

            var current_total_mo = [];
            var current_revenue = [];
            var current_gross_revenue = [];
            var current_net_revenue = [];
            var current_cost = [];
            var currentMonthROI = [];
            var current_pnl = [];


            var k = 0;
            $(this).find('span.current_revenue').each(function () {
                current_revenue = $(this).text();

                k++;
            });

            var m = 0;
            $(this).find('span.current_gross_revenue').each(function () {
                current_gross_revenue = $(this).text();
                m++;
            });

            $(this).find('span.current_net_revenue').each(function () {
                current_net_revenue = $(this).text();
                m++;
            });

            var n = 0;
            $(this).find('span.current_total_mo').each(function () {
                current_total_mo = $(this).text();
                n++;
            });

            var o = 0;
            $(this).find('span.current_cost').each(function () {
                current_cost = $(this).text();
                o++;
            });

            var p = 0;
            $(this).find('span.currentMonthROI').each(function () {
                currentMonthROI = $(this).text();
                p++;
            });

            var r = 0;
            $(this).find('span.current_pnl').each(function () {
                current_pnl = $(this).text();
                r++;
            });


            if (current_revenue.length > 0) {
                graph_data['Revenue'] = current_revenue;
            }
            if (current_gross_revenue.length > 0) {
                graph_data['Gross Revenue'] = current_gross_revenue;
            }
            if (current_net_revenue.length > 0) {
                graph_data['Net Revenue'] = current_net_revenue;
            }
            if (current_total_mo.length > 0) {
                graph_data['Total Mo'] = current_total_mo;
            }
            if (current_cost.length > 0) {
                graph_data['Cost'] = current_cost;
            }
            // if (currentMonthROI.length > 0) {
            //     graph_data['current_roi'] = currentMonthROI;
            // }
            if (current_pnl.length > 0) {
                graph_data['GP'] = current_pnl;
            }


        });


        //console.log("===");
        // console.log(graph_data);

        createPieChart(graph_data);
    }, 2000);
}






function createPieChart(data) {
    var cleanedData = {};
    Object.keys(data).forEach(function (key) {
        var cleanedValue = parseFloat(data[key].replace(/[^\d.\s]/g, '').replace(/\s/g, ''));
        cleanedData[key] = cleanedValue;
    });

    console.log(cleanedData);

    var containers = document.getElementsByClassName('pieChartContainer');
    Array.from(containers).forEach(function(container, containerIndex) {
        var canvas = document.createElement('canvas');
        container.appendChild(canvas);

        // Define colors array
        var colors = [
            'rgba(255, 99, 132, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(0, 255, 210, 0.5)',
            'rgba(75, 192, 192, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(255, 159, 64, 0.5)'
        ];

        var graphDataList = document.getElementById(`graphDataList${containerIndex + 1}`);

        // Clear previous data
        graphDataList.innerHTML = '';

        // Populate graph data list with pie colors
        Object.keys(cleanedData).forEach(function (key, index) {
            var color = colors[index % colors.length];
            var listItem = document.createElement('li');
            listItem.classList.add('data-item');
            listItem.innerHTML = `<span class="key">${key}</span> <span class="data">${cleanedData[key].toLocaleString()}</span>`;
            graphDataList.appendChild(listItem);
        });

        // Create dataset for pie chart with colors
        var dataset = {
            data: Object.values(cleanedData),
            backgroundColor: colors,
            borderColor: colors,
            borderWidth: 2
        };

        var pieChart = new Chart(canvas, {
            type: 'pie',
            data: {
                labels: Object.keys(cleanedData),
                datasets: [dataset]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,

                    legend: {
                        display: false // Hide legend
                    },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                var label = tooltipItem.label || '';
                                var value = tooltipItem.raw || '';
                                return label + ': ' + value.toLocaleString();
                            }
                        }
                    }

            }
        });
    });



}



































