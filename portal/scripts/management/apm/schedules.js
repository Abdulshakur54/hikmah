var submitType = _("submitType");
var form = _("form");
var hiddenPic = _("hiddenPic");
var image = _("image");
var picMsg = _("picMsg");
var token = _("token");

function submitForm() {
  submitType.value = "browse";
  let formvalues = getFormData("schedulesForm");
  getPage("management/apm/schedules.php", formvalues);
}

async function saveChanges() {
  submitType.value = "save";
  if (valPicture()) {
    //because of the attachment formData object is used
    let formData = new FormData(_("schedulesForm"));
    formData.append("op", "save_schedules");
    try {
      let rsp = await fetch("management/apm/responses/responses.php", {
        method: "POST",
        body: formData,
      });
      rsp = await rsp.json();
       let msgStyleClass;
        token.value = rsp.token;
      if (rsp.status == 204) {
        msgStyleClass = "success m-2";
      } else {
        msgStyleClass = "failure m-2";
      }
      const msgDiv = _("messageContainer");
      msgDiv.className = msgStyleClass;
      msgDiv.innerHTML = rsp.message;
    } catch (error) {
      console.log(error.message);
    }

    //  let formvalues = getFormData("schedulesForm");
    //  getPage("management/apm/schedules.php", formvalues);
  }
}

function showImage(event) {
  if (objLength(event.files) > 0) {
    if (event.files[0].size > 100 * 1024) {
      image.style.display = "none";
      picMsg.innerHTML =
        '<div class="failure">Logo should not be more than 100kb</div>';
      return;
    }
    picMsg.innerHTML = "";
    hiddenPic.value = "hasvalue";
    image.src = URL.createObjectURL(event.files[0]);
    image.style.display = "block";
  } else {
    hiddenPic.value = "";
    picMsg.innerHTML = '<div class="failure">Select a Logo</div>';
    image.style.display = "none";
  }
}

function valPicture() {
  if (!emp(image.src)) {
    picMsg.innerHTML = "";
    return true;
  } else {
    picMsg.innerHTML = '<div class="failure">Select a Picture</div>';
    return false;
  }
}

$(".js-example-basic-single").select2();
