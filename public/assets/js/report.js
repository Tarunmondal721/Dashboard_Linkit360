var base_url = window.location.origin+'/';

$('.graph-btn').on('click', function(){
	if($(this).text() == '- Graphs'){
        $('.graphs .card-body').css('display','none')
		$(this).html('<strong>+ Graphs</strong>')
	}else{
        $('.graphs .card-body').css('display','block')
		$(this).html('<strong>- Graphs</strong>')
	}
})

$('.pie-btn').on('click', function(){
    if($(this).text() == '- Pie'){
        $('.graphs .card-body').css('display','none')
        $(this).html('<strong>+ Pie</strong>')
    }else{
        $('.graphs .card-body').css('display','block')
        $(this).html('<strong>- Pie</strong>')
    }
})

$('#special').on('click', function(){
	if($(this).text() == '- Advanced Filter'){
		$('#special-section').css('display','none')
		$(this).html('<strong>+ Advanced Filter</strong>')
	}else{
		$('#special-section').css('display','block')
		$(this).html('<strong>- Advanced Filter</strong>')
	}
})

$('#reportsubmit').on('click', function(){
	var dataType = $('#data-type').val()
	var reportType = $('#report-type').val()

	var company_id = $('#dashboard-company').val() ? $('#dashboard-company').val() : ''
	var country_ids = $('#dashboard-country').val() ? $('#dashboard-country').val() : []
	var operator_ids = $('#dashboard-operator').val() ? $('#dashboard-operator').val() : []

	var from_datepicker = $('#from_datepicker').val() ? $('#from_datepicker').val() : ''
	var to_datepicker = $('#to_datepicker').val() ? $('#to_datepicker').val() : ''

	var date_analytics_picker = $('#date_analytics_picker').val() ? $('#date_analytics_picker').val() : ''


	var csrf_token = $('#csrf_token').val()

	var url = `?_csrf=${csrf_token}&company_id=${company_id}`

	if(country_ids){
		for (let country_id of country_ids) {
		  	url+=`&country_id[]=${country_id}`
		}
	}
	if(operator_ids){
		for (let operator_id of operator_ids) {
		  	url+=`&operator_id[]=${operator_id}`
		}
	}

	if(date_analytics_picker != ''){
		var  date_analytics_picker_obj = JSON.parse(date_analytics_picker);
		url+=`&from_datepicker=${date_analytics_picker_obj.start}&to_datepicker=${date_analytics_picker_obj.end}`
	}



	if(dataType == 'daily' && reportType == 'country'){

		window.location.href = base_url+'report/countrysummary'+url

	}else if(dataType == 'monthly' && reportType == 'operator'){

		window.location.href = base_url+'report/monthlysummary'+url

	}else if(dataType == 'daily' && reportType == 'ac manager'){

		window.location.href = base_url+'report/amsummary'+url

	}else if(dataType == 'monthly' && reportType == 'country'){

		window.location.href = base_url+'report/monthlycountrysummary'+url

	}else if(dataType == 'monthly' && reportType == 'ac manager'){

		window.location.href = base_url+'report/monthlyamsummary'+url

	}else{

		window.location.href = base_url+'report/summary'+url
	}
})

$('.operator_status_change').on('click',function(){
	var selector = $(this)
	var operator_id = selector.data('operator')
	var csrf_token= $("#csrf_token").val()

	if(operator_id && operator_id > 0){
		$.ajax({
			url: base_url+'administration/changeoperatorstatus',
			method: 'post',
			data: {operator_id: operator_id},
			headers: {
				'X-CSRF-Token': csrf_token
			},
			success: function(response){
				if(response){
					if(response == 1){
						selector.removeClass('btn-danger')
						selector.addClass('btn-success')
						selector.text('Active')
					}else{
						selector.removeClass('btn-success')
						selector.addClass('btn-danger')
						selector.text('Inactive')
					}
				}
			},
			error: function(){
				console.error('internal server error')
			}
		})
	}
})
$('.ossbtn').on('click',function(){
	var param = $(this).data('param')
	// var ads = $("#filter_ads option:selected").text();

	console.log(param);
	if($(this).text() == '-'){
		$('.'+param).slideUp()
		$(this).html('<strong>+</strong>')
	}else{
		// if(ads == 'Highest Cost Campaign'){
			var test = $("."+param).sort((a, b) => $(b).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '') - $(a).find(".subs").text().replace(/\$/g, '').replace(/\,/g, ''));
			console.log(test);
			$("."+param).sort((a, b) => $(b).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '') - $(a).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#"+param+"_adsTblBdy")
		// }


		$('.'+param).slideDown()
		$(this).html('<strong>-</strong>')
	}
})
$('.adsSummary').on('click', function () {
    var cat = $("#filter_ads_summary option:selected").text();
    console.log(cat);

    if (cat == 'Highest Transaction') {
        var sorting = $(".ptables").sort((a, b) => $(b).find(".transaction-col").text().replace(/\$/g, '').replace(/\,/g, '') - $(a).find(".transaction-col ").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#adsTbl")
    }
    else if (cat == 'Lowest Transaction') {
        var sorting = $(".ptables ").sort((a, b) => $(a).find(".transaction-col").text().replace(/\$/g, '').replace(/\,/g, '') - $(b).find(".transaction-col").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#adsTbl")
    }
    else if (cat == 'Highest Subscription') {
        var sorting = $(".ptables").sort((a, b) => $(b).find(".subscription-col").text().replace(/\$/g, '').replace(/\,/g, '') - $(a).find(".subscription-col ").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#adsTbl")
    }
    else if (cat == 'Lowest Subscription') {
        var sorting = $(".ptables ").sort((a, b) => $(a).find(".subscription-col").text().replace(/\$/g, '').replace(/\,/g, '') - $(b).find(".subscription-col").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#adsTbl")
    }
});
$('.summaryButton').on('click',function(){
	var param = $(this).data('param')
	// var ads = $("#filter_ads option:selected").text();

	console.log(param);
	if($(this).text() == '-'){
		$('.'+param).slideUp()
		$(this).html('<strong>+</strong>')
	}else{
		// if(ads == 'Highest Cost Campaign'){
			var test = $("."+param).sort((a, b) => $(b).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '') - $(a).find(".subs").text().replace(/\$/g, '').replace(/\,/g, ''));
			console.log(test);
			$("."+param).sort((a, b) => $(b).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '') - $(a).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#"+param+"_adsTblBdy")
		// }


		$('.'+param).slideDown()
		$(this).html('<strong>-</strong>')
	}
})
$('.opbtn').on('click',function(){
	var param = $(this).data('param')
	var ads = $("#filter_ads option:selected").text();
    console.log(ads);
	console.log(param);
	if($(this).text() == '-'){
		$('.'+param).slideUp()
		$(this).html('<strong>+</strong>')
	}else{
		if(ads == 'Highest Cost Campaign'){
			var test = $("."+param).sort((a, b) => $(b).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '') - $(a).find(".subs").text().replace(/\$/g, '').replace(/\,/g, ''));
			console.log(test);
			$("."+param).sort((a, b) => $(b).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '') - $(a).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#"+param+"_adsTblBdy")
		}

		if(ads == 'Lowest Cost Campaign'){
			$("."+param).sort((a, b) => $(a).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '') - $(b).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#"+param+"_adsTblBdy")
		}

		if(ads == 'Highest Revenue'){
			$("."+param).sort((a, b) => $(b).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '') - $(a).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#"+param+"_adsTblBdy")
		}

		if(ads == 'Lowest Revenue'){
			$("."+param).sort((a, b) => $(a).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '') - $(b).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#"+param+"_adsTblBdy")
		}

		if(ads == 'Highest end user revenue'){
			$("."+param).sort((a, b) => $(b).find(".gmv").text().replace(/\$/g, '').replace(/\,/g, '') - $(a).find(".gmv").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#"+param+"_reconcile")
		}

		if(ads == 'Lowest end user revenue'){
			$("."+param).sort((a, b) => $(a).find(".gmv").text().replace(/\$/g, '').replace(/\,/g, '') - $(b).find(".gmv").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#"+param+"_reconcile")
		}

		if(ads == 'Highest gross revenue'){
			$("."+param).sort((a, b) => $(b).find(".gros").text().replace(/\$/g, '').replace(/\,/g, '') - $(a).find(".gros").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#"+param+"_reconcile")
		}

		if(ads == 'Lowest gross revenue'){
			$("."+param).sort((a, b) => $(a).find(".gros").text().replace(/\$/g, '').replace(/\,/g, '') - $(b).find(".gros").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#"+param+"_reconcile")
		}

		if(ads == 'Highest net revenue'){
			$("."+param).sort((a, b) => $(b).find(".nets").text().replace(/\$/g, '').replace(/\,/g, '') - $(a).find(".nets").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#"+param+"_reconcile")
		}

		if(ads == 'Lowest net revenue'){
			$("."+param).sort((a, b) => $(a).find(".nets").text().replace(/\$/g, '').replace(/\,/g, '') - $(b).find(".nets").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#"+param+"_reconcile")
		}
		$('.'+param).slideDown()
		$(this).html('<strong>-</strong>')
	}
})

$('.revbtn').on('click',function(){
	var param = $(this).data('param')
	if($(this).text() == '-'){
		$('.'+param).slideUp()
		$(this).html('<strong>+</strong>')
	}else{
		$('.'+param).slideDown()
		$(this).html('<strong>-</strong>')
	}
})

$('.grevbtn').on('click',function(){
	var param = $(this).data('param')
	if($(this).text() == '-'){
		$('.'+param).slideUp()
		$(this).html('<strong>+</strong>')
	}else{
		$('.'+param).slideDown()
		$(this).html('<strong>-</strong>')
	}
})

$('#monitor-expand').on('change',function(){
	if($(this).prop('checked')){
		$('.expandable').slideDown()
		$('.opbtn').html('<strong>-</strong>')
	}else{
		$('.expandable').slideUp()
		$('.opbtn').html('<strong>+</strong>')
	}
})

$(document).ready(function(){
	$('.expandable').slideUp()
})

$('#monitor-remove').on('change',function(){
	if($(this).prop('checked')){
		$('.removeable').slideUp()
	}else{
		$('.removeable').slideDown()
	}
})

$('.dtbl tr').each(function(index){
	var total_month_days = $(document).find("#total_month_days").val();
   	var sum=0;
   	var length =0;
   	var current_month = $('#month_bool').val()
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
   $(this).find('.revenue_total', this).html("<span class='local_total_revenue'>"+trev+"</span>");
   //console.log(trev)

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

   if(length > 0){
	   var rev_avg = sumTotal / length;
	   var month_rem_days =  total_month_days - length;
	   var rev_mon = sumTotal + (rev_avg  * month_rem_days);

	   //set the value of currents rows sum to the total-combat element in the current row
	   $(this).find('.revenue_avg_usd', this).html(formatNumber(rev_avg.toFixed(2).toLocaleString('en')));
	   $(this).find('.revenue_monthly_usd', this).html(formatNumber(rev_mon.toFixed(2).toLocaleString('en')));
   }


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
})
