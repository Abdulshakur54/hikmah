var form = _("form");
var hiddenPic = _("hiddenPic");
var image = _("image");
var picMsg = _("picMsg");
var resetScores = 'false';

async function saveChanges() {
    _('resetScores').value = resetScores;
  try {
let msg =
  resetScores == "true"
    ? "<p>Are you sure you want to save changes</p><p>All scores already set for this term would be lost(reset)</p>"
    : "<p>Are you sure you want to save changes</p>";
    if (
      await swalConfirm(
        msg,
        "question"
      )
    ) {
      await getPostPageWithUpload(
        "scheduleForm",
        "management/hos/responses/responses.php",
        { op: "update_schedules" }
      );
    }
    resetInputStyling("scheduleForm", "inputsuccess", "inputfailure");
  } catch (e) {
    
  }
}

function showImage(event) {
  if (objLength(event.files) > 0) {
    if (event.files[0].size > 100 * 1024) {
      image.style.display = "none";
      picMsg.innerHTML =
        '<div class="failure">Signature should not be more than 100kb</div>';
      return;
    }
    picMsg.innerHTML = "";
    hiddenPic.value = "hasvalue";
    image.src = URL.createObjectURL(event.files[0]);
    image.style.display = "block";
  } else {
    hiddenPic.value = "";
    picMsg.innerHTML = '<div class="failure">Upload Signature</div>';
    image.style.display = "none";
  }
}

function scoreChanged(){
    resetScores = 'true';
}
