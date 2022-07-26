function _(id){
    return document.getElementById(id);
}
function __(className){
    return document.getElementsByClassName(className);
}

//this function returns the lenght of an object by counting its keys
function objLength(obj){
    return Object.keys(obj).length;
}

//this function returns the lenght of an object by counting its values
function objLen(obj){
    return Object.values(obj).length;
}

//this function checks if 2 strings are equal and returns a boolean
function equal(str1,str2){
    if(str1.toLowerCase() === str2.toLowerCase()){
        return true;
    }
    return false;
}

//this function expects and input dom element as its parameter
function empty(strval){
    if(strval.value.trim().length > 0){
        return false;
    }
    return true;
}

//this expects a string value as its parameter
function emp(val){
    if(val.trim().length > 0){
        return false;
    }
    return true;
}



