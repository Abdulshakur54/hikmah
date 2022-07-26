//the input elements
let address = _('address');
let picture = _('picture');
let fatherName = _('fatherName');
let motherName = _('motherName');
let phone = _('phone');
let email = _('email');
let applyBtn = _('applyBtn');
let hiddenPic = _('hiddenPic');
let image = _('image');

//error message divs
let picMsg = _('picMsg');
let fatherNameMsg = _('fatherNameMsg');
let motherNameMsg = _('motherNameMsg');
let phoneMsg = _('phoneMsg');
let emailMsg = _('emailMsg');
let genMsg = _('genMsg');

fatherName.addEventListener('keyup',function(){
    instantValidate(fatherName,{
        'name':'Father\'s Name',
        'required':true,
        'pattern':'^[a-zA-z` ]+$',
        'max':50,
        'min':3
        },fatherNameMsg);
});

fatherName.addEventListener('blur',function(){
    validate(fatherName,{
        'name':'Father\'s Name',
        'required':true,
        'pattern':'^[a-zA-z` ]+$',
        'max':50,
        'min':3
        },fatherNameMsg);
});

motherName.addEventListener('keyup',function(){
    instantValidate(motherName,{
        'name':'Mother\'s Name',
        'required':true,
        'pattern':'^[a-zA-z` ]+$',
        'max':50,
        'min':3
        },motherNameMsg);
});

motherName.addEventListener('blur',function(){
    validate(motherName,{
        'name':'Mother\'s Name',
        'required':true,
        'pattern':'^[a-zA-z` ]+$',
        'max':50,
        'min':3
        },motherNameMsg);
});

email.addEventListener('blur', function(){
         validate(email,{
        'name':'Email',
        'required':false,
        'pattern':'^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+$',
        'max':70,
        'min':7
        },emailMsg);
    }
);

email.addEventListener('keyup', function(){
        instantValidate(email,{
        'name':'Email',
        'required':false,
        'pattern':'^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+',
        'max':70
        },emailMsg,7);
    }
);

function showImage(event){
   if(objLength(event.files) > 0){
       if(event.files[0].size > 100 * 1024){
           image.style.display = 'none';
           picMsg.innerHTML = '<div class="failure">Picture should not be more than 100kb</div>';
           return;
       }
       picMsg.innerHTML = '';
       hiddenPic.value = 'hasvalue';
       image.src = URL.createObjectURL(event.files[0]);
       image.style.display = 'block';
   }else{
          hiddenPic.value = '';
          picMsg.innerHTML = '<div class="failure">Select a Picture</div>';
          image.style.display = 'none';
   }

}

phone.addEventListener('blur',function(){
    validate(phone,{
        'name':'Phone No',
        'required':true,
        'pattern':'^(080|070|090|081|091|071)[0-9]{8}$'
    },phoneMsg);
});

phone.addEventListener('keyup',function(){
    instantValidate(phone,{
        'name':'Phone No',
        'required':true,
        'pattern':'^(080|070|090|081|091|071)[0-9]+$',
        'max':11
    },phoneMsg,4);
});

function valPicture(){
    if(!emp(hiddenPic.value)){
         picMsg.innerHTML = '';
         return true;
    }else{
         picMsg.innerHTML = '<div class="failure">Select a Picture</div>';
         return false;
    }
}


function sumbitData(){
    if(valPicture() &&
            validate(fatherName,{
        'name':'Father\'s Name',
        'required':true,
        'pattern':'^[a-zA-z` ]+$',
        'max':50,
        'min':3
        },fatherNameMsg) &&
        validate(motherName,{
        'name':'Mother\'s Name',
        'required':true,
        'pattern':'^[a-zA-z` ]+$',
        'max':50,
        'min':3
        },motherNameMsg) &&
         validate(email,{
        'name':'Email',
        'required':false,
        'pattern':'^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+$',
        'max':70,
        'min':7
        },emailMsg) &&
         validate(phone,{
        'name':'Phone No',
        'required':true,
        'pattern':'^(080|070|090|081|091|071)[0-9]{8}$'
        },phoneMsg)  
        ){
        return true;
    }else{
        return false;
    }
    
}