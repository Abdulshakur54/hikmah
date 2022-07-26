let valFName = true;
let valLName = true;
let valOName = true;
let valUname = true;
let valPwd = true;
let valConPwd = true;

let fName = _('fname');
let lName = _('lname');
let oName = _('oname');
let uname = _('username');
let pwd = _('password');
let conPwd = _('c_password');

let fNameMsg = _('fNameMsg');
let lNameMsg = _('lNameMsg');
let oNameMsg = _('oNameMsg');
let unameMsg = _('usernameMsg');
let pwdMsg = _('pwdMsg');
let conPwdMsg = _('conPwdMsg');


fName.addEventListener('keyup',function(){
    valFName = instantValidate(fName,{
        'name':'First Name',
        'required':true,
        'pattern':'^[a-zA-z`]+$'
    },fNameMsg);
});

fName.addEventListener('blur',function(){
    valFName = validate(fName,{
        'name':'First Name',
        'required':true,
        'pattern':'^[a-zA-z`]+$'
    },fNameMsg);
});

lName.addEventListener('keyup',function(){
    valLName = instantValidate(lName,{
        'name':'Last Name',
        'required':true,
        'pattern':'^[a-zA-z`]+$'
    },lNameMsg);
});

lName.addEventListener('blur',function(){
    valLName = validate(lName,{
        'name':'Last Name',
        'required':true,
        'pattern':'^[a-zA-z`]+$'
    },lNameMsg);
});

oName.addEventListener('keyup',function(){
    valOName = instantValidate(oName,{
        'name':'Other Name',
        'required':false,
        'pattern':'^[a-zA-z`]+$'
    },oNameMsg);
});

oName.addEventListener('blur',function(){
    valOName = validate(oName,{
        'name':'Other Name',
        'required':false,
        'pattern':'^[a-zA-z`]+$'
    },oNameMsg);
});

uname.addEventListener('keyup',function(){
    valUname = instantValidate(uname,{
        'name':'Username',
        'required':true,
        'pattern':'^[a-zA-z`][a-zA-z 0-9]+$'
    },unameMsg);
});

uname.addEventListener('blur',function(){
    valUname = validate(uname,{
        'name':'Username',
        'required':true,
        'pattern':'^[a-zA-z`][a-zA-z`0-9]+$'
    },unameMsg);
});

pwd.addEventListener('blur',function(){
    valPos = validate(pwd,{
        'name':'Password',
        'required':true,
        'pattern':'^[a-zA-z 0-9]+$'
    },pwdMsg);
});

pwd.addEventListener('keyup',function(){
    valPos = instantValidate(pwd,{
        'name':'Password',
        'required':true,
        'pattern':'^[a-zA-z 0-9]+$'
    },pwdMsg);
});

conPwd.addEventListener('blur',function(){
    valConPwd = validate(conPwd,{
        'name':'Confirm Password',
        'required':true,
        'pattern':'^[a-zA-z 0-9]+$',
        'same':pwd
    },conPwdMsg);
});

conPwd.addEventListener('keyup',function(){
    valConPwd = instantValidate(conPwd,{
        'name':'Confirm Password',
        'required':true,
        'pattern':'^[a-zA-z 0-9]+$'
    },conPwdMsg);
});






function submitForm(){
    if(!valFName || !valLName || !valOName || !valPwd || !valConPwd){
        return false;
    }
    if(!empty(fName) && !empty(lName) && !empty(oName) && !empty(pwd) && !empty(conPwd)){
        return true;
    }
    return false
}



