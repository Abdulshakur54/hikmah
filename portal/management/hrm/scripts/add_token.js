let name = _('name');
let position = _('position');
let salary = _('salary');
let token = _('token');
let school = _('school');

let nameMsg = _('nameMsg');
let positionMsg = _('positionMsg');
let genMsg = _('genMsg');
let salaryMsg = _('salaryMsg');


let valName = false;
let valSalary;
let sch_abbr='All';

name.addEventListener('blur', function(){
     valName = validate(name,{
        'name':'Name',
        'required':true,
        'pattern':'^[a-zA-z` ]+$',
        'max':30,
        'min':3
        },nameMsg);
    }
);

name.addEventListener('focus',function(){genMsg.innerHTML = '';});

name.addEventListener('keyup', function(){
     valName = instantValidate(name,{
        'name':'Name',
        'required':true,
        'pattern':'^[a-zA-z` ]+$',
        'max':30,
        'min':3
    },nameMsg);
}
);


salary.addEventListener('blur', function(){
     valSalary = validate(salary,{
        'name':'Salary',
        'required':true,
        'pattern':'^[0-9]+\.?[0-9]+$'
        },salaryMsg);
    }
);

salary.addEventListener('keyup', function(){
     valSalary = instantValidate(salary,{
        'name':'Salary',
        'required':true,
        'pattern':'^[0-9]+\.?[0-9]+$'
        },salaryMsg,3);
    }
);


function addToken(){
    //event.preventDefault();
    if(valName && valSalary){
        ajaxRequest('responses/add_token.rsp.php', addTokenRsp,'name='+name.value+'&rank='+position.value+'&sch_abbr='+school.value+'&token='+token.value+'&salary='+salary.value);
    }else{
        genMsg.innerHTML = '<div class="failure">Fill all fields appropriately</div>';
    }
    return false; //to prevent the form from submitting
}

function addTokenRsp(){
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    name.innerHTML='';
    if(rsp.success){
        genMsg.innerHTML = '<div class="success">Pin successfully generated, Registration details below</div>'+'<div class="message">Name: '+name.value+'<br/>Token: '+rsp.createdToken+'</div>';
        name.value = '';
                
    }else{
        genMsg.innerHTML = '<div class="failure">'+rsp.message+'</div>';
    }
}


