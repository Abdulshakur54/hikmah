let valUname = false;
let valPwd = false;

let uname = _('username');
let pwd = _('password');
let unameMsg = _('unameMsg');
let pwdMsg = _('pwdMsg');

uname.addEventListener('keyup',function(){
    valUname = instantValidate(uname,{
        'name':'Username',
        'required':true,
        'pattern':'^[a-zA-Z`][a-zA-Z0-9`]+$'
    },unameMsg);
});

pwd.addEventListener('keyup',function(){
    valPwd = instantValidate(pwd,{
        'name':'Password',
        'required':true,
        'pattern':'^[a-zA-Z0-9]+$'
    },pwdMsg);    
});

uname.addEventListener('blur',function(){
    valUname = validate(uname,{
        'name':'Username',
        'required':true,
         'pattern':'^[a-zA-Z`][a-zA-Z0-9`]+$'
    },unameMsg);
});

pwd.addEventListener('blur',function(){
    valPwd = validate(pwd,{
        'name':'Password',
        'required':true,
        'pattern':'^[a-zA-Z0-9]+$'
    },pwdMsg);    
});

function submitForm(){
    if(!valPwd || !valUname){
        return false;
    }
    if(!empty(uname) && !empty(pwd)){
        return true;
    }
    return false
}



