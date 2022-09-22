var counter = _("counter");
var token = _("token");
var nameIndicator = _("nameIndicator");
var errMsg = _("errMsg");
var hiddenFileName = _("hiddenFileName");
var form = document.querySelector("form");

//this function is available for the apm
function populateLevel(e) {
  let genHtml = '<option value ="ALL">ALL</option>';
  let levObj = getLevels(e.value);
  if (objLength(levObj) > 0) {
    for (let objName in levObj) {
      genHtml +=
        '<option value="' + levObj[objName] + '">' + objName + "</option>";
    }
  }

  //insert the generated html into level select element
  _("level").innerHTML = genHtml;
}

function reSubmit() {
  getPage(
    "management/apm/admission_attachment.php?school=" +
      _("school").value +
      "&level=" +
      _("level").value +
      "&filter=true" +
      "&token=" +
      _("token").value
  );
}

function popId() {
  let count = counter.value;
  let valObj = [];
  for (let i = 1; i <= count; i++) {
    if (_("chk" + i).checked) {
      valObj.push(_("val" + i).value);
    }
  }
  return valObj;
}

function displayName(event, size) {
  if (objLength(event.files) > 0) {
    if (event.files[0].size > size * 1024 * 1024) {
      nameIndicator.innerHTML = "";
      errMsg.innerHTML =
        '<div class="failure">File should not be more than ' +
        size +
        "MB</div>";
      return;
    }
    errMsg.innerHTML = "";
    hiddenFileName.value = "hasvalue";
    nameIndicator.innerHTML = event.files[0].name;
  } else {
    hiddenFileName.value = "";
    errMsg.innerHTML = '<div class="failure">First select a file</div>';
  }
}

//helps to ensure a file is selected
function fileSelected() {
  if (!emp(hiddenFileName.value)) {
    errMsg.innerHTML = "";
    return true;
  } else {
    errMsg.innerHTML = '<div class="failure">Select a File</div>';
    return false;
  }
}

//function that uploads attachment
async function uploadFile() {
    try{
         if (fileSelected()) {
           let formData = new FormData(_("attachmentForm"));
           formData.append("op", "add_attachment");
           formData.append("token", token.value);
           let file = _("selectedFile").files[0];
           formData.append("selectedFile", file);
           let rsp = await fetch("management/apm/responses/responses.php", {
             method: "POST",
             body: formData,
           });
           rsp = await rsp.json();
           token.value = rsp.token;
           if(rsp.status == 204){
             getPage("management/apm/admission_attachment.php?school=" +
               _("school").value +
               "&level=" +
               _("level").value +
               "&filter=true" +
               "&token=" +
               token.value);
           }else{
            swalNotify(rsp.message,'error');
           }
           
         }
    }catch(error){
        swalNotify(error.message,'error');
    }
}

async function deleteAttachment(id) {
  if (await swalConfirm("This will delete the selected attachement")) {
    let postData =
      "id=" + id + "&token=" + token.value + "&op=delete_attachment";
    ld_startLoading("btn" + id, "ld_loader_" + id);
    ajaxRequest(
      "management/apm/responses/responses.php",
      acceptdeleteRsp,
      postData
    );

    async function acceptdeleteRsp() {
      ld_stopLoading("btn" + id, "ld_loader_" + id);
      let rsp = JSON.parse(xmlhttp.responseText);
      token.value = rsp.token;
      if (rsp.status == 204) {
        swalNotifyDismiss(rsp.message, "success");
      } else {
        swalNotifyDismiss(rsp.message, "danger");
      }
      _("row" + id).style.display = "none";
    }
  }
}

$(document).ready(function () {
  $("#attachmentTable").DataTable(dataTableOptions);
});

$(".js-example-basic-single").select2();
