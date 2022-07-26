function changeContent(tab) {
  const children = document.getElementsByClassName("tab")[0].children;
  for (let child of children) {
    child.classList.remove("active");
  }
  localStorage.setItem("user_type", tab);
  document.getElementById(tab).className = "active";
  if(tab==='admission'){
    tab  = 'admission student';
  }
  document.getElementById('headerInfo').innerHTML = 'Sign in as a '+tab;
}

function defaultTab(){
    if(localStorage.getItem('user_type')===null){
        changeContent('student');
    }else{
        changeContent(localStorage.getItem('user_type'));
    }
}

defaultTab();

function login(event){
    event.preventDefault();
   if(validate("loginForm", { validateOnSubmit: true })){
        document.getElementById('userType').value = localStorage.getItem('user_type');
        document.getElementById("signInBtn").disabled = true;
        event.target.submit();
   }
}

validate("loginForm");
