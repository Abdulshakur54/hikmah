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

function showImage(event) {
    const image = _('image');
    const hiddenPic = _('hiddenPic');
    const picMsg = _('picMsg');
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

async function updateAccount(event) {
  event.preventDefault();
  if(validate('updateAccountForm',{validateOnSubmit:true})){
    ld_startLoading("updateBtn");
    await getPostPageWithUpload(
      "updateAccountForm",
      "management/hos/responses/responses.php",
      { op: "update_account" },
      false
    );
    ld_stopLoading("updateBtn");
  }
}

 validate("updateAccountForm");
 $(".js-example-basic-single").select2();

