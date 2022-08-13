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
  $("#page").html(rsp);
  //  const notCountContainer = _('notificationCount');
  //  const notCount = parseInt(_("notCount").value);
  //  const requestCountContainer = _("requestCount");
  //  const requestCount = parseInt(_("reqCount").value);
  //  notCountContainer.innerHTML = notCount;
  //  requestCountContainer.innerHTML = requestCount;
}

setTimeout(function () {
  _("welcomeMessage").style.display = "none";
},10000);

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

function typeheadInput(elementId, dataSource) {
  const engine = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.whitespace,
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    local: dataSource,
  });

  $("#" + elementId).typeahead(
    {
      hint: true,
      highlight: true,
      minLength: 1,
    },
    {
      name: elementId + "_name",
      source: engine,
    }
  );
}
