let token = _('token');
let user_ids = {};
let user_names = {};
let currentSalary = null;
let hiddenIds = _('hiddenIds');
let hiddenNames = _('hiddenNames');

function updateSalary(user_id,id){
    let salaryElement = _('salary'+id);
    let salary = salaryElement.value;
    let name = _('name'+id).innerHTML;
    //validate salary
    if(salary.search(/^[0-9]+\.?[0-9]+$/) > -1){ //valid salary
        //send ajax request
        ajaxRequest('responses/manage_salary.rsp.php', manageSalaryRsp,'receiver='+user_id+'&salary='+salary+'&token='+token.value+'&category=updatesalary&name='+name+'&id='+id);
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
    ajaxRequest('responses/manage_salary.rsp.php', manageSalaryRsp,'receiver='+user_id+'&salary='+salary+'&token='+token.value+'&category=approvesalary&name='+name+'&id='+id);
}

function validateSalary(user_id,id,e){
    let salary = e.value;
    if(currentSalary !== salary){
        //validate salary
        if(salary.search(/^[0-9]+\.?[0-9]+$/) > -1){ //valid salary
            //store the id and names that needs update;
            user_ids[user_id]=salary;
            user_names[user_id] = _('name'+id).innerHTML;
        }else{
            alert('Invalid amount entered for Salary');
            e.focus = true;
        }
    }
}

function setvalidUserIds(e){
    currentSalary = e.value;
}

function manageSalaryRsp(){
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    if(rsp.success){
        if(rsp.category === 'updatesalary'){
            _('approval'+rsp.id).innerHTML = '<button class="actioncolumn" onclick="approveSalary(\''+rsp.receiver+'\','+rsp.id+')">request approval</button>';
            alert('Salary updated successfully. Confirmatory request sent to the accountant');
        }else{
            if(rsp.code === 0){
                alert('Confirmatory request successfully sent to the accountant');
            }else{
                alert('Request already sent');
            }
        }
    }
}

function fillIds(){
    hiddenIds.value = JSON.stringify(user_ids);
    hiddenNames.value = JSON.stringify(user_names);
    return true;
}