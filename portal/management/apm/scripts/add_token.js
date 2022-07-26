let name = _('name');
let school = _('school');
let level = _('level');
let token = _('token');
let showLevel = _('showLevel');
let rank = 11;

let nameMsg = _('nameMsg');
let genMsg = _('genMsg');

let valName = false;

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

level.addEventListener('change', changeLevel);


function changeLevel(){
    showLevel.innerHTML = 'Level '+ level.value;
}
school.addEventListener('change',changeSchool);

function changeSchool(){
   let genHtml = '';
   let sch_abbr = _('school').value
   let levels = getLevels(sch_abbr);
   for(let lev in levels){
       genHtml += '<option value="'+levels[lev]+'">'+lev+'</option>';
   }
   level.innerHTML = genHtml;
   showLevel.innerHTML = 'Level 1';
}

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


function addToken(){
    //event.preventDefault();
    if(valName){
        ajaxRequest('responses/add_token.rsp.php', addTokenRsp,'name='+name.value+'&rank='+rank+'&sch_abbr='+school.value+'&token='+token.value+'&level='+level.value);
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


