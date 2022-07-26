let ld_loader;
let ld_interval;
let ld_count = 1;


function ld_startLoading(conToDisable = '', loader=''){
    if(loader.length > 0){ // a particular loader is specified
        ld_loader = document.getElementById(loader);
    }else{
        ld_loader = document.getElementById('ld_loader');
    }
    
    if(conToDisable.length > 0){
        if(!Array.isArray(conToDisable)){
            document.getElementById(conToDisable).disabled = true;
        }else{
           for(let x of conToDisable){
               document.getElementById(x).disabled = true;
           }
        }
        
    }
    ld_loader.className = 'ld_loaderStyle';
    ld_loader.style.display='inline-block';
    ld_interval = setInterval(ld_loading,100); //you can change the frequency of rotaion here which is correctly set to 100;
}

function ld_loading(){
    switch(ld_count){
        case 1:
            ld_loader.style.borderColor = 'rgb(233,244,20)';  //you can change the colors of each box border here
            ld_loader.style.borderRightColor = 'rgb(13,8,234)';
        break;
        case 2:
            ld_loader.style.borderColor = 'rgb(233,244,20)';
            ld_loader.style.borderBottomColor = 'rgb(13,8,234)';
        break;
        case 3:
            ld_loader.style.borderColor = 'rgb(233,244,20)';
            ld_loader.style.borderLeftColor = 'rgb(13,8,234)';
        break;
        case 4:
            ld_loader.style.borderColor = 'rgb(233,244,20)';
            ld_loader.style.borderTopColor = 'rgb(13,8,234)';
        break;
    }
    ld_count++;
    if(ld_count > 4){
        ld_count = 1;
    }
}

function ld_stopLoading(conToDisable = '', loader=''){
    if(loader.length > 0){ // a particular loader is specified
        ld_loader = document.getElementById(loader);
    }else{
        ld_loader = document.getElementById('ld_loader');
    }
    clearInterval(ld_interval);
    ld_loader.style.display='none';
    if(conToDisable.length > 0){
        if(!Array.isArray(conToDisable)){
            document.getElementById(conToDisable).disabled = false;
        }else{
           for(let x of conToDisable){
               document.getElementById(x).disabled = false;
           }
        }
        
    }
}