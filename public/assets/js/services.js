var base_url = window.location.origin;
function serviceSubmit() {

    var country = $("#country").val();
    if (country == "") {
        $("#errorcountry").removeClass("gu-hide");
        $('#collapseOne').collapse('show');
        return false;
    } else {
        $("#errorcountry").addClass("gu-hide");
    }
    var company = $("#company").val();
    if (company == "") {
        $("#errorcompany").removeClass("gu-hide");
        $('#collapseOne').collapse('show');
        return false;
    } else {
        $("#errorcompany").addClass("gu-hide");
    }
    var service_type = $("input[name='service_type']:checked").val();
    console.log(service_type);
    if (!service_type) {
        $("#error_service_type").removeClass("gu-hide");
        $('#collapseOne').collapse('show');
        return false;
    } else {
        $("#error_service_type").addClass("gu-hide");
    }
    var operator = $("#operator").val();
    var Operators = $("#newOperator").val();
    console.log(operator);
    if (operator == "" &&  Operators == "" )  {
        $("#erroroperator").removeClass("gu-hide");
        $('#collapseOne').collapse('show');
        return false;
    } else {
        $("#erroroperator").addClass("gu-hide");
    }
    var airpay = $("input[name='is_airpay']:checked").val();
    if (!airpay) {
        $("#error_airpay").removeClass("gu-hide");
        $('#collapseOne').collapse('show');
        return false;
    } else {
        $("#error_airpay").addClass("gu-hide");
    }
    var agreetor = $("#aggregrator").val();
    if (agreetor == "") {
        $("#erroraggregrator").removeClass("gu-hide");
        $('#collapseOne').collapse('show');
        return false;
    } else {
        $("#erroraggregrator").addClass("gu-hide");
    }
    var servicename = $("#servicename").val();
    if (servicename == "") {
        $("#errorservicename").removeClass("gu-hide");
        $('#collapseOne').collapse('show');
        return false;
    } else {
        $("#errorservicename").addClass("gu-hide");
    }
    var subkeyword = $("#subkeyword").val();
    if (subkeyword == "") {
        $("#errorsubkeyword").removeClass("gu-hide");
        $('#collapseOne').collapse('show');
        return false;
    } else {
        $("#errorsubkeyword").addClass("gu-hide");
    }

    var short_code = $("#short_code").val();
    if (short_code == "") {
        $("#errorshort_code").removeClass("gu-hide");
        $('#collapseOne').collapse('show');
        return false;
    } else {
        $("#errorshort_code").addClass("gu-hide");
    }

    var start_date = $("#start_date").val();
    if (start_date == "") {
        $("#errorstartdate").removeClass("gu-hide");
        $('#collapseOne').collapse('show');
        return false;
    } else {

        $("#errorstartdate").addClass("gu-hide");
    }

    var end_date = $("#end_date").val();
    if (end_date == "") {
        $("#errorenddate").removeClass("gu-hide");
        $('#collapseOne').collapse('show');
        return false;
    } else {
         $('#collapseOne').collapse('hide');
        $("#errorenddate").addClass("gu-hide");
    }

    var go_live_date = $("#go_live_date").val();
    if (go_live_date == "") {
        $("#livedate").removeClass("gu-hide");
        return false;
    } else {
        $("#livedate").addClass("gu-hide");
    }

    // var channel_type = $("input[name='channel']:checked").val();
    // if (!channel_type) {
    //     $("#error_channel").removeClass("gu-hide");
    //     return false;
    // } else {
    //     $("#error_channel").addClass("gu-hide");
    // }

    var checked = false;
    $('input[type="checkbox"][name^="channel"]').each(function () {
        if ($(this).is(":checked")) {
            checked = true;
        }
    });

    if (!checked) {
        $("#error_channel").removeClass("gu-hide");
        $('#collapseTwo').collapse('show');
        return false;
    } else {
        $("#error_channel").addClass("gu-hide");

    }

    // var cycle_type = $("input[name^=cycle']:checked").val();
    // if (!cycle_type) {
    //     $("#error_cycle").removeClass("gu-hide");
    //     return false;
    // } else {
    //     $("#error_cycle").addClass("gu-hide");
    // }
    // var daily = $("#changeCycleDaily").val();
    // if (daily == "") {
    //     $("#errorr_daily").removeClass("gu-hide");
    //     return false;
    // } else {
    //     $("#errorr_daily").addClass("gu-hide");
    // }
    // var weekly = $("#changeCycleWeekly").val();
    // if (weekly == "") {
    //     $("#errorr_weekly").removeClass("gu-hide");
    //     return false;
    // } else {
    //     $("#errorr_weekly").addClass("gu-hide");
    // }

    // var monthly = $("#changeCycleMonthly").val();
    // if (monthly == "") {
    //     $("#errorr_monthly").removeClass("gu-hide");
    //     return false;
    // } else {
    //     $("#errorr_monthly").addClass("gu-hide");
    // }
    var check = false;
    $('input[type="checkbox"][name^="cycle"]').each(function () {
        if ($(this).is(":checked")) {
            check = true;
        }
    });

    if (!check) {
        $("#error_cycle").removeClass("gu-hide");
        $('#collapseTwo').collapse('show');
        return false;
    } else {
        $("#error_cycle").addClass("gu-hide");

    }

    var revenue = $("#revenueoperator").val();
    if (revenue == "") {
        $("#errorrevenue").removeClass("gu-hide");
        $('#collapseTwo').collapse('show');
        return false;
    } else {
        $("#errorrevenue").addClass("gu-hide");
    }

    var revenue_merc = $("#revenuemerchant").val();
    if (revenue_merc == "") {
        $("#error_merchant").removeClass("gu-hide");
        $('#collapseTwo').collapse('show');
        return false;
    } else {
        $("#error_merchant").addClass("gu-hide");
    }
    // var freemiun = $("#freemiumDays").val();
    // console.log(freemiun);
    // if (freemiun == "") {
    //     $("#error_freemium").removeClass("gu-hide");
    //     return false;
    // } else {
    //     $("#error_freemium").addClass("gu-hide");
    // }

    var freemium = $("#freemiumDays").val(); // Convert to a number

    var freeper = $("input[name='freemiumPermission']:checked").val();
    console.log(freemium);
    console.log(freeper);

    if (freemium === 0 && freeper === "no") {
        $("#error_freemium").addClass("gu-hide");
        // return false;
    } else if (freeper === "yes" && freemium <= 0) {
        $("#error_freemium").removeClass("gu-hide");
        $('#collapseTwo').collapse('show');
        return false;
    } else {
        $("#error_freemium").addClass("gu-hide");
    }





    var Currency = $("#currency").val();

    if (Currency == "") {
        $("#error_currency").removeClass("gu-hide");
        $('#collapseTwo').collapse('show');
        return false;
    } else {
        $("#error_currency").addClass("gu-hide");
    }

    var service_price = $("#service_price").val();
    console.log(service_price);
    if (service_price == "") {
        $("#error_service").removeClass("gu-hide");
        $('#collapseTwo').collapse('show');
        return false;
    } else {
        $("#error_service").addClass("gu-hide");
    }


    var source = $("#report_source").val();
    console.log(source);
    if (source == "") {
        $("#errorsource").removeClass("gu-hide");
        $('#collapseTwo').collapse('show');
        return false;
    } else {
        $("#errorsource").addClass("gu-hide");
    }
    var partner = $("#report_partner").val();
    if (partner == "") {
        $("#errorpartner").removeClass("gu-hide");
        $('#collapseTwo').collapse('show');
        return false;
    } else {
        $('#collapseTwo').collapse('hide');
        $("#errorpartner").addClass("gu-hide");
    }
    var domain = $("#domain_portal").val();
    if (domain == "") {
        $("#errordomain").removeClass("gu-hide");
        $('#collapseThree').collapse('show');
        return false;
    } else {
        $('#collapseThree').collapse('hide');
        $("#errordomain").addClass("gu-hide");
    }
    var account_manager = $("#account_manager").val();
    if (account_manager == "") {
        $("#erroraccount_manager").removeClass("gu-hide");
        $('#collapseNine').collapse('show');
        return false;
    } else {
        $("#erroraccount_manager").addClass("gu-hide");
    }
    var pmo = $("#pmo").val();
    if (pmo == "") {
        $("#errorpmo").removeClass("gu-hide");
        $('#collapseNine').collapse('show');
        return false;
    } else {
        $("#errorpmo").addClass("gu-hide");
    }

    var pmo = $("#backend").val();
    if (pmo == "") {
        $("#errorbackend").removeClass("gu-hide");
        $('#collapseNine').collapse('show');
        return false;
    } else {
        $("#errorbackend").addClass("gu-hide");
    }
    var pmo = $("#csteam").val();
    if (pmo == "") {
        $("#errorcsteam").removeClass("gu-hide");
        $('#collapseNine').collapse('show');
        return false;
    } else {
        $("#errorcsteam").addClass("gu-hide");
    }
    var pmo = $("#infrateam").val();
    if (pmo == "") {
        $("#errorinfrateam").removeClass("gu-hide");
        $('#collapseNine').collapse('show');
        return false;
    } else {
        $('#collapseNine').collapse('hide');
        $("#errorinfrateam").addClass("gu-hide");
    }

    // var currency = $("#currency").val();
    // if (currency == "") {
    //     $("#errorcurrency").removeClass("gu-hide");
    //     return false;
    // } else {
    //     $("#errorcurrency").addClass("gu-hide");
    // }
}



function addMoreSelect() {
    var selectContainer = document.getElementById('selectContainer');

    var clonedOption = selectContainer.children[0].cloneNode(true);
    var deleteBtn = clonedOption.querySelector('.delete-btn');
    deleteBtn.hidden = false;
    selectContainer.appendChild(clonedOption);

}


function removeSelect(button) {
    var selectOption = button.parentNode;
    var selectContainer = selectOption.parentNode;
    // Check if the select option being removed is not the default one
    if (selectContainer.children.length > 1) {
        selectContainer.removeChild(selectOption);
    }
}





function addPeople(Users) {

    var rowCount = $("#peopleTable tr").length + 1;

    var html = '';

    html += '<tr>';
    html += '<td class="rowCount">' + rowCount + '</td>';
    html += '<td>';
    html += '<select class="form-control selectUser" id="selectUser' + rowCount + '" onchange="Email(this)" name="team_name[]">';
    html += '<option value=""  selected>Select Name</option>';
    for (var i = 0; i < Users.length; i++) {

        html += '<option  value="' + Users[i].id + '">' + Users[i].name + '</option>';
    }
    html += '</select>';
    html += '</td>';
    html += '<td>';
    html += '<input type="text" class="form-control fild-Style" id="selectEmail' + rowCount + '" readonly style="height: 39px;" name="team_email[]" aria-describedby="emailHelp" placeholder="Email">';
    html += '</td>';
    html += '<td>';
    html += '<input type="number" class="form-control fild-Style" id="whatsapp_number_1" style="height: 39px;" name="team_whatsapp[]" aria-describedby="emailHelp" placeholder="Whatsapp Number">';
    html += '</td>';
    html += '<td>';
    html += '<select class="form-control" id="selectLevel' + rowCount + '" name="level[]">';
    html += '<option value=""  selected>Select Level</option>';

    html += '<option value="level1">Level 1</option>';
    html += '<option value="level2">Level 2</option>';
    html += '<option value="level3">Level 3</option>';
    html += '<option value="level4">Level 4</option>';
    html += '<option value="level5">Level 5</option>';
    html += '<option value="level6">Level 6</option>';
    html += '</select>';
    html += '</td>';
    html += '<td>';
    html += '<button type="button" class="delete-btn form-control"  style="height: 39px;" data-title="Delete"  onclick="removePeople(this)">';
    html += '<i class="fa fa-trash" style="color: red"></i>';
    html += '</button>';
    html += '</td>';
    html += '</tr>';

    $("#peopleTable").append(html);

    $("#selectUser" + rowCount).select2();
    $("#selectLevel" + rowCount).select2();


}

function addMore(countrys) {

    var rowCount = $("#priceAdd tr").length + 1;

    var html = '';

    html += '<tr>';
    html += '<td style="width: 32%;">';
    html += '<select class="form-control " id="currency' +rowCount + '"    name="currency[]">';
    html += '<option value="" selected>Currency</option>';
    for (var i = 0; i < countrys.length; i++) {

        html += '<option  value="' + countrys[i].currency_code + '">' + countrys[i].currency_code + '</option>';
    }
    html += '</select>';
    html += '</td>';
    html += '<td>';
    html += '<input type="number" class="form-control fild-Style" id="service_price" name="service_price[]" min="0" max="100" step="0.0000000001">';
    html += '</td>';
    html += '<td>';
    html += '<button type="button" class="delete-btn form-control"  style="height: 39px;" data-title="Delete"  onclick="removePeople(this)">';
    html += '<i class="fa fa-trash" style="color: red"></i>';
    html += '</button>';
    html += '</td>';
    html += '</tr>';

    $("#priceAdd").append(html);

    $("#currency" + rowCount).select2();
    // $("#service_price" + rowCount).select2();


}



function removePeople(button) {
    var row = button.closest('tr');
    var tableBody = row.parentNode;
    // Check if there is more than one row before removing
    if (tableBody.children.length > 1) {
        tableBody.removeChild(row);
    }

    // Select all elements with the class "rowCount"
    var rowCountElements = $(".rowCount");

    // Loop through each element and assign ascending values
    rowCountElements.each(function (index) {
        // Assigning index + 1 as the value (index is zero-based)
        $(this).text(index + 1);
    });
}

function removeClient(button) {
    var row = button.closest('tr');
    var tableBody = row.parentNode;
    // Check if there is more than one row before removing
    if (tableBody.children.length > 1) {
        tableBody.removeChild(row);
    }

    // Select all elements with the class "rowCount"
    var rowCountElements = $(".rowCount");

    // Loop through each element and assign ascending values
    rowCountElements.each(function (index) {
        // Assigning index + 1 as the value (index is zero-based)
        $(this).text(index + 1);
    });
}




// function removePeople(button) {
//     var row = button.closest('tr');
//     var tableBody = row.parentNode;
//     // Check if there is more than one row before removing
//     if (tableBody.children.length > 1) {
//         tableBody.removeChild(row);
//     }
// }






function checkOperatorType() { }
function aggregratorYes() {
    // $("#aggregrator").removeAttr(readonly)
    $("#aggregrator").attr("readonly", false);
}
function aggregratorNo() {
    $("#aggregrator").val("NULL");
    $("#aggregrator").attr("readonly", true);
}

function cyclePermission() {
    var daily = $("input[id=cycleDaily]:checked").val();
    if (typeof daily === "undefined") {
        // $("#changeCycleDailyPermission").addClass("gu-hide");
        $("#changeCycleDaily").attr("disabled", true);
    } else {
        // $("#changeCycleDailyPermission").removeClass("gu-hide");
        $("#changeCycleDaily").attr("disabled", false);
    }
    var weekly = $("input[id=cycleWeekly]:checked").val();
    console.log(weekly);
    if (typeof weekly === "undefined") {
        // $("#changeCycleWeeklyPermission").addClass("gu-hide");
        $("#changeCycleWeekly").attr("disabled", true);
    } else {
        // $("#changeCycleWeeklyPermission").removeClass("gu-hide");
        $("#changeCycleWeekly").attr("disabled", false);
    }
    var monthly = $("input[id=cycleMonthly]:checked").val();
    if (typeof monthly === "undefined") {
        // $("#changeCycleMonthlyPermission").addClass("gu-hide");
        $("#changeCycleMonthly").attr("disabled", true);
    } else {
        // $("#changeCycleMonthlyPermission").removeClass("gu-hide");
        $("#changeCycleMonthly").attr("disabled", false);
    }
}
function freemiumYes() {
    // $("#freemiumSelect").removeClass("gu-hide");
    $("#freemiumDays").attr("disabled", false);
}
function freemiumNo() {
    $("#freemiumDays").val("0"); // Set the value of the select element to an empty string
    $("#freemiumDays").prop("disabled", true); // Disable the select element
}

function toggleNote() {
    var isGoLive = document.getElementById("golivecheck").value;
    var noteTextarea = document.getElementById("note");
    if (isGoLive === "no") {
        noteTextarea.disabled = true;
        noteTextarea.value ='NULL';
    } else {
        noteTextarea.disabled = false;
        noteTextarea.value ='';
    }
}






function serviceEdit() {
    var country = $("#country").val();
    if (country == "") {
        $("#errorcountry").removeClass("gu-hide");
        return false;
    } else {
        $("#errorcountry").addClass("gu-hide");
    }
    var company = $("#company").val();
    if (company == "") {
        $("#errorcompany").removeClass("gu-hide");
        return false;
    } else {
        $("#errorcompany").addClass("gu-hide");
    }
    var operator = $("#operator").val();
    if (operator == "") {
        $("#erroroperator").removeClass("gu-hide");
        return false;
    } else {
        $("#erroroperator").addClass("gu-hide");
    }

    var servicename = $("#servicename").val();
    if (servicename == "") {
        $("#errorservicename").removeClass("gu-hide");
        return false;
    } else {
        $("#errorservicename").addClass("gu-hide");
    }
    var subkeyword = $("#subkeyword").val();
    if (subkeyword == "") {
        $("#errorsubkeyword").removeClass("gu-hide");
        return false;
    } else {
        $("#errorsubkeyword").addClass("gu-hide");
    }
    var short_code = $("#short_code").val();
    if (short_code == "") {
        $("#errorshort_code").removeClass("gu-hide");
        return false;
    } else {
        $("#errorshort_code").addClass("gu-hide");
    }

    [];
}



// When the user clicks on the button, open the modal
$("#operatordj").click(function () {
    $("#commonModaldj").modal('show');
});

$("#statuschange").click(function () {
    $("#commonstatus").modal('show');
});

$(".golive").click(function () {
    $("#golivecontent").modal('show');
});

$(document).ready(function(){

    $('#newstatus').click(function () {
        var status = $("input[name='commonstatus']:checked").val();
        if (!status) {
            alert('Please Check Message');
            return false;
        }
        var serviceId = $(this).data('service-id');
        var url = base_url + "service/update/status/" + serviceId;
        console.log(serviceId);
        $.ajax({
            url: url,
            data: { id: serviceId },
            type: "POST",
            success: function(response) {
                if (response.success) {
                    alert("Service Status changed successfully!");
                        window.location.href = response.redirect;
                } else {
                    alert("Error: " + response.error);
                }
            },
            error: function(xhr, status, error) {
                alert("Error: " + error);
            },
            complete: function() {
                $('#commonstatus').modal('hide');
            }
        });


    });
});


$(document).ready(function(){

    $('#golivedata').click(function () {
        var Note = $("#note").val();
        console.log(Note);
        var Golive =  document.getElementById("golivecheck").value;
        console.log(Golive);
        if (Note == '' && Golive === 'yes' ) {
            alert('Please Write Note');
            return false;
        }

        var serviceId = $(this).data('service-id');

        var url = base_url + "service/golive";
        console.log(serviceId);
        $.ajax({
            url: url,
            data: { id: serviceId, note:Note, golive:Golive},
            type: "POST",
            success: function(response) {
                if (response.success) {
                    alert("Go live Date Add Successfully");
                        window.location.href = response.redirect;
                } else {
                    alert("Error: " + response.error);
                }
            },
            error: function(xhr, status, error) {
                alert("Error: " + error);
            },
            complete: function() {
                $('#golivecontent').modal('hide');
            }
        });


    });
});

$("#newOperatorSave").click(function () {

    var url = base_url + "service/operator/create";
    // var country = $("#country").val();
    // if (country == "") {
    //     alert('Please select country')
    //     return false;
    // }
    // var operator = $('#ScOperator').val();
    var operatorName = $("#operatorName").val();
    if (operatorName == "") {
        alert('Please enter operator name');
        return false;
    }

    $.ajax({
        url: url,
        data: {
            // country: country,
            // operator: operator,
            operatorName: operatorName,
        },
        type: "POST",
        success: function (response) {
            console.log(response);
            $("#newOperator").val(response.operator_name); // Set the value of the input field
            console.log(response);
        },
    });
    $('#commonModaldj').modal('hide');
});

function operaterSelect() {
    var operator = $('#ScOperator').val();
    if (operator != '') {
        $("#operatorName").attr("readonly", true);
    } else {
        $("#operatorName").attr("readonly", false);
    }
}

function pivotUserSubmit() {

    var type = $("input[name='type[]']:checked").val();
    if (typeof type === "undefined") {
        $("#errortype").removeClass("gu-hide");
        return false;
    } else {
        $("#errortype").addClass("gu-hide");
    }
    var data = $("input[name='data[]']:checked").val();
    if (typeof data === "undefined") {
        $("#errordata").removeClass("gu-hide");
        return false;
    } else {
        $("#errordata").addClass("gu-hide");
    }
    var data = $("#date").val();
    if (data == '') {
        $("#errordate").removeClass("gu-hide");
        return false;
    } else {
        $("#errordate").addClass("gu-hide");
    }
}
function productSubmit() {
    var name = $("#name").val();
    if (name == "") {
        $("#errorname").removeClass("gu-hide");
        return false;
    } else {
        $("#errorname").addClass("gu-hide");
    }
    var doman = $("#doman").val();
    if (doman == "") {
        $("#errordoman").removeClass("gu-hide");
        return false;
    } else {
        $("#errordoman").addClass("gu-hide");
    }
    var analytical_id = $("#analytical_id").val();
    if (analytical_id == "") {
        $("#erroranalytical_id").removeClass("gu-hide");
        return false;
    } else {
        $("#erroranalytical_id").addClass("gu-hide");
    }
}




function addRow() {
    var table = document.getElementById("clientTable").getElementsByTagName('tbody')[0];
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
    var number = rowCount + 1;

    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var cell5 = row.insertCell(4);

    cell1.innerHTML = number;
    cell2.innerHTML = '<input type="text" class="form-control fild-Style" style="height: 39px;" name="client_name[]" placeholder="Name">';
    cell3.innerHTML = '<input type="email"  class="form-control fild-Style" style="height: 39px;" name="client_email[]" placeholder="Email">';
    cell4.innerHTML = '<input type="number"  class="form-control fild-Style" style="height: 39px;" name="client_whatsapp[]" placeholder="Whatsapp Number">';
    cell5.innerHTML = '<button type="button" class="delete-btn form-control fild-Style" style="height: 39px;" data-title="Delete" onclick="removePeople(this)"><i class="fa fa-trash" style="color: red"></i></button>';
}

function addTelco() {
    var table = document.getElementById("telcoTable").getElementsByTagName('tbody')[0];
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
    var number = rowCount + 1;

    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var cell5 = row.insertCell(4);

    cell1.innerHTML = number;
    cell2.innerHTML = '<input type="text" class="form-control fild-Style" style="height: 39px;" name="telco_name[]" placeholder="Name">';
    cell3.innerHTML = '<input type="email" class="form-control fild-Style" style="height: 39px;" name="telco_email[]" placeholder="Email">';
    cell4.innerHTML = '<input type="number" class="form-control fild-Style" style="height: 39px;" name="telco_whatsapp[]" placeholder="Whatsapp Number">';
    cell5.innerHTML = '<button type="button" class="delete-btn form-control fild-Style" style="height: 39px;" data-title="Delete" onclick="removePeople(this)"><i class="fa fa-trash" style="color: red"></i></button>';
}

function updateFileName(input) {
    var fileName = input.files[0].name;
    document.getElementById('file-selected').textContent = fileName;
    var stop  = document.getElementById('display7');
   stop.style.display="none";
}
function updateFileName1(input) {
    var fileName1 = input.files[0].name;
    document.getElementById('file-select').textContent = fileName1;
    var stop  = document.getElementById('display6');
   stop.style.display="none";
}
function updateFileName2(input) {
    var fileName2 = input.files[0].name;
    document.getElementById('file-select1').textContent = fileName2;
    var stop  = document.getElementById('display5');
   stop.style.display="none";
}
function updateFileName3(input) {
    var fileName3 = input.files[0].name;
    document.getElementById('file-select2').textContent = fileName3;
    var stop  = document.getElementById('display4');
   stop.style.display="none";
}
function updateFileName4(input) {
    var fileName4 = input.files[0].name;
    document.getElementById('file-select3').textContent = fileName4;
    var stop  = document.getElementById('display3');
   stop.style.display="none";
}
function updateFileName5(input) {
    var fileName5 = input.files[0].name;
    document.getElementById('file-select4').textContent = fileName5;
    var stop  = document.getElementById('display2');
   stop.style.display="none";
}
function updateFileName6(input) {
    var fileName6 = input.files[0].name;
    document.getElementById('file-select5').textContent = fileName6;
   var stop  = document.getElementById('display1');
   stop.style.display="none";
}
var baseUrl = window.location.origin + "/";

function Email(selectElement) {
    var user_Id = selectElement.value;

    var selectId = selectElement.id;

    var number = selectId.substring(10); // Extract the number from the ID

    $.ajax({
        type: "POST",
        url: baseUrl + "api/user/email",
        data: { 'userid': user_Id },
        dataType: "json",
        success: function (responses) {
            if (responses.data.email === '') {
                console.log(responses.data.email);
                $("#selectEmail" + number).val('');
            } else {
                // Set the value to the retrieved email
                $("#selectEmail" + number).val(responses.data.email);
            }
        },
    });

}



function Operators(){
    var e = document.getElementById("country");
    var value = e.value;
    console.log(value);



    var company = 'allcompany';

    $.ajax({
      type: "POST",
      url: baseUrl+"report/user/filter/operator",
      data: {'id':value,'company':company},
      dataType: "json",
      success: function (responses) {
        document.getElementById('operator').innerHTML = '<option value="">Operator Name</option>';

        $.each(responses, function(index,response){
            $("#operator").append('<option value="'+response.operator_name+'" >'+response.operator_name+'</option>');
        });
      },
    });
  }


  function updateEndDateOptions() {
    var startDate = document.getElementById('start_date').value;
    var endDateInput = document.getElementById('end_date');
    var endDate = endDateInput.value;

    // Disable weekends and dates before the selected start date
    var minDate = startDate;
    endDateInput.min = minDate;
    // Reset end date if it's before the new min date
    if (endDate < minDate) {
        endDateInput.value = minDate;
    }
}

function toggleOperatorInput() {
    var existingRadio = document.getElementById("existingRadio");
    var newRadio = document.getElementById("operatordj");
    var existingOperatorSelect = document.getElementById("existingOperatorSelect");
    var newOperatorInput = document.getElementById("newOperatorInput");

    if (existingRadio.checked) {
        existingOperatorSelect.style.display = "block";
        newOperatorInput.style.display = "none";
    } else if (newRadio.checked) {
        existingOperatorSelect.style.display = "none";
        newOperatorInput.style.display = "block";
    }
}

// Attach event listeners to radio buttons
document.getElementById("existingRadio").addEventListener("click", toggleOperatorInput);
document.getElementById("operatordj").addEventListener("click", toggleOperatorInput);

// Trigger initial visibility based on default selection
toggleOperatorInput();
