var form = _("scoresForm");
var updatedData = _("updateddata");
var scrIds = [];
var hasProject;
var tokenElement = _("token");
var msgDiv = _("genMsg");
var importMsgDiv = _("importMsg");
var subid = _("subid");
var scoreColumns = JSON.parse(_("scorecolumns").innerHTML);

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
  responsive: false,
  columnDefs: [{ responsivePriority: 1, targets: 1 }],
  fnRowCallback: function (nRow, aData, iDisplayIndex) {
    $("td:first", nRow).html(iDisplayIndex + 1);
    return nRow;
  },
};

$(document).ready(function () {
  scoreTable = $("#scoresTable").DataTable(dataTableOptions);
});

function runFirst() {
  hasProject = _("hasProject").value;
}

function update(scrId) {
  if (!scrIds.includes(scrId)) {
    scrIds.push(scrId);
  }
}

function convertToValidFloat(x) {
  if (x !== "") {
    return parseFloat(x);
  }
}

async function saveData() {
  importMsgDiv.innerHTML = ""; //clears any message associated with import if there is
  if (validate("scoresForm", { validateOnSubmit: true })) {
    if (
      await swalConfirm("Are you sure you want to save changes", "question")
    ) {
      let updData = [];
      for (let x of scrIds) {
        let scrArr = []; //to hold all the scores as an array
        for (let y of scoreColumns) {
          scrArr.push(convertToValidFloat(_(y + "_" + x).value));
        }
        updData.push([x, scrArr]);
      }
      let updDataObj = {};
      updDataObj["updatedData"] = updData;
      let postData =
        "updatedData=" +
        JSON.stringify(updDataObj) +
        "&subid=" +
        _("subid").value +
        "&token=" +
        tokenElement.value;
      ld_startLoading("save", "save_loader");
      ajaxRequest("staff/responses/update_scores.rsp.php", saveRsp, postData);
    }
  } else {
    swalNotify("Invalid scores entered into Scores Table", "warning");
  }
}

function saveRsp() {
  let rsp = JSON.parse(xmlhttp.responseText);
  tokenElement.value = rsp.token;
  ld_stopLoading("save", "save_loader");
  let msg;
  switch (rsp.statuscode) {
    case 1:
      msg = '<div class="success">Changes saved successfully</div>';
      swalNotifyDismiss(msg, "success", 2000);
      break;
    case 0:
      let errors = rsp.errors;
      msg = "";
      for (let x of errors) {
        msg += '<div class="failure">' + x + "</div>";
      }
      swalNotify(msg, "error");
      break;
  }
}

function importFile(obj) {
  if (!window.FormData) {
    importMsgDiv.innerHTML =
      '<div class="failure">Upgrade your browser to be able to upload files</div>';
  } else {
    const formData = new FormData();
    const filesArray = obj.files;
    if (filesArray.length > 0) {
      formData.append("uploadedFile", filesArray[0]);
      formData.append("token", tokenElement.value);
      formData.append("subid", _("subid").value);
      importMsgDiv.innerHTML = "";
      ld_startLoading("importTrigger", "import_loader");
      ajaxRequestWithUpload(
        "staff/responses/import_scores.rsp.php",
        uploadScoresRsp,
        formData
      );
    } else {
      importMsgDiv.innerHTML = '<div class="failure">Choose a file first</div>';
    }
  }
}

function uploadScoresRsp (){
  let rsp = JSON.parse(xmlhttp.responseText);
  tokenElement.value = rsp.token;
  ld_stopLoading("importTrigger", "import_loader");
  switch (rsp.statuscode) {
    case 1:
      getPage("staff/scores.php?subid=" + subid.value);
      break;
    case 0:
      let errors = rsp.errors;
      let msg = "";
      for (let x of errors) {
        msg += '<div class="failure">' + x + "</div>";
      }
      importMsgDiv.innerHTML = msg;
      break;
  }
};

function downloadScores (){
  location.assign(
    "staff/download_scripts/scores.php?subid=" + subid.value + "&download=true"
  );
};

runFirst();
