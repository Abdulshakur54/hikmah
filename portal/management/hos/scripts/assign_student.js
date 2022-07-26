let counter = _('counter');
let form = _('form');
let submitType = _('submittype');
let studentIds = _('studentIds');

function submitForm(){
    form.submit();
}


function confirmSubmission(){
    if(confirm('Do you want to proceed with assignment')){
        studentIds.value = popId().toString();
        submitType.value = 'assign';
        if(studentIds.value.length === 0){
            alert('Make a selection');
        }else{
            form.submit();
        }
        
    }
}


function checkAll(event){
    let count = counter.value;
    if(event.checked){
        for(let i=1;i<=count;i++){
           _('chk'+i).checked = true;
        }
    }else{
        for(let i=1;i<=count;i++){
           _('chk'+i).checked = false;
        }
    }
}

function popId(){
    let count = counter.value;
    let valObj = [];
    for(let i=1;i<=count;i++){
        if(_('chk'+i).checked){
           valObj.push(_('chk'+i).value); 
        }
    }
    return valObj;
}
