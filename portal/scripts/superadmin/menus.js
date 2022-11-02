var table = $("#menusTable").DataTable(dataTableOptions);
function saveMenu(op) {
  const menu = _("menu").value;
  const display_name = _("display_name").value;
  const description = _("description").value;
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
      `op=${op}&menu_id=${menuId}&menu=${menu}&display_name=${display_name}&description=${description}&url=${url}&menu_order=${menu_order}&parent_id=${parent_id}&parent_order=${parent_order}&shown=${shown}&active=${active}&token=${token.value}`
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
      emptyInputs([
        "menu",
        "url",
        "menu_order",
        "parent_id",
        "parent_order",
        "display_name",
        "icon",
      ]);
    }
    _("token").value = rsp.token;
    const msgDiv = _("messageContainer");
    msgDiv.className = msgStyleClass;
    msgDiv.innerHTML = rsp.message;
  }
}

async function deleteMenu(menuId) {
  const token = _("token");
  if (await swalConfirm("Confirm you want to delete menu", "warning")) {
    ld_startLoading("delete_" + menuId, "ld_loader_delete_" + menuId);
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
    if (rsp.status != 204) {
      swalNotifyDismiss(rsp.message, "error");
    } else {
      table
        .row("#row" + menuId)
        .remove()
        .draw();
      swalNotifyDismiss(rsp.message, "success");
    }
  }
}

function setChecked(event, menuId, type) {
  const checked = event.checked ? 1 : 0;
  let newData;
  if (checked === 1) {
    newData = ` <td id="td_${type}_${menuId}">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="shown_${menuId}" checked onchange="setChecked(this,'${menuId}','${type}')"><span id="ld_loader_${type}_${menuId}"></span>
                        <label class="custom-control-label" for="${type}_${menuId}"></label>
                    </div>
                </td>`;
  } else {
    newData = ` <td id="td_${type}_${menuId}">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="shown_${menuId}" onchange="setChecked(this,'${menuId}','${type}')"><span id="ld_loader_${type}_${menuId}"></span>
                        <label class="custom-control-label" for="${type}_${menuId}"></label>
                    </div>
                </td>`;
  }
  table
                .cell(`#td_${type}_${menuId}`)
                .data(newData)
                .draw();

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
