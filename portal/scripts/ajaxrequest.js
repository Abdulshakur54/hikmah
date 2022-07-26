const xmlhttp =  new XMLHttpRequest();

function ajaxRequest(url,callBack,postData=null){
    xmlhttp.onreadystatechange = function(){ 
        if(xmlhttp.readyState===4 && xmlhttp.status===200){
            callBack();
        }
    };
    if(postData === null){
        xmlhttp.open('GET',url,true);
        xmlhttp.send();
    }else{
        xmlhttp.open('POST',url,true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(postData);
    }

}

function ajaxRequestWithUpload(url,callBack,postData){ 
    xmlhttp.onreadystatechange = function(){ 
        if(xmlhttp.readyState===4 && xmlhttp.status===200){
            callBack();
        }
    };
    xmlhttp.open('POST',url,true);
    xmlhttp.send(postData);
}