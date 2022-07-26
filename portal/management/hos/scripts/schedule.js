let form = _('form');
let hiddenPic = _('hiddenPic');
let image = _('image');
let picMsg = _('picMsg');


function saveChanges(){
    if(confirm('Are you sure you want to save changes\nAll scores already set for this term would be lost(reset)')){
        form.submit();
    }
    
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

