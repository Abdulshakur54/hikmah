var pageToken = document.getElementById("page_token");
var dataTableOptions = {
  pageLength: 10,
  lengthChange: true,
  lengthMenu: [
    [10, 25, 50, -1],
    [10, 25, 50, "All"],
  ],
  dom: "Bfrtip",
  buttons: {
    buttons: [
      { extend: "excel", className: "btn btn-secondary btn-sm mb-3" },
      { extend: "pdf", className: "btn btn-secondary btn-sm mb-3" },
    ],
  },
  responsive: true,
  columnDefs: [{ responsivePriority: 1, targets: 1 }],
  fnRowCallback: function (nRow, aData, iDisplayIndex) {
    $("td:first", nRow).html(iDisplayIndex + 1);
    return nRow;
  },
};

function getPage(url, postData = null) {
  $("#page").block({
    message: "<div>Loading...</div>",
    css: {
      display: "flex",
      width: "100%",
      height: "100vh",
      justifyContent: "center",
      alignItems: "center",
      border:'none',
      fontWeight:'bold'
    },
  });
  if (postData == null) {
    //get request
    if (url.indexOf("?") === -1) {
      url += "?page_token=" + _("page_token").value;
    } else {
      url += "&page_token=" + _("page_token").value;
    }
    ajaxRequest(url, loadPage);
  } else {
    //post request
    postData += "&page_token=" + _("page_token").value;
    ajaxRequest(url, loadPage, postData);
  }
  $('#page').unblock();
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
}, 10000);

function getAltPage(altPage) {
  getPage(altPage);
}

function swalNotify(message, icon) {
  Swal.fire({
    html: message,
    icon: icon,
    allowOutsideClick: false,
  });
}

function swalNotifyDismiss(message, icon, timer = 1700) {
  Swal.fire({
    html: message,
    icon: icon,
    showConfirmButton: false,
    allowOutsideClick: false,
    timer,
  });
}

async function swalConfirm(message, icon) {
  const resp = await Swal.fire({
    html: message,
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

function getPostPage(formId, url) {
  let formvalues = getFormData(formId);
  getPage(url, formvalues);
}

async function getPostPageWithUpload(formId, url, op, reload = true) {
  reload &&
    $("#page").block({
      message: "<div>Loading...</div>",
      css: {
        display: "flex",
        width: "100%",
        height: "100vh",
        justifyContent: "center",
        alignItems: "center",
        border: "none",
        fontWeight: "bold",
      },
    });
  let form = _(formId);
  let formData = new FormData(form);
  formData.append("page_token", _("page_token").value);
  formData.append("op", op);
  let rsp = await fetch(url, {
    method: "POST",
    body: formData,
  });

  rsp = await rsp.json();
  let status = rsp.status;
  if (!(status == 200 || status == 201 || status == 204)) {
    swalNotify(rsp.message, "warning");
  } else {
    if (reload) {
     $("#page").unblock();
      location.reload();
    } else {
      swalNotify(rsp.message, "success");
    }
  }
}

function convertStringToHTML(text) {
  let domObj = new DOMParser();
  let htmlDoc = domObj.parseFromString(text, "text/html");
  return htmlDoc.body.childNodes[0];
}
