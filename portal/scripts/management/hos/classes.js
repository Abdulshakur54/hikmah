$(".js-example-basic-single").select2();

function saveClass() {
  getPostPage("classForm", "management/hos/add_class.php");
}

$(document).ready(function () {
  $("#classesTable").DataTable(dataTableOptions);
});

async function deleteClass(id) {
  let token = _("token");
  let school = _("school");
  let tableBody = document.querySelector("tbody");

  if (await swalConfirm("This will delete the selected Class", "warning")) {
    ajaxRequest(
      "management/hos/responses/responses.php",
      deleteClassRsp,
      "classid=" + id + "&op=delete_class&school="+school.value +"&token="+ token.value
    );
  }

  function deleteClassRsp() {
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    if (rsp.status === 204) {
      let tableRow = _("row" + id);
      tableBody.removeChild(tableRow);
      swalNotifyDismiss("Successfully deleted class", "success");
    } else {
      swalNotifyDismiss("Unable to delete class", "error");
    }
  }
}

