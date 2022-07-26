function validate(elementId,rule={},msgDivId){
    let empty;
    let val = (elementId.value);
    let msgDiv = msgDivId;
    if(val.trim().length > 0){
        empty = false;
    }else{
        empty = true;
    }
    let name;
    let rule_val;
    for(let x in rule){
        rule_val = rule[x];
        switch(x){
            case 'name': 
                name = rule_val; //set the name that will be used to refer to the form field
                break;
            case 'required':
                let required = rule_val; //determines if a field is required
                if(empty && required){
                    msgDiv.innerHTML = name+' is required';
                    msgDiv.style.display = 'block';
                    return false;
                }
                msgDiv.innerHTML = '';
                break;

            case 'max':
                if(!empty && val.length > rule_val){
                    msgDiv.innerHTML = name+' should be maximum of '+rule_val+ ' characters';
                    msgDiv.style.display = 'block';
                    return false;
                }
                msgDiv.innerHTML = '';
                break;
            case 'min':
                if(!empty && val.length < rule_val){
                    msgDiv.innerHTML = name+' should be minimum of '+rule_val+ ' characters';
                    msgDiv.style.display = 'block';
                    return false;
                }
                msgDiv.innerHTML = '';
                break;
            case 'pattern':
                if(!empty && !(val.search(rule_val) > -1)){
                    msgDiv.innerHTML = 'Invalid '+name;
                    msgDiv.style.display = 'block';
                    return false;
                }
                msgDiv.innerHTML = '';
                break;
            case 'same':
                if(val !== rule_val.value){
                    msgDiv.innerHTML = name +' must match Password Field';
                    msgDiv.style.display = 'block';
                    return false;
                }
                msgDiv.innerHTML = '';
                break;
            case 'in':
                if(val.indexOf(rule_val)){
                    msgDiv.innerHTML = name+ ' is invalid';
                    msgDiv.style.display = 'block';
                    return false;
                }
                msgDiv.innerHTML = '';
            break;
            case 'maximum':
                if(!empty && parseInt(val) > rule_val){	
                    msgDiv.innerHTML = name+' should be maximum of '+rule_val;
                    msgDiv.style.display = 'block';
                    return false;
                }
                msgDiv.innerHTML = '';
            break;
            case 'minimum':
                if(!empty && parseInt(val) < rule_val){	
                    msgDiv.innerHTML = name+' should be minimum of '+rule_val;
                    msgDiv.style.display = 'block';
                    return false;
                }
                msgDiv.innerHTML = '';
                msgDiv.style.display = 'none';
            break;
        }
    }
    return true;
}


function instantValidate(elementId,rule={},msgDivId, min=3){
    let empty;
    let val = (elementId.value);
    let msgDiv = msgDivId;
    let inputLen = val.trim().length;
    if(inputLen > 0){
        empty = false;
    }else{
        empty = true;
    }
    if(inputLen >= min){
        let name;
        let rule_val;
        for(let x in rule){
            rule_val = rule[x];
            switch(x){
                case 'name': 
                    name = rule_val; //set the name that will be used to refer to the form field
                    break;
                case 'required':
                    let required = rule_val; //determines if a field is required
                    if(empty && required){
                        msgDiv.innerHTML = name+' is required';
                        msgDiv.style.display = 'block';
                        return false;
                    }
                    msgDiv.innerHTML = '';
                    break;
    
                case 'max':
                    if(!empty && val.length > rule_val){
                        msgDiv.innerHTML = name+' should be maximum of '+rule_val+ ' characters';
                        msgDiv.style.display = 'block';
                        return false;
                    }
                    msgDiv.innerHTML = '';
                    break;
                case 'min':
                    if(!empty && val.length < rule_val){
                        msgDiv.innerHTML = name+' should be minimum of '+rule_val+ ' characters';
                        msgDiv.style.display = 'block';
                        return false;
                    }
                    msgDiv.innerHTML = '';
                    break;
                case 'pattern':
                    if(!empty && !(val.search(rule_val) > -1)){
                        msgDiv.innerHTML = 'Invalid '+name;
                        msgDiv.style.display = 'block';
                        return false;
                    }
                    msgDiv.innerHTML = '';
                    break;
                case 'same':
                    if(val !== rule_val.value){
                        msgDiv.innerHTML = name +' must match Password Field';
                        msgDiv.style.display = 'block';
                        return false;
                    }
                    msgDiv.innerHTML = '';
                    break;
                case 'in':
                    if(val.indexOf(rule_val)){
                        msgDiv.innerHTML = name+ ' is invalid';
                        msgDiv.style.display = 'block';
                        return false;
                    }
                    msgDiv.innerHTML = '';
                break;
                case 'maximum':
                    if(!empty && parseInt(val) > rule_val){	
                        msgDiv.innerHTML = name+' should be maximum of '+rule_val;
                        msgDiv.style.display = 'block';
                        return false;
                    }
                    msgDiv.innerHTML = '';
                break;
                case 'minimum':
                    if(!empty && parseInt(val) < rule_val){	
                        msgDiv.innerHTML = name+' should be minimum of '+rule_val;
                        msgDiv.style.display = 'block';
                        return false;
                    }
                    msgDiv.innerHTML = '';
                    msgDiv.style.display = 'none';
                break;
            }
        }
        return true; 
    }else{
        msgDiv.innerHTML = '';
    }
}

//this function ensures that the script tag is not inputted by a user
function noScripts(val){
    if(val.search(/<[ ]*script[ ]*>/) > -1){
        return false;
    }
    return true;
}