
let token = _('token');
let tableBody = document.querySelector('tbody');
function deleteToken(id){
    if(confirm('This will delete the selected pin')){ //this is to confirm the user wants to delete
        ajaxRequest('responses/delete_token.rsp.php',deleteTokenRsp,'id='+id+'&token='+token.value);
    }
}


//this is a response to the ajax delete token request
function deleteTokenRsp(){
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    if(rsp.success){
        //remove the token row
        let tableRow = _('row'+rsp.id);
        tableBody.removeChild(tableRow);
    }else{
        alert('unable to delete pin');
    }
}

