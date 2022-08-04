function saveMenu(op) {
  const menu = _("menu").value;
  const menuId = _("menu_id").value;
  const token = _("token");
  const url = _("url").value;
  const menu_order = _("menu_order").value;
  const parent_id = _("parent_id").value;
  const parent_order = _("parent_order").value;
  const shown = _("shown").checked ? 1 : 0;
  const active = _("active").checked ? 1 : 0;
  if (validate("menuForm", { validateOnSubmit: true })) {
    ld_startLoading("saveBtn");
    ajaxRequest(
      "superadmin/responses/responses.php",
      handleSaveMenuReq,
      `op=${op}&menu_id=${menuId}&menu=${menu}&url=${url}&menu_order=${menu_order}&parent_id=${parent_id}&parent_order=${parent_order}&shown=${shown}&active=${active}&token=${token.value}`
    );
  }

  function handleSaveMenuReq() {
    ld_stopLoading("saveBtn");
    const rsp = JSON.parse(xmlhttp.responseText);
    let msgStyleClass = "success m-2";
    if (rsp.status != 204) {
      msgStyleClass = "failure m-2";
    }
    resetInputStyling("menuForm", "inputsuccess", "inputfailure");
    if (op === "add_menu") {
      emptyInputs(["menu", "url", "menu_order", "parent_id", "parent_order"]);
    }
    _("token").value = rsp.token;
    const msgDiv = _("messageContainer");
    msgDiv.className = msgStyleClass;
    msgDiv.innerHTML = rsp.message;
  }
}

async function deleteMenu(menuId) {
  const token = _("token");
  ld_startLoading("delete_" + menuId, "ld_loader_delete_" + menuId);
  if (await swalConfirm("Confirm you want to delete menu", "warning")) {
    ajaxRequest(
      "superadmin/responses/responses.php",
      handleDeleteMenuReq,
      `op=delete_menu&menu_id=${menuId}&token=${token.value}`
    );
  }

  async function handleDeleteMenuReq() {
    ld_stopLoading("delete_" + menuId, "ld_loader_delete_" + menuId);
    const rsp = JSON.parse(xmlhttp.responseText);
    _("token").value = rsp.token;
    _("row" + menuId).style.display = "none";
    if (rsp.status != 204) {
      swalNotifyDismiss(rsp.message, "error");
    } else {
      swalNotifyDismiss(rsp.message, "success");
    }
  }
}

$(document).ready(function () {
  $("#menusTable").DataTable(dataTableOptions);
});

function setChecked(event, menuId, type) {
  const checked = event.checked ? 1 : 0;
  ld_startLoading(type + "_" + menuId, "ld_loader_" + type + "_" + menuId);
  ajaxRequest(
    "superadmin/responses/responses.php",
    handleSetChecked,
    `op=set_checked&menu_id=${menuId}&type=${type}&checked=${checked}&token=${token.value}`
  );

  function handleSetChecked() {
    ld_stopLoading(type + "_" + menuId, "ld_loader_" + type + "_" + menuId);
    const rsp = JSON.parse(xmlhttp.responseText);
    if (rsp.status != 204) {
      swalNotifyDismiss(rsp.message, "error");
    }
    _("token").value = rsp.token;
  }
}
