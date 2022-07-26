let form = document.querySelector('form');
function acceptAdmission(){
    if(confirm('Confirm you want to accept Admission','Accept','Decline')){
        form.submit();
    }
}

function declineAdmission(){
    if(confirm('Confirm you want to decline your Admission','Decline','Close')){
        form.submit();
    }
}
