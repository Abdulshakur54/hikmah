var token = _("token");
var genMsg = _("genMsg");
var main = document.getElementById("requestsContainer");
var row_id;
async function accept(id, requester_id, category) {
  if (await swalConfirm("Confirm you want to accept request", "info")) {
      ld_startLoading("accept_" + id, "ld_loader_" + id);
    row_id = id;
    let other = _("other" + id).innerHTML;
    ajaxRequest(
      "staff/responses/requests.rsp.php",
      requestRsp,
      "id=" +
        id +
        "&requester_id=" +
        requester_id +
        "&category=" +
        category +
        "&other=" +
        other +
        "&confirm=true&token=" +
        token.value
    );
  }
}

async function decline(id, requester_id, category) {
  if (await swalConfirm("This will decline the request", "info")) {
      ld_startLoading("decline_" + id, "ld_loader_" + id);
    row_id = id;
    let other = _("other" + id).innerHTML;
    ajaxRequest(
      "staff/responses/requests.rsp.php",
      requestRsp,
      "id=" +
        id +
        "&requester_id=" +
        requester_id +
        "&category=" +
        category +
        "&other=" +
        other +
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
      ld_stopLoading('accept_'+id,'ld_loader_'+id);
    //handles response for accepted requests
    main.removeChild(divRow); //remove the div from the page
    swalNotify(
      "You have successfully approve the request",
      "success",
      2000
    );
  } else {
      ld_stopLoading('decline_'+id,'ld_loader_'+id);
    main.removeChild(divRow); //remove the div from the page
    swalNotify(
      "You have successfully declined the request",
      "success",
      2000
    );
  }
}
