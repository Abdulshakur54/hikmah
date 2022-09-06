var counter = _('counter');
var form = _('form');
var submitType = _('submittype');
var subjectIds = _('subjectIds');

 async function confirmSubmission(){
    if(await swalConfirm('Do you want to proceed to deregister selected subjects','question')){
        subjectIds.value = popId().toString();
        submitType.value = 'deregister';
        if(subjectIds.value.length === 0){
            swalNotify('Make a selection first','warning');
        }else{
            getPostPage("subDeRegForm", "student/unreg_sub.php");
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

$(document).ready(function () {
  $("#subjectTable").DataTable(dataTableOptions);
});

