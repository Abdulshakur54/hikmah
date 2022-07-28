function saveRole(op) {
  const role = _("role").value;
  const roleId = _("role_id").value;
  const token = _("token");
  if (validate("roleForm", { validateOnSubmit: true})) {
     console.log("valid");
     ld_startLoading("saveBtn");
      ajaxRequest(
        "superadmin/responses/responses.php",
        handleSaveRoleReq,
        `op=${op}&role_id=${roleId}&role=${role}&token=${token.value}`
      );
  }else{
    console.log('invalid');
  }
  
}

function handleSaveRoleReq() {
  ld_stopLoading("saveBtn");
  const rsp = JSON.parse(xmlhttp.responseText);
  let msgStyleClass = "success m-2";
  if (rsp.status != 204) {
    msgStyleClass = "failure m-2";
  }
  _("token").value = rsp.token;
  emptyInputs("role");
  const msgDiv = _("messageContainer");
  msgDiv.className = msgStyleClass;
  msgDiv.innerHTML = rsp.message;
}

async function deleteRole(roleId) {
  const token = _("token");
  //ld_startLoading("deleteBtn");
  if (await swalConfirm("Confirm you want to delete role", "warning")) {
    ajaxRequest(
      "superadmin/responses/responses.php",
      handleDeleteRoleReq,
      `op=delete_role&role_id=${roleId}&token=${token.value}`
    );
  }

  async function handleDeleteRoleReq() {
    //ld_stopLoading("deleteBtn");
    const rsp = JSON.parse(xmlhttp.responseText);
     _("token").value = rsp.token;
     _("row" + roleId).style.display = "none";
    if (rsp.status != 204) {
      swalNotifyDismiss(rsp.message, "error");
    } else {
      swalNotifyDismiss(rsp.message, "success");
    }
  }
}

 $(document).ready(function () {
   $("#rolesTable").DataTable(dataTableOptions);
 });





