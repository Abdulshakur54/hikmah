let form = document.querySelector('form');
let decision = _('decision');


function acceptAdmission(){
    if(confirm('Confirm you want to accept our offer')){
        decision.value = 'accept';
        form.submit();
    }
    
}

function declineAdmission(){
    if(confirm('Confirm you want to decline our offer\nYou would not be able to accept it after declination')){
        decision.value = 'decline';
        form.submit();
    }
    
}

function downloadAdmission(){
    decision.value = 'download';
    form.submit();
    
}