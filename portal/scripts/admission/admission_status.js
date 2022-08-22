var form = document.querySelector('form');
var token = _('token');
var admId = _("admId");
var id = _("id");


async function acceptAdmission(){
    if(await swalConfirm('Confirm you want to accept our offer','question')){
        let op = 'accept_admission';
        ld_startLoading(['acceptBtn','declineBtn','downloadBtn']);
        ajaxRequest(
          "admission/responses/responses.php",
          acceptAdmissionRsp,
          `op=${op}&adm_id=${admId.value}&id=${id.value}&token=${token.value}`
        );
    }

    async function acceptAdmissionRsp(){
        
    ld_stopLoading(["acceptBtn", "declineBtn", "downloadBtn"]);
    const rsp = JSON.parse(xmlhttp.responseText);
    let msgStyleClass = "success m-2";
    if (rsp.status != 204) {
      msgStyleClass = "failure m-2";
    }else{
        location.assign("admission/success.php?acceptAdmission=true");
        return;
    }
    _("token").value = rsp.token;
    const msgDiv = _("messageContainer");
    msgDiv.className = msgStyleClass;
    msgDiv.innerHTML = rsp.message;
  
    }
    
}

async function declineAdmission(){
    if(await swalConfirm('Confirm you want to decline our offer\nYou would not be able to accept it after declination','question')){
       let op = "decline_admission";
       ld_startLoading(["acceptBtn", "declineBtn", "downloadBtn"]);
       ajaxRequest(
         "admission/responses/responses.php",
         declineAdmissionRsp,
         `op=${op}&adm_id=${admId.value}&id=${id.value}&token=${token.value}`
       );
    }

     async function declineAdmissionRsp() {
       ld_stopLoading(["acceptBtn", "declineBtn", "downloadBtn"]);
       const rsp = JSON.parse(xmlhttp.responseText);
       let msgStyleClass = "success m-2";
       if (rsp.status != 204) {
         msgStyleClass = "failure m-2";
       } else {
        getPage('admission/admission_status.php');
         return;
       }
       _("token").value = rsp.token;
       const msgDiv = _("messageContainer");
       msgDiv.className = msgStyleClass;
       msgDiv.innerHTML = rsp.message;
     }
    
}

function downloadAdmission(){
    decision.value = 'download';
    getPage('admission/admission_decision.php?decision='+decision.value+'&token='+token.value);
    
}