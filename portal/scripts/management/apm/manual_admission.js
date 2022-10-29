function showImage(event) {
  const image = _("image");
  const hiddenPic = _("hiddenPic");
  const picMsg = _("picMsg");
  if (objLength(event.files) > 0) {
    if (event.files[0].size > 100 * 1024) {
      image.style.display = "none";
      picMsg.innerHTML =
        '<div class="failure">Picture should not be more than 100kb</div>';
      return;
    }
    picMsg.innerHTML = "";
    hiddenPic.value = "hasvalue";
    image.src = URL.createObjectURL(event.files[0]);
    image.style.display = "block";
  } else {
    hiddenPic.value = "";
    picMsg.innerHTML = '<div class="failure">Select a Picture</div>';
    image.style.display = "none";
  }
}
function populateLGA(obj) {
  const stateId = obj.value;
  if (stateId.trim().length > 0) {
    ajaxRequest(
      "responses/responses.php",
      handleLGAListResponse,
      `op=get_lga_list&state_id=${stateId}&token=${token.value}`
    );
  }

  function handleLGAListResponse() {
    const rsp = JSON.parse(xmlhttp.responseText);
    _("token").value = rsp.token;
    if (rsp.status === 200) {
      const lgaContainer = _("lga");
      const lgas = rsp.data;
      let output = ``;
      for (let lga of lgas) {
        output += `<option value="${lga.id}">${lga.name}</option>`;
      }
      lgaContainer.innerHTML = output;
    }
  }
}

async function admitStudent() {
  if (validate("manualAdmissionForm", { validateOnSubmit: true })) {
    ld_startLoading('admitBtn');
    await getPostPageWithUpload('manualAdmissionForm','management/apm/responses/responses.php','manual_admission',false);
    emptyInputs(["fname", "lname", "oname",'email','fatherName','motherName','stdid','dob','doa','state','lga','password']);
    resetInputStyling("manualAdmissionForm", "inputsuccess", "inputfailure");
    ld_stopLoading('admitBtn');
  }
}

validate("manualAdmissionForm");
$(".js-example-basic-single").select2();
