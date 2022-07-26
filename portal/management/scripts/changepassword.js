let valConNewPwd = false;
let valNewPwd = false;
let valOldPwd = false;

let newPwd = _('new_pwd');
let coNewPwd = _('c_new_pwd');
let oldPwd = _('old_pwd');
let newPwdMsg = _('newPwdMsg');
let coNewPwdMsg = _('conNewPwdMsg');
let oldPwdMsg = _('oldPwdMsg');

newPwd.addEventListener('keyup',function(){
    valNewPwd = instantValidate(newPwd,{
        'name':'New Password',
        'required':true,
        'pattern':'^[a-zA-z0-9]+$'
    },newPwdMsg);
});

coNewPwd.addEventListener('keyup',function(){
    valConNewPwd = instantValidate(coNewPwd,{
        'name':'Confirm Password',
        'required':true,
        'pattern':'^[a-zA-z0-9]+$'
    },coNewPwdMsg);    
});

newPwd.addEventListener('blur',function(){
    valNewPwd = validate(newPwd,{
        'name':'New Password',
        'required':true,
        'pattern':'^[a-zA-z0-9]+$'
    },newPwdMsg);
});

coNewPwd.addEventListener('blur',function(){
    valConNewPwd = validate(coNewPwd,{
        'name':'Confirm Password',
        'required':true,
        'pattern':'^[a-zA-z0-9]+$',
        'same':newPwd
    },coNewPwdMsg);    
});



oldPwd.addEventListener('keyup',function(){
    valOldPwd = instantValidate(oldPwd,{
        'name':'Old Password',
        'required':true,
        'pattern':'^[a-zA-z0-9]+$'
    },oldPwdMsg);
});

oldPwd.addEventListener('blur',function(){
    valOldPwd = validate(oldPwd,{
        'name':'Old Password',
        'required':true,
        'pattern':'^[a-zA-z0-9]+$'
    },oldPwdMsg);    
});


function submitForm(){
    if(!valNewPwd || !valConNewPwd || !valOldPwd){
        return false;
    }
    if(!empty(oldPwd) && !empty(newPwd) && !empty(coNewPwd)){
        return true;
    }
    return false
}



