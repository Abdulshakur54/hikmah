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

async function updateSignature(event) {
  event.preventDefault();
  ld_startLoading("saveBtn");
  await getPostPageWithUpload(
    "updateSignatureForm",
    "management/accountant/responses/responses.php",
    { op: "update_accountant_signature" },
    false
  );
  ld_stopLoading("saveBtn");
}
