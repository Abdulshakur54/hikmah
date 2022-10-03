var table = $("#tokensTable").DataTable(dataTableOptions);
async function deleteToken(id){
     let token = _("token");
     let tableBody = document.querySelector("tbody");
    if (await swalConfirm("This will delete the selected Token", "warning")) {
      ajaxRequest(
        "management/apm/responses/delete_token.rsp.php",
        deleteTokenRsp,
        "id=" + id + "&token=" + token.value
      );
    }

    function deleteTokenRsp() {
      let rsp = JSON.parse(xmlhttp.responseText);
      token.value = rsp.token;
      if (rsp.success) {
        table
          .row("#row" + rsp.id)
          .remove()
          .draw();
        swalNotifyDismiss("Successfully deleted token", "success");
      } else {
         swalNotifyDismiss("Unable to delete token", "error");
      }
    }
}




