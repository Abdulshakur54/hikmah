let fname = _('fname');
let lname = _('lname');
let oname = _('oname');
let pwd = _('password');
let conPwd = _('c_password');
let phone = _('phone');
let pin = _('pin');
let account = _('account');
let hiddenPic = _('hiddenPic');
let image = _('image');

let fNameMsg = _('fNameMsg');
let lNameMsg = _('lNameMsg');
let oNameMsg = _('oNameMsg');
let pwdMsg = _('pwdMsg');
let conPwdMsg = _('conPwdMsg');
let phoneMsg = _('phoneMsg');
let pinMsg = _('pinMsg');
let accountMsg = _('accountMsg');
let picMsg = _('picMsg');

let genMsg = _('genMsg');



fname.addEventListener('blur', function(){
     validate(fname,{
        'name':'First Name',
        'required':true,
        'pattern':'^[a-zA-z`]+$',
        'max':30,
        'min':3
        },fNameMsg);
    }
);


fname.addEventListener('keyup', function(){
     instantValidate(fname,{
        'name':'First Name',
        'required':true,
        'pattern':'^[a-zA-z`]+$',
        'max':30,
        'min':3
    },fNameMsg);
}
);


lname.addEventListener('blur', function(){
     validate(lname,{
        'name':'Last Name',
        'required':true,
        'pattern':'^[a-zA-z`]+$',
        'max':30,
        'min':3
        },lNameMsg);
    }
);


lname.addEventListener('keyup', function(){
     instantValidate(lname,{
        'name':'Last Name',
        'required':true,
        'pattern':'^[a-zA-z`]+$',
        'max':30,
        'min':3
    },lNameMsg);
}
);


oname.addEventListener('blur', function(){
     validate(oname,{
        'name':'Other Name',
        'pattern':'^[a-zA-z`]+$',
        'max':30,
        'min':3
        },oNameMsg);
    }
);


oname.addEventListener('keyup', function(){
     instantValidate(oname,{
        'name':'Other Name',
        'pattern':'^[a-zA-z`]+$',
        'max':30,
        'min':3
        },oNameMsg);
    }
);


pwd.addEventListener('blur',function(){
    validate(pwd,{
        'name':'Password',
        'required':true,
        'pattern':'^[a-zA-z 0-9]+$',
        'min':8,
        'max':20
    },pwdMsg);
});

pwd.addEventListener('keyup',function(){
    instantValidate(pwd,{
        'name':'Password',
        'required':true,
        'pattern':'^[a-zA-z 0-9]+$',
        'max':20
    },pwdMsg);
});

conPwd.addEventListener('blur',function(){
    validate(conPwd,{
        'name':'Confirm Password',
        'required':true,
        'same':pwd
    },conPwdMsg);
});

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

pin.addEventListener('blur',function(){
    validate(pin,{
        'name':'Pin',
        'required':true,
        'length':14,
        'pattern':'^[0-9a-zA-Z]+$'
    },pinMsg);
});

pin.addEventListener('keyup',function(){
    instantValidate(pin,{
        'name':'Pin',
        'required':true,
        'pattern':'^[0-9a-zA-Z]+$',
        'max':14
    },pinMsg);
});


account.addEventListener('blur', function(){
     validate(account,{
        'name':'Account No',
        'required':true,
        'pattern':'^[0-9]+$',
        'max':15,
        'min':10
        },accountMsg);
    }
);

account.addEventListener('keyup', function(){
     instantValidate(account,{
        'name':'Account No',
        'required':true,
        'pattern':'^[0-9]+$',
        'max':15
        },accountMsg,1);
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

function valPicture(){
    if(!emp(hiddenPic.value)){
         picMsg.innerHTML = '';
         return true;
    }else{
         picMsg.innerHTML = '<div class="failure">Select a Picture</div>';
         return false;
    }
}
function submitForm(){
    if(valPicture() &&
        validate(fname,{
        'name':'First Name',
        'required':true,
        'pattern':'^[a-zA-z`]+$',
        'max':30,
        'min':3
        },fNameMsg) &&  validate(lname,{
        'name':'Last Name',
        'required':true,
        'pattern':'^[a-zA-z`]+$',
        'max':30,
        'min':3
        },lNameMsg) && validate(oname,{
        'name':'Other Name',
        'pattern':'^[a-zA-z`]+$',
        'max':30,
        'min':3
        },oNameMsg) &&  validate(pwd,{
        'name':'Password',
        'required':true,
        'pattern':'^[a-zA-z 0-9]+$',
        'min':8,
        'max':20
        },pwdMsg) && validate(conPwd,{
        'name':'Confirm Password',
        'required':true,
        'same':pwd
        },conPwdMsg) && validate(phone,{
        'name':'Phone No',
        'required':true,
        'pattern':'^(080|070|090|081|091|071)[0-9]{8}$'
        },phoneMsg) && validate(pin,{
        'name':'Pin',
        'required':true,
        'length':14,
        'pattern':'^[0-9a-zA-Z]+$'
         },pinMsg) && validate(account,{
        'name':'Account No',
        'required':true,
        'pattern':'^[0-9]+$',
        'max':15,
        'min':10
        },accountMsg)){
        return true;
    }else{
        return false;
    }
}