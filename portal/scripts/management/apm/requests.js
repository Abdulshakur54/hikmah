var token = _('token');
var genMsg = _('genMsg');
var main = document.getElementById("requestsContainer");
var row_id;
async function accept(id,requester_id,category){
    if(await swalConfirm('This will accept the request','warning')){
        ld_startLoading("accept_" + id, "ld_loader_" + id);
        row_id = id;
        ajaxRequest(
          "management/apm/responses/requests.rsp.php",
          requestRsp,
          "id=" +
            id +
            "&requester_id=" +
            requester_id +
            "&category=" +
            category +
            "&confirm=true&token=" +
            token.value
        );
         ajaxRequest('responses/requests.rsp.php', requestRsp,'id='+id+'&requester_id='+requester_id+'&category='+category+'&confirm=true&token='+token.value);
    } 
}

async function decline(id,requester_id,category){
    if(await swalConfirm('This will decline the request','warning')){
        ld_startLoading("accept_" + id, "ld_loader_" + id);
        row_id = id;
    ajaxRequest(
      "management/apm/responses/requests.rsp.php",
      requestRsp,
      "id=" +
        id +
        "&requester_id=" +
        requester_id +
        "&category=" +
        category +
        "&confirm=false&token=" +
        token.value
    );
    }
    
}

function requestRsp() {
  let rsp = JSON.parse(xmlhttp.responseText);
  token.value = rsp.token;
  let divRow = _("row" + row_id);
  if (rsp.confirm) {
    //handles response for accepted requests
    ld_stopLoading("accept_" + row_id, "ld_loader_" + row_id);
    main.removeChild(divRow); //remove the div from the page
    swalNotify("You have accepted the request", "success");
  } else {
    ld_stopLoading("decline_" + row_id, "ld_loader_" + row_id);
    main.removeChild(divRow); //remove the div from the page
    swalNotify("Request was successfully declined", "success");
  }
}

