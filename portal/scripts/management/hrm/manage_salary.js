var token = _('token');
var user_ids = {};
var user_names = {};
var currentSalary = null;
var hiddenIds = _('hiddenIds');
var hiddenNames = _('hiddenNames');

async function updateSalary(user_id,id){
    let salaryElement = _('salary'+id);
    let salary = salaryElement.value;
    let name = _('name'+id).innerHTML;
    //validate salary
    if(salary.search(/^[0-9]+\.?[0-9]+$/) > -1){ //valid salary
        //send ajax request
        ajaxRequest('management/hrm/responses/manage_salary.rsp.php', manageSalaryRsp,'receiver='+user_id+'&salary='+salary+'&token='+token.value+'&category=updatesalary&name='+name+'&id='+id);
    }else{
        alert('Invalid amount entered for Salary');
        salaryElement.setFocus = true;
    }
}



function approveSalary(user_id, id){
    let salaryElement = _('salary'+id);
    let salary = salaryElement.value;
    let name = _('name'+id).innerHTML;
    //send ajax request
    ajaxRequest('management/hrm/responses/manage_salary.rsp.php', manageSalaryRsp,'receiver='+user_id+'&salary='+salary+'&token='+token.value+'&category=approvesalary&name='+name+'&id='+id);
}


function manageSalaryRsp() {
  let rsp = JSON.parse(xmlhttp.responseText);
  token.value = rsp.token;
  if (rsp.success) {
    if (rsp.category === "updatesalary") {
      _("approval" + rsp.id).innerHTML =
        '<button class="btn btn-primary btn-md p-2" onclick="approveSalary(\'' +
        rsp.receiver +
        "'," +
        rsp.id +
        ')">request approval</button>';
      swalNotify(
        "Salary updated successfully. Confirmatory request sent to the accountant",
        "success"
      );
    } else {
      if (rsp.code === 0) {
        swalNotify(
          "Confirmatory request successfully sent to the accountant",
          "success"
        );
      } else {
        swalNotify("Request already sent", "success");
      }
    }
  }
}


$(document).ready(function () {
  $("#manageSalaryTable").DataTable(dataTableOptions);
});