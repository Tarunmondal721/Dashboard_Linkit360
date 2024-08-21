var base_url= window.location.origin+'/';
$('a:not(a[href="javascript:void(0)"])').on('click', function(){
	$('.loader_wrapper').css('display','')
})
$('button[type="submit"]').on('click', function(){
	$('.loader_wrapper').css('display','')
})

$('.toggle-menu').on('click',function(){
	if($(this).find('span').html() == '<i class="fa fa-chevron-down"></i>'){
		$(this).next('ul').css('display','block')
		$(this).find('span').html('<i class="fa fa-chevron-up"></i>')
	}else{
		$(this).next('ul').css('display','none')
		$(this).find('span').html('<i class="fa fa-chevron-down"></i>')
	}
})

//alert("updated jjjjj");

function formatNumber(nStr) {
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}
$('.detail-download-xls1').on('click', function(){
	$('#excelData').excelexportjs({
		containerid: 'excelData',
		datatype: 'table'
	});
});
$('.detail-download-xls2').on('click', function(){
	$('#excelData2').excelexportjs({
		containerid: 'excelData2',
		datatype: 'table'
	});
});
$('.detail-download-xls3').on('click', function(){
	$('#excelData3').excelexportjs({
		containerid: 'excelData3',
		datatype: 'table'
	});
});
$('.detail-download-xls4').on('click', function(){
	$('#excelData4').excelexportjs({
		containerid: 'excelData4',
		datatype: 'table'
	});
});

$('#from_datepicker').on('change', function(){
	if($(this).val() == ''){
		$('#to_datepicker').prop('disabled', true)
		$('.search_btn_summery_data1').prop('disabled', true)
	}else{
		$('#to_datepicker').prop('disabled', false)
		$('.search_btn_summery_data1').prop('disabled', false)
	}
})

//Dailywise start
function fetchGraph(yeardata,yaxes){
	// Summary Graph
	var newarr = [];
	var avgarr = [];
	var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	var ctx = document.getElementById('revCanvas').getContext('2d');
	var dates = yeardata.dates;
	$.each(yeardata.monthdata,function(k, v){
		if(v != ''){
			newarr.push(parseFloat(v.replace(/,/g, '')));
		} else {
			newarr.push(parseInt(0));
		}
	});
	//console.log(newarr);

	var t_rev = 0;
	var t_revenue = [...newarr]
	t_revenue = t_revenue.reverse()
	for(i=0; i<t_revenue.length; i++){
		day = i+1
		t_rev += parseFloat(t_revenue[i])
		avgarr[i] = (t_rev/day).toFixed(2)
	}
	avgarr = avgarr.reverse()
	//console.log(avgarr)

	var config = {
		type: 'line',
		data: {
			labels: dates,
			datasets: [{
				label: 'Total',
				backgroundColor: window.chartColors.red,
				borderColor: window.chartColors.red,
				data: newarr,
				fill: false,
			},{
				label: 'Average',
				backgroundColor: window.chartColors.orange,
				borderColor: window.chartColors.orange,
				data: avgarr,
				fill: false,
			}]
		},
		options: {
			responsive: true,
			title: {
				display: true,
				text: 'Summary Data Chart'
			},
			scales: {
				xAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Date'
					}
				}],
				yAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Value'
					}
				}]
			}
		}
	};
	window.myLine = new Chart(ctx, config);
}

//average line1
function fetchGraph_Average_Rev(yeardata,yaxes){
	// Summary Graph
	var newarr = [];
	var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	var ctx = document.getElementById('avg-revCanvas').getContext('2d');
	var dates = yeardata.dates;
	$.each(yeardata.monthdata,function(k, v){
		if(v != ''){
			newarr.push(parseFloat(v.replace(/,/g, '')));
		} else {
			newarr.push(parseInt(0));
		}
	});
	//console.log(newarr);

	var config = {
		type: 'line',
		data: {
			labels: dates,
			datasets: [{
				label: '',
				backgroundColor: window.chartColors.orange,
				borderColor: window.chartColors.orange,
				data: newarr,
				fill: false,
			}]
		},
		options: {
			responsive: true,
			title: {
				display: true,
				text: 'Average Line Revenue Chart'
			},
			scales: {
				xAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Date'
					}
				}],
				yAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Value'
					}
				}]
			}
		}
	};
	window.myLine = new Chart(ctx, config);
}

//average line2
function fetchGraph_Average_Subs(yeardata,yaxes){
	// Summary Graph
	var newarr = [];
	var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	var ctx = document.getElementById('avg-subCanvas').getContext('2d');
	var dates = yeardata.dates;
	$.each(yeardata.monthdata,function(k, v){
		if(v != ''){
			newarr.push(parseFloat(v.replace(/,/g, '')));
		} else {
			newarr.push(parseInt(0));
		}
	});
	//console.log(newarr);

	var config = {
		type: 'line',
		data: {
			labels: dates,
			datasets: [{
				label: '',
				backgroundColor: window.chartColors.green,
				borderColor: window.chartColors.green,
				data: newarr,
				fill: false,
			}]
		},
		options: {
			responsive: true,
			title: {
				display: true,
				text: 'Average Line Subactive Chart'
			},
			scales: {
				xAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Date'
					}
				}],
				yAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Value'
					}
				}]
			}
		}
	};
	window.myLine = new Chart(ctx, config);
}

function mixedfetchGraph(yeardata){
	var reg = [];
	var unreg = [];
	var purged = [];
	var subs = [];
	var ctx = document.getElementById('mixedCanvas').getContext('2d');
	var dates = yeardata.dates;
	$.each(yeardata.monthdata.t_reg,function(k, v){
		if(v != ''){
			reg.push(parseFloat(v.replace(/,/g, '')));
		} else {
			reg.push(parseInt(0));
		}
	});
	$.each(yeardata.monthdata.t_unreg,function(k, v){
		if(v != ''){
			unreg.push(parseFloat(v.replace(/,/g, '')));
		} else {
			unreg.push(parseInt(0));
		}
	});
	$.each(yeardata.monthdata.t_purged,function(k, v){
		if(v != ''){
			purged.push(parseFloat(v.replace(/,/g, '')));
		} else {
			purged.push(parseInt(0));
		}
	});
	$.each(yeardata.monthdata.t_actvsubs,function(k, v){
		if(v != ''){
			subs.push(v);
		} else {
			subs.push(parseInt(0));
		}
	});
	/*console.log(reg);
	console.log(unreg);
	console.log(purged);
	console.log(subs);
*/
	window.mixedChart = new Chart(ctx, {
	    type: 'bar',
	    data: {
	      labels: dates,
	      datasets: [{
	          label: "Reg",
	          type: "bar",
	          backgroundColor: window.chartColors.purple,
	          data: reg,
	        }, {
	          label: "Unreg",
	          type: "bar",
	          backgroundColor: window.chartColors.red,
	          data: unreg,
	          stack: 'bar-stacked'
	        }, {
	          label: "Purged",
	          type: "bar",
	          backgroundColor: window.chartColors.blue,
	          data: purged,
	          stack: 'bar-stacked'
	        }, {
	          label: "Net New Sub",
	          type: "line",
	          borderColor: "#8e5ea2",
	          data: subs,
	          fill: false
	        }
	      ]
	    },
	    options: {
	      	responsive: true,
			title: {
				display: true,
				text: 'Summary Data Chart of Reg, Unreg, Purged & Net New Sub'
			},
			tooltips: {
				mode: 'index',
				intersect: false
			},
			scales: {
				xAxes: [{
					stacked: true,
				}],
				yAxes: [{
					stacked: true
				}]
			}
	    }
	});
}

function mixedaxesfetchGraph(yeardata){
	var renewal = [];
	var subs = [];
	var asubs = [];
	var ctx = document.getElementById('subCanvas').getContext('2d');
	var dates = yeardata.dates;
	//console.warn(yeardata.monthdata.t_subactive)
	$.each(yeardata.monthdata.t_subactive,function(k, v){
		if(v != ''){
			subs.push(v.toFixed(2));
		} else {
			subs.push(parseInt(0));
		}
	});
	$.each(yeardata.monthdata.t_renewal,function(k, v){
		if(v != ''){
			renewal.push(parseFloat(v.replace(/,/g, '')));
		} else {
			renewal.push(parseInt(0));
		}
	});

	//console.log(subs);
	// console.log(renewal);
	// console.log(dates);

	var t_sub = 0;
	var t_subactive = [...subs];
	t_subactive = t_subactive.reverse()
	for(j=0; j<t_subactive.length; j++){
		day = j+1
		t_sub += parseInt(t_subactive[j])
		asubs[j] = Math.round(t_sub/day)
	}
	asubs = asubs.reverse()
	//console.log(asubs);

	window.mixedChart = new Chart(ctx, {
	    type: 'bar',
	    data: {
	      labels: dates,
	      datasets: [{
	          label: "T Sub",
	          type: "bar",
	          yAxisID: 'y-axis-1',
	          backgroundColor: window.chartColors.purple,
	          data: subs,
	        },{
				label: "Avg Sub",
				type: "bar",
				yAxisID: 'y-axis-1',
				backgroundColor: window.chartColors.green,
				data: asubs,
				fill: false
			},{
	          label: "Renewal",
	          type: "line",
	          yAxisID: 'y-axis-2',
	          borderColor: window.chartColors.yellow,
	          data: renewal,
	          fill: false
	        }
	      ]
	    },
	    options: {
	      	responsive: true,
			title: {
				display: true,
				text: 'Summary Data Chart of Subs and Renewal (Subactive value must x1000)'
			},
			tooltips: {
				mode: 'index',
				intersect: true
			},
			scales: {
				yAxes: [{
					type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
					display: true,
					position: 'left',
					id: 'y-axis-1',
				}, {
					type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
					display: true,
					position: 'right',
					id: 'y-axis-2',
					gridLines: {
						drawOnChartArea: false
					}
				}],
			}
	    }
	});
}

function callAjaxForGraph(graphdata,yaxes){
	var csrf_token= $("#csrf_token").val();
	//console.log(graphdata);
	$.ajax({
		url    : base_url+'dashboard/getsummarygraphdata',
		type   : 'post',
		data   : {'graphdata':graphdata,'yaxes':yaxes},
		datatype: 'json',
		headers: {
            'X-CSRF-Token': csrf_token
      	},
		success: function (response)
		{
			var res = JSON.parse(response);
			fetchGraph(res,yaxes);
		},
		error  : function ()
		{
			console.log('internal server error');
		}
	});
}

//average line1
function callAjaxForGraph_Average_Rev(graphdata,yaxes){
	var csrf_token= $("#csrf_token").val();
	//console.log(graphdata)
	//console.log(yaxes)
	$.ajax({
		url    : base_url+'dashboard/getsummarygraphdata',
		type   : 'post',
		data   : {'graphdata':graphdata,'yaxes':yaxes},
		datatype: 'json',
		headers: {
            'X-CSRF-Token': csrf_token
      	},
		success: function (response)
		{
			var res = JSON.parse(response);
			//console.log(res)
			fetchGraph_Average_Rev(res,yaxes);
		},
		error  : function ()
		{
			console.log('internal server error');
		}
	});
}

//average line2
function callAjaxForGraph_Average_Subs(graphdata,yaxes){
	var csrf_token= $("#csrf_token").val();
	//console.log(graphdata)
	//console.log(yaxes)
	$.ajax({
		url    : base_url+'dashboard/getsummarygraphdata',
		type   : 'post',
		data   : {'graphdata':graphdata,'yaxes':yaxes},
		datatype: 'json',
		headers: {
            'X-CSRF-Token': csrf_token
      	},
		success: function (response)
		{
			var res = JSON.parse(response);
			//console.log(res)
			fetchGraph_Average_Subs(res,yaxes);
		},
		error  : function ()
		{
			console.log('internal server error');
		}
	});
}

function callAjaxForMixedGraph(graphdata){
	var csrf_token= $("#csrf_token").val();
	//console.log(graphdata);
	$.ajax({
		url    : base_url+'dashboard/getmixedgraphdata',
		type   : 'post',
		data   : {'graphdata':graphdata},
		datatype: 'json',
		headers: {
            'X-CSRF-Token': csrf_token
      	},
		success: function (response)
		{
			var res = JSON.parse(response);
			mixedfetchGraph(res);
		},
		error  : function ()
		{
			console.log('internal server error');
		}
	});
}

function callMixedAjaxForAxesGraph(graphdata){
	var csrf_token= $("#csrf_token").val();
	//console.log(graphdata);
	$.ajax({
		url    : base_url+'dashboard/getmixedgraphaxesdata',
		type   : 'post',
		data   : {'graphdata':graphdata},
		datatype: 'json',
		headers: {
            'X-CSRF-Token': csrf_token
      	},
		success: function (response)
		{
			var res = JSON.parse(response);
			mixedaxesfetchGraph(res);
		},
		error  : function ()
		{
			console.log('internal server error');
		}
	});
}

function callGraph(button){
	//alert("ddddd");
	var graph_data = {};
	setTimeout(function(){
		$(document).find('#dtbl:eq(0) tbody tr').each(function(){
			var t_revenue = [];
			var t_average = [];
			var t_monthend = [];
			var t_subactive = [];
			var t_renewal = [];
			var i =0;
			$(this).find('td.gross_revenue').each(function(){
				t_revenue[i] = $(this).text();
				i++;
			});
			var j = 0;
			$(this).find('td.subactive_data').each(function(){
				t_subactive[j] = $(this).text();
				j++;
			});
			var k = 0;
			$(this).find('td.total_sent_data').each(function(){
				t_renewal[k] = $(this).text();
				k++;
			});

			if(t_revenue.length > 0){
				graph_data['t_revenue'] = t_revenue;
			}
			if(t_subactive.length > 0){
				graph_data['t_subactive'] = t_subactive;
			}
			if(t_renewal.length > 0){
				graph_data['t_renewal'] = t_renewal;
			}

			graph_data['t_average'] = t_average;
			graph_data['t_monthend'] = t_monthend;
		});

		//console.log(graph_data);
		//callAjaxForGraph(graph_data,button);
	},2000);
}

//average line1
function callGraph_Average_Rev(button){
	//alert("ddddd");
	var graph_data = {};
	setTimeout(function(){
		$(document).find('#dtbl:eq(0) tbody tr').each(function(){
			var t_revenue = [];
			var t_average = [];
			var t_monthend = [];
			var t_subactive = [];
			var t_renewal = [];
			var i =0;
			$(this).find('td.gross_revenue').each(function(){
				t_revenue[i] = $(this).text();
				i++;
			});
			var t_rev = 0;
			t_revenue = t_revenue.reverse()
			for(i=0; i<t_revenue.length; i++){
				day = i+1
				t_rev += parseFloat(t_revenue[i].replace(/\,/g,''))
				t_revenue[i] = (t_rev/day).toFixed(2)
			}
			t_revenue = t_revenue.reverse()

			var j = 0;
			$(this).find('td.subactive_data').each(function(){
				t_subactive[j] = $(this).text();
				j++;
			});
			var k = 0;
			$(this).find('td.total_sent_data').each(function(){
				t_renewal[k] = $(this).text();
				k++;
			});
			if(t_revenue.length > 0){
				graph_data['t_revenue'] = t_revenue;
			}
			if(t_subactive.length > 0){
				graph_data['t_subactive'] = t_subactive;
			}
			if(t_renewal.length > 0){
				graph_data['t_renewal'] = t_renewal;
			}

			graph_data['t_average'] = t_average;
			graph_data['t_monthend'] = t_monthend;
		});

		//console.log(graph_data);
		//callAjaxForGraph_Average_Rev(graph_data,button);
	},2000);
}

//average line2
function callGraph_Average_Subs(button){
	//alert("ddddd");
	var graph_data = {};
	setTimeout(function(){
		$(document).find('#dtbl:eq(0) tbody tr').each(function(){
			var t_revenue = [];
			var t_average = [];
			var t_monthend = [];
			var t_subactive = [];
			var t_renewal = [];
			var i =0;
			$(this).find('td.gross_revenue').each(function(){
				t_revenue[i] = $(this).text();
				i++;
			});

			var j = 0;
			$(this).find('td.subactive_data').each(function(){
				t_subactive[j] = $(this).text();
				j++;
			});
			var t_sub = 0;
			t_subactive = t_subactive.reverse()
			for(j=0; j<t_subactive.length; j++){
				day = j+1
				t_sub += parseInt(t_subactive[j].replace(/\,/g,''))
				t_subactive[j] = Math.ceil(t_sub/day)
				//console.log(day, t_sub, t_subactive[j])
			}
			t_subactive = t_subactive.reverse()

			var k = 0;
			$(this).find('td.total_sent_data').each(function(){
				t_renewal[k] = $(this).text();
				k++;
			});
			if(t_revenue.length > 0){
				graph_data['t_revenue'] = t_revenue;
			}
			if(t_subactive.length > 0){
				graph_data['t_subactive'] = t_subactive;
			}
			if(t_renewal.length > 0){
				graph_data['t_renewal'] = t_renewal;
			}

			graph_data['t_average'] = t_average;
			graph_data['t_monthend'] = t_monthend;
		});

		//console.log(graph_data);
		//callAjaxForGraph_Average_Subs(graph_data,button);
	},2000);
}

function mixedGraph(){
	var graph_data = {};
	setTimeout(function(){
		$(document).find('#dtbl:eq(0) tbody tr').each(function(){
			var t_purged = [];
			var t_reg = [];
			var t_unreg = [];

			var k = 0;
			$(this).find('td.reg_data').each(function(){
				t_reg[k] = $(this).text();
				k++;
			});

			var m = 0;
			$(this).find('td.unreg_data').each(function(){
				t_unreg[m] = $(this).text();
				m++;
			});

			var n = 0;
			$(this).find('td.purged_data').each(function(){
				t_purged[n] = $(this).text();
				n++;
			});


			if(t_reg.length > 0){
				graph_data['t_reg'] = t_reg;
			}
			if(t_unreg.length > 0){
				graph_data['t_unreg'] = t_unreg;
			}
			if(t_purged.length > 0){
				graph_data['t_purged'] = t_purged;
			}
		});
		//console.log("===");
		//console.log(graph_data);
		callAjaxForMixedGraph(graph_data);
	},2000);
}

function mixedAxesGraph(){
	var graph_data = {};
	setTimeout(function(){
		$(document).find('#dtbl:eq(0) tbody tr').each(function(){
			var t_subactive = [];
			var t_renewal = [];
			var j = 0;
			$(this).find('td.subactive_data').each(function(){
				t_subactive[j] = $(this).text();
				j++;
			});
			var k = 0;
			$(this).find('td.total_sent_data').each(function(){
				t_renewal[k] = $(this).text();
				k++;
			});
			if(t_subactive.length > 0){
				graph_data['t_subactive'] = t_subactive;
			}
			if(t_renewal.length > 0){
				graph_data['t_renewal'] = t_renewal;
			}
		});

		//console.log(graph_data);
		callMixedAjaxForAxesGraph(graph_data);
	},2000);
}
//Daily wise end

//Month wise start
function fetchGraph_Monthly(yeardata,yaxes){
	// Summary Graph
	var newarr = [];
	var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	var ctx = document.getElementById('revCanvas-monthly').getContext('2d');
	var dates = []
	for(date of yeardata.dates){
		dates.push(months[date-1])
	}
	$.each(yeardata.monthdata,function(k, v){
		if(v != ''){
			newarr.push(parseFloat(v.replace(/,/g, '')));
		} else {
			newarr.push(parseInt(0));
		}
	});
	//console.log(dates);

	var config = {
		type: 'line',
		data: {
			labels: dates,
			datasets: [{
				label: '',
				backgroundColor: window.chartColors.red,
				borderColor: window.chartColors.red,
				data: newarr,
				fill: false,
			}]
		},
		options: {
			responsive: true,
			title: {
				display: true,
				text: 'Summary Data Chart'
			},
			scales: {
				xAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Month'
					}
				}],
				yAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Value'
					}
				}]
			}
		}
	};
	window.myLine = new Chart(ctx, config);
}

function mixedfetchGraph_Monthly(yeardata){
	var reg = [];
	var unreg = [];
	var purged = [];
	var subs = [];
	var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	var ctx = document.getElementById('mixedCanvas-monthly').getContext('2d');
	var dates = [];
	for(date of yeardata.dates){
		dates.push(months[date-1])
	}
	$.each(yeardata.monthdata.t_reg,function(k, v){
		if(v != ''){
			reg.push(parseFloat(v.replace(/,/g, '')));
		} else {
			reg.push(parseInt(0));
		}
	});
	$.each(yeardata.monthdata.t_unreg,function(k, v){
		if(v != ''){
			unreg.push(parseFloat(v.replace(/,/g, '')));
		} else {
			unreg.push(parseInt(0));
		}
	});
	$.each(yeardata.monthdata.t_purged,function(k, v){
		if(v != ''){
			purged.push(parseFloat(v.replace(/,/g, '')));
		} else {
			purged.push(parseInt(0));
		}
	});
	$.each(yeardata.monthdata.t_actvsubs,function(k, v){
		if(v != ''){
			subs.push(v);
		} else {
			subs.push(parseInt(0));
		}
	});
	/*console.log(reg);
	console.log(unreg);
	console.log(purged);
	console.log(subs);
*/
	window.mixedChart = new Chart(ctx, {
	    type: 'bar',
	    data: {
	      labels: dates,
	      datasets: [{
	          label: "Reg",
	          type: "bar",
	          backgroundColor: window.chartColors.purple,
	          data: reg,
	        }, {
	          label: "Unreg",
	          type: "bar",
	          backgroundColor: window.chartColors.red,
	          data: unreg,
	          stack: 'bar-stacked'
	        }, {
	          label: "Purged",
	          type: "bar",
	          backgroundColor: window.chartColors.blue,
	          data: purged,
	          stack: 'bar-stacked'
	        }, {
	          label: "Net New Sub",
	          type: "line",
	          borderColor: "#8e5ea2",
	          data: subs,
	          fill: false
	        }
	      ]
	    },
	    options: {
	      	responsive: true,
			title: {
				display: true,
				text: 'Summary Data Chart of Reg, Unreg, Purged & Net New Sub'
			},
			tooltips: {
				mode: 'index',
				intersect: false
			},
			scales: {
				xAxes: [{
					stacked: true,
				}],
				yAxes: [{
					stacked: true
				}]
			}
	    }
	});
}

function mixedaxesfetchGraph_Monthly(yeardata){
	var renewal = [];
	var subs = [];
	var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	var ctx = document.getElementById('subCanvas-monthly').getContext('2d');
	var dates = [];
	for(date of yeardata.dates){
		dates.push(months[date-1])
	}
	$.each(yeardata.monthdata.t_subactive,function(k, v){
		if(v != ''){
			subs.push(v.toFixed(2));
		} else {
			subs.push(parseInt(0));
		}
	});
	$.each(yeardata.monthdata.t_renewal,function(k, v){
		if(v != ''){
			renewal.push(parseFloat(v.replace(/,/g, '')));
		} else {
			renewal.push(parseInt(0));
		}
	});

	/*console.log(subs);
	console.log(renewal);
	console.log(dates);*/

	window.mixedChart = new Chart(ctx, {
	    type: 'bar',
	    data: {
	      labels: dates,
	      datasets: [{
	          label: "Subactive",
	          type: "bar",
	          yAxisID: 'y-axis-1',
	          backgroundColor: window.chartColors.purple,
	          data: subs,
	        }, {
	          label: "Renewal",
	          type: "line",
	          yAxisID: 'y-axis-2',
	          borderColor: window.chartColors.yellow,
	          data: renewal,
	          fill: false
	        }
	      ]
	    },
	    options: {
	      	responsive: true,
			title: {
				display: true,
				text: 'Summary Data Chart of Subs and Renewal (Subactive value must x1000)'
			},
			tooltips: {
				mode: 'index',
				intersect: true
			},
			scales: {
				yAxes: [{
					type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
					display: true,
					position: 'left',
					id: 'y-axis-1',
				}, {
					type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
					display: true,
					position: 'right',
					id: 'y-axis-2',
					gridLines: {
						drawOnChartArea: false
					}
				}],
			}
	    }
	});
}

function callAjaxForGraph_Monthly(graphdata,yaxes){
	var csrf_token= $("#csrf_token").val();
	//console.log(graphdata);
	$.ajax({
		url    : base_url+'dashboard/getsummarygraphdata',
		type   : 'post',
		data   : {'graphdata':graphdata,'yaxes':yaxes},
		datatype: 'json',
		headers: {
            'X-CSRF-Token': csrf_token
      	},
		success: function (response)
		{
			var res = JSON.parse(response);
			fetchGraph_Monthly(res,yaxes);
		},
		error  : function ()
		{
			console.log('internal server error');
		}
	});
}

function callAjaxForMixedGraph_Monthly(graphdata){
	var csrf_token= $("#csrf_token").val();
	//console.log(graphdata);
	$.ajax({
		url    : base_url+'dashboard/getmixedgraphdata',
		type   : 'post',
		data   : {'graphdata':graphdata},
		datatype: 'json',
		headers: {
            'X-CSRF-Token': csrf_token
      	},
		success: function (response)
		{
			var res = JSON.parse(response);
			mixedfetchGraph_Monthly(res);
		},
		error  : function ()
		{
			console.log('internal server error');
		}
	});
}

function callMixedAjaxForAxesGraph_Monthly(graphdata){
	var csrf_token= $("#csrf_token").val();
	//console.log(graphdata);
	$.ajax({
		url    : base_url+'dashboard/getmixedgraphaxesdata',
		type   : 'post',
		data   : {'graphdata':graphdata},
		datatype: 'json',
		headers: {
            'X-CSRF-Token': csrf_token
      	},
		success: function (response)
		{
			var res = JSON.parse(response);
			mixedaxesfetchGraph_Monthly(res);
		},
		error  : function ()
		{
			console.log('internal server error');
		}
	});
}

function callGraph_Monthly(button){
	//alert("ddddd");
	var graph_data = {};
	setTimeout(function(){
		$(document).find('#dtbl:eq(0) tbody tr').each(function(){
			var t_revenue = [];
			var t_average = [];
			var t_monthend = [];
			var t_subactive = [];
			var t_renewal = [];
			var i =0;
			$(this).find('td.gross_revenue').each(function(){
				t_revenue[i] = $(this).text();
				i++;
			});
			var j = 0;
			$(this).find('td.subactive_data').each(function(){
				t_subactive[j] = $(this).text();
				j++;
			});
			var k = 0;
			$(this).find('td.total_sent_data').each(function(){
				t_renewal[k] = $(this).text();
				k++;
			});

			if(t_revenue.length > 0){
				graph_data['t_revenue'] = t_revenue;
			}
			if(t_subactive.length > 0){
				graph_data['t_subactive'] = t_subactive;
			}
			if(t_renewal.length > 0){
				graph_data['t_renewal'] = t_renewal;
			}

			graph_data['t_average'] = t_average;
			graph_data['t_monthend'] = t_monthend;
		});

		//console.log(graph_data);
		//callAjaxForGraph_Monthly(graph_data,button);
	},2000);
}

function mixedGraph_Monthly(){
	var graph_data = {};
	setTimeout(function(){
		$(document).find('#dtbl:eq(0) tbody tr').each(function(){
			var t_purged = [];
			var t_reg = [];
			var t_unreg = [];

			var k = 0;
			$(this).find('td.reg_data').each(function(){
				t_reg[k] = $(this).text();
				k++;
			});

			var m = 0;
			$(this).find('td.unreg_data').each(function(){
				t_unreg[m] = $(this).text();
				m++;
			});

			var n = 0;
			$(this).find('td.purged_data').each(function(){
				t_purged[n] = $(this).text();
				n++;
			});


			if(t_reg.length > 0){
				graph_data['t_reg'] = t_reg;
			}
			if(t_unreg.length > 0){
				graph_data['t_unreg'] = t_unreg;
			}
			if(t_purged.length > 0){
				graph_data['t_purged'] = t_purged;
			}
		});
		//console.log("===");
		//console.log(graph_data);
		callAjaxForMixedGraph_Monthly(graph_data);
	},2000);
}

function mixedAxesGraph_Monthly(){
	var graph_data = {};
	setTimeout(function(){
		$(document).find('#dtbl:eq(0) tbody tr').each(function(){
			var t_subactive = [];
			var t_renewal = [];
			var j = 0;
			$(this).find('td.subactive_data').each(function(){
				t_subactive[j] = $(this).text();
				j++;
			});
			var k = 0;
			$(this).find('td.total_sent_data').each(function(){
				t_renewal[k] = $(this).text();
				k++;
			});
			if(t_subactive.length > 0){
				graph_data['t_subactive'] = t_subactive;
			}
			if(t_renewal.length > 0){
				graph_data['t_renewal'] = t_renewal;
			}
		});

		//console.log(graph_data);
		callMixedAjaxForAxesGraph_Monthly(graph_data);
	},2000);
}
//Monthwise end

//Week wise start
function fetchGraph_Weekly(yeardata,yaxes){
	// Summary Graph
	var newarr = [];
	var weeks = ['Week1', 'Week2', 'Week3', 'Week4', 'Week5'];
	var ctx = document.getElementById('revCanvas-weekly').getContext('2d');
	var dates = []
	for(date of yeardata.dates){
		dates.push(weeks[date-1])
	}
	$.each(yeardata.monthdata,function(k, v){
		if(v != ''){
			newarr.push(parseFloat(v.replace(/,/g, '')));
		} else {
			newarr.push(parseInt(0));
		}
	});
	//console.log(dates);

	var config = {
		type: 'line',
		data: {
			labels: dates,
			datasets: [{
				label: '',
				backgroundColor: window.chartColors.red,
				borderColor: window.chartColors.red,
				data: newarr,
				fill: false,
			}]
		},
		options: {
			responsive: true,
			title: {
				display: true,
				text: 'Summary Data Chart'
			},
			scales: {
				xAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Week'
					}
				}],
				yAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Value'
					}
				}]
			}
		}
	};
	window.myLine = new Chart(ctx, config);
}

function mixedfetchGraph_Weekly(yeardata){
	var reg = [];
	var unreg = [];
	var purged = [];
	var subs = [];
	var weeks = ['Week1', 'Week2', 'Week3', 'Week4', 'Week5'];
	var ctx = document.getElementById('mixedCanvas-weekly').getContext('2d');
	var dates = [];
	for(date of yeardata.dates){
		dates.push(weeks[date-1])
	}
	$.each(yeardata.monthdata.t_reg,function(k, v){
		if(v != ''){
			reg.push(parseFloat(v.replace(/,/g, '')));
		} else {
			reg.push(parseInt(0));
		}
	});
	$.each(yeardata.monthdata.t_unreg,function(k, v){
		if(v != ''){
			unreg.push(parseFloat(v.replace(/,/g, '')));
		} else {
			unreg.push(parseInt(0));
		}
	});
	$.each(yeardata.monthdata.t_purged,function(k, v){
		if(v != ''){
			purged.push(parseFloat(v.replace(/,/g, '')));
		} else {
			purged.push(parseInt(0));
		}
	});
	$.each(yeardata.monthdata.t_actvsubs,function(k, v){
		if(v != ''){
			subs.push(v);
		} else {
			subs.push(parseInt(0));
		}
	});
	/*console.log(reg);
	console.log(unreg);
	console.log(purged);
	console.log(subs);
*/
	window.mixedChart = new Chart(ctx, {
	    type: 'bar',
	    data: {
	      labels: dates,
	      datasets: [{
	          label: "Reg",
	          type: "bar",
	          backgroundColor: window.chartColors.purple,
	          data: reg,
	        }, {
	          label: "Unreg",
	          type: "bar",
	          backgroundColor: window.chartColors.red,
	          data: unreg,
	          stack: 'bar-stacked'
	        }, {
	          label: "Purged",
	          type: "bar",
	          backgroundColor: window.chartColors.blue,
	          data: purged,
	          stack: 'bar-stacked'
	        }, {
	          label: "Net New Sub",
	          type: "line",
	          borderColor: "#8e5ea2",
	          data: subs,
	          fill: false
	        }
	      ]
	    },
	    options: {
	      	responsive: true,
			title: {
				display: true,
				text: 'Summary Data Chart of Reg, Unreg, Purged & Net New Sub'
			},
			tooltips: {
				mode: 'index',
				intersect: false
			},
			scales: {
				xAxes: [{
					stacked: true,
				}],
				yAxes: [{
					stacked: true
				}]
			}
	    }
	});
}

function mixedaxesfetchGraph_Weekly(yeardata){
	var renewal = [];
	var subs = [];
	var weeks = ['Week1', 'Week2', 'Week3', 'Week4', 'Week5'];
	var ctx = document.getElementById('subCanvas-weekly').getContext('2d');
	var dates = [];
	for(date of yeardata.dates){
		dates.push(weeks[date-1])
	}
	$.each(yeardata.monthdata.t_subactive,function(k, v){
		if(v != ''){
			subs.push(v.toFixed(2));
		} else {
			subs.push(parseInt(0));
		}
	});
	$.each(yeardata.monthdata.t_renewal,function(k, v){
		if(v != ''){
			renewal.push(parseFloat(v.replace(/,/g, '')));
		} else {
			renewal.push(parseInt(0));
		}
	});

	/*console.log(subs);
	console.log(renewal);
	console.log(dates);*/

	window.mixedChart = new Chart(ctx, {
	    type: 'bar',
	    data: {
	      labels: dates,
	      datasets: [{
	          label: "Subactive",
	          type: "bar",
	          yAxisID: 'y-axis-1',
	          backgroundColor: window.chartColors.purple,
	          data: subs,
	        }, {
	          label: "Renewal",
	          type: "line",
	          yAxisID: 'y-axis-2',
	          borderColor: window.chartColors.yellow,
	          data: renewal,
	          fill: false
	        }
	      ]
	    },
	    options: {
	      	responsive: true,
			title: {
				display: true,
				text: 'Summary Data Chart of Subs and Renewal (Subactive value must x1000)'
			},
			tooltips: {
				mode: 'index',
				intersect: true
			},
			scales: {
				yAxes: [{
					type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
					display: true,
					position: 'left',
					id: 'y-axis-1',
				}, {
					type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
					display: true,
					position: 'right',
					id: 'y-axis-2',
					gridLines: {
						drawOnChartArea: false
					}
				}],
			}
	    }
	});
}

function callAjaxForGraph_Weekly(graphdata,yaxes){
	var csrf_token= $("#csrf_token").val();
	//console.log(graphdata);
	$.ajax({
		url    : base_url+'dashboard/getsummarygraphdata',
		type   : 'post',
		data   : {'graphdata':graphdata,'yaxes':yaxes},
		datatype: 'json',
		headers: {
            'X-CSRF-Token': csrf_token
      	},
		success: function (response)
		{
			var res = JSON.parse(response);
			fetchGraph_Weekly(res,yaxes);
		},
		error  : function ()
		{
			console.log('internal server error');
		}
	});
}

function callAjaxForMixedGraph_Weekly(graphdata){
	var csrf_token= $("#csrf_token").val();
	//console.log(graphdata);
	$.ajax({
		url    : base_url+'dashboard/getmixedgraphdata',
		type   : 'post',
		data   : {'graphdata':graphdata},
		datatype: 'json',
		headers: {
            'X-CSRF-Token': csrf_token
      	},
		success: function (response)
		{
			var res = JSON.parse(response);
			mixedfetchGraph_Weekly(res);
		},
		error  : function ()
		{
			console.log('internal server error');
		}
	});
}

function callMixedAjaxForAxesGraph_Weekly(graphdata){
	var csrf_token= $("#csrf_token").val();
	//console.log(graphdata);
	$.ajax({
		url    : base_url+'dashboard/getmixedgraphaxesdata',
		type   : 'post',
		data   : {'graphdata':graphdata},
		datatype: 'json',
		headers: {
            'X-CSRF-Token': csrf_token
      	},
		success: function (response)
		{
			var res = JSON.parse(response);
			mixedaxesfetchGraph_Weekly(res);
		},
		error  : function ()
		{
			console.log('internal server error');
		}
	});
}

function callGraph_Weekly(button){
	//alert("ddddd");
	var graph_data = {};
	setTimeout(function(){
		$(document).find('#dtbl:eq(0) tbody tr').each(function(){
			var t_revenue = [];
			var t_average = [];
			var t_monthend = [];
			var t_subactive = [];
			var t_renewal = [];
			var i =0;
			$(this).find('td.gross_revenue').each(function(){
				t_revenue[i] = $(this).text();
				i++;
			});
			var j = 0;
			$(this).find('td.subactive_data').each(function(){
				t_subactive[j] = $(this).text();
				j++;
			});
			var k = 0;
			$(this).find('td.total_sent_data').each(function(){
				t_renewal[k] = $(this).text();
				k++;
			});

			if(t_revenue.length > 0){
				graph_data['t_revenue'] = t_revenue;
			}
			if(t_subactive.length > 0){
				graph_data['t_subactive'] = t_subactive;
			}
			if(t_renewal.length > 0){
				graph_data['t_renewal'] = t_renewal;
			}

			graph_data['t_average'] = t_average;
			graph_data['t_monthend'] = t_monthend;
		});

		//console.log(graph_data);
		//callAjaxForGraph_Weekly(graph_data,button);
	},2000);
}

function mixedGraph_Weekly(){
	var graph_data = {};
	setTimeout(function(){
		$(document).find('#dtbl:eq(0) tbody tr').each(function(){
			var t_purged = [];
			var t_reg = [];
			var t_unreg = [];

			var k = 0;
			$(this).find('td.reg_data').each(function(){
				t_reg[k] = $(this).text();
				k++;
			});

			var m = 0;
			$(this).find('td.unreg_data').each(function(){
				t_unreg[m] = $(this).text();
				m++;
			});

			var n = 0;
			$(this).find('td.purged_data').each(function(){
				t_purged[n] = $(this).text();
				n++;
			});


			if(t_reg.length > 0){
				graph_data['t_reg'] = t_reg;
			}
			if(t_unreg.length > 0){
				graph_data['t_unreg'] = t_unreg;
			}
			if(t_purged.length > 0){
				graph_data['t_purged'] = t_purged;
			}
		});
		//console.log("===");
		//console.log(graph_data);
		callAjaxForMixedGraph_Weekly(graph_data);
	},2000);
}

function mixedAxesGraph_Weekly(){
	var graph_data = {};
	setTimeout(function(){
		$(document).find('#dtbl:eq(0) tbody tr').each(function(){
			var t_subactive = [];
			var t_renewal = [];
			var j = 0;
			$(this).find('td.subactive_data').each(function(){
				t_subactive[j] = $(this).text();
				j++;
			});
			var k = 0;
			$(this).find('td.total_sent_data').each(function(){
				t_renewal[k] = $(this).text();
				k++;
			});
			if(t_subactive.length > 0){
				graph_data['t_subactive'] = t_subactive;
			}
			if(t_renewal.length > 0){
				graph_data['t_renewal'] = t_renewal;
			}
		});

		//console.log(graph_data);
		callMixedAjaxForAxesGraph_Weekly(graph_data);
	},2000);
}
//Week wise end

function generateNewKey(){
	var csrf_token= $("#csrf_token").val();
	$.ajax({
		url    : base_url+'service/getnewkey',
		datatype: 'json',
		headers: {
            'X-CSRF-Token': csrf_token
      	},
		success: function (response)
		{
			$("#generateKeyTxt").val(response);
			$("#exampleTxt").html("http://merchant.airpay.mobi/api/latest?access_key="+response);
		},
		error  : function ()
		{
			console.log('internal server error');
		}
	});
}

function copy() {
  /* Get the text field */
  var copyText = $("#generateKeyTxt");

  /* Select the text field */
  copyText.select();

  /* Copy the text inside the text field */
  document.execCommand("copy");
}

function callAjaxForCountryOperators(country){
	var csrf_token= $("meta[name='csrf-token']").attr('content');
	$.ajax({
		url    : base_url+'dashboard/getoperatorbycountry',
		type   : 'post',
		data   : {'country':country},
		datatype: 'json',
		headers: {
            'X-CSRF-Token': csrf_token
      	},
		success: function (response)
			{
				//console.log(response);
				var data = '';
				$.each(JSON.parse(response), function(k, v) {
					data += "<li><input type='checkbox' name='service_operators[]' id='cb_operator_"+v.id_operator+"'  value='"+v.id_operator+"'/><label for='cb_operator_"+v.id_operator+"'><img src='https://img.etimg.com/thumb/msid-68721417,width-643,imgsize-1016106,resizemode-4/nature1_gettyimages.jpg' /></label><p class='text-center'>"+v.operator_name.charAt(0).toUpperCase()+ v.operator_name.slice(1) +"</p></li>";
				});
				$("#country_operators").html(data);
			},
			error  : function ()
			{
				console.log('internal server error');
			}
		});
}

function callAjaxForService(csrf_token, country, operator){
	//$(".loader_image").show();
	$.ajax({
		url    : base_url+'dashboard/getservicedatabyoperator',
		type   : 'post',
		data   : {'operator_id':operator, 'country':country},
		datatype: 'json',
		headers: {
			'X-CSRF-Token': csrf_token
		},
		success: function (response)
		{

			if(response == '<option value="">Service Name</option>'){
				$('.dashboardSearchBtn').attr('disabled','disabled');
			}else{
				$('.dashboardSearchBtn').removeAttr('disabled');
			}

			$('#dashboardservice').empty();
			$('#dashboardkeyword').empty();
			var servicedata= '<option value="">Service Name</option>';
			var keyworddata= '<option value="">Subkeyword</option>';
			console.warn(response);
			$.each(JSON.parse(response), function(k, v) {
				//var servicename = `${v.keyword.toUpperCase()} (SUBSCRIPTIONS) (${v.sdc})`
				servicedata += '<option value="'+v.id_service+'">'+v.servicename+'</option>';
				keyworddata += '<option value="'+v.id_service+'">'+v.keyword+'</option>';
			/// do stuff
			});
			$('#dashboardservice').append(servicedata);
			//$('#dashboardkeyword').append(keyworddata);
			$('#dashboardservice').on('change', function(){
				var service = $(this).val();
				var result = JSON.parse(response).filter(obj => obj.id_service == service)
				var subkeyword = (result.length != 0) ? result[0].keyword : ''
				$('#dashboardkeyword').attr('value',subkeyword);
			})


			$(".loader_image").hide();
		//console.log(getupdatedata);
		},
		error  : function ()
		{
			console.log('internal server error');
		}
	});
}

$(function(){
	var current_fs, next_fs, previous_fs;
	var path = window.location.pathname;
	if(path == "/report/summary" || path == "/report/countrysummary" || path == "/report/amsummary"){
		callGraph('t_revenue');
		mixedGraph();
		mixedAxesGraph();
	}
	if(path == "/report/monthlysummary" || path == "/report/monthlycountrysummary" || path == "/report/monthlyamsummary"){
		callGraph_Monthly('t_revenue');
		mixedGraph_Monthly();
		mixedAxesGraph_Monthly();
	}
	// if(path == "/dashboard/weeklysummary"){
	// 	callGraph_Weekly('t_revenue');
	// 	mixedGraph_Weekly();
	// 	mixedAxesGraph_Weekly();
	// }

	$('.collapse').on('show.bs.collapse', function () {
      $(this).parent().find(".fa-plus").removeClass("fa-plus").addClass("fa-minus");
    })

    $('.collapse').on('hide.bs.collapse', function () {
      $(this).parent().find(".fa-minus").removeClass("fa-minus").addClass("fa-plus");
    })

	$(document).find("#summarycompany").on('change', function(){
		$(".search_btn_summery_data1").prop('disabled',false);

		var company = $( "#summarycompany option:selected" ).text();
		var csrf_token= $("meta[name='csrf-token']").attr('content');
		$.ajax({
			url    : base_url+'dashboard/getcountriesbycompany',
			type   : 'post',
			data   : {'company':company},
			datatype: 'json',
			headers: {
	            'X-CSRF-Token': csrf_token
	      	},
			success: function (response)
			{
				//console.log(response);
				var data = '';
				$("#summarycountry").html('');
				data += '<option value="">Country Name</option>';
				$.each(JSON.parse(response), function(k, v) {
					data += "<option value='"+v+"'>"+ v+ "</option>";
				});
				$("#summarycountry").html(data);
			},
			error  : function ()
			{
				console.log('internal server error');
			}
		});
	});

	$(document).find("#companies-country").on('change', function(){
		var country = $(this).val();
		var csrf_token= $("meta[name='csrf-token']").attr('content');
		$.ajax({
			url    : base_url+'administration/getoperatorsbycountry',
			type   : 'post',
			data   : {'country':country},
			datatype: 'json',
			headers: {
	            'X-CSRF-Token': csrf_token
	      	},
			success: function (response)
			{
				//console.log(response);
				var data = '';
				$("#companies-operator_id").html('');
				data += '<option value="">Select Operator</option>';
				$.each(JSON.parse(response), function(k, v) {
					data += "<option value='"+v.id_operator+"'>"+ v.operator_name + "</option>";
				});
				$("#companies-operator_id").html(data);
			},
			error  : function ()
			{
				console.log('internal server error');
			}
		});
	});

	$(document).find(".billDivs").on('click',function(){
		var sign = $(this).attr("data-sign");
		if(sign == "plus"){
			$(this).attr("data-sign", "dash");
			$(".billSeperateBtn").html('-');
			$(".billExtendedRows").slideDown();
		} else {
			$(this).attr("data-sign", "plus");
			$(".billSeperateBtn").html('+');
			$(".billExtendedRows").slideUp();
		}
	});

	$(document).find(".billSeperateBtn").on('click',function(){
        var sign = $(this).attr("data-sign");
        if(sign == "plus"){
            $(this).attr("data-sign", "dash");
            $(this).html('-');
            $(this).closest("tr").next(".billExtendedRows").slideDown();
            $(this).closest("tr").next(".billExtendedRows").next("tr").slideDown();
        } else {
            $(this).attr("data-sign", "plus");
            $(this).html('+');
             $(this).closest("tr").next(".billExtendedRows").slideUp();
            $(this).closest("tr").next(".billExtendedRows").next("tr").slideUp();
        }
    });

    $(document).find(".revenueSeperateBtn").on('click',function(){
        var sign = $(this).attr("data-sign");
        if(sign == "plus"){
            $(this).attr("data-sign", "dash");
            $(this).html('-');
            $(this).closest("tr").next(".revExtendedRows").slideDown();
            $(this).closest("tr").next(".revExtendedRows").next("tr.revExtendedRows").slideDown();
            $(this).closest("tr").next(".revExtendedRows").next("tr.revExtendedRows").next("tr.revExtendedRows").slideDown();
			$(this).closest("tr").next(".revExtendedRows").next("tr.revExtendedRows").next("tr.revExtendedRows").next("tr.revExtendedRows").slideDown();
        } else {
            $(this).attr("data-sign", "plus");
            $(this).html('+');
            $(this).closest("tr").next(".revExtendedRows").slideUp();
            $(this).closest("tr").next(".revExtendedRows").next("tr.revExtendedRows").slideUp();
            $(this).closest("tr").next(".revExtendedRows").next("tr.revExtendedRows").next("tr.revExtendedRows").slideUp();
			$(this).closest("tr").next(".revExtendedRows").next("tr.revExtendedRows").next("tr.revExtendedRows").next("tr.revExtendedRows").slideUp();
        }
    });

    $(document).find(".arpuSeperateBtn").on('click',function(){
        var sign = $(this).attr("data-sign");
        if(sign == "plus"){
            $(this).attr("data-sign", "dash");
            $(this).html('-');
            $(this).closest("tr").next(".arpuExtendedRows").slideDown();
            $(this).closest("tr").next(".arpuExtendedRows").next("tr").slideDown();
            $(this).closest("tr").next(".arpuExtendedRows").next("tr").next("tr").slideDown();
        } if(sign != "plus"){
            $(this).attr("data-sign", "plus");
            $(this).html('+');
            $(this).closest("tr").next(".arpuExtendedRows").slideUp();
            $(this).closest("tr").next(".arpuExtendedRows").next("tr").slideUp();
            $(this).closest("tr").next(".arpuExtendedRows").next("tr").next("tr").slideUp();
        }
    });

    $(document).find(".OtherCostSepBtn").on('click',function(){
        var sign = $(this).attr("data-sign");
        if(sign == "plus"){
            $(this).attr("data-sign", "dash");
            $(this).html('-');
            $(this).closest("tr").next(".hiddenSoSOtherCostTr").slideDown();
            $(this).closest("tr").next(".hiddenSoSOtherCostTr").next("tr.hiddenSoSOtherCostTr").slideDown();
            $(this).closest("tr").next(".hiddenSoSOtherCostTr").next("tr.hiddenSoSOtherCostTr").next("tr.hiddenSoSOtherCostTr").slideDown();
			$(this).closest("tr").next(".hiddenSoSOtherCostTr").next("tr.hiddenSoSOtherCostTr").next("tr.hiddenSoSOtherCostTr").next("tr.hiddenSoSOtherCostTr").slideDown();
        } else {
            $(this).attr("data-sign", "plus");
            $(this).html('+');
            $(this).closest("tr").next(".hiddenSoSOtherCostTr").slideUp();
            $(this).closest("tr").next(".hiddenSoSOtherCostTr").next("tr.hiddenSoSOtherCostTr").slideUp();
            $(this).closest("tr").next(".hiddenSoSOtherCostTr").next("tr.hiddenSoSOtherCostTr").next("tr.hiddenSoSOtherCostTr").slideUp();
			$(this).closest("tr").next(".hiddenSoSOtherCostTr").next("tr.hiddenSoSOtherCostTr").next("tr.hiddenSoSOtherCostTr").next("tr.hiddenSoSOtherCostTr").slideUp();
        }
    });

    $(document).find(".grev_plus").on('click',function(){
        var sign = $(this).attr("data-sign");
        if(sign == "plus"){
            $(this).attr("data-sign", "dash");
            $(this).html('-');
            $(this).closest("tr").next(".hiddenRevTr").slideDown();
            $(this).closest("tr").next(".hiddenRevTr").next("tr.hiddenRevTr").slideDown();
            $(this).closest("tr").next(".hiddenRevTr").next("tr.hiddenRevTr").next("tr.hiddenRevTr").slideDown();
			$(this).closest("tr").next(".hiddenRevTr").next("tr.hiddenRevTr").next("tr.hiddenRevTr").next("tr.hiddenRevTr").slideDown();
        } else {
            $(this).attr("data-sign", "plus");
            $(this).html('+');
            $(this).closest("tr").next(".hiddenRevTr").slideUp();
            $(this).closest("tr").next(".hiddenRevTr").next("tr.hiddenRevTr").slideUp();
            $(this).closest("tr").next(".hiddenRevTr").next("tr.hiddenRevTr").next("tr.hiddenRevTr").slideUp();
			$(this).closest("tr").next(".hiddenRevTr").next("tr.hiddenRevTr").next("tr.hiddenRevTr").next("tr.hiddenRevTr").slideUp();
        }
    });

    $(document).find(".gross_rev_usd_plus").on('click',function(){
        var sign = $(this).attr("data-sign");
        if(sign == "plus"){
            $(this).attr("data-sign", "dash");
            $(this).html('-');
            $(this).closest("tr").next(".hiddenGrossRevUsdTr").slideDown();
            $(this).closest("tr").next(".hiddenGrossRevUsdTr").next("tr.hiddenGrossRevUsdTr").slideDown();
            $(this).closest("tr").next(".hiddenGrossRevUsdTr").next("tr.hiddenGrossRevUsdTr").next("tr.hiddenGrossRevUsdTr").slideDown();
			$(this).closest("tr").next(".hiddenGrossRevUsdTr").next("tr.hiddenGrossRevUsdTr").next("tr.hiddenGrossRevUsdTr").next("tr.hiddenGrossRevUsdTr").slideDown();
        } else {
            $(this).attr("data-sign", "plus");
            $(this).html('+');
            $(this).closest("tr").next(".hiddenGrossRevUsdTr").slideUp();
            $(this).closest("tr").next(".hiddenGrossRevUsdTr").next("tr.hiddenGrossRevUsdTr").slideUp();
            $(this).closest("tr").next(".hiddenGrossRevUsdTr").next("tr.hiddenGrossRevUsdTr").next("tr.hiddenGrossRevUsdTr").slideUp();
			$(this).closest("tr").next(".hiddenGrossRevUsdTr").next("tr.hiddenGrossRevUsdTr").next("tr.hiddenGrossRevUsdTr").next("tr.hiddenGrossRevUsdTr").slideUp();
        }
    });

    $(document).find(".other_cost").on('click',function(){
        var sign = $(this).attr("data-sign");
        if(sign == "plus"){
            $(this).attr("data-sign", "dash");
            $(this).html('-');
            $(this).closest("tr").next(".hiddenOtherCostTr").slideDown();
            $(this).closest("tr").next(".hiddenOtherCostTr").next("tr.hiddenOtherCostTr").slideDown();
            $(this).closest("tr").next(".hiddenOtherCostTr").next("tr.hiddenOtherCostTr").next("tr.hiddenOtherCostTr").slideDown();
			$(this).closest("tr").next(".hiddenOtherCostTr").next("tr.hiddenOtherCostTr").next("tr.hiddenOtherCostTr").next("tr.hiddenOtherCostTr").slideDown();
        } else {
            $(this).attr("data-sign", "plus");
            $(this).html('+');
            $(this).closest("tr").next(".hiddenOtherCostTr").slideUp();
            $(this).closest("tr").next(".hiddenOtherCostTr").next("tr.hiddenOtherCostTr").slideUp();
            $(this).closest("tr").next(".hiddenOtherCostTr").next("tr.hiddenOtherCostTr").next("tr.hiddenOtherCostTr").slideUp();
			$(this).closest("tr").next(".hiddenOtherCostTr").next("tr.hiddenOtherCostTr").next("tr.hiddenOtherCostTr").next("tr.hiddenOtherCostTr").slideUp();
        }
    });

    $(document).find(".arpu_plus").on('click',function(){
        var sign = $(this).attr("data-sign");
        if(sign == "plus"){
            $(this).attr("data-sign", "dash");
            $(this).html('-');
            $(this).closest("tr").next(".hiddenArpuTr").slideDown();
            $(this).closest("tr").next(".hiddenArpuTr").next("tr.hiddenArpuTr").slideDown();
            $(this).closest("tr").next(".hiddenArpuTr").next("tr.hiddenArpuTr").next("tr.hiddenArpuTr").slideDown();
			$(this).closest("tr").next(".hiddenArpuTr").next("tr.hiddenArpuTr").next("tr.hiddenArpuTr").next("tr.hiddenArpuTr").slideDown();
        } else {
            $(this).attr("data-sign", "plus");
            $(this).html('+');
            $(this).closest("tr").next(".hiddenArpuTr").slideUp();
            $(this).closest("tr").next(".hiddenArpuTr").next("tr.hiddenArpuTr").slideUp();
            $(this).closest("tr").next(".hiddenArpuTr").next("tr.hiddenArpuTr").next("tr.hiddenArpuTr").slideUp();
			$(this).closest("tr").next(".hiddenArpuTr").next("tr.hiddenArpuTr").next("tr.hiddenArpuTr").next("tr.hiddenArpuTr").slideUp();
        }
    });

    $(".next").click(function(){
        current_fs = $(this).closest('.justify-content-md-center').css('background-color','red');
        next_fs = $(this).closest('.justify-content-md-center').next();
        $("#progressbar li").eq($('.justify-content-md-center').index(next_fs)).addClass("active");
        $("#progressbar li").eq($('.justify-content-md-center').index(current_fs)).removeClass("active");
        next_fs.show();
        current_fs.hide();
    });
    $(".skip").click(function(){
        current_fs = $(this).closest('.justify-content-md-center');
        previous_fs = $(this).closest('.justify-content-md-center').prev();
        $("#progressbar li").eq($('.justify-content-md-center').index(previous_fs)).addClass("active");
        $("#progressbar li").eq($('.justify-content-md-center').index(current_fs)).removeClass("active");
        previous_fs.show();
        current_fs.hide();
    });

	$("#service_step_one").on('click',function(){
		var service_type = $("input[name='service_type']:checked").val();
		var payment_type = $("input[name='payment_type']:checked").val();
		var step="first";
		if(service_type == undefined && payment_type == undefined){
			$("#select_service_type").css('border','5px solid red');
			$("#select_payment_type").css('border','5px solid red');
		} else if(service_type == undefined && payment_type != undefined){
			$("#select_service_type").css('border','5px solid red');
			$("#select_payment_type").css('border','0');
		} else if(service_type != undefined && payment_type == undefined){
			$("#select_payment_type").css('border','5px solid red');
			$("#select_service_type").css('border','0');
		} else {
			$("#select_service_type").css('border','0');
			$("#select_payment_type").css('border','0');

			current_fs = $(this).closest('.justify-content-md-center').css('background-color','red');
	        next_fs = $(this).closest('.justify-content-md-center').next();
	        $("#progressbar li").eq($('.justify-content-md-center').index(next_fs)).addClass("active");
	        $("#progressbar li").eq($('.justify-content-md-center').index(current_fs)).removeClass("active");
	        next_fs.show();
	        current_fs.hide();
	        //new ajax
	        /*$.ajax({
			url    : base_url+'service/create',
			type   : 'post',
			data   : {'service_type':service_type,'payment_type':payment_type,'action_scope':step},
			datatype: 'json',
			success: function (response)
			{
			console.log(response);
			var first_data = '';
				$.each(JSON.parse(response), function(k, v) {
					first_data += "<li><input type='checkbox' name='service_operators[]' id='cb_operator_"+v.id_operator+"'  value='"+v.id_operator+"'/><label for='cb_operator_"+v.id_operator+"'><img src='https://img.etimg.com/thumb/msid-68721417,width-643,imgsize-1016106,resizemode-4/nature1_gettyimages.jpg' /></label><p class='text-center'>"+v.operator_name.charAt(0).toUpperCase()+ v.operator_name.slice(1) +"</p></li>";
				});
			},
			error  : function ()
			{
				console.log('internal server error');
			}
		});*/
		}
	});

	$("#service_step_two").on('click',function(){
		var service_country = $("input[name='service_country']:checked").val();
		if(service_country == undefined){
			$("#select_service_country").css('border','5px solid red');
		} else {
			$("#select_service_country").css('border','0');
			callAjaxForCountryOperators(service_country);

			current_fs = $(this).closest('.justify-content-md-center').css('background-color','red');
	        next_fs = $(this).closest('.justify-content-md-center').next();
	        $("#progressbar li").eq($('.justify-content-md-center').index(next_fs)).addClass("active");
	        $("#progressbar li").eq($('.justify-content-md-center').index(current_fs)).removeClass("active");
	        next_fs.show();
	        current_fs.hide();
		}
	});

	$("#service_step_three").on('click',function(){
		var service_operators = $("input[name='service_operators[]']:checked").val();
		if(service_operators == undefined){
			$("#select_service_operators").css('border','5px solid red');
		} else {
			$("#select_service_operators").css('border','0');

			current_fs = $(this).closest('.justify-content-md-center').css('background-color','red');
	        next_fs = $(this).closest('.justify-content-md-center').next();
	        $("#progressbar li").eq($('.justify-content-md-center').index(next_fs)).addClass("active");
	        $("#progressbar li").eq($('.justify-content-md-center').index(current_fs)).removeClass("active");
	        next_fs.show();
	        current_fs.hide();
		}
	});

	$("#service_step_four").on('click',function(){

		var service_name 		= $("input[id='service_name']").val();
		var call_back_url 		= $("input[id='call_back_url']").val();
		var service_url 		= $("input[id='service_url']").val();
		var provider_name 		= $("input[id='provider_name']").val();
		var description 		= $("input[id='description']").val();
		var keyword 			= $("input[id='keyword']").val();
		var sub_keyword 		= $("input[id='sub_keyword']").val();
		var channel_type 		= $("input[name='channel_type']:checked").val();
		var subscribe_duration 	= $("input[name='subscribe_duration']:checked").val();
		var service_logo 		= $("input[id='service_logo']").val();

		if(service_name == '' || call_back_url == '' || service_url == '' || provider_name == '' || description == '' || keyword == '' || sub_keyword == '' || channel_type == undefined || subscribe_duration == undefined || service_logo == ''){
			$("#service_information").css('border','5px solid red');

		} else {
			$("#service_information").css('border','0');

			current_fs = $(this).closest('.justify-content-md-center').css('background-color','red');
	        next_fs = $(this).closest('.justify-content-md-center').next();
	        $("#progressbar li").eq($('.justify-content-md-center').index(next_fs)).addClass("active");
	        $("#progressbar li").eq($('.justify-content-md-center').index(current_fs)).removeClass("active");
	        next_fs.show();
	        current_fs.hide();
		}
	});

	$("#copyBtn").click(function(){
        copy();
    });

	$("#regenerateBtn, #list-messages-list").click(function(){
        generateNewKey();
    });

	/*$(document).on('click', '.graph_buttons', function(){
		var button = $(this).data('attribute');
		if($("#graphSummary").is(":hidden")){
			$("#graphSummary").show();
			$(this).addClass('active');
		} else {
			if($(this).hasClass('active')){
				$(this).removeClass('active');
				$("#graphSummary").hide();
			} else {
				return false;
			}
		}
		if(window.myLine != undefined){
			window.myLine.destroy();
		}
		callGraph(button);
	});

	$(document).on('click', '.mixed_graph_button', function(){
		var button = $(this).data('attribute');
		if($("#mixedgraphSummary").is(":hidden")){
			$("#mixedgraphSummary").show();
			$(this).addClass('active');
		} else {
			if($(this).hasClass('active')){
				$(this).removeClass('active');
				$("#mixedgraphSummary").hide();
			} else {
				return false;
			}
		}
		if(window.mixedChart != undefined){
			window.mixedChart.destroy();
		}
		mixedGraph();
	});

	$(document).on('click','i.fa.fa-angle-down.submenuopen',function(){
		$(this).parent().addClass('active-sidenav');
		$(this).parent().parent().find('ul.submenu').slideDown();
		$(this).addClass('fa-angle-up');
		$(this).removeClass('fa-angle-down');
	})

	$(document).on('click','i.fa.fa-angle-up.submenuopen',function(){
		$(this).parent().removeClass('active-sidenav');
		$(this).parent().parent().find('ul.submenu').slideUp();
		$(this).addClass('fa-angle-down');
		$(this).removeClass('fa-angle-up');
	})

	var current = location.pathname;
	$('.sidebar ul li').removeClass('active');

	$('.parent_menu .submenu li a').each(function(){
		var $this = $(this);
		if($this.attr('href') == current){
			$this.addClass('active active-sidenav');
			$this.closest('ul.submenu').slideDown();
			$this.closest('ul.submenu').parent().find('i.fa.fa-angle-down.submenuopen').addClass('fa-angle-up');
			$this.closest('ul.submenu').parent().find('i.fa.fa-angle-down.submenuopen').removeClass('fa-angle-down');
		}
	}); */

	var selector = 'li.nav-item';
    var url = window.location.href;
    var origin = window.location.origin;
    var target = url.replace(origin, "");
	//console.log(target);
	if(target.indexOf('?') !== -1){
		var qindex = target.indexOf('?');
		var params = target.substring(qindex)
		target = target.replace(params,"")
	}
    $(selector).each(function(){
        if($(this).find('a').attr('href') === target){
          $(selector).removeClass('active');
          $(this).removeClass('active').addClass('active');
		  $(this).find('a').removeClass('text-white').addClass('text-white');
		  $(this).closest('ul').css('display','block')
		  $(this).closest('ul').prev().find('span').html('<i class="fa fa-chevron-up"></i>')
        }else{
			if((target == '/dashboard/monthlysummary' || target == '/report/monthlysummary' || target == '/dashboard/countrysummary' || target == '/report/countrysummary' || target == '/report/amsummary' || target == '/report/monthlycountrysummary' || target == '/report/monthlyamsummary') && $(this).find('a').attr('href') === '/report/summary'){
				$(selector).removeClass('active');
				$(this).removeClass('active').addClass('active');
				$(this).find('a').removeClass('text-white').addClass('text-white');
				$(this).closest('ul').css('display','block')
				$(this).closest('ul').prev().find('span').html('<i class="fa fa-chevron-up"></i>')
			}
			if((target == '/pnl_country_dsummary') || (target == '/pnl_operator_detail_monthly_summary') || (target == '/pnl_company_dsummary')){
				$(selector).removeClass('active');
				$(document).find('a[ href="/pnl_country_detail"]').removeClass('active').addClass('active');
				$(document).find('a[ href="/pnl_country_detail"]').removeClass('text-white').addClass('text-white');
				$(document).find('a[ href="/pnl_country_detail"]').closest('ul').css('display','block')
				$(document).find('a[ href="/pnl_country_detail"]').closest('ul').prev().find('span').html('<i class="fa fa-chevron-up"></i>')
			}

			if((target == '/pnl_operator_detail') || (target == '/pnl_country_detail')  || (target == '/pnl_company_detail')){
				$(selector).removeClass('active');
				$(document).find('a[ href="/pnl_country_detail"]').removeClass('active').addClass('active');
				$(document).find('a[ href="/pnl_country_detail"]').removeClass('text-white').addClass('text-white');
				$(document).find('a[ href="/pnl_country_detail"]').closest('ul').css('display','block')
				$(document).find('a[ href="/pnl_country_detail"]').closest('ul').prev().find('span').html('<i class="fa fa-chevron-up"></i>')
			}

			if((target == '/pnl_country_msummary') || (target == '/pnl_operator_monthly_summary') || (target == '/pnl_company_msummary')){
				$(selector).removeClass('active');
				$(document).find('a[ href="/pnl_country_summary"]').removeClass('active').addClass('active');
				$(document).find('a[ href="/pnl_country_summary"]').removeClass('text-white').addClass('text-white');
				$(document).find('a[ href="/pnl_country_summary"]').closest('ul').css('display','block')
				$(document).find('a[ href="/pnl_country_summary"]').closest('ul').prev().find('span').html('<i class="fa fa-chevron-up"></i>')
			}
			if((target ==  '/pnl_operator_summary' || target ==  '/pnl_company_summary' || target == '/pnl_operator_monthly_summary' || target == '/pnl-service-details' || target == '/pnlcampaign/monthlyservicesummary' || target == '/testpnlcampaign/udata')  && $(this).find('a').attr('href') === '/pnl_country_summary'){
				$(selector).removeClass('active');
				$(this).removeClass('active').addClass('active');
				$(this).find('a').removeClass('text-white').addClass('text-white');
				$(this).closest('ul').css('display','block')
				$(this).closest('ul').prev().find('span').html('<i class="fa fa-chevron-up"></i>')
			}

			if((target == '/monthly-logperformance')  && $(this).find('a').attr('href') === '/daily-logperformance'){
				$(selector).removeClass('active');
				$(this).removeClass('active').addClass('active');
				$(this).find('a').removeClass('text-white').addClass('text-white');
				$(this).closest('ul').css('display','block')
				$(this).closest('ul').prev().find('span').html('<i class="fa fa-chevron-up"></i>')
			}
			if((target == '/finance/createrevenuereconcile')  && $(this).find('a').attr('href') === '/finance/revenuereconcile'){
				$(selector).removeClass('active');
				$(this).removeClass('active').addClass('active');
				$(this).find('a').removeClass('text-white').addClass('text-white');
				$(this).closest('ul').css('display','block')
				$(this).closest('ul').prev().find('span').html('<i class="fa fa-chevron-up"></i>')
			}
			if((target == '/finance/createtarget')  && $(this).find('a').attr('href') === '/finance/targetrevenue'){
				$(selector).removeClass('active');
				$(this).removeClass('active').addClass('active');
				$(this).find('a').removeClass('text-white').addClass('text-white');
				$(this).closest('ul').css('display','block')
				$(this).closest('ul').prev().find('span').html('<i class="fa fa-chevron-up"></i>')
			}
		}
    });

    $('[data-toggle="tooltip"]').tooltip();
    $(".side-nav .collapse").on("hide.bs.collapse", function() {
        $(this).prev().find(".fa").eq(1).removeClass("fa-angle-right").addClass("fa-angle-down");
    });
    $('.side-nav .collapse').on("show.bs.collapse", function() {
        $(this).prev().find(".fa").eq(1).removeClass("fa-angle-down").addClass("fa-angle-right");
    });

    $(".form_datetime").datetimepicker({format: 'YYYY-MM-DD HH:mm'});
    $(".form_datetime1").datetimepicker({format: 'YYYY-MM-DD', 'showTimepicker': false, viewMode: 'days'});
    $(".to_datetime").datetimepicker({format: 'YYYY-MM-DD', 'showTimepicker': false, viewMode: 'days'});

	$(".form_month").datetimepicker({format: 'YYYY-MM', 'showTimepicker': false, 'changeDay': false, viewMode: 'months', minViewMode: 'months', todayHighlight: false});

    //Search button click in customer service msisdn page......
	$(document).on('click', '#searchBtn', function () {
		var csrf_token= $("#csrf_token").val();
		var msisdn = $("#msisdn").val(); //alert(msisdn);
		var from_date = $("#from").val(); //alert(from_date);
		var to_date = $("#to").val(); //alert(to_date);
		$(".loader_image").show();

		if(from_date == "" || to_date == ""){
			$('.error_block').empty();
			$('.error_block').append('<span class="error">Please Fill up the fields</span>');
			$(".loader_image").hide();
			return false;
		}

		// submit form
		$.ajax({
			url    : base_url+'customerservicetools/getmsisdndetaillog',
			type   : 'post',
			data   : {'msisdn':msisdn,'from_date':from_date,'to_date':to_date,_csrf :csrf_token},
			datatype: 'html',
			success: function (response)
			{
				$('.error_block').empty();
				$('.from_date_class').empty();
				$('.from_date_class').append(from_date);
				$('.to_date_class').empty();
				$('.to_date_class').append(to_date);
				$('#msisdn_logs_tbody').empty();

				var getupdatedata = $(response);
				$('#msisdn_logs_tbody').html(getupdatedata);
				$(".loader_image").hide();
			//console.log(getupdatedata);
			},
			error  : function ()
			{
				console.log('internal server error');
			}
		});
		return false;
	});

    //Serach button click in transaction page......
	$(document).on('click', '.search_btn_summery', function () {
		var csrf_token= $("#csrf_token").val();
		var end_user_id= $("#end_user_id").val();
		var from_date= $("#from_date").val();
		var to_date= $("#to_date").val();
		$(".loader_image").show();

		if(end_user_id == "" || from_date == "" || to_date == ""){
			$('.error_block').empty();
			$('.error_block').append('<span class="error">Please Fill up the fields</span>');
			$(".loader_image").hide();
			return false;
		}


		// submit form
		$.ajax({
				url    : base_url+'dashboard/gettransaction',
				type   : 'post',
				data   : {'end_user_id':end_user_id,'from_date':from_date,'to_date':to_date},
				datatype: 'html',
				headers: {
                    'X-CSRF-Token': csrf_token
              	},
				success: function (response)
					{
						$('.error_block').empty();
						$('.from_date_class').empty();
						$('.from_date_class').append(from_date);
						$('.to_date_class').empty();
						$('.to_date_class').append(to_date);
						$('#tansactionTable tbody').empty();

						var getupdatedata = $(response);
					// $.pjax.reload('#note_update_id'); for pjax update
						$('#tansactionTable tbody').html(getupdatedata);
						$(".loader_image").hide();
					//console.log(getupdatedata);
					},
					error  : function ()
					{
						console.log('internal server error');
					}
		});
		return false;
	});

	//Serach button click in Summary page......
	$(document).on('click', '.search_btn_summery_data', function () {

		var csrf_token= $("#csrf_token").val();
		$('.error_block').empty();
		var operator_id= $("#summery_operator_id").val();
		var dashboardservice_id= $("#dashboardservice").val();
		var summary_days= $("#summery_days").val();
		var summery_month= $("#summery_month").val();
		var summery_year= $("#summery_year").val();


		var to_date= summery_year+'-'+summery_month+'-'+summary_days;
		var from_date= summery_year+'-'+summery_month+'-01';



		$(".loader_image").show();

		if(operator_id == "" || from_date == "" || to_date == "" || dashboardservice_id == ""){
			$('.error_block').append('<span class="error">Please Fill up the fields</span>');
			$(".loader_image").hide();
			return false;
		}


		// submit form
		$.ajax({
				url    : base_url+'dashboard/getsummery',
				type   : 'post',
				data   : {'operator_id':dashboardservice_id,'from_date':from_date,'to_date':to_date},
				dataType: 'json',
				headers: {
                    'X-CSRF-Token': csrf_token
              	},
				success: function (response)
					{
				$('.SummayDetail2').empty();
				$('.SummayDetail1').show();

				$('.summay_cuurent_month').empty();
				$('.summay_cuurent_month').append($('#summery_operator_id option:selected').text()+' '+response.CurrentMonth);

				$('#dtbl tr').each(function(){
					$(this).find('td:gt(1)').remove();
					$(this).find('th:gt(1)').remove();
				});


						//Head tr change and append
				$('#dtbl tr').each(function(index){
					var ccurrentThis= $(this);
					if(index == 0){
						$.each(response.summaryData, function(i, item) {
							var dateDays= item.date.split("-");
							ccurrentThis.append('<th>'+dateDays[2]+'</th>');
						});
					}

					if(index == 1){
						$.each(response.summaryData, function(i, item) {
							if(('gross_revenue' in item)){

								var gross_revenue= item.gross_revenue;

							}else{
								var gross_revenue= 0;


							}
							ccurrentThis.append('<td>'+gross_revenue+'</td>');
						});
						var mixTotal=0;

						ccurrentThis.children('td:gt(1)').each(function () {
								mixTotal += parseInt($(this).text());
						});
						$('.revenue_total').empty();
						//console.log(mixTotal)
						$('.revenue_total').append(mixTotal);


					}

					if(index == 2){
						$.each(response.summaryData, function(i, item) {
							ccurrentThis.append('<td>0</td>');
						});
					}
					if(index == 3){
						$.each(response.summaryData, function(i, item) {
							ccurrentThis.append('<td>0</td>');
						});
					}
					if(index == 4){
						$.each(response.summaryData, function(i, item) {
							ccurrentThis.append('<td>0</td>');
						});
					}

					/*if(index == 5){
						$.each(response.summaryData, function(i, item) {
							ccurrentThis.append('<td>0</td>');
						});
					}*/

					if(index == 5){
						$.each(response.summaryData, function(i, item) {

							if(('total' in item)){

								var total= item.total;

							}else{
								var total= 0;


							}



							ccurrentThis.append('<td>'+total+'</td>');
						});

						var mixTotal1=0;

						ccurrentThis.children('td:gt(1)').each(function () {
								mixTotal1 += parseInt($(this).text());
						});
						$('.subactive_total').empty();
						$('.subactive_total').append(mixTotal1);
					}




					if(index == 6){
						$.each(response.summaryData, function(i, item) {
							if(('total_reg' in item)){

								var total_reg= item.total_reg;

							}else{
								var total_reg= 0;


							}
							ccurrentThis.append('<td>'+total_reg+'</td>');
						});

						var mixTotal1=0;

						ccurrentThis.children('td:gt(1)').each(function () {
								mixTotal1 += parseInt($(this).text());
						});
						$('.reg_total').empty();
						$('.reg_total').append(mixTotal1);
					}
					if(index == 7){
						$.each(response.summaryData, function(i, item) {
							if(('total_unreg' in item)){

								var total_unreg= item.total_unreg;

							}else{
								var total_unreg= 0;


							}
							ccurrentThis.append('<td>'+total_unreg+'</td>');
						});

						var mixTotal2=0;

						ccurrentThis.children('td:gt(1)').each(function () {
								mixTotal2 += parseInt($(this).text());
						});
						$('.unreg_total').empty();
						$('.unreg_total').append(mixTotal2);
					}




					if(index == 8){
						$.each(response.summaryData, function(i, item) {
							ccurrentThis.append('<td>0</td>');
						});
					}
					if(index == 9){
						$.each(response.summaryData, function(i, item) {
							ccurrentThis.append('<td>0</td>');
						});
					}


				});






						$('.from_date_class').empty();
						$('.from_date_class').append(from_date);
						$('.to_date_class').empty();
						$('.to_date_class').append(to_date);
						$('#tansactionTable tbody').empty();

						var getupdatedata = $(response);
					// $.pjax.reload('#note_update_id'); for pjax update
						$('#tansactionTable tbody').html(getupdatedata);
						$(".loader_image").hide();
					//console.log(getupdatedata);
					},
					error  : function ()
					{
						console.log('internal server error');
					}
		});
		return false;
	});

	var revenuemixTotal=0;
	var no_of_days= $('#summery_days').val();
	var total_average_data= 0;
	setTimeout(function(){
		$('.revenue_total').each(function(index){
		revenuemixTotal += parseFloat($(this).text().replace(/,/g, ''));
	})
	$('.top_total_revenue').empty();
	$('.top_total_revenue').append(parseFloat(revenuemixTotal).toFixed(2).toLocaleString('en'));
	total_average_data= revenuemixTotal/no_of_days;
	$('.top_total_average').empty();
	$('.top_total_average').append(total_average_data);
	},1500)

    //Get total reg unredg etc.
    $('#dtbl tr').each(function(index){
     	var shareTotal = $(this).find('.share_total', this).attr('data-share');
     	var shareUSDTotal = $(this).find('.share_total_usd', this).attr('data-share');
     	var subs = $(this).find('.share_total', this).attr('data-subs');
     	var total_month_days = $(document).find("#total_month_days").val();
        var sum=0;
        var length =0;
		var current_month = $('#is_current_month').val()
		//console.log(current_month)

        //find the combat elements in the current row and sum it
        $(this).find('.gross_revenue').each(function (i,k) {
        	if(i > 0){
            	var combat = $(this).text().replace(/,/g, '');
				//console.log(combat)
	            if (!isNaN(combat) && combat.length !== 0) {
	                sum += parseFloat(combat);
	            }
	            length += 1;
        	}else{
				if(current_month == 0){
					var combat = $(this).text().replace(/,/g, '');
					if (!isNaN(combat) && combat.length !== 0) {
						sum += parseFloat(combat);
					}
					length += 1;
				}
			}
        });

        if(length > 0){
        	var month_rem_days =  total_month_days - length;
        	var r_avg = sum / length;
        	var r_mon = sum + (r_avg  * month_rem_days);
			//console.warn(r_mon)
	        //set the value of currents rows sum to the total-combat element in the current row
	        $(this).find('.revenue_avg', this).html(formatNumber(parseFloat(r_avg).toFixed(2).toLocaleString('en')));
	        $(this).find('.revenue_monthly', this).html(formatNumber(parseFloat(r_mon).toFixed(2).toLocaleString('en')));
        }


        //set the value of currents rows sum to the total-combat element in the current row
        var localcurrency = $(this).find('.revenue_total', this).attr('data-local-currency');
        var usd = $(this).find('.revenue_total', this).attr('data-usd');
        var country = $(this).find('.revenue_total', this).attr('data-country');
        if(country == ''){
        	var trev = formatNumber(parseFloat(sum).toFixed(2).toLocaleString('en'));
        	var frev = '';
        } else {
        	if(country == 'Cambodia'){
        		var frev = formatNumber(parseFloat(sum).toFixed(2).toLocaleString('en')) + '(USD)';
        		var trev = formatNumber(parseFloat(sum).toFixed(2).toLocaleString('en'));
	        }else {
	        	var trev = formatNumber(parseFloat(sum).toFixed(2).toLocaleString('en'));
        		var frev = formatNumber(parseFloat(sum * usd).toFixed(2).toLocaleString('en')) + '(USD)';
	        }
        }
        var revShare = formatNumber(parseFloat(sum * shareTotal).toFixed(2).toLocaleString('en'));
        $(this).find('.revenue_total', this).html("<span class='local_total_revenue'>"+trev+"</span>");
		//console.log(trev)
        $(this).find('.share_total', this).text(revShare);

        var sumTotal=0;
        var length = 0;
        //find the combat elements in the current row and sum it
        $(this).find('.gross_revenue_usd').each(function (i,k) {
        	if(i > 0){
            	var combat = $(this).text().replace(/,/g, '');
	            if (!isNaN(combat) && combat.length !== 0) {
	                sumTotal += parseFloat(combat);
	            }
	            length += 1;
        	}else{
				if(current_month == 0){
					var combat = $(this).text().replace(/,/g, '');
					if (!isNaN(combat) && combat.length !== 0) {
						sumTotal += parseFloat(combat);
					}
					length += 1;
				}
			}
        });
        var revUSDShare = parseFloat(sumTotal * shareUSDTotal).toFixed(2);

        $(this).find('td.revenue_total_usd', this).html("<span class='format_total_revenue'>"+(formatNumber(sumTotal.toLocaleString('en')))+"(USD)</span>");
        $(this).find('td.share_total_usd', this).text(formatNumber(revUSDShare.toLocaleString('en'))+"(USD)");

        if(length > 0){
	        var rev_avg = sumTotal / length;
	        var month_rem_days =  total_month_days - length;
	        var rev_mon = sumTotal + (rev_avg  * month_rem_days);

	        //set the value of currents rows sum to the total-combat element in the current row
	        $(this).find('.revenue_avg_usd', this).html(formatNumber(rev_avg.toFixed(2).toLocaleString('en')));
	        $(this).find('.revenue_monthly_usd', this).html(formatNumber(rev_mon.toFixed(2).toLocaleString('en')));
	    }


        var sumRevShare=0;
        var length =0;
        //find the combat elements in the current row and sum it
        $(this).find('.rev_share_data').each(function (i,k) {
        	if(i > 0){
            	var combat = $(this).text().replace(/,/g, '');
	            if (!isNaN(combat) && combat.length !== 0) {
	                sumRevShare += parseFloat(combat);
	            }
	            length += 1;
        	}else{
				if(current_month == 0){
					var combat = $(this).text().replace(/,/g, '');
					if (!isNaN(combat) && combat.length !== 0) {
						sumRevShare += parseFloat(combat);
					}
					length += 1;
				}
			}
        });
        var month_rem_days =  total_month_days - length;
        var avgRevShare = sumRevShare /  length;
        var monRevShare = sumRevShare + (avgRevShare  * month_rem_days);

        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.rev_share_total', this).html(formatNumber(sumRevShare.toFixed(2).toLocaleString('en')));
        $(this).find('.rev_share_avg', this).html(formatNumber(avgRevShare.toFixed(2).toLocaleString('en')));
        $(this).find('.rev_share_monthly', this).html(formatNumber(monRevShare.toFixed(2).toLocaleString('en')));

        var sumUSDRevShare=0;
        var length = 0;
        //find the combat elements in the current row and sum it
        $(this).find('.rev_usd_share_data').each(function (i,k) {
        	if(i > 0){
            	var combat = $(this).text().replace(/,/g, '');
	            if (!isNaN(combat) && combat.length !== 0) {
	                sumUSDRevShare += parseFloat(combat);
	            }
	            length += 1;
        	}else{
				if(current_month == 0){
					var combat = $(this).text().replace(/,/g, '');
					if (!isNaN(combat) && combat.length !== 0) {
						sumUSDRevShare += parseFloat(combat);
					}
					length += 1;
				}
			}
        });
        var month_rem_days =  total_month_days - length;
        var avgUSDRevShare = sumUSDRevShare / length;
        var monUSDRevShare = sumUSDRevShare + (avgUSDRevShare  * month_rem_days);

        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.rev_usd_share_total', this).html(formatNumber(sumUSDRevShare.toFixed(2).toLocaleString('en')));
        $(this).find('.rev_usd_share_avg', this).html(formatNumber(avgUSDRevShare.toFixed(2).toLocaleString('en')));
        $(this).find('.rev_usd_share_monthly', this).html(formatNumber(monUSDRevShare.toFixed(2).toLocaleString('en')));


        var sum1=0;
        var length = 0;
        //find the combat elements in the current row and sum it
        $(this).find('.reg_data').each(function (i,k) {
        	if(i > 0){
            	var combat = $(this).text().replace(/,/g, '');
	            if (!isNaN(combat) && combat.length !== 0) {
	                sum1 += parseFloat(combat);
	            }
	            length += 1;
        	}else{
				if(current_month == 0){
					var combat = $(this).text().replace(/,/g, '');
					if (!isNaN(combat) && combat.length !== 0) {
						sum1 += parseFloat(combat);
					}
					length += 1;
				}
			}
        });
        var month_rem_days =  total_month_days - length;
        var regAvg = sum1  /  length;
        var regMon = sum1 + (regAvg  * month_rem_days);

        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.reg_total', this).html(sum1.toLocaleString('en'));
        $(this).find('.reg_avg', this).html(Math.round(regAvg).toLocaleString('en'));
        $(this).find('.reg_monthly', this).html(Math.round(regMon).toLocaleString('en'));

        var sum2=0;
        var length=0;
        //find the combat elements in the current row and sum it
        $(this).find('.unreg_data').each(function (i,k) {
        	if(i > 0){
            	var combat = $(this).text().replace(/,/g, '');
	            if (!isNaN(combat) && combat.length !== 0) {
	                sum2 += parseFloat(combat);
	            }
	            length += 1;
        	}else{
				if(current_month == 0){
					var combat = $(this).text().replace(/,/g, '');
					if (!isNaN(combat) && combat.length !== 0) {
						sum2 += parseFloat(combat);
					}
					length += 1;
				}
			}
        });
        var month_rem_days =  total_month_days - length;
        var unregAvg = sum2 /  length;
        var unregMon = sum2 + (unregAvg  * month_rem_days);

        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.unreg_total', this).html(sum2.toLocaleString('en'));
        $(this).find('.unreg_avg', this).html(Math.round(unregAvg).toLocaleString('en'));
        $(this).find('.unreg_monthly', this).html(Math.round(unregMon).toLocaleString('en'));


	    var sum3=0;
        var length = 0;
        //var currentVal = 0;
        //find the combat elements in the current row and sum it
        $(this).find('.subactive_data').each(function (i,k) {
        	if(i == 0){
            	var combat = $(this).text().replace(/,/g, '');
            	if (!isNaN(combat) && combat.length !== 0) {
                	sum3 = parseFloat(combat);
            	}
        	}
        	length += 1;
        });
        var month_rem_days =  total_month_days - length;
        var subAvg = sum3 /  length;
        var subMon = sum3 + (subAvg  * month_rem_days);

        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.subactive_total', this).html(sum3.toLocaleString('en'));
        $(this).find('.subactive_avg', this).html(Math.round(subAvg).toLocaleString('en'));
        $(this).find('.subactive_monthly', this).html(Math.round(subMon).toLocaleString('en'));

        var sum4=0;
        //find the combat elements in the current row and sum it
        $(this).find('.mt_failed').each(function () {
            var combat = $(this).text().replace(/,/g, '');
            if (!isNaN(combat) && combat.length !== 0) {
                sum4 += parseFloat(combat);
            }
        });
        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.mt_total_failed', this).html(sum4.toLocaleString('en'));


        var sum5=0;
        //find the combat elements in the current row and sum it
        $(this).find('.mt_delivered').each(function () {
            var combat = $(this).text().replace(/,/g, '');
            if (!isNaN(combat) && combat.length !== 0) {
                sum5 += parseFloat(combat);
            }
        });
        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.mt_total_delivered', this).html(sum5.toLocaleString('en'));



         var sum6=0;
        //find the combat elements in the current row and sum it
        $(this).find('.mt_revenue').each(function () {
            var combat = $(this).text().replace(/,/g, '');
            if (!isNaN(combat) && combat.length !== 0) {
                sum6 += parseFloat(combat);
            }
        });
        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.mt_total_revenue', this).html(sum6.toLocaleString('en'));


         var sum7=0;
         var length=0;
        //find the combat elements in the current row and sum it
        $(this).find('.br_data').each(function (i,k) {
        	if(i > 0){
        		var combat = $(this).text().replace('%', '');
	            if (!isNaN(combat) && combat.length !== 0) {

	                sum7 += parseFloat(combat);
	            }
	            length += 1;
        	}else{
				if(current_month == 0){
					var combat = $(this).text().replace('%', '');
					if (!isNaN(combat) && combat.length !== 0) {

						sum7 += parseFloat(combat);
					}
					length += 1;
				}
			}
        });
        var month_rem_days =  total_month_days - length;
        var brAvg 			= sum7 /  length;
        var brMon 			= brAvg * total_month_days;

        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.total_br', this).html(brAvg.toFixed(2).toLocaleString('en')+'%');
        $(this).find('.br_avg', this).html(brAvg.toFixed(2).toLocaleString('en')+'%');
        $(this).find('.br_monthly', this).html(brMon.toFixed(2).toLocaleString('en')+'%');

        var sum8=0;
        var sum9=0;
        var lc = $(this).find('.total_arpu', this).attr('data-currency');
        var country = $(this).find('.total_arpu', this).attr('data-country');
        //find the combat elements in the current row and sum it
        $(this).find('.arpu_data').each(function (i,k) {
        	//if(i > 0){
	            var combat = $(this).attr('data-arpu').replace(/,/g, '');
	            if (!isNaN(combat) && combat.length !== 0) {
	                sum8 += parseFloat(combat);
	            }

	            var combatUsd = $(this).attr('data-arpu-usd').replace(/,/g, '');
	            if (!isNaN(combatUsd) && combatUsd.length !== 0) {
	                sum9 += parseFloat(combatUsd);
	            }
	       // }
        });

        var sumPurged=0;
        var length=0;
        var currentVal = 0;
        //find the combat elements in the current row and sum it
        $(this).find('.purged_data').each(function (i,k) {
        	if(i > 0){
            	var combat = $(this).text().replace(/,/g, '');
            	if (!isNaN(combat) && combat.length !== 0) {
                	sumPurged += parseFloat(combat);
            	}
            	length += 1;
        	}else{
				if(current_month == 0){
					var combat = $(this).text().replace(/,/g, '');
					if (!isNaN(combat) && combat.length !== 0) {
						sumPurged += parseFloat(combat);
					}
					length += 1;
				}
			}
        });
        var month_rem_days =  total_month_days - length;
        var purgeAvg = sumPurged /  (length);
        var purgeMon = sumPurged + (purgeAvg  * month_rem_days);

        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.purged_total', this).html(sumPurged.toLocaleString('en'));
        $(this).find('.purged_avg', this).html(Math.round(purgeAvg).toLocaleString('en'));
        $(this).find('.purged_monthly', this).html(Math.round(purgeMon).toLocaleString('en'));


        var sumSent=0;
        var length=0;
        //find the combat elements in the current row and sum it
        $(this).find('.total_sent_data').each(function (i,k) {
        	if(i > 0){
        		var combat = $(this).text().replace(/,/g, '');
	        	if (!isNaN(combat) && combat.length !== 0) {
	            	sumSent += parseFloat(combat);
	        	}
	        	length += 1;
        	}else{
				if(current_month == 0){
					var combat = $(this).text().replace(/,/g, '');
					if (!isNaN(combat) && combat.length !== 0) {
						sumSent += parseFloat(combat);
					}
					length += 1;
				}
			}

        });
        var month_rem_days =  total_month_days - length;
        var sentAvg = sumSent /  length;
        var sentMon = sumSent + (sentAvg  * month_rem_days);

        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.total_sent', this).html(sumSent.toLocaleString('en'));
        $(this).find('.total_sent_avg', this).html(Math.round(sentAvg).toLocaleString('en'));
        $(this).find('.total_sent_mon', this).html(Math.round(sentMon).toLocaleString('en'));

        if(country == ''){
        	$(this).find('.total_arpu', this).html(sum8.toFixed(2).toLocaleString('en') + '('+ lc +')');
        } else {
        	$(this).find('.total_arpu', this).html(sum8.toFixed(2).toLocaleString('en') + '('+ lc +')' + '</br>' + sum9.toFixed(2).toLocaleString('en') + '(USD)');
        }

        var sumArpu7=0;
        var length=0;
        //find the combat elements in the current row and sum it
        $(this).find('.arpu7_data').each(function (i,k) {
        	if(i > 0){
        		var combat = $(this).text().replace(/,/g, '');
	        	if (!isNaN(combat) && combat.length !== 0) {
	            	sumArpu7 += parseFloat(combat);
	        	}
	        	length += 1;
        	}else{
				if(current_month == 0){
					var combat = $(this).text().replace(/,/g, '');
					if (!isNaN(combat) && combat.length !== 0) {
						sumArpu7 += parseFloat(combat);
					}
					length += 1;
				}
			}
        });
        var month_rem_days =  total_month_days - length;
        var arpu7Avg = sumArpu7 /  length;
        var arpu7Mon = sumArpu7 + (arpu7Avg  * month_rem_days);

        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.arpu_seven_days', this).html(formatNumber(sumArpu7.toFixed(2).toLocaleString('en')));
        $(this).find('.arpu_seven_avg', this).html(formatNumber(arpu7Avg.toFixed(3).toLocaleString('en')));
        $(this).find('.arpu_seven_monthly', this).html(formatNumber(arpu7Mon.toFixed(2).toLocaleString('en')));

        var sumUsdArpu7=0;
        var length=0;
        //find the combat elements in the current row and sum it
        $(this).find('.usd_arpu7_data').each(function (i,k) {
        	if(i > 0){
        		var combat = $(this).text().replace(/,/g, '');
	        	if (!isNaN(combat) && combat.length !== 0) {
	            	sumUsdArpu7 += parseFloat(combat);
	        	}
	        	length += 1;
        	}else{
				if(current_month == 0){
					var combat = $(this).text().replace(/,/g, '');
					if (!isNaN(combat) && combat.length !== 0) {
						sumUsdArpu7 += parseFloat(combat);
					}
					length += 1;
				}
			}
        });
        var month_rem_days =  total_month_days - length;
        var arpuUsd7Avg = sumUsdArpu7 /  length;
        var arpuUsd7Mon = sumUsdArpu7 + (arpuUsd7Avg  * month_rem_days);

        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.arpu_usd_seven_days', this).html(formatNumber(sumUsdArpu7.toFixed(4).toLocaleString('en')));
        $(this).find('.arpu_usd_seven_avg', this).html(formatNumber(arpuUsd7Avg.toFixed(3).toLocaleString('en')));
        $(this).find('.arpu_usd_seven_monthly', this).html(formatNumber(arpuUsd7Mon.toFixed(4).toLocaleString('en')));

        var sumArpu30=0;
        var length=0;
        //find the combat elements in the current row and sum it
        $(this).find('.arpu30_data').each(function (i,k) {
        	if(i > 0){
        		var combat = $(this).text().replace(/,/g, '');
	        	if (!isNaN(combat) && combat.length !== 0) {
	            	sumArpu30 += parseFloat(combat);
	        	}
	        	length += 1;
        	}else{
				if(current_month == 0){
					var combat = $(this).text().replace(/,/g, '');
					if (!isNaN(combat) && combat.length !== 0) {
						sumArpu30 += parseFloat(combat);
					}
					length += 1;
				}
			}
        });
        var month_rem_days =  total_month_days - length;
        var arpu30Avg = sumArpu30 /  length;
        var arpu30Mon = sumArpu30 + (arpu30Avg  * month_rem_days);

        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.arpu_30_days', this).html(formatNumber(sumArpu30.toFixed(2).toLocaleString('en')));
        $(this).find('.arpu_30_avg', this).html(formatNumber(arpu30Avg.toFixed(3).toLocaleString('en')));
        $(this).find('.arpu_30_monthly', this).html(formatNumber(arpu30Mon.toFixed(2).toLocaleString('en')));

        var sumUsdArpu30=0;
        var length=0;
        //find the combat elements in the current row and sum it
        $(this).find('.usd_arpu30_data').each(function (i,k) {
        	if(i > 0){
        		var combat = $(this).text().replace(/,/g, '');
	        	if (!isNaN(combat) && combat.length !== 0) {
	            	sumUsdArpu30 += parseFloat(combat);
	        	}
	        	length += 1;
        	}else{
				if(current_month == 0){
					var combat = $(this).text().replace(/,/g, '');
					if (!isNaN(combat) && combat.length !== 0) {
						sumUsdArpu30 += parseFloat(combat);
					}
					length += 1;
				}
			}
        });
        var month_rem_days =  total_month_days - length;
        var arpuUsd30Avg = sumUsdArpu30 /  length;
        var arpuUsd30Mon = sumUsdArpu30 + (arpuUsd30Avg  * month_rem_days);

        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.arpu_usd_30_days', this).html(formatNumber(sumUsdArpu30.toFixed(4).toLocaleString('en')));
        $(this).find('.arpu_usd_30_avg', this).html(formatNumber(arpuUsd30Avg.toFixed(3).toLocaleString('en')));
        $(this).find('.arpu_usd_30_monthly', this).html(formatNumber(arpuUsd30Mon.toFixed(4).toLocaleString('en')));

        var sumChurn=0;
        var length=0;
        //find the combat elements in the current row and sum it
        $(this).find('.churn_data').each(function (i,k) {
        	if(i > 0){
        		var combat = $(this).text().replace('%', '');
	            if (!isNaN(combat) && combat.length !== 0) {
	                sumChurn += parseFloat(combat);
	            }
	            length += 1;
        	}else{
				if(current_month == 0){
					var combat = $(this).text().replace('%', '');
					if (!isNaN(combat) && combat.length !== 0) {
						sumChurn += parseFloat(combat);
					}
					length += 1;
				}
			}
        });
        //console.log(sumChurn + "----" + length);
        var month_rem_days =  total_month_days - length;
        var churnAvg       = sumChurn /  length;
        var churnMonth     = churnAvg * total_month_days;

        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.churn_total', this).html(churnAvg.toFixed(2).toLocaleString('en')+'%');
        $(this).find('.churn_avg', this).html(churnAvg.toFixed(2).toLocaleString('en')+'%');
        $(this).find('.churn_monthly', this).html(churnMonth.toFixed(2).toLocaleString('en')+'%');

        var sumFirstPush=0;
        var length=0;
        //find the combat elements in the current row and sum it
        $(this).find('.first_push_data').each(function (i,k) {
            //var combat = $(this).text().replace(/,/g, '');
            if(i > 0){
        		var combat = $(this).text().replace('%', '');
	            if (!isNaN(combat) && combat.length !== 0) {
	                sumFirstPush += parseFloat(combat);
	            }
	            length += 1;
        	}
        });
        var month_rem_days =  total_month_days - length;
        var firstPushAvg   = sumFirstPush /  length;
        var firstPushMon   = firstPushAvg * total_month_days;

        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.first_push_total', this).html(formatNumber(firstPushAvg.toFixed(2).toLocaleString('en'))+'%');
        $(this).find('.first_push_avg', this).html(formatNumber(firstPushAvg.toFixed(2).toLocaleString('en'))+'%');
        $(this).find('.first_push_mon', this).html(formatNumber(firstPushMon.toFixed(2).toLocaleString('en'))+'%');

        var sumDailyPush=0;
        var length=0;
        //find the combat elements in the current row and sum it
        $(this).find('.daily_push_data').each(function (i,k) {
            //var combat = $(this).text().replace(/,/g, '');
            if(i > 0){
        		var combat = $(this).text().replace('%', '');
	            if (!isNaN(combat) && combat.length !== 0) {
	                sumDailyPush += parseFloat(combat);
	            }
	            length += 1;
        	}else{
				if(current_month == 0){
					var combat = $(this).text().replace('%', '');
					if (!isNaN(combat) && combat.length !== 0) {
						sumDailyPush += parseFloat(combat);
					}
					length += 1;
				}
			}
        });
       // console.log(sumDailyPush);

        var month_rem_days =  total_month_days - length;
        var dailyPushAvg   = sumDailyPush /  length;
        var dailyPushMon   = dailyPushAvg * total_month_days;


        //set the value of currents rows sum to the total-combat element in the current row
        $(this).find('.daily_push_total', this).html(formatNumber(dailyPushAvg.toFixed(2).toLocaleString('en'))+'%');
        $(this).find('.daily_push_avg', this).html(formatNumber(dailyPushAvg.toFixed(2).toLocaleString('en'))+'%');
        $(this).find('.daily_push_mon', this).html(formatNumber(dailyPushMon.toFixed(2).toLocaleString('en'))+'%');

        //Total Average add
        setTimeout(function(){
        	var all_sub_sum = 0;
        	var all_sub_avg_sum = 0;
        	var all_sub_mon_sum = 0;
        	var all_renewal_sum = 0;
        	var all_renewal_avg_sum = 0;
        	var all_renewal_mon_sum = 0;

        	$('.revenue_total').each(function(){
	        	var localcurrency = $(this).attr('data-local-currency');
	        	var usd = $(this).attr('data-usd');
	        	var country = $(this).attr('data-country');
			    var total_average= '';
			    var total_month_end= 0;
			    var total_remaining_amount=0;
			    var no_of_days= $('#summery_days').val();
			    var remaining_days= 31-no_of_days;
			    var local_total_revenue= parseFloat($(this).find('.local_total_revenue').text().replace(/,/g, ''));
			    var format_total_revenue= parseFloat($(this).parent().parent().find('.format_total_revenue').text().replace(/,/g, ''));
			    var total_success_rate=0;
			    var total_before_per=0;
			    var total_sent= $(this).text().replace(/,/g, '');
			    var total_delivered= $(this).parent().parent().find('td.mt_total_delivered').text().replace(/,/g, '');
			    if(total_sent == 0.00){
			    	total_before_per = 0;
			    } else {
			    	total_before_per = total_delivered/total_sent;
			    }
			    total_success_rate= parseFloat(total_before_per)*100;


			    if(country == ''){
		        	var tavg = Math.round(local_total_revenue / no_of_days).toLocaleString('en') + '('+ localcurrency +')';
		        	var favg = '';
		        	total_remaining_amount= parseFloat(local_total_revenue / no_of_days)*remaining_days;
		        } else {
		        	/*if(country == 'Cambodia'){
		        		var favg = Math.round(format_total_revenue / no_of_days).toLocaleString('en') + '(USD)';
		        		var tavg = Math.round(local_total_revenue / no_of_days).toLocaleString('en') + '('+ localcurrency +')' ;
			        	total_remaining_amount= parseFloat(format_total_revenue / no_of_days)*remaining_days;
			        }else {
			        	var tavg = Math.round(local_total_revenue / no_of_days).toLocaleString('en') + '('+ localcurrency +')';
		        		var favg = Math.round(format_total_revenue / no_of_days).toLocaleString('en') + '(USD)';
			        	total_remaining_amount= parseFloat(local_total_revenue / no_of_days)*remaining_days;
			        }*/
			        var tavg = parseFloat(local_total_revenue / no_of_days).toLocaleString('en') + '('+ localcurrency +')';
	        		var favg = parseFloat(format_total_revenue / no_of_days).toLocaleString('en') + '(USD)';
		        	total_remaining_amount= parseFloat(local_total_revenue / no_of_days)*remaining_days;
		        }
		        //console.log(tavg);

			    total_average= "<span class='local_total_average'>"+tavg+"</span><br/><span class='format_total_average'>" + favg+"</span>";
				total_month_end= parseFloat(local_total_revenue)+parseFloat(total_remaining_amount);

			    $(this).parent().parent().find('td.total_average').html(total_average);
			 	$(this).parent().parent().find('td.total_month_end').html(parseFloat(total_month_end).toLocaleString('en'));
			 	$(this).parent().parent().find('td.mt_total_success').html(total_success_rate.toFixed(2)+'%');
			})

			$('.share_total').each(function(i,k){
				var RevShare = 0;
	        	var share = parseFloat($(this).text().replace(/,/g, ''));
                var subs = $(this).attr('data-subs');
			    var arpuRevShare = share / subs;
			    if (!isNaN(arpuRevShare) && arpuRevShare.length !== 0) {
	                RevShare = parseFloat(arpuRevShare);
	            }

			    var country = $(this).attr('data-country');
			    var currency = $(this).attr('data-local-currency');

			    if(RevShare == 'Infinity'){
		    		RevShare = 'Not defined';
		    	} else {
		    		RevShare = RevShare.toFixed(2).toLocaleString('en')+'('+currency+')';
		    	}

			    $(this).parent().parent().find('td.arpu_after_total').find('span.revarpuShare').html(RevShare);
			})

			var allsharetotal = 0;
			$('.share_total_usd').each(function(i,k){
				var RevUSDShare = 0;
	        	var share = parseFloat($(this).text().replace(/,/g, ''));
                var subs = $(this).attr('data-subs');
			    var arpuRevUSDShare = share / subs;
			    if (!isNaN(arpuRevUSDShare) && arpuRevUSDShare.length !== 0) {
	                RevUSDShare = parseFloat(arpuRevUSDShare);
	            }

			    var country = $(this).attr('data-country');

			    if(country != ''){
			    	if(RevUSDShare == 'Infinity'){
			    		RevUSDShare = 'Not defined';
			    	} else {
			    		RevUSDShare = RevUSDShare.toFixed(2).toLocaleString('en')+'(USD)';
			    	}
		        	$(this).parent().parent().find('td.arpu_after_total').find('span.revarpuShareUSD').html(RevUSDShare);
		        }

		        allsharetotal += parseFloat(share);
			})

			$('.share_total').each(function(i,k){
				if(i == 0){
					$(this).text(formatNumber(allsharetotal.toFixed(2).toLocaleString('en')+'(USD)'));
				}
			})

			$('.subactive_total').each(function(){
				var combat = $(this).text().replace(/,/g, '');
		        if (!isNaN(combat) && combat.length !== 0) {
		            all_sub_sum += parseFloat(combat);
		        }
			})

			$('.subactive_avg').each(function(){
				var combat = $(this).text().replace(/,/g, '');
		        if (!isNaN(combat) && combat.length !== 0) {
		            all_sub_avg_sum += parseFloat(combat);
		        }
			})

			$('.subactive_monthly').each(function(){
				var combat = $(this).text().replace(/,/g, '');
		        if (!isNaN(combat) && combat.length !== 0) {
		            all_sub_mon_sum += parseFloat(combat);
		        }
			})

			$('.total_sent').each(function(){
				var combat = $(this).text().replace(/,/g, '');
		        if (!isNaN(combat) && combat.length !== 0) {
		            all_renewal_sum += parseFloat(combat);
		        }
			})

			$('.total_sent_avg').each(function(){
				var combat = $(this).text().replace(/,/g, '');
		        if (!isNaN(combat) && combat.length !== 0) {
		            all_renewal_avg_sum += parseFloat(combat);
		        }
			})

			$('.total_sent_mon').each(function(){
				var combat = $(this).text().replace(/,/g, '');
		        if (!isNaN(combat) && combat.length !== 0) {
		            all_renewal_mon_sum += parseFloat(combat);
		        }
			})
        	$(document).find("td.all_subactive_total").html(formatNumber(all_sub_sum));
        	$(document).find("td.all_subactive_avg").html(formatNumber(all_sub_avg_sum));
        	$(document).find("td.all_subactive_monthly").html(formatNumber(all_sub_mon_sum));
        	$(document).find("td.all_total_sent").html(formatNumber(all_renewal_sum));
        	$(document).find("td.all_total_sent_avg").html(formatNumber(all_renewal_avg_sum));
        	$(document).find("td.all_total_sent_mon").html(formatNumber(all_renewal_mon_sum));
        },5000);
	});


	function format(n, sep, decimals) {
	    sep = sep || "."; // Default to period as decimal separator
	    decimals = decimals || 2; // Default to 2 decimals

	    return n.toLocaleString().split(sep)[0]
	        + sep
	        + n.toFixed(decimals).split(sep)[1];
	}
    //After click the operator name type shows in the summary page.
	/*$(document).on('click','.summay_cuurent_month',function(){
			$('.serviceType').show();
	});*/

	//Get list of country changes on dashboard..
	$(document).on('change', '#dashboardcountry', function () {
		$('.error_block').html('');
		$(".loader_image").show();
		var country = $(this).val();
		var csrf_token= $("#csrf_token").val();
		$.ajax({
				url    : base_url+'dashboard/getoperatorbycountry',
				type   : 'post',
				data   : {'country':country},
				datatype: 'json',
				headers: {
                    'X-CSRF-Token': csrf_token
              	},
				success: function (response)
					{
						//console.log(response);

						if(response == '<option value="">Operator Name</option>'){
							$('.dashboardSearchBtn').attr('disabled','disabled');
						}else{
							$('.dashboardSearchBtn').removeAttr('disabled');

						}
						$('#dashboardoperator').empty();
						$('#dashboardservice').empty();
						var operatordata = '<option value="">Operator Name</option><option value="all">All</option>';
						var servicedata= '<option value="">Service Name</option>';
						$.each(JSON.parse(response), function(k, v) {
							var operator_name = v.operator_name;
							if(operator_name == 'truemove'){
								operator_name = 'true-cyb';
							}
							if(operator_name == 'th-ais-old'){
								operator_name = 'th-ais-cm';
							}
							operatordata += '<option value="'+v.id_operator+'">'+operator_name+'</option>';
						/// do stuff
						});
						$('#dashboardoperator').append(operatordata);
						$('#dashboardservice').append(servicedata);
						$(".loader_image").hide();
					},
					error  : function ()
					{
						console.log('internal server error');
					}
		});
		return false;
	});

	var country = $(document).find("#no_filter_country").val();
	var operator = $(document).find("#no_filter_operator").val();
	var csrf_token= $("#csrf_token").val();
	if(country != '' && country != undefined){
		$.ajax({
				url    : base_url+'dashboard/getoperatorbycountry',
				type   : 'post',
				data   : {'country':country},
				datatype: 'json',
				headers: {
	                'X-CSRF-Token': csrf_token
	          	},
				success: function (response)
				{
					if(response == '<option value="">Operator Name</option>'){
						$('.search_btn_summery_data1').attr('disabled','disabled');
					}else{
						$('.search_btn_summery_data1').removeAttr('disabled');
					}
					$(document).find('#summery_operator_id').empty();
					var operatordata = new Option('View All', 'all');
					$("#summery_operator_id").append(operatordata);

					var res = JSON.parse(response);
					$.each(res, function(k, v) {
						operatordata = new Option(v.operator_name, v.id_operator);
						if(parseInt(v.id_operator) == operator){
							operatordata.selected = true;
						}
						$("#summery_operator_id").append(operatordata);
					});
					$("#summery_operator_id").trigger("change");
				},
				error  : function ()
				{
					console.log('internal server error');
				}
		});
	}

	//Get list of operators on country change on summary and details page..
	$(document).on('change', '#summarycountry', function () {
		$('.error_block').html('');
		$("#search_btn_summery_data1").prop('disabled',false);
		//$(".loader_image").show();
		var country = $(this).val();
		var company = $(document).find('#summarycompany').val();
		var csrf_token= $("#csrf_token").val();
		if(country == ''){
			$('#detailsearch').prop('disabled',true);
			$('#summery_operator_id').empty();
			var operatordata = '<option value="">Operator Name</option>';
			$('#summery_operator_id').append(operatordata);
		} else {
			$('#detailsearch').prop('disabled',true);
			$.ajax({
				url    : base_url+'dashboard/getoperatorbycountry',
				type   : 'post',
				data   : {'country':country, 'company':company},
				datatype: 'json',
				headers: {
                    'X-CSRF-Token': csrf_token
              	},
				success: function (response)
				{
					//console.log(response);

					if(response == '<option value="">Operator Name</option>'){
						$('.search_btn_summery_data1').attr('disabled','disabled');
					}else{
						$('.search_btn_summery_data1').removeAttr('disabled');

					}
					$('#summery_operator_id').empty();
					$('#dashboardservice').empty();
					//console.log(window.location.href)
					var operatordata = (window.location.href != base_url+'dashboard' || window.location.href != base_url+'dashboard/operator' || window.location.href != base_url+'dashboard/company') ? '<option value="all">Operator Name</option>' : '<option value="">Operator Name</option>';
					var servicedata= '<option value="">Service Name</option>';
					$.each(JSON.parse(response), function(k, v) {
						if(v.operator_name == 'truemove'){
							v.operator_name = 'true-cyb';
						}
						if(v.operator_name == 'th-ais-old'){
							v.operator_name = 'th-ais-cm';
						}
						operatordata += '<option value="'+v.id_operator+'">'+v.operator_name+'</option>';
					/// do stuff
					});
					$('#summery_operator_id').append(operatordata);
					$('#dashboardservice').append(servicedata);
					//$(".loader_image").hide();
				},
				error  : function ()
				{
					console.log('internal server error');
				}
			});
		}
	});

	//Get list of service of operator changes..
	$(document).on('change', '#dashboardoperator', function () {
		$('.error_block').html('');
		var operator_id= $(this).val();
		var csrf_token= $("#csrf_token").val();
		var country = $("#dashboardcountry").val();
		if(country == ''){
			$('.error_block').html('<span class="error" style="color:red;">Please fill up the fields</span>');
			return false;
		} else {


			$.ajax({
					url    : base_url+'dashboard/getservicedatabyoperator',
					type   : 'post',
					data   : {'operator_id':operator_id, 'country':country},
					datatype: 'json',
					headers: {
	                    'X-CSRF-Token': csrf_token
	              	},
					success: function (response)
						{
							if(response == '<option value="">Service Name</option>'){
								$('.dashboardSearchBtn').attr('disabled','disabled');
							}else{
								$('.dashboardSearchBtn').removeAttr('disabled');
							}

							$('#dashboardservice').empty();
							$('#dashboardkeyword').empty();
							var servicedata= '<option value="">Service Name</option><option value="all">All</option>';
							var keyworddata= '<option value="">Subkeyword</option><option value="all">All</option>';
							//console.warn(response);
							$.each(JSON.parse(response), function(k, v) {
								servicedata += '<option value="'+v.id_service+'">'+v.servicename+'</option>';
								keyworddata += '<option value="'+v.id_service+'">'+v.keyword+'</option>';
							/// do stuff
							});
							$('#dashboardservice').append(servicedata);
							$('#dashboardkeyword').append(keyworddata);


							$(".loader_image").hide();
						//console.log(getupdatedata);
						},
						error  : function ()
						{
							console.log('internal server error');
						}
			});
		}

		return false;
	});

	// $('#dashboardservice').on('change', function(){
	// 	var country = $("#dashboardcountry").val();
	// 	var operator_id= $('#summery_operator_id').val();
	// 	var service = $(this).val();
	// 	var csrf_token= $("#csrf_token").val();

	// 	$('#dashboardkeyword').attr('value','test');
	// })

	$(document).on('change', '#summery_operator_id', function () {
			var country = $("#summarycountry").val();
			var operator_id= $(this).val();
			//console.log(country, operator_id);
			var csrf_token= $("#csrf_token").val();
			$("#detailsearch").prop('disabled', true);
			$("#summery_operator_id").prop('disabled',false);
			$(this).css('border','1px solid rgba(0,0,0,.125)');
			if(country == ''){
				$('.error_block').html('');
				$("#summarycountry").css('border','1px solid red');
				$("#detailsearch").prop('disabled', true);
				$(".search_btn_summery_data1").prop('disabled', true);
				$('.error_block').html('<span class="error">Please fill up the fields</span>');
				$(this).val('');
				return false;
			} else{
				$("#summarycountry").css('border','1px solid rgba(0,0,0,.125)');
				if(operator_id == '' || operator_id == 'all'){
					$(this).css('border','1px solid red');
					if(window.location.href != base_url+'dashboard')
						$('.error_block').html('<span class="error">Please fill up the fields</span>');
					$("#detailsearch").prop('disabled', true);
					$('.search_btn_summery_data1').prop('disabled', true);
					return false;
				} else {
					$('.error_block').html('');
					$(this).css('border','1px solid rgba(0,0,0,.125)');
					$("#detailsearch").prop('disabled', false);
					$('.search_btn_summery_data1').prop('disabled', false);
				}
				callAjaxForService(csrf_token, country, operator_id);
			}
	});

	//Get list of service of operator changes..
	// $(document).on('click', '.dashboardSearchBtn', function () {
	// 	$('.error_block').html('');
	// 	var country = $("#dashboardcountry").val();
	// 	var service_id= $('#dashboardservice').val();
	// 	var operator_id = $('#dashboardoperator').val();
	// 	var csrf_token= $("#csrf_token").val();
	// 	var redirect_url  = base_url+'dashboard/reporting-details?_csrf='+csrf_token+'&country-name='+country+'&operator-name='+operator_id+'&service-name='+service_id+'&dashboardsubkeyword=&summary-date=';

	// 	if(country == "" || operator_id == ""){
	// 		$('.error_block').html('');
	// 		$('.error_block').html('<span class="error" style="color:red;">Please fill up the fields</span>');
	// 		if(country == '' && operator_id != ''){
	// 			$('.error_block').html('');
	// 			 $('.error_block').html('<span class="error" style="color:red;">Please select country</span>');
	// 		} else if(country != '' && operator_id == ''){
	// 			$('.error_block').html('');
	// 			 $('.error_block').html('<span class="error" style="color:red;">Please select operator</span>');
	// 		} else if(country == "" && service_id == ""){
	// 			$('.error_block').html('');
	// 			$('.error_block').html('<span class="error" style="color:red;">Please fill up the fields</span>');
	// 		}
	// 		return false;
	// 	}

	// 	var html = "";

	// 	$.ajax({
	// 			url    : base_url+'dashboard/getalldatabyserviceid',
	// 			type   : 'post',
	// 			data   : {'service_id':service_id, 'country':country, 'operator_id':operator_id},
	// 			dataType: 'json',
	// 			headers: {
    //                 'X-CSRF-Token': csrf_token
    //           	},
	// 			success: function (response)
	// 				{
	// 					html = '';
	// 					$(".loader_image").hide();

	// 					var data = response.country_service_revenue;

	// 					var currency = response.currency;

	// 					$.each(data,function(key,value){

	// 						html += '<div class="d-flex align-items-center my-2"><span class="badge badge-secondary px-2 bg-primary">'+key+'</span><span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span></div><div class="row"><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient">Current Monthly</div><div class="card-body"><div class="card-text"><p class="mb-2"><strong>Revenue :</strong></p>';
    //             			if(country == 'Cambodia'){
	// 							html += '<p class="mb-0">'+value.current_monthly_revenue+'(USD)'+'</p><p class="mb-0">'+value.current_monthly_revenue_usd+'('+currency+')'+'</p>';
	// 						} else {
	// 							html += '<p class="mb-0">'+value.current_monthly_revenue_usd+'(USD)'+'</p><p class="mb-0">'+value.current_monthly_revenue+'('+currency+')'+'</p>';
	// 						}
	// 						html += '<p class="mb-0 mt-2"><strong>MO :</strong>&nbsp;'+value.current_monthly_mo+'</p>';
	// 						html += '<p class="mb-0 mt-2"><strong>PNL :</strong>&nbsp;'+value.current_monthly_pnl+'</p></div></div></div></div><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient-red">Current AVG Daily</div><div class="card-body"><div class="card-text"><p class="mb-2"><strong>Revenue :</strong></p><p class="mb-0">';
	// 						if(country == 'Cambodia'){
	// 							if(value.current_average_daily_usd < value.previous_average_daily_usd){
	// 								var revenueIndicator = '<span class="text-danger"><i class="fa fa-arrow-down"></i></span>';
	// 							} else {
	// 								var revenueIndicator = '<span class="text-success"><i class="fa fa-arrow-up"></i></span>';
	// 							}
	// 							html += '<p class="mb-0">'+value.current_average_daily+'(USD)</p><p class="mb-0">'+value.current_average_daily_usd+'('+currency+')'+ revenueIndicator+'</p>';
	// 						} else {
	// 							if(value.current_average_daily < value.previous_average_daily){
	// 								var revenueIndicator = '<span class="text-danger"><i class="fa fa-arrow-down"></i></span>';
	// 							} else {
	// 								var revenueIndicator = '<span class="text-success"><i class="fa fa-arrow-up"></i></span>';
	// 							}
	// 							html += '<p class="mb-0">'+value.current_average_daily_usd+'(USD)</p><p class="mb-0">'+value.current_average_daily+'('+currency+')'+revenueIndicator+'</p>';
	// 						}
	// 						if(value.current_average_mo < value.previous_average_mo){
	// 							var moIndicator = '<span class="text-danger"><i class="fa fa-arrow-down"></i></span>';
	// 						} else {
	// 							var moIndicator = '<span class="text-success"><i class="fa fa-arrow-up"></i></span>';
	// 						}

	// 						if(value.average_monthly_pnl < value.previous_average_pnl){
	// 							var pnlIndicator = '<span class="text-danger"><i class="fa fa-arrow-down"></i></span>';
	// 						} else {
	// 							var pnlIndicator = '<span class="text-success"><i class="fa fa-arrow-up"></i></span>';
	// 						}

	// 						html += '<p class="mb-0 mt-2"><strong>MO :</strong>&nbsp;'+value.current_average_mo+moIndicator+'</p><p class="mb-0 mt-2"><strong>PNL :</strong>&nbsp;'+value.average_monthly_pnl+pnlIndicator+'</p></div></div></div></div><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient-green">Estimated EOM</div><div class="card-body"><div class="card-text"><p class="mb-2"><strong>Revenue :</strong></p>';
    //           				if(country == 'Cambodia'){
	// 							html += '<p class="mb-0">'+value.estimated_revenue+'(USD)'+'</p><p class="mb-0">'+value.estimated_revenue_usd+'('+currency+')'+'</p>';
	// 						} else {
	// 							html += '<p class="mb-0">'+value.estimated_revenue_usd+'(USD)'+'</p><p class="mb-0">'+value.estimated_revenue+'('+currency+')'+'</p>';
	// 						}
	// 						html += '<p class="mb-0 mt-2"><strong>MO :</strong>&nbsp;'+value.estimated_mo+'</p><p class="mb-0 mt-2"><strong>PNL :</strong>&nbsp;'+value.estimated_pnl+'</p></div></div></div></div><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient-purple">Previous Month AVG</div><div class="card-body"><div class="card-text"><p class="mb-2"><strong>Revenue :</strong></p>';
	// 						if(country == 'Cambodia'){
	// 							html += '<p class="mb-0">'+value.previous_average_daily+'(USD)'+'</p><p class="mb-0">'+value.previous_average_daily_usd+'('+currency+')'+'</p>';
	// 						} else {
	// 							html += '<p class="mb-0">'+value.previous_average_daily_usd+'(USD)'+'</p><p class="mb-0">'+value.previous_average_daily+'('+currency+')'+'</p>';
	// 						}
    //             			html += '<p class="mb-0 mt-2"><strong>MO :</strong>&nbsp;' + value.previous_average_mo + '</p><p class="mb-0 mt-2"><strong>PNL :</strong>&nbsp;' + value.previous_average_pnl + '</p></div></div></div></div></div>';
	// 					});

	// 					$("#dashboardCountrydata").html(html);
	// 				},
	// 				error  : function ()
	// 				{
	// 					console.log('internal server error');
	// 				}
	// 	});
	// 	return false;
	// });
	$(document).on('click', '.dashboardSearchBtn', function () {
	 $('.dash').hide();
		$('.error_block').html('');
		var country = $("#summarycountry").val();
		var service_id= $('#dashboardservice').val();
		var operator_id = $('#summery_operator_id').val();
		var csrf_token= $("#csrf_token").val();
		var redirect_url  = base_url+'dashboard/reporting-details?_csrf='+csrf_token+'&country-name='+country+'&operator-name='+operator_id+'&service-name='+service_id+'&dashboardsubkeyword=&summary-date=';

		if(country == "" || operator_id == ""){
			$('.error_block').html('');
			$('.error_block').html('<span class="error" style="color:red;">Please fill up the fields</span>');
			if(country == '' && operator_id != ''){
				$('.error_block').html('');
				 $('.error_block').html('<span class="error" style="color:red;">Please select country</span>');
			} else if(country != '' && operator_id == ''){
				$('.error_block').html('');
				 $('.error_block').html('<span class="error" style="color:red;">Please select operator</span>');
			} else if(country == "" && service_id == ""){
				$('.error_block').html('');
				$('.error_block').html('<span class="error" style="color:red;">Please fill up the fields</span>');
			}
			return false;
		}

		var html = "";

		$.ajax({
				url    : base_url+'dashboard/getalldatabyserviceid',
				type   : 'post',
				data   : {'service_id':service_id, 'country':country, 'operator_id':operator_id},
				dataType: 'json',
				headers: {
                    'X-CSRF-Token': csrf_token
              	},
				success: function (response)
					{

						//$('#dashboardCountrydata').hide();
						$('#dashboard-data').empty();
						$('#dashboard-data').html('<div id="dashboardCountrydata" class="dash"></div>');


						$(".loader_image").hide();

						var data = response.country_service_revenue;

						var currency = response.currency;

						$.each(data,function(key,value){
										//current
							html += '<div class="d-flex align-items-center my-2"><span class="badge badge-secondary px-2 bg-primary">'+key+'</span><span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span></div><div class="row"><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient">Current Monthly</div><div class="card-body"><div class="card-text"><p class="mb-0"><strong>Revenue :</strong></p>';
                			if(country == 'Cambodia'){
								html += '<p class="mb-0">'+value.current_monthly_revenue+'(USD)'+'</p><p class="mb-0">'+value.current_monthly_revenue_usd+'('+currency+')'+'</p>';
							} else {
								html += '<p class="mb-0">'+value.current_monthly_revenue_usd+'(USD)'+'</p><p class="mb-0">'+value.current_monthly_revenue+'('+currency+')'+'</p>';
							}
							if(country == 'Cambodia'){
								if(value.current_average_daily_usd < value.previous_average_daily_usd){
									var revenueIndicator = '<span class="text-danger"><i class="fa fa-arrow-down"></i></span>';
								} else {
									var revenueIndicator = '<span class="text-success"><i class="fa fa-arrow-up"></i></span>';
								}
								html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.current_average_daily_usd+ revenueIndicator+'(USD)'+'</p>';
							} else {
								if(value.current_average_daily < value.previous_average_daily){
									var revenueIndicator = '<span class="text-danger"><i class="fa fa-arrow-down"></i></span>';
								} else {
									var revenueIndicator = '<span class="text-success"><i class="fa fa-arrow-up"></i></span>';
								}
								html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.current_average_daily+revenueIndicator+'('+currency+')'+'</p>';
							}
							html += '<p class="mb-0 "><strong>Cost Campaign :</strong>&nbsp;'+value.current_cost+'</p>';

							html += '<p class="mb-0 "><strong>MO :</strong>&nbsp;'+value.current_monthly_mo+'</p>';

							if(value.current_average_mo < value.previous_average_mo){
								var moIndicator = '<span class="text-danger"><i class="fa fa-arrow-down"></i></span>';
							} else {
								var moIndicator = '<span class="text-success"><i class="fa fa-arrow-up"></i></span>';
							}

							html += '<p class="mb-0 "><strong>AVG MO :</strong>&nbsp;'+value.current_average_mo+moIndicator+'</p>';
							html += '<p class="mb-0 "><strong>PNL :</strong>&nbsp;'+value.current_monthly_pnl+'</p>';
							html += '<p class="mb-0 "><strong>AVG PNL :</strong>&nbsp;'+value.average_monthly_pnl+'</p></div></div></div></div><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient-green">Estimate EOM</div><div class="card-body"><div class="card-text"><p class="mb-0"><strong>Revenue:</strong></p><p class="mb-0">';
							//estiamted
							if(country == 'Cambodia'){
								html += '<p class="mb-0">'+value.estimated_revenue+'(USD)'+'</p><p class="mb-0">'+value.estimated_revenue_usd+'('+currency+')'+'</p>';
							} else {
								html += '<p class="mb-0">'+value.estimated_revenue_usd+'(USD)'+'</p><p class="mb-0">'+value.estimated_revenue+'('+currency+')'+'</p>';
							}
								if(country == 'Cambodia'){
								html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.estimated_avg_revenue_usd+'(USD)'+'</p>';
							} else {
								html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.estimated_avg_revenue+'('+currency+')'+'</p>';
							}html += '<p class="mb-0"><strong>Cost Campaign :</strong>&nbsp;'+value.estimated_cost+'</p>';

									html += '<p class="mb-0 "><strong>MO :</strong>&nbsp;'+value.estimated_mo+'</p>';
							html += '<p class="mb-0 "><strong>AVG MO :</strong>&nbsp;'+value.estimated_average_mo+'</p>';
								html += '<p class="mb-0 "><strong>PNL :</strong>&nbsp;'+value.estimated_pnl+'</p>';
							html += '<p class="mb-0 "><strong>AVG PNL :</strong>&nbsp;'+value.estimated_avg_pnl+'</p></div></div></div></div><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient-red">Last Month</div><div class="card-body"><div class="card-text"><p class="mb-0"><strong>Revenue :</strong></p><p class="mb-0">';
							//last month
							if(country == 'Cambodia'){
								html += '<p class="mb-0">'+value.previous_month_revenue+'(USD)'+'</p><p class="mb-0">'+value.previous_month_revenue_usd+'('+currency+')'+'</p>';
							} else {
								html += '<p class="mb-0">'+value.previous_month_revenue_usd+'(USD)'+'</p><p class="mb-0">'+value.previous_month_revenue+'('+currency+')'+'</p>';
							}
								if(country == 'Cambodia'){
								html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.previous_average_daily_usd+'(USD)'+'</p>';
							} else {
								html += '<p class="mb-0"><strong>AVG Revenue:</strong>&nbsp;'+value.previous_average_daily+'('+currency+')'+'</p>';
							}
							html += '<p class="mb-0"><strong>Cost Campaign :</strong>&nbsp;'+value.previous_cost+'</p>';

							html += '<p class="mb-0 "><strong>MO :</strong>&nbsp;'+value.previous_mo+'</p>';
							html += '<p class="mb-0 "><strong>AVG MO :</strong>&nbsp;'+value.previous_average_mo+'</p>';
							html += '<p class="mb-0 "><strong>PNL :</strong>&nbsp;'+value.previous_pnl+'</p>';
							html += '<p class="mb-0 "><strong>AVG PNL :</strong>&nbsp;'+value.previous_average_pnl+'</p></div></div></div></div><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient-purple">Previous Month </div><div class="card-body"><div class="card-text"><p class="mb-0"><strong>Revenue :</strong></p><p class="mb-0">';
                            //previous month
							if(country == 'Cambodia'){
								html += '<p class="mb-0">'+value.previous2_month_revenue+'(USD)'+'</p><p class="mb-0">'+value.previous2_month_revenue_usd+'('+currency+')'+'</p>';
							} else {
								html += '<p class="mb-0">'+value.previous2_month_revenue_usd+'(USD)'+'</p><p class="mb-0">'+value.previous2_month_revenue+'('+currency+')'+'</p>';
							}

							if(country == 'Cambodia'){
								html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.previous2_average_daily_usd+'(USD)'+'</p>';
							} else {
								html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.previous2_average_daily+'('+currency+')'+'</p>';
							}
							html += '<p class="mb-0 "><strong>Cost Campaign :</strong>&nbsp;'+value.previous2_cost+'</p>';

								html += '<p class="mb-0"><strong>MO :</strong>&nbsp;'+value.previous2_mo+'</p>';
							html += '<p class="mb-0"><strong>AVG MO :</strong>&nbsp;'+value.previous2_average_mo+'</p>';
							html += '<p class="mb-0 "><strong>PNL :</strong>&nbsp;'+value.previous2_pnl+'</p>';
							html += '<p class="mb-0 "><strong>AVG PNL :</strong>&nbsp;'+value.previous2_average_pnl+'</p></div></div></div></div>';

						});

						$("#dashboardCountrydata").html(html);
					},
					error  : function ()
					{
						console.log('internal server error');
					}
		});
		return false;
	});


	$(document).on('click', '.search_btn_summery_data1', function (e) {
		e.preventDefault();
		var company = $(document).find("#summarycompany").val();
		if(company != ''){
			$(this).prop('disabled', false);

			var country = $("#summarycountry").val();
			var operator = $("#summery_operator_id").val();

			/*if(country == '' && operator == ''){
				$(this).prop('disabled', true);
				alert("Please select country and operators");
			} else if(country ==''){
				$(this).prop('disabled', true);
				alert("Please select country");
			} else {
				$(this).prop('disabled', false);
				$("#summaryForm").submit();
			}*/
			$("#summaryForm").submit();
		}  else {
			$(this).prop('disabled', false);
			$("#summaryForm").submit();
		}
	});

	/*$(document).on('click', '.search_btn_summery_data1', function () {
		$('.error_block').html('');
		$("#summarycountry,#summery_operator_id").css('border','1px solid rgba(0,0,0,.125)');
		$(".loader_image").show();
		var country = $("#summarycountry").val();
		var operator_id = $('#summery_operator_id').val();

		if(country == "" || operator_id == ""){
			$('.error_block').html('');
			$("#summarycountry,#summery_operator_id").css('border','1px solid red');
			$('.error_block').html('<span class="error">Please fill up the marked fields</span>');
			if(country == '' && operator_id !=''){
				 $("#summarycountry").css('border','1px solid red');
				 $("#summery_operator_id").css('border','1px solid rgba(0,0,0,.125)');
			} else if(operator_id == '' && country != ''){
				 $("#summery_operator_id").css('border','1px solid red');
				 $("#summarycountry").css('border','1px solid rgba(0,0,0,.125)');
			}
			$(this).prop('disabled', true);
			return false;
		} else {
			if(country != "" && operator_id != ""){
				$('.error_block').html('');
				$("#summery_operator_id").css('border','1px solid rgba(0,0,0,.125)');
				$("#summarycountry").css('border','1px solid rgba(0,0,0,.125)');
				$(this).prop('disabled', false);
			}
		}
	});*/


	/*$(document).on('click','.next_description',function() {
		$('.loader').css('display','block');
		setTimeout(function(){
			 $(".service_description").css({'display':'none'});
	    	 $('.screenshot_flow_block').css({'display':'flex'});
	 $('.forcast_block').css({'display':'none'});
			 $('.loader').css('display','none');
		},1500);
	});

	$(document).on('click','.back_screenshot_flow_block',function() {
		$('.loader').css('display','block');
		setTimeout(function(){
			 $(".service_description").css({'display':'flex'});
	    	 $('.screenshot_flow_block').css({'display':'none'});
			  $('.forcast_block').css({'display':'none'});
			 $('.loader').css('display','none');
		},1500);

	   // $('.service_description').hide();
	});*/

	/*$(document).on('click','.next_screenshot_flow_block',function() {
		$('.loader').css('display','block');
		setTimeout(function(){
			  $(".service_description").css({'display':'none'});
			 $(".screenshot_flow_block").css({'display':'none'});
	    	 $('.forcast_block').css({'display':'flex'});
			 $('.loader').css('display','none');
		},1500);
	});*/

	/*$(document).on('click','.back_forecast_block',function() {
		$('.loader').css('display','block');
		setTimeout(function(){
			  $(".service_description").css({'display':'none'});
			 $(".screenshot_flow_block").css({'display':'flex'});
	    	 $('.forcast_block').css({'display':'none'});
			 $('.loader').css('display','none');
		},1500);
	});*/

	/*$(document).on('click','.next_forecast_block',function() {
		$('.loader').css('display','block');
		setTimeout(function(){
			$(".service_description").css({'display':'none'});
			$(".screenshot_flow_block").css({'display':'none'});
			$('.forcast_block').css({'display':'none'});
			$('.pricepoint_block').css({'display':'flex'});
			$('.loader').css('display','none');
		},1500);
	});*/

	//Price Point next back click..
	/*$(document).on('click','.back_pricepoint_block',function() {
		$('.loader').css('display','block');
		setTimeout(function(){
			$(".service_description").css({'display':'none'});
			$(".screenshot_flow_block").css({'display':'none'});
			$('.forcast_block').css({'display':'flex'});
			$('.pricepoint_block').css({'display':'none'});
			$('.loader').css('display','none');
		},1500);
	});

	$(document).on('click','.next_pricepoint_block',function() {
		$('.loader').css('display','block');
		setTimeout(function(){
			$(".service_description").css({'display':'none'});
			$(".screenshot_flow_block").css({'display':'none'});
			$('.forcast_block').css({'display':'none'});
			$('.pricepoint_block').css({'display':'none'});
			$('.service_setting_block').css({'display':'flex'});
			$('.loader').css('display','none');
		},1500);
	});
*/
	//Service Setting
	/*$(document).on('click','.back_servicesetting_block',function() {
		$('.loader').css('display','block');
		setTimeout(function(){
			$(".service_description").css({'display':'none'});
			$(".screenshot_flow_block").css({'display':'none'});
			$('.forcast_block').css({'display':'none'});
			$('.pricepoint_block').css({'display':'flex'});
			$('.service_setting_block').css({'display':'none'});
			$('.loader').css('display','none');
		},1500);
	});*/
});

$('.report-download-xlssss').on('click', function(){
	var id = $(this).data('operator')
	var cycle =  $(this).data('cycle')
	var data = {}
	data.title = id
	data.cycle = cycle
	var csrf_token= $("#csrf_token").val()

	$(document).find('.'+id+' thead tr').each(function(){
		data.header = []
		$(this).find('th').each(function(){
			data.header.push($(this).text())
		});
	});
	$(document).find('.'+id+' tbody .rev').each(function(){
		data.t_rev = []
		$(this).find('td').each(function(){
			data.t_rev.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .subs').each(function(){
		data.t_subs = []
		$(this).find('td').each(function(){
			data.t_subs.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .reg').each(function(){
		data.reg = []
		$(this).find('td').each(function(){
			data.reg.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .unreg').each(function(){
		data.unreg = []
		$(this).find('td').each(function(){
			data.unreg.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .purged').each(function(){
		data.purged = []
		$(this).find('td').each(function(){
			data.purged.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .churn').each(function(){
		data.churn = []
		$(this).find('td').each(function(){
			data.churn.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .renewal').each(function(){
		data.renewal = []
		$(this).find('td').each(function(){
			data.renewal.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .bill').each(function(){
		data.bill = []
		$(this).find('td').each(function(){
			data.bill.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .arpu7').each(function(){
		data.arpu7 = []
		$(this).find('td').each(function(){
			data.arpu7.push($(this).text().trim())
		});
	});

	//console.log(data)

	$.ajax({
		url: base_url+'dashboard/reportexport?filename=Report_Summary_for_'+id+'.xlsx',
		method: 'POST',
		datatype: 'json',
		data: data,
		headers: {
			'X-CSRF-Token': csrf_token
		},
		success: function(response){
			window.location.href = base_url+'xlsx/'+response;
			console.log('download success')
		},
		error: function(){
			console.error('internal server error')
		}
	});
});


$('.pnl-download-xlsss').on('click', function(){
	var id = $(this).data('country')
	var cycle =  $(this).data('cycle')
	var data = {}
	data.title = id
	data.cycle = cycle
	var csrf_token= $("#csrf_token").val()

	$(document).find('.'+id+' thead tr').each(function(){
		data.header = []
		$(this).find('th').each(function(){
			data.header.push($(this).text())
		});
	});
	$(document).find('.'+id+' tbody .cost_campaign').each(function(){
		data.cost_campaign = []
		$(this).find('td').each(function(){
			data.cost_campaign.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .mo').each(function(){
		data.mo = []
		$(this).find('td').each(function(){
			data.mo.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .price_mo').each(function(){
		data.price_mo = []
		$(this).find('td').each(function(){
			data.price_mo.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .active_subscriber').each(function(){
		data.active_subscriber = []
		$(this).find('td').each(function(){
			data.active_subscriber.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .arpu7').each(function(){
		data.arpu7 = []
		$(this).find('td').each(function(){
			data.arpu7.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .end_user_revenue').each(function(){
		data.end_user_revenue = []
		$(this).find('td').each(function(){
			data.end_user_revenue.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .gross_revenue').each(function(){
		data.gross_revenue = []
		$(this).find('td').each(function(){
			data.gross_revenue.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .billing_rate').each(function(){
		data.billing_rate = []
		$(this).find('td').each(function(){
			data.billing_rate.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .o_cost').each(function(){
		data.other_cost = []
		$(this).find('td').each(function(){
			data.other_cost.push($(this).text().trim())
		});
	});
	$(document).find('.'+id+' tbody .pnl').each(function(){
		data.pnl = []
		$(this).find('td').each(function(){
			data.pnl.push($(this).text().trim())
		});
	});

	//console.log(data)

	$.ajax({
		url: base_url+'pnlcampaign/pnlexport?filename=Pnl_Summary_for_'+id+'.xlsx',
		method: 'POST',
		datatype: 'json',
		data: data,
		headers: {
			'X-CSRF-Token': csrf_token
		},
		success: function(response){
			window.location.href = base_url+'xlsx/'+response;
			console.log('download success')
		},
		error: function(){
			console.error('internal server error')
		}
	});
});

$('.detail-download-xlssss').on('click', function(){
	var id = $(this).data('operator')
	var cycle =  $(this).data('cycle')
	var data = {}
	data.title = id
	data.cycle = cycle
	var csrf_token= $("#csrf_token").val()

	$(document).find('.main_detail thead tr').each(function(){
		data.main_header = []
		$(this).find('th').each(function(){
			data.main_header.push($(this).text())
		});
	});
	$(document).find('.main_detail tbody .t_rev').each(function(){
		data.t_rev = []
		$(this).find('td').each(function(){
			data.t_rev.push($(this).text().trim())
		});
	});
	$(document).find('.main_detail tbody .t_subs').each(function(){
		data.t_subs = []
		$(this).find('td').each(function(){
			data.t_subs.push($(this).text().trim())
		});
	});
	$(document).find('.main_detail tbody .t_reg').each(function(){
		data.reg = []
		$(this).find('td').each(function(){
			data.reg.push($(this).text().trim())
		});
	});
	$(document).find('.main_detail tbody .t_unreg').each(function(){
		data.unreg = []
		$(this).find('td').each(function(){
			data.unreg.push($(this).text().trim())
		});
	});
	$(document).find('.main_detail tbody .billing_rate').each(function(){
		data.billing_rate = []
		$(this).find('td').each(function(){
			data.billing_rate.push($(this).text().trim())
		});
	});
	$(document).find('.main_detail tbody .avg').each(function(){
		data.avg = []
		$(this).find('td').each(function(){
			data.avg.push($(this).text().trim())
		});
	});

	$(document).find('.sub_detail thead tr').each(function(){
		data.sub_header = []
		$(this).find('th').each(function(){
			data.sub_header.push($(this).text())
		});
	});
	$(document).find('.sub_detail').each(function(){
		var i = 0
		data.details = []
		$(this).find('tbody').each(function(){
			data.details[i] = {}
			$(this).find('.service_info').each(function(){
				data.details[i].service_info = []
				$(this).find('td').each(function(){
					data.details[i].service_info.push($(this).text().trim())
				});
			});
			$(this).find('.t_subs').each(function(){
				data.details[i].subs = []
				$(this).find('td').each(function(){
					data.details[i].subs.push($(this).text().trim())
				});
			});
			$(this).find('.t_reg').each(function(){
				data.details[i].reg = []
				$(this).find('td').each(function(){
					data.details[i].reg.push($(this).text().trim())
				});
			});
			$(this).find('.t_unreg').each(function(){
				data.details[i].unreg = []
				$(this).find('td').each(function(){
					data.details[i].unreg.push($(this).text().trim())
				});
			});
			$(this).find('.billing_rate').each(function(){
				data.details[i].billing_rate = []
				$(this).find('td').each(function(){
					data.details[i].billing_rate.push($(this).text().trim())
				});
			});
			$(this).find('.avg').each(function(){
				data.details[i].avg = []
				$(this).find('td').each(function(){
					data.details[i].avg.push($(this).text().trim())
				});
			});
			$(this).find('.t_rev').each(function(){
				data.details[i].rev = []
				$(this).find('td').each(function(){
					data.details[i].rev.push($(this).text().trim())
				});
			});
			i++
		});
	});

	//console.log(data)

	$.ajax({
		url: base_url+'dashboard/detailexport?filename=Reporting_Details_for_'+id+'.xlsx',
		method: 'POST',
		datatype: 'json',
		data: data,
		headers: {
			'X-CSRF-Token': csrf_token
		},
		success: function(response){
			window.location.href = base_url+'xlsx/'+response;
			console.log('download success')
		},
		error: function(){
			console.error('internal server error')
		}
	});
});

$('.detail-download-xls').on('click', function(){
	$('#excelData').excelexportjs({
		containerid: 'excelData',
		datatype: 'table'
	});
});

$('.msummary-download-xls').on('click', function(){
	var operator = $(this).data('operator')
	$('#'+operator).excelexportjs({
		containerid: operator,
		datatype: 'table'
	});
});

$('.pnl-xls').on('click', function(){
	var param = $(this).data('param')
	$('#'+param).excelexportjs({
		containerid: param,
		datatype: 'table'
	});
});
$('.report-xls').on('click', function(){
	var param = $(this).data('param')
	$('#'+param).excelexportjs({
		containerid: param,
		datatype: 'table'
	});
});
$('.country-xls').on('click', function(){
	var param = $(this).data('param')
	$('#'+param+' h1').text('Country Report Summary For '+param.toUpperCase())
	$('#'+param).excelexportjs({
		containerid: param,
		datatype: 'table'
	});
	$('#'+param+' h1').text('')
});

$('#report-summary-btn').on('click', function(){
	var cycle = $('#logcycle').val()
	switch(cycle){
		case 'monthly':
			window.location.href = base_url+'report/monthlysummary'
			break
		case 'weekly':
			window.location.href = base_url+'dashboard/weeklysummary'
			break
		default:
			window.location.href = base_url+'report/summary'
			break
	}
});

$('#dashNavBtn').on('click', function(){
	var base = $('#dashMenu').val()
	switch(base){
		case 'operator':
			window.location.href = base_url+'dashboard/operator'
			break
		case 'company':
			window.location.href = base_url+'dashboard/company'
			break
		default:
			window.location.href = base_url+'dashboard/'
			break
	}
});

$('#submit').on('click', function(){
	var base = $('#filtertype').val()
	var company_id = $('#dashboard-company').val()
	var country_id = $('#dashboard-country').val()
	var operator_id = $('#dashboard-operator').val()
	var csrf_token = $('#csrf_token').val()
	var url = `?_csrf=${csrf_token}&company_id=${company_id}&country_id=${country_id}&operator_id=${operator_id}`
	switch(base){
		case 'operator':
			window.location.href = base_url+'operator/dashboard'+url
			localStorage.setItem('dashboard_url',base_url+'operator/dashboard'+url)
			Cookies.set('dashboard_url', base_url+'operator/dashboard'+url);
			break
		case 'company':
			window.location.href = base_url+'company/dashboard'+url
			localStorage.setItem('dashboard_url',base_url+'company/dashboard'+url)
			Cookies.set('dashboard_url', base_url+'operator/dashboard'+url);

			break
		default:

			window.location.href = base_url+'country/dashboard'+url
			localStorage.setItem('dashboard_url',base_url+'country/dashboard'+url)
			Cookies.set('dashboard_url', base_url+'operator/dashboard'+url);

			break
	}
	// dashboardRedirect()
	// e.preventDefault()
});

function dashboardRedirect(){
	if(localStorage.getItem('dashboard_url') ){
		window.location.href = localStorage.getItem('operator_dashboard_url')
	}


}

// if(!document.getElementById('submit').clicked){
// 	dashboardRedirect()
// }

$('.dashboardSearchBtn1').on('click', function () {
	var company=$("#summarycompany").val()
	var country = $("#summarycountry").val()
	var operator_id = $('#summery_operator_id').val()
	var csrf_token= $("#csrf_token").val()

	$.ajax({
		url: base_url+'dashboard/getalldatabyserviceid1',
		type: 'POST',
		data: {'company':company, 'country':country, 'operator_id':operator_id},
		headers: {
			'X-CSRF-Token': csrf_token
		},
		success: function(response){
			$('#dashboard-data').empty();
			$('#dashboard-data').html('<div id="dashboardCountrydata" class="dash"></div>');
			if(!response.company_filter){
				var data = response.country_service_revenue;
				var currency = response.currency;

				$.each(data,function(key,value){
								//current
					html += '<div class="d-flex align-items-center my-2"><span class="badge badge-secondary px-2 bg-primary">'+key+'</span><span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span></div><div class="row"><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient">Current Monthly</div><div class="card-body"><div class="card-text"><p class="mb-0"><strong>Revenue :</strong></p>';
					if(country == 'Cambodia'){
						html += '<p class="mb-0">'+value.current_monthly_revenue+'(USD)'+'</p><p class="mb-0">'+value.current_monthly_revenue_usd+'('+currency+')'+'</p>';
					} else {
						html += '<p class="mb-0">'+value.current_monthly_revenue_usd+'(USD)'+'</p><p class="mb-0">'+value.current_monthly_revenue+'('+currency+')'+'</p>';
					}
					if(country == 'Cambodia'){
						if(value.current_average_daily_usd < value.previous_average_daily_usd){
							var revenueIndicator = '<span class="text-danger"><i class="fa fa-arrow-down"></i></span>';
						} else {
							var revenueIndicator = '<span class="text-success"><i class="fa fa-arrow-up"></i></span>';
						}
						html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.current_average_daily_usd+ revenueIndicator+'(USD)'+'</p>';
					} else {
						if(value.current_average_daily < value.previous_average_daily){
							var revenueIndicator = '<span class="text-danger"><i class="fa fa-arrow-down"></i></span>';
						} else {
							var revenueIndicator = '<span class="text-success"><i class="fa fa-arrow-up"></i></span>';
						}
						html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.current_average_daily+revenueIndicator+'('+currency+')'+'</p>';
					}
					html += '<p class="mb-0 "><strong>Cost Campaign :</strong>&nbsp;'+value.current_cost+'</p>';

					html += '<p class="mb-0 "><strong>MO :</strong>&nbsp;'+value.current_monthly_mo+'</p>';

					if(value.current_average_mo < value.previous_average_mo){
						var moIndicator = '<span class="text-danger"><i class="fa fa-arrow-down"></i></span>';
					} else {
						var moIndicator = '<span class="text-success"><i class="fa fa-arrow-up"></i></span>';
					}

					html += '<p class="mb-0 "><strong>AVG MO :</strong>&nbsp;'+value.current_average_mo+moIndicator+'</p>';
					html += '<p class="mb-0 "><strong>PNL :</strong>&nbsp;'+value.current_monthly_pnl+'</p>';
					html += '<p class="mb-0 "><strong>AVG PNL :</strong>&nbsp;'+value.average_monthly_pnl+'</p></div></div></div></div><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient-green">Estimate EOM</div><div class="card-body"><div class="card-text"><p class="mb-0"><strong>Revenue:</strong></p><p class="mb-0">';
					//estiamted
					if(country == 'Cambodia'){
						html += '<p class="mb-0">'+value.estimated_revenue+'(USD)'+'</p><p class="mb-0">'+value.estimated_revenue_usd+'('+currency+')'+'</p>';
					} else {
						html += '<p class="mb-0">'+value.estimated_revenue_usd+'(USD)'+'</p><p class="mb-0">'+value.estimated_revenue+'('+currency+')'+'</p>';
					}
						if(country == 'Cambodia'){
						html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.estimated_avg_revenue_usd+'(USD)'+'</p>';
					} else {
						html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.estimated_avg_revenue+'('+currency+')'+'</p>';
					}html += '<p class="mb-0"><strong>Cost Campaign :</strong>&nbsp;'+value.estimated_cost+'</p>';

							html += '<p class="mb-0 "><strong>MO :</strong>&nbsp;'+value.estimated_mo+'</p>';
					html += '<p class="mb-0 "><strong>AVG MO :</strong>&nbsp;'+value.estimated_average_mo+'</p>';
						html += '<p class="mb-0 "><strong>PNL :</strong>&nbsp;'+value.estimated_pnl+'</p>';
					html += '<p class="mb-0 "><strong>AVG PNL :</strong>&nbsp;'+value.estimated_avg_pnl+'</p></div></div></div></div><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient-red">Last Month</div><div class="card-body"><div class="card-text"><p class="mb-0"><strong>Revenue :</strong></p><p class="mb-0">';
					//last month
					if(country == 'Cambodia'){
						html += '<p class="mb-0">'+value.previous_month_revenue+'(USD)'+'</p><p class="mb-0">'+value.previous_month_revenue_usd+'('+currency+')'+'</p>';
					} else {
						html += '<p class="mb-0">'+value.previous_month_revenue_usd+'(USD)'+'</p><p class="mb-0">'+value.previous_month_revenue+'('+currency+')'+'</p>';
					}
						if(country == 'Cambodia'){
						html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.previous_average_daily_usd+'(USD)'+'</p>';
					} else {
						html += '<p class="mb-0"><strong>AVG Revenue:</strong>&nbsp;'+value.previous_average_daily+'('+currency+')'+'</p>';
					}
					html += '<p class="mb-0"><strong>Cost Campaign :</strong>&nbsp;'+value.previous_cost+'</p>';

					html += '<p class="mb-0 "><strong>MO :</strong>&nbsp;'+value.previous_mo+'</p>';
					html += '<p class="mb-0 "><strong>AVG MO :</strong>&nbsp;'+value.previous_average_mo+'</p>';
					html += '<p class="mb-0 "><strong>PNL :</strong>&nbsp;'+value.previous_pnl+'</p>';
					html += '<p class="mb-0 "><strong>AVG PNL :</strong>&nbsp;'+value.previous_average_pnl+'</p></div></div></div></div><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient-purple">Previous Month </div><div class="card-body"><div class="card-text"><p class="mb-0"><strong>Revenue :</strong></p><p class="mb-0">';
					//previous month
					if(country == 'Cambodia'){
						html += '<p class="mb-0">'+value.previous2_month_revenue+'(USD)'+'</p><p class="mb-0">'+value.previous2_month_revenue_usd+'('+currency+')'+'</p>';
					} else {
						html += '<p class="mb-0">'+value.previous2_month_revenue_usd+'(USD)'+'</p><p class="mb-0">'+value.previous2_month_revenue+'('+currency+')'+'</p>';
					}

					if(country == 'Cambodia'){
						html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.previous2_average_daily_usd+'(USD)'+'</p>';
					} else {
						html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.previous2_average_daily+'('+currency+')'+'</p>';
					}
					html += '<p class="mb-0 "><strong>Cost Campaign :</strong>&nbsp;'+value.previous2_cost+'</p>';

						html += '<p class="mb-0"><strong>MO :</strong>&nbsp;'+value.previous2_mo+'</p>';
					html += '<p class="mb-0"><strong>AVG MO :</strong>&nbsp;'+value.previous2_average_mo+'</p>';
					html += '<p class="mb-0 "><strong>PNL :</strong>&nbsp;'+value.previous2_pnl+'</p>';
					html += '<p class="mb-0 "><strong>AVG PNL :</strong>&nbsp;'+value.previous2_average_pnl+'</p></div></div></div></div>';

				});

				$("#dashboardCountrydata").html(html);
			}else{
				$("#dashboardCountrydata").html('Test succes')
			}
		},
		error: function(){
			console.error('internal server error')
		}
	})
});


$('#total').on('click',function(){
	var csrf_token= $("#csrf_token").val()
	var country = $(this).data('country')
	var operator =  $(this).data('operator')
	var data = {}
	data.country = country
	data.operator = operator

	var row = Number($('#row').val());
      var count = Number($('#postCount').val());
      var limit = 3;
      row = row + limit;
      $('#row').val(row);
      $("#loadBtn").val('Loading...');

	$.ajax({
		url: base_url+'dash/getoperator1',
		type: 'POST',
		dataType:'json',
		data: data,
		headers: {
			'X-CSRF-Token': csrf_token
		},

		success: function(response){
			$('#dash').empty();
			$('#dash').html('<div id="dash1" class="a"></div>');
				var data = response.country_service_revenue;
				var currency=data.currency;
                var html = ' '
				$.each(data,function(key,value){
								//current
					html += '<div class="d-flex align-items-center my-2"><div class="op"><span class="badge badge-secondary px-2 bg-primary text-uppercase">'+key+'</span><span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span></div><div class="row"><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient">Current Month</div><div class="card-body"><div class="card-text"><p class="mb-0"><strong>Revenue :</strong></p>';
					if(country == 'Cambodia'){
						html += '<p class="mb-0">'+value.current_monthly_revenue+'(USD)'+'</p><p class="mb-0">'+value.current_monthly_revenue_usd+'('+value.currency+')'+'</p>';
					} else {
						html += '<p class="mb-0">'+value.current_monthly_revenue_usd+'(USD)'+'</p><p class="mb-0">'+value.current_monthly_revenue+'('+value.currency+')'+'</p>';
					}
					if(value.country == 'Cambodia'){
						if(value.current_average_daily_usd < value.previous_average_daily_usd){
							var revenueIndicator = '<span class="text-danger"><i class="fa fa-arrow-down"></i></span>';
						} else {
							var revenueIndicator = '<span class="text-success"><i class="fa fa-arrow-up"></i></span>';
						}
						html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.current_average_daily_usd+ revenueIndicator+'(USD)'+'</p>';
					} else {
						if(value.current_average_daily < value.previous_average_daily){
							var revenueIndicator = '<span class="text-danger"><i class="fa fa-arrow-down"></i></span>';
						} else {
							var revenueIndicator = '<span class="text-success"><i class="fa fa-arrow-up"></i></span>';
						}
						html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.current_average_daily+revenueIndicator+'('+value.currency+')'+'</p>';
					}
					html += '<p class="mb-0 "><strong>Cost Campaign :</strong>&nbsp;'+value.current_cost+'</p>';

					html += '<p class="mb-0 "><strong>MO :</strong>&nbsp;'+value.current_monthly_mo+'</p>';

					if(value.current_average_mo < value.previous_average_mo){
						var moIndicator = '<span class="text-danger"><i class="fa fa-arrow-down"></i></span>';
					} else {
						var moIndicator = '<span class="text-success"><i class="fa fa-arrow-up"></i></span>';
					}

					html += '<p class="mb-0 "><strong>AVG MO :</strong>&nbsp;'+value.current_average_mo+moIndicator+'</p>';
					html += '<p class="mb-0 "><strong>PNL :</strong>&nbsp;'+value.current_monthly_pnl+'</p>';
					html += '<p class="mb-0 "><strong>AVG PNL :</strong>&nbsp;'+value.average_month+'</p></div></div></div></div><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient-green">Estimated EOM</div><div class="card-body"><div class="card-text"> <p class="mb-0"><strong>Revenue:</strong></p>';
					//estiamted
					if(value.country == 'Cambodia'){
						html += '<p class="mb-0">'+value.estimated_revenue+'(USD)'+'</p><p class="mb-0">'+value.estimated_revenue_usd+'('+value.currency+')'+'</p>';
					} else {
						html += '<p class="mb-0">'+value.estimated_revenue_usd+'(USD)'+'</p><p class="mb-0">'+value.estimated_revenue+'('+value.currency+')'+'</p>';
					}
						if(value.country == 'Cambodia'){
						html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.estimated_avg_revenue_usd+'(USD)'+'</p>';
					} else {
						html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.estimated_avg_revenue+'('+value.currency+')'+'</p>';
					}html += '<p class="mb-0"><strong>Cost Campaign :</strong>&nbsp;'+value.estimated_cost+'</p>';

							html += '<p class="mb-0 "><strong>MO :</strong>&nbsp;'+value.estimated_mo+'</p>';
					html += '<p class="mb-0 "><strong>AVG MO :</strong>&nbsp;'+value.estimated_avg_mo+'</p>';
						html += '<p class="mb-0 "><strong>PNL :</strong>&nbsp;'+value.estimated_pnl+'</p>';
					html += '<p class="mb-0 "><strong>AVG PNL :</strong>&nbsp;'+value.estimated_avg_pnl+'</p></div></div></div></div><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient-red">Last Month</div><div class="card-body"><div class="card-text"> <p class="mb-0"><strong>Revenue:</strong></p>';
					//last month
					if(value.country == 'Cambodia'){
						html += '<p class="mb-0">'+value.previous_revenue+'(USD)'+'</p><p class="mb-0">'+value.previous_revenue_usd+'('+value.currency+')'+'</p>';
					} else {
						html += '<p class="mb-0">'+value.previous_revenue_usd+'(USD)'+'</p><p class="mb-0">'+value.previous_revenue+'('+value.currency+')'+'</p>';
					}
						if(value.country == 'Cambodia'){
						html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.previous_average_daily_usd+'(USD)'+'</p>';
					} else {
						html += '<p class="mb-0"><strong>AVG Revenue:</strong>&nbsp;'+value.previous_average_daily+'('+value.currency+')'+'</p>';
					}
					html += '<p class="mb-0"><strong>Cost Campaign :</strong>&nbsp;'+value.previous_cost+'</p>';

					html += '<p class="mb-0 "><strong>MO :</strong>&nbsp;'+value.previous_mo+'</p>';
					html += '<p class="mb-0 "><strong>AVG MO :</strong>&nbsp;'+value.previous_average_mo+'</p>';
					html += '<p class="mb-0 "><strong>PNL :</strong>&nbsp;'+value.previous_pnl+'</p>';
					html += '<p class="mb-0 "><strong>AVG PNL :</strong>&nbsp;'+value.previous_average_pnl+'</p></div></div></div></div><div class="col-lg-4"><div class="card shadow-sm"><div class="p-2 text-center gradient-red">Previous Month</div><div class="card-body"><div class="card-text"> <p class="mb-0"><strong>Revenue:</strong></p>';
					//previous month
					if(value.country == 'Cambodia'){
						html += '<p class="mb-0">'+value.previous2_revenue+'(USD)'+'</p><p class="mb-0">'+value.previous2_revenue_usd+'('+value.currency+')'+'</p>';
					} else {
						html += '<p class="mb-0">'+value.previous2_revenue_usd+'(USD)'+'</p><p class="mb-0">'+value.previous2_revenue+'('+value.currency+')'+'</p>';
					}

					if(value.country == 'Cambodia'){
						html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.previous2_average_daily_usd+'(USD)'+'</p>';
					} else {
						html += '<p class="mb-0"><strong>AVG Revenue :</strong>&nbsp;'+value.previous2_average_daily+'('+value.currency+')'+'</p>';
					}
					html += '<p class="mb-0 "><strong>Cost Campaign :</strong>&nbsp;'+value.previous2_cost+'</p>';

					html += '<p class="mb-0"><strong>MO :</strong>&nbsp;'+value.previous2_mo+'</p>';
					html += '<p class="mb-0"><strong>AVG MO :</strong>&nbsp;'+value.previous2_average_mo+'</p>';
					html += '<p class="mb-0 "><strong>PNL :</strong>&nbsp;'+value.previous2_pnl+'</p>';
					html += '<p class="mb-0 "><strong>AVG PNL :</strong>&nbsp;'+value.previous2_average_pnl+'</p></div></div></div></div></div></div> ';

				});

				$("#dash1").html(html);
			},
		error: function(){
			console.error('internal server error')
		}
	})
});

// //loadmore operator
// $(document).ready(function () {
//     size_li1 = $(".main1").size();
//     size_li2 = $(".main2").size();
//     size_li3 = $(".main3").size();
//     size_li4 = $(".main4").size();
//     x=10;
//     y=10;
//     z=10;
//     m=10;
//     $('.main1:lt('+x+')').show();
//     $('.main2:lt('+y+')').show();
//     $('.main3:lt('+z+')').show();
//     $('.main4:lt('+m+')').show();
//     $('#loadMore').click(function () {

//         x= (x <= size_li1) ? x+10 : size_li1;
//         $('.main1:lt('+x+')').show();
//         y= (y<= size_li2) ? y+10 : size_li2;
//         $('.main2:lt('+y+')').show();
//         z= (z <= size_li3) ? z+10 : size_li3;
//         $('.main3:lt('+z+')').show();
//         m= (m<= size_li4) ? m+10 : size_li4;
//         $('.main4:lt('+m+')').show();
//         $(this).toggle(x < size_li1);
//         $(this).toggle(y < size_li2);
//         $(this).toggle(z < size_li3);
//         $(this).toggle(m < size_li)4;
//     });

// });

$('.load-country-data').on('click', function(){
	var csrf_token = $("#csrf_token").val()
	var country = $(this).data("country")
	var urlSearchParams = new URLSearchParams(window.location.search)
	var params = Object.fromEntries(urlSearchParams.entries())
	var filter_company = params.company
	var filter_country = params.country
	var filter_operator = params.operator
	$.ajax({
		url: base_url+'dash/loadcountrydata',
		type: 'POST',
		datatype: 'json',
		data: {
			country: country,
			filter_company: filter_company,
			filter_country: filter_country,
			filter_operator: filter_operator
		},
		headers: {
            'X-CSRF-Token': csrf_token
      	},
		beforeSend: function(){
			$('.'+country).css('display', 'block');
		},
		success: function(response){
			$('.'+country).css('display', 'none');
			if(response){
				if(country == 'all'){
					$('.'+country+' .current-rev').html(`${Number(response.total_current_revenue.toFixed(2)).toLocaleString()}(USD)`)
					if(response.av_image == 'up'){
						$('.'+country+' .current-avg-rev').html(`${Number(response.total_current_average_daily.toFixed(2)).toLocaleString()}(USD) <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .current-avg-rev').addClass('text-success')
					}else if(response.av_image == 'down'){
						$('.'+country+' .current-avg-rev').html(`${Number(response.total_current_average_daily.toFixed(2)).toLocaleString()}(USD) <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .current-avg-rev').addClass('text-danger')
					}else{
						$('.'+country+' .current-avg-rev').html(`${Number(response.total_current_average_daily.toFixed(2)).toLocaleString()}(USD)`)
						$('.'+country+' .current-avg-rev').addClass('')
					}
					$('.'+country+' .current-mo').html(`${Number(response.total_current_mo.toFixed(0))}`)
					if(response.mo_image == 'up'){
						$('.'+country+' .current-avg-mo').html(`${Number(response.total_current_average_mo.toFixed(0)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .current-avg-mo').addClass('text-success')
					}else if(response.mo_image == 'down'){
						$('.'+country+' .current-avg-mo').html(`${Number(response.total_current_average_mo.toFixed(0)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .current-avg-mo').addClass('text-danger')
					}else{
						$('.'+country+' .current-avg-mo').html(`${Number(response.total_current_average_mo.toFixed(0)).toLocaleString()}`)
						$('.'+country+' .current-avg-mo').addClass('')
					}
					$('.'+country+' .current-campaign').html(`${Number(response.total_current_monthly_cost.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .current-pnl').html(`${Number(response.total_current_monthly_pnl.toFixed(2)).toLocaleString()}`)
					if(response.pnl_image == 'up'){
						$('.'+country+' .current-avg-pnl').html(`${Number(response.total_current_average_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .current-avg-pnl').addClass('text-success')
					}else if(response.pnl_image == 'down'){
						$('.'+country+' .current-avg-pnl').html(`${Number(response.total_current_average_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .current-avg-pnl').addClass('text-danger')
					}else{
						$('.'+country+' .current-avg-pnl').html(`${Number(response.total_current_average_pnl.toFixed(2)).toLocaleString()}`)
						$('.'+country+' .current-avg-pnl').addClass('')
					}

					$('.'+country+' .estimated-rev').html(`${Number(response.total_estimated_revenue.toFixed(2)).toLocaleString()}(USD)`)
					if(response.eav_image == 'up'){
						$('.'+country+' .estimated-avg-rev').html(`${Number(response.total_estimated_avg_revenue.toFixed(2)).toLocaleString()}(USD) <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .estimated-avg-rev').addClass('text-success')
					}else if(response.eav_image == 'down'){
						$('.'+country+' .estimated-avg-rev').html(`${Number(response.total_estimated_avg_revenue.toFixed(2)).toLocaleString()}(USD) <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .estimated-avg-rev').addClass('text-danger')
					}else{
						$('.'+country+' .estimated-avg-rev').html(`${Number(response.total_estimated_avg_revenue.toFixed(2)).toLocaleString()}(USD)`)
						$('.'+country+' .estimated-avg-rev').addClass('')
					}
					$('.'+country+' .estimated-mo').html(`${Number(response.total_estimated_mo.toFixed(0))}`)
					if(response.emo_image == 'up'){
						$('.'+country+' .estimated-avg-mo').html(`${Number(response.total_estimated_avg_mo.toFixed(0)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .estimated-avg-mo').addClass('text-success')
					}else if(response.emo_image == 'down'){
						$('.'+country+' .estimated-avg-mo').html(`${Number(response.total_estimated_avg_mo.toFixed(0)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .estimated-avg-mo').addClass('text-danger')
					}else{
						$('.'+country+' .estimated-avg-mo').html(`${Number(response.total_estimated_avg_mo.toFixed(0)).toLocaleString()}`)
						$('.'+country+' .estimated-avg-mo').addClass('')
					}
					$('.'+country+' .estimated-campaign').html(`${Number(response.total_estimated_cost.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .estimated-pnl').html(`${Number(response.total_estimated_pnl.toFixed(2))}`)
					if(response.epnl_image == 'up'){
						$('.'+country+' .estimated-avg-pnl').html(`${Number(response.total_estimated_avg_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .estimated-avg-pnl').addClass('text-success')
					}else if(response.epnl_image == 'down'){
						$('.'+country+' .estimated-avg-pnl').html(`${Number(response.total_estimated_avg_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .estimated-avg-pnl').addClass('text-danger')
					}else{
						$('.'+country+' .estimated-avg-pnl').html(`${Number(response.total_estimated_avg_pnl.toFixed(2)).toLocaleString()}`)
						$('.'+country+' .estimated-avg-pnl').addClass('')
					}

					$('.'+country+' .last-rev').html(`${Number(response.total_previous_revenue.toFixed(2)).toLocaleString()}(USD)`)
					if(response.pav_image == 'up'){
						$('.'+country+' .last-avg-rev').html(`${Number(response.total_previous_average_daily.toFixed(2)).toLocaleString()}(USD) <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .last-avg-rev').addClass('text-success')
					}else if(response.pav_image == 'down'){
						$('.'+country+' .last-avg-rev').html(`${Number(response.total_previous_average_daily.toFixed(2)).toLocaleString()}(USD) <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .last-avg-rev').addClass('text-danger')
					}else{
						$('.'+country+' .last-avg-rev').html(`${Number(response.total_previous_average_daily.toFixed(2)).toLocaleString()}(USD)`)
						$('.'+country+' .last-avg-rev').addClass('')
					}
					$('.'+country+' .last-mo').html(`${Number(response.total_previous_mo.toFixed(0)).toLocaleString()}`)
					if(response.pmo_image == 'up'){
						$('.'+country+' .last-avg-mo').html(`${Number(response.total_previous_average_mo.toFixed(0)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .last-avg-mo').addClass('text-success')
					}else if(response.pmo_image == 'down'){
						$('.'+country+' .last-avg-mo').html(`${Number(response.total_previous_average_mo.toFixed(0)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .last-avg-mo').addClass('text-danger')
					}else{
						$('.'+country+' .last-avg-mo').html(`${Number(response.total_previous_average_mo.toFixed(0)).toLocaleString()}`)
						$('.'+country+' .last-avg-mo').addClass('')
					}
					$('.'+country+' .last-campaign').html(`${Number(response.total_previous_cost.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .last-pnl').html(`${response.total_previous_pnl}`)
					if(response.ppnl_image == 'up'){
						$('.'+country+' .last-avg-pnl').html(`${Number(response.total_previous_average_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .last-avg-pnl').addClass('text-success')
					}else if(response.ppnl_image == 'down'){
						$('.'+country+' .last-avg-pnl').html(`${Number(response.total_previous_average_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .last-avg-pnl').addClass('text-danger')
					}else{
						$('.'+country+' .last-avg-pnl').html(`${Number(response.total_previous_average_pnl.toFixed(2)).toLocaleString()}`)
						$('.'+country+' .last-avg-pnl').addClass('')
					}

					$('.'+country+' .prev-rev').html(`${Number(response.total_previous2_revenue.toFixed(2)).toLocaleString()}(USD)`)
					$('.'+country+' .prev-avg-rev').html(`${Number(response.total_previous2_average_daily.toFixed(2)).toLocaleString()}(USD)`)
					$('.'+country+' .prev-mo').html(`${Number(response.total_previous2_mo.toFixed(0)).toLocaleString()}`)
					$('.'+country+' .prev-avg-mo').html(`${Number(response.total_previous2_average_mo.toFixed(0)).toLocaleString()}`)
					$('.'+country+' .prev-campaign').html(`${Number(response.total_previous2_cost.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .prev-pnl').html(`${Number(response.total_previous2_pnl.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .prev-avg-pnl').html(`${Number(response.total_previous2_average_pnl.toFixed(2)).toLocaleString()}`)
				}else{
					$('.'+country+' .current-rev-usd').html(`${Number(response.data.current_monthly_revenue_usd.toFixed(2)).toLocaleString()}(USD)`)
					$('.'+country+' .current-rev').html(`${Number(response.data.current_monthly_revenue).toFixed(2).toLocaleString()}(${response.currency})`)
					if(response.data.av_image == 'up'){
						$('.'+country+' .current-avg-rev').html(`${Number(response.data.current_average_daily.toFixed(2)).toLocaleString()}(${response.currency}) <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .current-avg-rev').addClass('text-success')
					}else if(response.data.av_image == 'down'){
						$('.'+country+' .current-avg-rev').html(`${Number(response.data.current_average_daily.toFixed(2)).toLocaleString()}(${response.currency}) <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .current-avg-rev').addClass('text-danger')
					}else{
						$('.'+country+' .current-avg-rev').html(`${Number(response.data.current_average_daily.toFixed(2)).toLocaleString()}(${response.currency})`)
						$('.'+country+' .current-avg-rev').addClass('')
					}
					$('.'+country+' .current-mo').html(`${Number(response.data.current_monthly_mo.toFixed(0)).toLocaleString()}`)
					if(response.data.mo_image == 'up'){
						$('.'+country+' .current-avg-mo').html(`${Number(response.data.current_average_mo.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .current-avg-mo').addClass('text-success')
					}else if(response.data.mo_image == 'down'){
						$('.'+country+' .current-avg-mo').html(`${Number(response.data.current_average_mo.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .current-avg-mo').addClass('text-danger')
					}else{
						$('.'+country+' .current-avg-mo').html(`${Number(response.data.current_average_mo.toFixed(0)).toLocaleString()}`)
						$('.'+country+' .current-avg-mo').addClass('')
					}
					$('.'+country+' .current-campaign').html(`${Number(response.data.current_cost.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .current-pnl').html(`${Number(response.data.current_monthly_pnl.toFixed(2)).toLocaleString()}`)
					if(response.data.pnl_image == 'up'){
						$('.'+country+' .current-avg-pnl').html(`${Number(response.data.average_monthly_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .current-avg-pnl').addClass('text-success')
					}else if(response.data.pnl_image == 'down'){
						$('.'+country+' .current-avg-pnl').html(`${Number(response.data.average_monthly_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .current-avg-pnl').addClass('text-danger')
					}else{
						$('.'+country+' .current-avg-pnl').html(`${Number(response.data.average_monthly_pnl.toFixed(2)).toLocaleString()}`)
						$('.'+country+' .current-avg-pnl').addClass('')
					}

					$('.'+country+' .estimated-rev-usd').html(`${Number(response.data.estimated_revenue_usd.toFixed(2)).toLocaleString()}(USD)`)
					$('.'+country+' .estimated-rev').html(`${Number(response.data.estimated_revenue.toFixed(2)).toLocaleString()}(${response.currency})`)
					if(response.data.eav_image == 'up'){
						$('.'+country+' .estimated-avg-rev').html(`${Number(response.data.estimated_avg_revenue.toFixed(2)).toLocaleString()}(${response.currency}) <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .estimated-avg-rev').addClass('text-success')
					}else if(response.data.eav_image == 'down'){
						$('.'+country+' .estimated-avg-rev').html(`${Number(response.data.estimated_avg_revenue.toFixed(2)).toLocaleString()}(${response.currency}) <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .estimated-avg-rev').addClass('text-danger')
					}else{
						$('.'+country+' .estimated-avg-rev').html(`${Number(response.data.estimated_avg_revenue.toFixed(2)).toLocaleString()}(${response.currency})`)
						$('.'+country+' .estimated-avg-rev').addClass('')
					}
					$('.'+country+' .estimated-mo').html(`${Number(response.data.estimated_mo.toFixed(0))}`)
					if(response.data.emo_image == 'up'){
						$('.'+country+' .estimated-avg-mo').html(`${Number(response.data.estimated_avg_mo.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .estimated-avg-mo').addClass('text-success')
					}else if(response.data.emo_image == 'down'){
						$('.'+country+' .estimated-avg-mo').html(`${Number(response.data.estimated_avg_mo.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .estimated-avg-mo').addClass('text-danger')
					}else{
						$('.'+country+' .estimated-avg-mo').html(`${Number(response.data.estimated_avg_mo.toFixed(0)).toLocaleString()}`)
						$('.'+country+' .estimated-avg-mo').addClass('')
					}
					$('.'+country+' .estimated-campaign').html(`${Number(response.data.estimated_cost.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .estimated-pnl').html(`${Number(response.data.estimated_pnl.toFixed(2)).toLocaleString()}`)
					if(response.data.epnl_image == 'up'){
						$('.'+country+' .estimated-avg-pnl').html(`${Number(response.data.estimated_avg_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .estimated-avg-pnl').addClass('text-success')
					}else if(response.data.epnl_image == 'down'){
						$('.'+country+' .estimated-avg-pnl').html(`${Number(response.data.estimated_avg_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .estimated-avg-pnl').addClass('text-danger')
					}else{
						$('.'+country+' .estimated-avg-pnl').html(`${Number(response.data.estimated_avg_pnl.toFixed(2)).toLocaleString()}`)
						$('.'+country+' .estimated-avg-pnl').addClass('')
					}

					$('.'+country+' .last-rev-usd').html(`${Number(response.data.previous_revenue_usd.toFixed(2)).toLocaleString()}(USD)`)
					$('.'+country+' .last-rev').html(`${Number(response.data.previous_revenue).toFixed(2).toLocaleString()}(${response.currency})`)
					if(response.data.pav_image == 'up'){
						$('.'+country+' .last-avg-rev').html(`${Number(response.data.previous_average_daily.toFixed(2)).toLocaleString()}(${response.currency}) <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .last-avg-rev').addClass('text-success')
					}else if(response.data.pav_image == 'down'){
						$('.'+country+' .last-avg-rev').html(`${Number(response.data.previous_average_daily.toFixed(2)).toLocaleString()}(${response.currency}) <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .last-avg-rev').addClass('text-danger')
					}else{
						$('.'+country+' .last-avg-rev').html(`${Number(response.data.previous_average_daily.toFixed(2)).toLocaleString()}(${response.currency})`)
						$('.'+country+' .last-avg-rev').addClass('')
					}
					$('.'+country+' .last-mo').html(`${Number(response.data.previous_mo.toFixed(0)).toLocaleString()}`)
					if(response.data.emo_image == 'up'){
						$('.'+country+' .last-avg-mo').html(`${Number(response.data.previous_average_mo.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .last-avg-mo').addClass('text-success')
					}else if(response.data.emo_image == 'down'){
						$('.'+country+' .last-avg-mo').html(`${Number(response.data.previous_average_mo.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .last-avg-mo').addClass('text-danger')
					}else{
						$('.'+country+' .last-avg-mo').html(`${Number(response.data.previous_average_mo.toFixed(0)).toLocaleString()}`)
						$('.'+country+' .last-avg-mo').addClass('')
					}
					$('.'+country+' .last-campaign').html(`${Number(response.data.previous_cost.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .last-pnl').html(`${Number(response.data.previous_pnl.toFixed(2)).toLocaleString()}`)
					if(response.data.epnl_image == 'up'){
						$('.'+country+' .last-avg-pnl').html(`${Number(response.data.previous_average_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
						$('.'+country+' .last-avg-pnl').addClass('text-success')
					}else if(response.data.epnl_image == 'down'){
						$('.'+country+' .last-avg-pnl').html(`${Number(response.data.previous_average_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
						$('.'+country+' .last-avg-pnl').addClass('text-danger')
					}else{
						$('.'+country+' .last-avg-pnl').html(`${Number(response.data.previous_average_pnl.toFixed(2)).toLocaleString()}`)
						$('.'+country+' .last-avg-pnl').addClass('')
					}

					$('.'+country+' .prev-rev-usd').html(`${Number(response.data.previous2_revenue_usd.toFixed(2)).toLocaleString()}(USD)`)
					$('.'+country+' .prev-rev').html(`${Number(response.data.previous2_revenue).toFixed(2).toLocaleString()}(${response.currency})`)
					$('.'+country+' .prev-avg-rev').html(`${Number(response.data.previous2_average_daily.toFixed(2)).toLocaleString()}(${response.currency})`)
					$('.'+country+' .prev-mo').html(`${Number(response.data.previous2_mo.toFixed(0)).toLocaleString()}`)
					$('.'+country+' .prev-avg-mo').html(`${Number(response.data.previous2_average_mo.toFixed(0)).toLocaleString()}`)
					$('.'+country+' .prev-campaign').html(`${Number(response.data.previous2_cost.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .prev-pnl').html(`${Number(response.data.previous2_pnl.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .prev-avg-pnl').html(`${Number(response.data.previous2_average_pnl.toFixed(2)).toLocaleString()}`)
				}
			}
		},
		error: function(){
			console.error('internal server error')
		}
	})
})

/*$('.dashboard-dataaa').ready(function(){
	var urlSearchParams = new URLSearchParams(window.location.search)
	var params = Object.fromEntries(urlSearchParams.entries())
	var filter_company = params.company ? params.company : ''
	var filter_country = params.country ? params.country : ''
	var filter_operator = params.operator ? params.operator : ''

	$(this).find('small').each(function(){
		$('.'+country+'-loader').css('display', 'block');
		var country = $(this).text()
		fetch(`${base_url}country-dashboard/${country}`, {
			headers: {
				'Accept': 'application/json',
				'Content-Type': 'application/json'
			},
			method: 'POST',
			body: JSON.stringify({company: filter_company, country: filter_country, operator: filter_operator})
		})
		.then(res => res.json())
		.then((data) => {
			var response = data
			if(country == 'all'){
				$('.'+country+' .current-rev').html(`${Number(response.total_current_revenue.toFixed(2)).toLocaleString()}(USD)`)
				if(response.av_image == 'up'){
					$('.'+country+' .current-avg-rev').html(`${Number(response.total_current_average_daily.toFixed(2)).toLocaleString()}(USD) <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .current-avg-rev').addClass('text-success')
				}else if(response.av_image == 'down'){
					$('.'+country+' .current-avg-rev').html(`${Number(response.total_current_average_daily.toFixed(2)).toLocaleString()}(USD) <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .current-avg-rev').addClass('text-danger')
				}else{
					$('.'+country+' .current-avg-rev').html(`${Number(response.total_current_average_daily.toFixed(2)).toLocaleString()}(USD)`)
					$('.'+country+' .current-avg-rev').addClass('')
				}
				$('.'+country+' .current-mo').html(`${Number(response.total_current_mo.toFixed(0))}`)
				if(response.mo_image == 'up'){
					$('.'+country+' .current-avg-mo').html(`${Number(response.total_current_average_mo.toFixed(0)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .current-avg-mo').addClass('text-success')
				}else if(response.mo_image == 'down'){
					$('.'+country+' .current-avg-mo').html(`${Number(response.total_current_average_mo.toFixed(0)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .current-avg-mo').addClass('text-danger')
				}else{
					$('.'+country+' .current-avg-mo').html(`${Number(response.total_current_average_mo.toFixed(0)).toLocaleString()}`)
					$('.'+country+' .current-avg-mo').addClass('')
				}
				$('.'+country+' .current-campaign').html(`${Number(response.total_current_monthly_cost.toFixed(2)).toLocaleString()}`)
				$('.'+country+' .current-pnl').html(`${Number(response.total_current_monthly_pnl.toFixed(2)).toLocaleString()}`)
				if(response.pnl_image == 'up'){
					$('.'+country+' .current-avg-pnl').html(`${Number(response.total_current_average_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .current-avg-pnl').addClass('text-success')
				}else if(response.pnl_image == 'down'){
					$('.'+country+' .current-avg-pnl').html(`${Number(response.total_current_average_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .current-avg-pnl').addClass('text-danger')
				}else{
					$('.'+country+' .current-avg-pnl').html(`${Number(response.total_current_average_pnl.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .current-avg-pnl').addClass('')
				}

				$('.'+country+' .estimated-rev').html(`${Number(response.total_estimated_revenue.toFixed(2)).toLocaleString()}(USD)`)
				if(response.eav_image == 'up'){
					$('.'+country+' .estimated-avg-rev').html(`${Number(response.total_estimated_avg_revenue.toFixed(2)).toLocaleString()}(USD) <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .estimated-avg-rev').addClass('text-success')
				}else if(response.eav_image == 'down'){
					$('.'+country+' .estimated-avg-rev').html(`${Number(response.total_estimated_avg_revenue.toFixed(2)).toLocaleString()}(USD) <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .estimated-avg-rev').addClass('text-danger')
				}else{
					$('.'+country+' .estimated-avg-rev').html(`${Number(response.total_estimated_avg_revenue.toFixed(2)).toLocaleString()}(USD)`)
					$('.'+country+' .estimated-avg-rev').addClass('')
				}
				$('.'+country+' .estimated-mo').html(`${Number(response.total_estimated_mo.toFixed(0))}`)
				if(response.emo_image == 'up'){
					$('.'+country+' .estimated-avg-mo').html(`${Number(response.total_estimated_avg_mo.toFixed(0)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .estimated-avg-mo').addClass('text-success')
				}else if(response.emo_image == 'down'){
					$('.'+country+' .estimated-avg-mo').html(`${Number(response.total_estimated_avg_mo.toFixed(0)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .estimated-avg-mo').addClass('text-danger')
				}else{
					$('.'+country+' .estimated-avg-mo').html(`${Number(response.total_estimated_avg_mo.toFixed(0)).toLocaleString()}`)
					$('.'+country+' .estimated-avg-mo').addClass('')
				}
				$('.'+country+' .estimated-campaign').html(`${Number(response.total_estimated_cost.toFixed(2)).toLocaleString()}`)
				$('.'+country+' .estimated-pnl').html(`${Number(response.total_estimated_pnl.toFixed(2))}`)
				if(response.epnl_image == 'up'){
					$('.'+country+' .estimated-avg-pnl').html(`${Number(response.total_estimated_avg_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .estimated-avg-pnl').addClass('text-success')
				}else if(response.epnl_image == 'down'){
					$('.'+country+' .estimated-avg-pnl').html(`${Number(response.total_estimated_avg_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .estimated-avg-pnl').addClass('text-danger')
				}else{
					$('.'+country+' .estimated-avg-pnl').html(`${Number(response.total_estimated_avg_pnl.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .estimated-avg-pnl').addClass('')
				}

				$('.'+country+' .last-rev').html(`${Number(response.total_previous_revenue.toFixed(2)).toLocaleString()}(USD)`)
				if(response.pav_image == 'up'){
					$('.'+country+' .last-avg-rev').html(`${Number(response.total_previous_average_daily.toFixed(2)).toLocaleString()}(USD) <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .last-avg-rev').addClass('text-success')
				}else if(response.pav_image == 'down'){
					$('.'+country+' .last-avg-rev').html(`${Number(response.total_previous_average_daily.toFixed(2)).toLocaleString()}(USD) <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .last-avg-rev').addClass('text-danger')
				}else{
					$('.'+country+' .last-avg-rev').html(`${Number(response.total_previous_average_daily.toFixed(2)).toLocaleString()}(USD)`)
					$('.'+country+' .last-avg-rev').addClass('')
				}
				$('.'+country+' .last-mo').html(`${Number(response.total_previous_mo.toFixed(0)).toLocaleString()}`)
				if(response.pmo_image == 'up'){
					$('.'+country+' .last-avg-mo').html(`${Number(response.total_previous_average_mo.toFixed(0)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .last-avg-mo').addClass('text-success')
				}else if(response.pmo_image == 'down'){
					$('.'+country+' .last-avg-mo').html(`${Number(response.total_previous_average_mo.toFixed(0)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .last-avg-mo').addClass('text-danger')
				}else{
					$('.'+country+' .last-avg-mo').html(`${Number(response.total_previous_average_mo.toFixed(0)).toLocaleString()}`)
					$('.'+country+' .last-avg-mo').addClass('')
				}
				$('.'+country+' .last-campaign').html(`${Number(response.total_previous_cost.toFixed(2)).toLocaleString()}`)
				$('.'+country+' .last-pnl').html(`${response.total_previous_pnl}`)
				if(response.ppnl_image == 'up'){
					$('.'+country+' .last-avg-pnl').html(`${Number(response.total_previous_average_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .last-avg-pnl').addClass('text-success')
				}else if(response.ppnl_image == 'down'){
					$('.'+country+' .last-avg-pnl').html(`${Number(response.total_previous_average_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .last-avg-pnl').addClass('text-danger')
				}else{
					$('.'+country+' .last-avg-pnl').html(`${Number(response.total_previous_average_pnl.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .last-avg-pnl').addClass('')
				}

				$('.'+country+' .prev-rev').html(`${Number(response.total_previous2_revenue.toFixed(2)).toLocaleString()}(USD)`)
				$('.'+country+' .prev-avg-rev').html(`${Number(response.total_previous2_average_daily.toFixed(2)).toLocaleString()}(USD)`)
				$('.'+country+' .prev-mo').html(`${Number(response.total_previous2_mo.toFixed(0)).toLocaleString()}`)
				$('.'+country+' .prev-avg-mo').html(`${Number(response.total_previous2_average_mo.toFixed(0)).toLocaleString()}`)
				$('.'+country+' .prev-campaign').html(`${Number(response.total_previous2_cost.toFixed(2)).toLocaleString()}`)
				$('.'+country+' .prev-pnl').html(`${Number(response.total_previous2_pnl.toFixed(2)).toLocaleString()}`)
				$('.'+country+' .prev-avg-pnl').html(`${Number(response.total_previous2_average_pnl.toFixed(2)).toLocaleString()}`)
			}else{
				$('.'+country+' .current-rev-usd').html(`${Number(response.data.current_monthly_revenue_usd.toFixed(2)).toLocaleString()}(USD)`)
				$('.'+country+' .current-rev').html(`${Number(response.data.current_monthly_revenue).toFixed(2).toLocaleString()}(${response.currency})`)
				if(response.data.av_image == 'up'){
					$('.'+country+' .current-avg-rev').html(`${Number(response.data.current_average_daily.toFixed(2)).toLocaleString()}(${response.currency}) <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .current-avg-rev').addClass('text-success')
				}else if(response.data.av_image == 'down'){
					$('.'+country+' .current-avg-rev').html(`${Number(response.data.current_average_daily.toFixed(2)).toLocaleString()}(${response.currency}) <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .current-avg-rev').addClass('text-danger')
				}else{
					$('.'+country+' .current-avg-rev').html(`${Number(response.data.current_average_daily.toFixed(2)).toLocaleString()}(${response.currency})`)
					$('.'+country+' .current-avg-rev').addClass('')
				}
				$('.'+country+' .current-mo').html(`${Number(response.data.current_monthly_mo.toFixed(0)).toLocaleString()}`)
				if(response.data.mo_image == 'up'){
					$('.'+country+' .current-avg-mo').html(`${Number(response.data.current_average_mo.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .current-avg-mo').addClass('text-success')
				}else if(response.data.mo_image == 'down'){
					$('.'+country+' .current-avg-mo').html(`${Number(response.data.current_average_mo.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .current-avg-mo').addClass('text-danger')
				}else{
					$('.'+country+' .current-avg-mo').html(`${Number(response.data.current_average_mo.toFixed(0)).toLocaleString()}`)
					$('.'+country+' .current-avg-mo').addClass('')
				}
				$('.'+country+' .current-campaign').html(`${Number(response.data.current_cost.toFixed(2)).toLocaleString()}`)
				$('.'+country+' .current-pnl').html(`${Number(response.data.current_monthly_pnl.toFixed(2)).toLocaleString()}`)
				if(response.data.pnl_image == 'up'){
					$('.'+country+' .current-avg-pnl').html(`${Number(response.data.average_monthly_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .current-avg-pnl').addClass('text-success')
				}else if(response.data.pnl_image == 'down'){
					$('.'+country+' .current-avg-pnl').html(`${Number(response.data.average_monthly_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .current-avg-pnl').addClass('text-danger')
				}else{
					$('.'+country+' .current-avg-pnl').html(`${Number(response.data.average_monthly_pnl.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .current-avg-pnl').addClass('')
				}

				$('.'+country+' .estimated-rev-usd').html(`${Number(response.data.estimated_revenue_usd.toFixed(2)).toLocaleString()}(USD)`)
				$('.'+country+' .estimated-rev').html(`${Number(response.data.estimated_revenue.toFixed(2)).toLocaleString()}(${response.currency})`)
				if(response.data.eav_image == 'up'){
					$('.'+country+' .estimated-avg-rev').html(`${Number(response.data.estimated_avg_revenue.toFixed(2)).toLocaleString()}(${response.currency}) <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .estimated-avg-rev').addClass('text-success')
				}else if(response.data.eav_image == 'down'){
					$('.'+country+' .estimated-avg-rev').html(`${Number(response.data.estimated_avg_revenue.toFixed(2)).toLocaleString()}(${response.currency}) <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .estimated-avg-rev').addClass('text-danger')
				}else{
					$('.'+country+' .estimated-avg-rev').html(`${Number(response.data.estimated_avg_revenue.toFixed(2)).toLocaleString()}(${response.currency})`)
					$('.'+country+' .estimated-avg-rev').addClass('')
				}
				$('.'+country+' .estimated-mo').html(`${Number(response.data.estimated_mo.toFixed(0))}`)
				if(response.data.emo_image == 'up'){
					$('.'+country+' .estimated-avg-mo').html(`${Number(response.data.estimated_avg_mo.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .estimated-avg-mo').addClass('text-success')
				}else if(response.data.emo_image == 'down'){
					$('.'+country+' .estimated-avg-mo').html(`${Number(response.data.estimated_avg_mo.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .estimated-avg-mo').addClass('text-danger')
				}else{
					$('.'+country+' .estimated-avg-mo').html(`${Number(response.data.estimated_avg_mo.toFixed(0)).toLocaleString()}`)
					$('.'+country+' .estimated-avg-mo').addClass('')
				}
				$('.'+country+' .estimated-campaign').html(`${Number(response.data.estimated_cost.toFixed(2)).toLocaleString()}`)
				$('.'+country+' .estimated-pnl').html(`${Number(response.data.estimated_pnl.toFixed(2)).toLocaleString()}`)
				if(response.data.epnl_image == 'up'){
					$('.'+country+' .estimated-avg-pnl').html(`${Number(response.data.estimated_avg_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .estimated-avg-pnl').addClass('text-success')
				}else if(response.data.epnl_image == 'down'){
					$('.'+country+' .estimated-avg-pnl').html(`${Number(response.data.estimated_avg_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .estimated-avg-pnl').addClass('text-danger')
				}else{
					$('.'+country+' .estimated-avg-pnl').html(`${Number(response.data.estimated_avg_pnl.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .estimated-avg-pnl').addClass('')
				}

				$('.'+country+' .last-rev-usd').html(`${Number(response.data.previous_revenue_usd.toFixed(2)).toLocaleString()}(USD)`)
				$('.'+country+' .last-rev').html(`${Number(response.data.previous_revenue).toFixed(2).toLocaleString()}(${response.currency})`)
				if(response.data.pav_image == 'up'){
					$('.'+country+' .last-avg-rev').html(`${Number(response.data.previous_average_daily.toFixed(2)).toLocaleString()}(${response.currency}) <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .last-avg-rev').addClass('text-success')
				}else if(response.data.pav_image == 'down'){
					$('.'+country+' .last-avg-rev').html(`${Number(response.data.previous_average_daily.toFixed(2)).toLocaleString()}(${response.currency}) <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .last-avg-rev').addClass('text-danger')
				}else{
					$('.'+country+' .last-avg-rev').html(`${Number(response.data.previous_average_daily.toFixed(2)).toLocaleString()}(${response.currency})`)
					$('.'+country+' .last-avg-rev').addClass('')
				}
				$('.'+country+' .last-mo').html(`${Number(response.data.previous_mo.toFixed(0)).toLocaleString()}`)
				if(response.data.emo_image == 'up'){
					$('.'+country+' .last-avg-mo').html(`${Number(response.data.previous_average_mo.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .last-avg-mo').addClass('text-success')
				}else if(response.data.emo_image == 'down'){
					$('.'+country+' .last-avg-mo').html(`${Number(response.data.previous_average_mo.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .last-avg-mo').addClass('text-danger')
				}else{
					$('.'+country+' .last-avg-mo').html(`${Number(response.data.previous_average_mo.toFixed(0)).toLocaleString()}`)
					$('.'+country+' .last-avg-mo').addClass('')
				}
				$('.'+country+' .last-campaign').html(`${Number(response.data.previous_cost.toFixed(2)).toLocaleString()}`)
				$('.'+country+' .last-pnl').html(`${Number(response.data.previous_pnl.toFixed(2)).toLocaleString()}`)
				if(response.data.epnl_image == 'up'){
					$('.'+country+' .last-avg-pnl').html(`${Number(response.data.previous_average_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-up"></i>`)
					$('.'+country+' .last-avg-pnl').addClass('text-success')
				}else if(response.data.epnl_image == 'down'){
					$('.'+country+' .last-avg-pnl').html(`${Number(response.data.previous_average_pnl.toFixed(2)).toLocaleString()} <i class="fa fa-arrow-down"></i>`)
					$('.'+country+' .last-avg-pnl').addClass('text-danger')
				}else{
					$('.'+country+' .last-avg-pnl').html(`${Number(response.data.previous_average_pnl.toFixed(2)).toLocaleString()}`)
					$('.'+country+' .last-avg-pnl').addClass('')
				}

				$('.'+country+' .prev-rev-usd').html(`${Number(response.data.previous2_revenue_usd.toFixed(2)).toLocaleString()}(USD)`)
				$('.'+country+' .prev-rev').html(`${Number(response.data.previous2_revenue).toFixed(2).toLocaleString()}(${response.currency})`)
				$('.'+country+' .prev-avg-rev').html(`${Number(response.data.previous2_average_daily.toFixed(2)).toLocaleString()}(${response.currency})`)
				$('.'+country+' .prev-mo').html(`${Number(response.data.previous2_mo.toFixed(0)).toLocaleString()}`)
				$('.'+country+' .prev-avg-mo').html(`${Number(response.data.previous2_average_mo.toFixed(0)).toLocaleString()}`)
				$('.'+country+' .prev-campaign').html(`${Number(response.data.previous2_cost.toFixed(2)).toLocaleString()}`)
				$('.'+country+' .prev-pnl').html(`${Number(response.data.previous2_pnl.toFixed(2)).toLocaleString()}`)
				$('.'+country+' .prev-avg-pnl').html(`${Number(response.data.previous2_average_pnl.toFixed(2)).toLocaleString()}`)
			}
			$('.'+country+'-loader').css('display', 'none');
		})
		.catch((err) => console.error(err))
	})
})*/


$('#dashboard-company').on('change', function(){
	var csrf_token = $("#csrf_token").val();
	var company = $(this).val()

	if(company != ''){
		$.ajax({
			url: base_url+'dash/dashboardcountriesbycompany',
			method: 'POST',
			headers: {
				'X-CSRF-Token': csrf_token
			},
			data: {'company_id':company},
			success: function(response){
				$('#dashboard-country').empty()
				$('#dashboard-operator').empty()
				$('#dashboard-operator').html('<option value="">Operator Name</option>')
				$('#dashboard-service').empty()
				$('#dashboard-service').html('<option value="">Service Name</option>')
				if(response){
					var html = '<option value="">Country Name</option>'
					$.each(response, function(k,v){
						html += `<option value="${k}">${v}</option>`
					})
					$('#dashboard-country').html(html)
				}else{
					$('#dashboard-country').html('<option value="">Country Name</option>')
					$('#dashboard-operator').html('<option value="">Operator Name</option>')
					$('#dashboard-service').html('<option value="">Service Name</option>')
				}
			},
			error: function(){
				console.error('internal server error')
			}
		})
	}else{
		$('#dashboard-country').html('<option value="">Country Name</option>')
		$('#dashboard-operator').html('<option value="">Operator Name</option>')
		$('#dashboard-service').html('<option value="">Service Name</option>')
	}
})

$('#dashboard-country').on('change', function(){
	var csrf_token = $("#csrf_token").val()
	var company = $('#dashboard-company').val()
	var country = $(this).val()

	if(company != '' || country != ''){
		$.ajax({
			url: base_url+'dash/dashboardoperatorsbycountry',
			method: 'POST',
			headers: {
				'X-CSRF-Token': csrf_token
			},
			data: {'company_id':company, 'country_id':country},
			success: function(response){
				$('#dashboard-operator').empty()
				$('#dashboard-service').empty()
				$('#dashboard-service').html('<option value="">Service Name</option>')
				if(response){
					var html = '<option value="">Operator Name</option>'
					$.each(response, function(k,v){
						html += `<option value="${k}">${v}</option>`
					})
					$('#dashboard-operator').html(html)
				}else{
					$('#dashboard-operator').html('<option value="">Operator Name</option>')
					$('#dashboard-service').html('<option value="">Service Name</option>')
				}
			},
			error: function(){
				console.error('internal server error')
			}
		})
	}else{
		$('#dashboard-operator').html('<option value="">Operator Name</option>')
		$('#dashboard-service').html('<option value="">Service Name</option>')
	}
})

$('#dashboard-operator').on('change', function(){
	var csrf_token = $("#csrf_token").val()
	var operator = $(this).val()

	if(operator != ''){
		$.ajax({
			url: base_url+'dash/dashboardservicesbyoperator',
			method: 'POST',
			headers: {
				'X-CSRF-Token': csrf_token
			},
			data: {'operator_id':operator},
			success: function(response){
				$('#dashboard-service').empty()
				$('#dashboard-service').html('<option value="">Service Name</option>')
				if(response){
					var html = '<option value="">Service Name</option>'
					$.each(response, function(k,v){
						html += `<option value="${k}">${v}</option>`
					})
					$('#dashboard-service').html(html)
				}else{
					$('#dashboard-service').html('<option value="">Service Name</option>')
				}
			},
			error: function(){
				console.error('internal server error')
			}
		})
	}else{
		$('#dashboard-service').html('<option value="">Service Name</option>')
	}
})

$('#service-country').on('change', function(){
	var csrf_token = $("#csrf_token").val()
	var country = $(this).val()

	if(country != ''){
		$.ajax({
			url: base_url+'servicecatalogue/getcompaniesoperatorsbycountry',
			method: 'POST',
			headers: {
				'X-CSRF-Token': csrf_token
			},
			data: {'country_id':country},
			success: function(response){
				$('#service-operator').empty()
				$('#service-company').empty()
				if(response){
					var chtml = '<option value="">Select</option>'
					$.each(response.companies, function(k,v){
						chtml += `<option value="${k}">${v}</option>`
					})
					$('#service-company').html(chtml)

					var html = '<option value="">Select</option>'
					$.each(response.operators, function(k,v){
						html += `<option value="${k}">${v}</option>`
					})
					$('#service-operator').html(html)
				}else{
					$('#service-operator').html('<option value="">Select</option>')
					$('#service-company').html('<option value="">Select</option>')
				}
			},
			error: function(){
				console.error('internal server error')
			}
		})
	}else{
		$('#dashboard-operator').html('<option value="">Operator Name</option>')
		$('#dashboard-service').html('<option value="">Service Name</option>')
	}
})

$(document).ready(function (){

	$('#alphBnt').on('click', function(){

		var option = $("#sorting_pnl_orders option:selected").text();

        if(option == 'Highest Revenue USD'){
			if(window.location.pathname != '/report/amsummary'){
				$(".box-panel").sort((a, b) => parseFloat($(b).find(".revenue_total_usd").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(a).find(".revenue_total_usd").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#container")
			}else{
				$(".box-panel").sort((a, b) => parseFloat($(b).find(".revenue_total").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(a).find(".revenue_total").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#container")
			}
        }
		else if (option == 'Lowest Revenue USD'){
			if(window.location.pathname != '/report/amsummary'){
				$(".box-panel").sort((a, b) => parseFloat($(a).find(".revenue_total_usd").text().replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(b).find(".revenue_total_usd").text().replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#container")
			}else{
				$(".box-panel").sort((a, b) => parseFloat($(a).find(".revenue_total").text().replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(b).find(".revenue_total").text().replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#container")
			}
		}
		else if(option == 'Highest Reg'){
			$(".box-panel").sort((a, b) => parseFloat($(b).find(".reg_total").text().replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(a).find(".reg_total").text().replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#container")
		}
		else if(option == 'Lowest Reg'){
			$(".box-panel").sort((a, b) => parseFloat($(a).find(".reg_total").text().replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(b).find(".reg_total").text().replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#container")
		}
		else if(option == 'Highest Unreg'){
			$(".box-panel").sort((a, b) => parseFloat($(b).find(".unreg_total").text().replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(a).find(".unreg_total").text().replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#container")
		}
		else if(option == 'Lowest Unreg'){
			$(".box-panel").sort((a, b) => parseFloat($(a).find(".unreg_total").text().replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(b).find(".unreg_total").text().replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#container")
		}
		else if(option == 'Highest Renewal'){
			$(".box-panel").sort((a, b) => parseFloat($(b).find(".total_sent").text().replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(a).find(".total_sent").text().replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#container")
		}
		else if(option == 'Lowest Renewal'){
			$(".box-panel").sort((a, b) => $(a).find(".total_sent").text().replace(/\$/g, '').replace(/\,/g, '')  - $(b).find(".total_sent").text().replace(/\$/g, '').replace(/\,/g, '') ).appendTo("#container")
		}
		else if(option == 'Highest Bill Rate'){
			$(".box-panel").sort((a, b) => parseFloat($(a).find(".bill .br_avg").text().replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(b).find(".bill .br_avg").text().replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#container")
		}
		else if(option == 'Lowest Bill Rate') {
			$(".box-panel").sort((a, b) => parseFloat($(a).find(".br_avg").text().replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(b).find(".br_avg").text().replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#container")
		}
		else{
			$(".box-panel").sort((a, b) => parseFloat($(b).find(".revenue_total").text().replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(a).find(".revenue_total").text().replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#container")
		}

    });

	if(window.location.pathname != '/report/amsummary'){
		$(".box-panel").sort((a, b) => parseFloat($(b).find(".revenue_total_usd").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(a).find(".revenue_total_usd").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#container")
	}else{
		$(".box-panel").sort((a, b) => parseFloat($(b).find(".revenue_total").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(a).find(".revenue_total").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#container")
	}
});



$(document).ready(function (){

	var selectedOption = $("#filter_dashboard option:selected").text();

	if(selectedOption == 'Highest Revenue'){
			$(".revfilter").sort((a, b) => parseFloat($(b).find(".current_month_revenue").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(a).find(".current_month_revenue").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#dashboardCountrydata")
    }



	$('#filterBnt').on('click', function(){

		var option = $("#filter_dashboard option:selected").text();
		// alert(option);

		if(option == 'Highest Revenue'){
			$(".revfilter").sort((a, b) => parseFloat($(b).find(".current_month_revenue").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(a).find(".current_month_revenue").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#dashboardCountrydata")
        }
        else if(option == 'Lowest Revenue'){
			$(".revfilter").sort((a, b) => parseFloat($(a).find(".current_month_revenue").text().replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(b).find(".current_month_revenue").text().replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#dashboardCountrydata")
		}
		else if(option == 'Highest Mo'){
			$(".revfilter").sort((a, b) => parseFloat($(b).find(".current_month_mo").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(a).find(".current_month_mo").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#dashboardCountrydata")
        }
        else if(option == 'Lowest Mo'){
			$(".revfilter").sort((a, b) => parseFloat($(a).find(".current_month_mo").text().replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(b).find(".current_month_mo").text().replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#dashboardCountrydata")
		}
		else if(option == 'Highest Cost Campaign'){
			$(".revfilter").sort((a, b) => parseFloat($(b).find(".current_month_costCampaign").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(a).find(".current_month_costCampaign").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#dashboardCountrydata")
        }
        else if(option == 'Lowest Cost Campaign'){
			$(".revfilter").sort((a, b) => parseFloat($(a).find(".current_month_costCampaign").text().replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(b).find(".current_month_costCampaign").text().replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#dashboardCountrydata")
		}
		else if(option == 'Highest Pnl'){
			$(".revfilter").sort((a, b) => parseFloat($(b).find(".current_month_pnl").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(a).find(".current_month_pnl").text().replace(/\USD/g, '').replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#dashboardCountrydata")
        }
        else if(option == 'Lowest Pnl'){
			$(".revfilter").sort((a, b) => parseFloat($(a).find(".current_month_pnl").text().replace(/\$/g, '').replace(/\,/g, '')) - parseFloat($(b).find(".current_month_pnl").text().replace(/\$/g, '').replace(/\,/g, ''))).appendTo("#dashboardCountrydata")
		}
	});
});







// $("#sorting_pnl_orders").on('change', function(){
// 	var option = $(this).val()
// 	if(option == ''){
// 		$('#alphBnt').prop('disabled',true)
// 	}else{
// 		$('#alphBnt').prop('disabled',false)
// 	}
// });
