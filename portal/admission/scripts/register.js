let fname = _('fname');
let lname = _('lname');
let oname = _('oname');
let pwd = _('password');
let conPwd = _('c_password');
let phone = _('phone');
let pin = _('pin');
let email = _('email');
let scriptValid = _('scriptValid');

let fNameMsg = _('fNameMsg');
let lNameMsg = _('lNameMsg');
let oNameMsg = _('oNameMsg');
let pwdMsg = _('pwdMsg');
let conPwdMsg = _('conPwdMsg');
let phoneMsg = _('phoneMsg');
let pinMsg = _('pinMsg');
let emailMsg = _('emailMsg');

let valFName = false;
let valLName = false;
let valOName = true;
let valPwd = false;
let valConPwd = false;
let valPhone = false;
let valPin = false;
let valEmail = true;

let genMsg = _('genMsg');


runFirst();

function runFirst(){
    if(scriptValid.value === 'true'){ //this means the form has been submitted via post
        //set all validation variable to true to avoid re validation
        valFName = true;
        valLName = true;
        valPhone = true;
        valPin = true;
    }
}

fname.addEventListener('blur', function(){
     valFName = validate(fname,{
        'name':'First Name',
        'required':true,
        'pattern':'^[a-zA-z`]+$',
        'max':30,
        'min':3
        },fNameMsg);
    }
);


fname.addEventListener('keyup', function(){
     valFName = instantValidate(fname,{
        'name':'First Name',
        'required':true,
        'pattern':'^[a-zA-z`]+$',
        'max':30,
        'min':3
    },fNameMsg);
}
);


lname.addEventListener('blur', function(){
     valLName = validate(lname,{
        'name':'Last Name',
        'required':true,
        'pattern':'^[a-zA-z`]+$',
        'max':30,
        'min':3
        },lNameMsg);
    }
);


lname.addEventListener('keyup', function(){
     valLName = instantValidate(lname,{
        'name':'Last Name',
        'required':true,
        'pattern':'^[a-zA-z`]+$',
        'max':30,
        'min':3
    },lNameMsg);
}
);


oname.addEventListener('blur', function(){
     valOName = validate(oname,{
        'name':'OtherName',
        'pattern':'^[a-zA-z`]+$',
        'max':30,
        'min':3
        },oNameMsg);
    }
);


oname.addEventListener('keyup', function(){
     valOName = instantValidate(oname,{
        'name':'OtherName',
        'pattern':'^[a-zA-z`]+$',
        'max':30,
        'min':3
        },oNameMsg);
    }
);


pwd.addEventListener('blur',function(){
    valPwd = validate(pwd,{
        'name':'Password',
        'required':true,
        'pattern':'^[a-zA-z 0-9]+$',
        'min':8,
        'max':20
    },pwdMsg);
});

pwd.addEventListener('keyup',function(){
    valPwd = instantValidate(pwd,{
        'name':'Password',
        'required':true,
        'pattern':'^[a-zA-z 0-9]+$',
        'max':20
    },pwdMsg);
});

conPwd.addEventListener('blur',function(){
    valConPwd = validate(conPwd,{
        'name':'Confirm Password',
        'required':true,
        'same':pwd
    },conPwdMsg);
});

phone.addEventListener('blur',function(){
    valPhone = validate(phone,{
        'name':'Phone No',
        'required':true,
        'pattern':'^(080|070|090|081|091|071)[0-9]{8}$'
    },phoneMsg);
});

phone.addEventListener('keyup',function(){
    valPhone = instantValidate(phone,{
        'name':'Phone No',
        'required':true,
        'pattern':'^(080|070|090|081|091|071)[0-9]+$',
        'max':11
    },phoneMsg,4);
});

pin.addEventListener('blur',function(){
    valPin = validate(pin,{
        'name':'Pin',
        'required':true,
        'length':14,
        'pattern':'^[0-9a-zA-Z]+$'
    },pinMsg);
});

pin.addEventListener('keyup',function(){
    valPin = instantValidate(pin,{
        'name':'Pin',
        'required':true,
        'pattern':'^[0-9a-zA-Z]+$',
        'max':14
    },pinMsg);
});


email.addEventListener('blur', function(){
     valEmail = validate(email,{
        'name':'Email',
        'required':true,
        'pattern':'^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+$',
        'max':70,
        'min':7
        },emailMsg);
    }
);

email.addEventListener('keyup', function(){
     valEmail = instantValidate(email,{
        'name':'Email',
        'required':true,
        'pattern':'^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+$',
        'max':70
        },emailMsg,3);
    }
);


function submitForm(){
    if(valFName && valLName && valOName && valPwd && valConPwd && valPhone && valPin && valEmail){
        return true;
    }
    genMsg.innerHTML = 'Ensure you fill all fields appropriately';
    return false;
}