var form = _('form');
var hiddenPic = _('hiddenPic');
var image = _('image');
var picMsg = _('picMsg');


async function saveChanges(){
    if (
      await swalConfirm("Are you sure you want to save changes", "question")
    ) {
      await getPostPageWithUpload(
        "scheduleForm",
        "staff/responses/responses.php",
        "update_schedules"
      );
    }
   resetInputStyling("scheduleForm", "inputsuccess", "inputfailure");
    
}

function showImage(event){
   if(objLength(event.files) > 0){
       if(event.files[0].size > 100 * 1024){
           image.style.display = 'none';
           picMsg.innerHTML = '<div class="failure">Signature should not be more than 100kb</div>';
           return;
       }
       picMsg.innerHTML = '';
       hiddenPic.value = 'hasvalue';
       image.src = URL.createObjectURL(event.files[0]);
       image.style.display = 'block';
   }else{
          hiddenPic.value = '';
          picMsg.innerHTML = '<div class="failure">Upload Signature</div>';
          image.style.display = 'none';
   }

}

  $(".js-example-basic-single").select2();
