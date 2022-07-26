let valFName = true;
let valLName = true;
let valOName = true;

let fName = _('fname');
let lName = _('lname');
let oName = _('oname');
let fNameMsg = _('firstNameMsg');
let lNameMsg = _('lastNameMsg');
let oNameMsg = _('otherNameMsg');

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

function submitForm(){
    if(!valFName || !valLName || !valOName){
        return false;
    }
    if(!empty(fName) && !empty(lName) && !empty(oName)){
        return true;
    }
    return false
}



