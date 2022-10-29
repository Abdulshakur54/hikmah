var table = $("#schemesTable").DataTable(dataTableOptions);
function saveScheme(op) {
  const title = _("title").value;
  const scheme = _("scheme").value;
  const schemeId = _("scheme_id").value;
  const token = _("token");
  const order = _("order").value;
  const term = _("term").value;
  const subId = _("subid").value;
  if (validate("schemeForm", { validateOnSubmit: true })) {
    ld_startLoading("saveBtn");
    ajaxRequest(
      "staff/responses/responses.php",
      handleSaveReq,
      `op=${op}&scheme_id=${schemeId}&scheme=${scheme}&subid=${subId}&title=${title}&order=${order}&term=${term}&token=${token.value}`
    );
  }

  function handleSaveReq() {
    ld_stopLoading("saveBtn");
    const rsp = JSON.parse(xmlhttp.responseText);
    let msgStyleClass = "success m-2";
    let successCode = [200, 201, 204];
    if (!successCode.includes(rsp.status)) {
      msgStyleClass = "failure m-2";
    }
    resetInputStyling("schemeForm", "inputsuccess", "inputfailure");
    if (op === "add_scheme") {
      emptyInputs(["scheme", "title", "order"]);
    }
    _("token").value = rsp.token;
    const msgDiv = _("messageContainer");
    msgDiv.className = msgStyleClass;
    msgDiv.innerHTML = rsp.message;
  }
}

async function deleteScheme(schemeId) {
  const token = _("token");
  if (await swalConfirm("Confirm you want to delete scheme", "warning")) {
    ld_startLoading("delete_" + schemeId, "ld_loader_delete_" + schemeId);
    ajaxRequest(
      "staff/responses/responses.php",
      handleDeleteReq,
      `op=delete_scheme&scheme_id=${schemeId}&token=${token.value}`
    );
  }

  async function handleDeleteReq() {
    ld_stopLoading("delete_" + schemeId, "ld_loader_delete_" + schemeId);
    const rsp = JSON.parse(xmlhttp.responseText);
    _("token").value = rsp.token;
    let successCode = [200, 201, 204];
    if (successCode.includes(rsp.status)) {
      swalNotifyDismiss(rsp.message, "success");
      table
        .row("#row" + schemeId)
        .remove()
        .draw();
    } else {
      swalNotifyDismiss(rsp.message, "error");
    }
  }
}
