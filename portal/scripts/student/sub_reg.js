var counter = _('counter');
var form = _('form');
var submitType = _('submittype');
var subjectIds = _('subjectIds');

 function confirmSubmission(){
    if(confirm('Do you want to proceed with Registeration')){
        subjectIds.value = popId().toString();
        submitType.value = 'register';
        if(subjectIds.value.length === 0){
            swalNotify('Make a selection first','warning');
        }else{
           getPostPage('subRegForm');
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

