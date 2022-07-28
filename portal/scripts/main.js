let pageToken = document.getElementById("page_token");
const dataTableOptions = {
  pageLength: 10,
  lengthChange: true,
  lengthMenu: [
    [10, 25, 50, -1],
    [10, 25, 50, "All"],
  ],
  responsive: true,
  fnRowCallback: function (nRow, aData, iDisplayIndex) {
    $("td:first", nRow).html(iDisplayIndex + 1);
    return nRow;
  },
};
function getPage(url) {
  if (url.indexOf("?") === -1) {
    url += "?page_token=" + _("page_token").value;
  } else {
    url += "&page_token=" + _("page_token").value;
  }
  ajaxRequest(url, loadPage);
}

function loadPage() {
  const rsp = xmlhttp.responseText;
 $('#page').html(rsp);
}

function getAltPage(altPage) {
  getPage(altPage);
}

function swalNotify(message, icon) {
  Swal.fire({
    text: message,
    icon: icon,
    allowOutsideClick: false,
  });
}

function swalNotifyDismiss(message, icon) {
  Swal.fire({
    text: message,
    icon: icon,
    showConfirmButton: false,
    allowOutsideClick: false,
    timer: 1700,
  });
}

async function swalConfirm(message, icon) {
  const resp = await Swal.fire({
    text: message,
    icon: icon,
    showCancelButton: true,
    allowOutsideClick: false,
  });
  return resp.isConfirmed;
}

function emptyInputs(input) {
  if (Array.isArray(input)) {
    for (let x of input) {
      _(x).value = "";
    }
  } else {
    _(input).value = "";
  }
}

function clearHTML(elementId) {
  _(elementId).innerHTML = "";
}

