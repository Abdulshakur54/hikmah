var rolesArray;
ajaxRequest(
  "superadmin/responses/responses.php",
  rolesResponse,
  `op=get_roles&token=${token.value}`
);
function rolesResponse() {
  var rsp = JSON.parse(xmlhttp.responseText);
  if (rsp.status === 200) {
    var data = rsp.data;
    rolesArray = data;
    var roles = data.map((row) => {
      return `<option value="${row.id}">${row.role}</option>`;
    });
   _('role').innerHTML = '<option value="">:::Select Role:::</option>.'+roles.join("");
  }
  _("token").value = rsp.token;
}

$(".js-example-basic-single").select2();

var roleElement = _("role");
function populateMenuTable(event) {
  if (event.value.length > 0) {
    ld_startLoading("role", "ld_loader_browse");
    ajaxRequest(
      "superadmin/responses/responses.php",
      menuSearchResponse,
      `op=get_role_menus&token=${token.value}&role_id=${event.value}`
    );
  } else {
    document.querySelector('tbody').innerHTML = '';
    messageContainer.className = "failure text-center";
    messageContainer.innerHTML = "Select a role first";
  }
}

function menuSearchResponse() {
  ld_stopLoading("role", "ld_loader_browse");
  var rsp = JSON.parse(xmlhttp.responseText);
  if (rsp.status === 200) {
    const rows = rsp.data;
    let retVal = "";
    let x = 1;
    for (let row of rows) {
      retVal += `<tr><td>${x}</td><td>${row.menu}</td><td>${row.url}</td><td><input type="checkbox" value="${row.id}"/></td></tr>`;
      x++;
    }
    $("tbody").html(retVal);
  }
  _("token").value = rsp.token;
}

function checkAll(e) {
  const tbody = document.querySelector("tbody");
  const checkboxes = tbody.querySelectorAll("input[type=checkbox]");
  if (e.checked) {
    for (let checkbox of checkboxes) {
      checkbox.checked = true;
    }
  } else {
    for (let checkbox of checkboxes) {
      checkbox.checked = false;
    }
  }
}

function removeMenuToRole() {
  const tbody = document.querySelector("tbody");
  const checkboxes = tbody.querySelectorAll("input[type=checkbox]");
  const checkedCheckboxes = [];
  for (let checkbox of checkboxes) {
    if (checkbox.checked) {
      checkedCheckboxes.push(parseInt(checkbox.value));
    }
  }
  if (checkedCheckboxes.length > 0) {
    ld_startLoading("removeBtn", "ld_loader_remove");
    ajaxRequest(
      "superadmin/responses/responses.php",
      removeMenuFromRoleResponse,
      `op=remove_menu_from_roles&token=${token.value}&menu_ids=${JSON.stringify(
        checkedCheckboxes
      )}&role_id=${_('role').value}`
    );
  }else{
    swalNotify('No menu have been selected','warning');
  }

  function removeMenuFromRoleResponse(){
    ld_stopLoading("removeBtn", "ld_loader_remove");
    var rsp = JSON.parse(xmlhttp.responseText);
    if (rsp.status === 200) {
        const rows = rsp.data;
        let retVal = "";
        let x = 1;
        for (let row of rows) {
          retVal += `<tr><td>${x}</td><td>${row.menu}</td><td>${row.url}</td><td><input type="checkbox" value="${row.id}"/></td></tr>`;
          x++;
        }
        $("tbody").html(retVal);
     swalNotify(rsp.message,'success');
    }else{
      swalNotify('Error encountered when trying to remove menus','error');
    }
    _("token").value = rsp.token;
  }
}
