
var stdIds = [];
function storeId(e) {
  if (!stdIds.includes(e.id)) {
    stdIds.push(e.id);
  }
}

function save() {
  if (stdIds.length > 0) {
    let updData = [];
    for (let stdId of stdIds) {
      let stdArr = []; //to hold each student and their comment
      stdArr.push(stdId.replace(/_/g, "/"));
      stdArr.push(_(stdId).value);
      updData.push(stdArr);
    }
    ld_startLoading("saveBtn");
    ajaxRequest(
      "staff/responses/responses.php",
      handleSaveReq,
      `op=update_comment&updated_data=${JSON.stringify(updData)}&term=${_('term').value}&token=${
        _('token').value
      }`
    );
  } else {
    swalNotifyDismiss("No changes made", "info");
  }

  function handleSaveReq() {
    ld_stopLoading("saveBtn");
    const rsp = JSON.parse(xmlhttp.responseText);
    let msgStyleClass = "success m-2";
    let successCode = [200, 201, 204];
    if (!successCode.includes(rsp.status)) {
    swalNotify('An error was encountered while updating comment','error');
    }else{
         swalNotifyDismiss("Update was successful", "success");
    }
   
    _("token").value = rsp.token;
  }
}

function changeClass(e){
  getPage('management/hos/comments.php?class_id='+e.value+'&token='+_('token').value);
}
$(".js-example-basic-single").select2();
