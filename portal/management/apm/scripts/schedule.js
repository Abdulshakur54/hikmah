let submitType = _('submitType');
let form = _('form');
let hiddenPic = _('hiddenPic');
let image = _('image');
let picMsg = _('picMsg');

function submitForm(){
    submitType.value = 'browse';
    form.submit();
}

function saveChanges(){
    submitType.value = 'save';
    if(valPicture()){
        form.submit();
    } 
}

function showImage(event){
   if(objLength(event.files) > 0){
       if(event.files[0].size > 100 * 1024){
           image.style.display = 'none';
           picMsg.innerHTML = '<div class="failure">Logo should not be more than 100kb</div>';
           return;
       }
       picMsg.innerHTML = '';
       hiddenPic.value = 'hasvalue';
       image.src = URL.createObjectURL(event.files[0]);
       image.style.display = 'block';
   }else{
          hiddenPic.value = '';
          picMsg.innerHTML = '<div class="failure">Select a Logo</div>';
          image.style.display = 'none';
   }

}

function valPicture(){
    if(!emp(image.src)){
         picMsg.innerHTML = '';
         return true;
    }else{
         picMsg.innerHTML = '<div class="failure">Select a Picture</div>';
         return false;
    }
}
