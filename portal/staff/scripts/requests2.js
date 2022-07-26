let token = _('token');
let genMsg = _('genMsg');
let main = document.querySelector('main');
let row_id;
let other = _('other').innerHTML;
function accept(id,requester_id,category){
    if(confirm('This will accept the request')){
        row_id = id;
        ajaxRequest('responses/requests.rsp.php', requestRsp,'id='+id+'&requester_id='+requester_id+'&category='+category+'&other='+other+'&confirm=true&token='+token.value);
    } 
}

function decline(id,requester_id,category){
    if(confirm('This will decline the request')){
        row_id = id;
        ajaxRequest('responses/requests.rsp.php', requestRsp,'id='+id+'&requester_id='+requester_id+'&category='+category+'&other='+other+'&confirm=false&token='+token.value);
    }
    
}

function requestRsp(){
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    let divRow = _('row'+row_id);
    if(rsp.confirm){ //handles response for accepted requests
        main.removeChild(divRow);  //remove the div from the page
        alert('You have successfully approve the request');
    }else{
        main.removeChild(divRow);  //remove the div from the page
        alert('You have successfully declined the request');
    }
    
}
