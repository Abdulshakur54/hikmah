async function deleteToken(id){
     let token = _("token");
     let tableBody = document.querySelector("tbody");
    if (await swalConfirm("Confirm you want to delete token", "warning")) {
      ajaxRequest(
        "management/director/responses/delete_token.rsp.php",
        deleteTokenRsp,
        "id=" + id + "&token=" + token.value
      );
    }

    function deleteTokenRsp() {
      let rsp = JSON.parse(xmlhttp.responseText);
      token.value = rsp.token;
      if (rsp.success) {
        let tableRow = _("row" + rsp.id);
        tableBody.removeChild(tableRow);
        swalNotifyDismiss("Successfully deleted token", "success");
      } else {
         swalNotifyDismiss("Unable to delete token", "error");
      }
    }
}

 $(document).ready(function () {
   $("#tokensTable").DataTable(dataTableOptions);
 });



