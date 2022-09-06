var counter = _('counter');
var form = _('form');
var submitType = _('submittype');
var studentIds = _('studentIds');

function submitForm(){
     getPostPage("studentForm", "management/hos/assign_student.php");
}


async function confirmSubmission(){
    if(await swalConfirm('Do you want to proceed with assignment','question')){
        studentIds.value = popId().toString();
        submitType.value = 'assign';
        if(studentIds.value.length === 0){
            swalNotifyDismiss('Make a selection','info');
        }else{
            getPostPage("studentForm", "management/hos/assign_student.php");
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
  $("#studentsTable").DataTable(dataTableOptions);
  if ($(".js-example-basic-single").length) {
    $(".js-example-basic-single").select2();
  }
});
