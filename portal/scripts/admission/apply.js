function applyAdmission() {
  if (validate("applyForm", { validateOnSubmit: true })) {
    ld_startLoading("applyBtn");
    const fatherName = _("fatherName");
    const motherName = _("motherName");
    const token = _("token");
    const username = _("username");
    ajaxRequest(
      "admission/responses/responses.php",
      applyAdmissionRsp,
      "fathername=" +
        fatherName.value +
        "&mothername=" +
        motherName.value +
        "&op=apply_admission"+
        "&username="+username.value+
        "&token=" +
        token.value
    );
  }

  function applyAdmissionRsp() {
    let rsp = JSON.parse(xmlhttp.responseText);
    let msgStyleClass;
    const msgDiv = _("messageContainer");
     _("token").value = rsp.token;
    if (rsp.status == 204) {
      msgStyleClass = "success m-2";
     getPage('admission/apply.php?complete=yes');
    } else {
      msgStyleClass = "failure m-2";
      msgDiv.innerHTML = "<div>" + rsp.message + "</div>";
       msgDiv.className = msgStyleClass;
       resetInputStyling("applyForm", "inputsuccess", "inputfailure");
       ld_stopLoading("applyBtn");
    }
   
  }
}
