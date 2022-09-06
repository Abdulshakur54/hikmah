var token = _("token");
var genMsg = _("genMsg");
var main = document.getElementById("requestsContainer");
var row_id;
async function accept(id, requester_id, category) {
  if (await swalConfirm("Confirm you want to accept request", "info")) {
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
    //handles response for accepted requests
    main.removeChild(divRow); //remove the div from the page
    swalNotifyDismiss(
      "You have successfully approve the request",
      "success",
      2000
    );
  } else {
    main.removeChild(divRow); //remove the div from the page
    swalNotifyDismiss(
      "You have successfully declined the request",
      "success",
      2000
    );
  }
}
