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
  // $("#page").block({
  //   message: "<div>Loading...</div>",
  //   css: {
  //     display: "flex",
  //     width: "100%",
  //     height: "100vh",
  //     justifyContent: "center",
  //     alignItems: "center",
  //     border:'none',
  //     fontWeight:'bold'
  //   },
  // });
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
  // $('#page').unblock();
}

function loadPage() {
  const rsp = xmlhttp.responseText;
  $("#page").html(rsp);
  const notCount = parseInt(_("notCount").value);
  const reqCount = parseInt(_("reqCount").value);
  if (notCount > 0) {
    _("notificationLink").style.display = "block";
    _("notificationCount").innerHTML = notCount;
  } else {
    _("notificationLink").style.display = "none";
  }
  if (reqCount > 0) {
    _("requestLink").style.display = "block";
    _("requestCount").innerHTML = reqCount;
  } else {
    _("requestLink").style.display = "none";
  }
}

setTimeout(function () {
  _("welcomeMessage").style.visibility = "hidden";
}, 5000);

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

// function typeheadInput(elementId, dataSource) {
//   const engine = new Bloodhound({
//     datumTokenizer: Bloodhound.tokenizers.whitespace,
//     queryTokenizer: Bloodhound.tokenizers.whitespace,
//     local: dataSource,
//   });

//   $("#" + elementId).typeahead(
//     {
//       hint: true,
//       highlight: true,
//       minLength: 1,
//     },
//     {
//       name: elementId + "_name",
//       source: engine,
//     }
//   );
// }

function getPostPage(formId, url) {
  let formvalues = getFormData(formId);
  getPage(url, formvalues);
}

/**
 *
 * @param {*} formId
 * @param {*} url
 * @param {*} hiddenFields
 * @param {*} reload
 * @param {*} returnResponse  'response would only be returned when reload is set to false'
 * @returns
 */
async function getPostPageWithUpload(
  formId,
  url,
  hiddenFields,
  reload = true,
  returnResponse = false
) {
  // reload &&
  //   $("#page").block({
  //     message: "<div>Loading...</div>",
  //     css: {
  //       display: "flex",
  //       width: "100%",
  //       height: "100vh",
  //       justifyContent: "center",
  //       alignItems: "center",
  //       border: "none",
  //       fontWeight: "bold",
  //     },
  //   });
  let form = _(formId);
  let formData = new FormData(form);
  hiddenFields = Object.entries(hiddenFields);
  for (let hiddenField of hiddenFields) {
    formData.append(hiddenField[0], hiddenField[1]);
  }
  formData.append("page_token", _("page_token").value);
  let rsp = await fetch(url, {
    method: "POST",
    body: formData,
  });

  rsp = await rsp.json();
  let status = rsp.status;
  _("token").value = rsp.token;
  if (!reload && returnResponse) {
    return rsp;
  } else {
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
}

function convertStringToHTML(text) {
  let domObj = new DOMParser();
  let htmlDoc = domObj.parseFromString(text, "text/html");
  return htmlDoc.body.childNodes[0];
}
