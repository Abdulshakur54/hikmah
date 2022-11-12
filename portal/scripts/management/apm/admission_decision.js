var acceptAdm = _("acceptAdm");
var declineAdm = _("declineAdm");
var token = _("token");
var form = _("form");
var table = $("#admissionTable").DataTable(dataTableOptions);

acceptAdm.addEventListener("click", acceptAdmission);
declineAdm.addEventListener("click", declineAdmission);
async function acceptAdmission() {
  let idObj = { id: popId() };
  if (idObj.id.length > 0) {
    if (
      await swalConfirm(
        "This will Admit the selected applicants to the school",
        "warning"
      )
    ) {
      let postData =
        "idarr=" +
        JSON.stringify(idObj) +
        "&token=" +
        token.value +
        "&type=accept";
      ld_startLoading(["acceptAdm", "declineAdm"]);
      ajaxRequest(
        "management/apm/responses/admission_decision.rsp.php",
        acceptAdmRsp,
        postData
      );
    }
  } else {
    swalNotify("No applicant selected", "warning");
  }

  async function acceptAdmRsp() {
    ld_stopLoading(["acceptAdm", "declineAdm"]);
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    if (rsp.success) {
      let ids = idObj.id;
      for (let id of ids) {
        let formattedId = id.toString().replaceAll("/", "_");
        let new_status_data =
          '<span class="text-success font-weight-bold">offered</span>';
        let new_chk_data = "";
        let new_reset_data =
          '<button class="btn btn-sm btn-secondary" onclick="resetDecision(\'' +
          id +
          '\')" id="reset_btn_' +
          formattedId +
          '">reset decision</button><span id="ld_loader_' +
          formattedId +
          '"></span>';
        table.cell("#status_" + formattedId).data(new_status_data);
        table.cell("#check_" + formattedId).data(new_chk_data);
        table.cell("#reset_" + formattedId).data(new_reset_data);
      }
      table.draw();
      swalNotify(
        "Operation was successful\nAn email has been sent to the selected students to respond to the offer",
        "success"
      );
    } else {
      swalNotify("Operation was not successful", "danger");
    }
  }
}

async function declineAdmission() {
  let idObj = { id: popId() };
  if (idObj.id.length > 0) {
    if (
      await swalConfirm(
        "This will decline the admission request of the selected students",
        "warning"
      )
    ) {
      let postData =
        "idarr=" +
        JSON.stringify(idObj) +
        "&token=" +
        token.value +
        "&type=decline";
      ld_startLoading("declineAdm");
      ajaxRequest(
        "management/apm/responses/admission_decision.rsp.php",
        declineAdmRsp,
        postData
      );
    }
  } else {
    swalNotify("No applicant selected", "warning");
  }

  async function declineAdmRsp() {
    ld_stopLoading("declineAdm");
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    if (rsp.success) {
      let ids = idObj.id;
      for (let id of ids) {
        let formattedId = id.toString().replaceAll("/", "_");
        let new_status_data =
          '<span class="text-danger font-weight-bold">rejected</span>';
        let new_reset_data =
          '<button class="btn btn-sm btn-secondary" onclick="resetDecision(\'' +
          id +
          '\')" id="reset_btn_' +
          formattedId +
          '">reset decision</button><span id="ld_loader_' +
          formattedId +
          '"></span>';
        let new_chk_data = "";
        table.cell("#status_" + formattedId).data(new_status_data);
        table.cell("#check_" + formattedId).data(new_chk_data);
        table.cell("#reset_" + formattedId).data(new_reset_data);
      }
      table.draw();
      swalNotify(
        "Declination was successful\nThe selected student have been notified of the declination via email",
        "success"
      );
    } else {
      swalNotify("Operation was not successful", "danger");
    }
  }
}

//this function is available for the apm
function populateLevel(e) {
  let genHtml = '<option value ="ALL">ALL</option>';
  let levObj = getLevels(e.value);
  if (levObj != null) {
    if (objLength(levObj) > 0) {
      for (let objName in levObj) {
        genHtml +=
          '<option value="' + levObj[objName] + '">' + objName + "</option>";
      }
    }
  }

  //insert the generated html into level select element
  _("level").innerHTML = genHtml;
}

function reSubmit() {
  getPage(
    "management/apm/admission_decision.php?sch_abbr=" +
      _("sch_abbr").value +
      "&level=" +
      _("level").value
  );
}

function popId() {
  let checkElements = document.querySelectorAll("tbody input[type=checkbox]");

  let valObj = [];
  for (let chk of checkElements) {
    if (chk.checked) {
      let inputId = chk.id.replace("chk", "val");
      valObj.push(_(inputId).value);
    }
  }
  return valObj;
}

function checkAll(event) {
  let checkElements = document.querySelectorAll("tbody input[type=checkbox]");
  if (event.checked) {
    for (let chk of checkElements) {
      chk.checked = true;
    }
  } else {
    for (let chk of checkElements) {
      chk.checked = false;
    }
  }
}

async function resetDecision(admId) {
   let formattedId = admId.toString().replaceAll("/", "_");
  if (
    await swalConfirm(
      "Are you sure you want to reset your decision on the selected applicant",
      "question"
    )
  ) {
    ld_startLoading("reset_btn_" + formattedId, "ld_loader_" + formattedId);
    ajaxRequest(
      "management/apm/responses/responses.php",
      resetDecisionResponse,
      "token=" +
        _("token").value +
        "&adm_id=" +
        admId +
        "&op=reset_admission_decision"
    );
  }

  async function resetDecisionResponse() {
    ld_stopLoading("reset_btn_" + formattedId, "ld_loader_" + formattedId);
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    $successStatuses = [200, 201, 204];
    if ($successStatuses.includes(rsp.status)) {
      let new_status_data = '<span class="font-weight-bold">pending</span>';
      let new_chk_data =
        '<input type="checkbox" id="chk' +
        formattedId +
        '" checked /><input type="hidden" id="val' +
        formattedId +
        '" value="' +
        admId +
        '"/>';
      let new_reset_data = "";
      table.cell("#status_" + formattedId).data(new_status_data);
      table.cell("#check_" + formattedId).data(new_chk_data);
      table.cell("#reset_" + formattedId).data(new_reset_data);
      table.draw();
      swalNotify(rsp.message, "success");
    } else {
      swalNotify(rsp.message, "danger");
    }
  }
}

$(".js-example-basic-single").select2();
