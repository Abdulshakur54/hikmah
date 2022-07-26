let name = _('name');
let position = _('position');
let salary = _('salary');
let schoolDiv = _('schoolDiv');
let token = _('token');

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

position.addEventListener('change',function(){
    let genHtml = '<label>School</label><div><select id="school" onchange="schoolChange()">';
    let schools;
    switch(position.value){
        case '5':
            schools = getConvectionalSchools();
            for(let school in schools){
                genHtml+='<option value="'+schools[school]+'">'+school+'</option>';
            }
            genHtml+='</select></div>';
            sch_abbr = 'HCK';
        break;
        case '17':
           schools = getIslamiyahSchools();
           for(let school in schools){
               genHtml+='<option value="'+schools[school]+'">'+school+'</option>';
           }
           genHtml+='</select></div>';
           sch_abbr = 'HM';
        break;
        default:
            genHtml='';
            sch_abbr = 'All';
    }
    schoolDiv.innerHTML = genHtml;
});

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
        },salaryMsg,1);
    }
);


function schoolChange(event){
    sch_abbr = _('school').value;
}

function addToken(){
    //event.preventDefault();
    if(valName && valSalary){
        ajaxRequest('responses/add_token.rsp.php', addTokenRsp,'name='+name.value+'&rank='+position.value+'&sch_abbr='+sch_abbr+'&token='+token.value+'&salary='+salary.value);
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


