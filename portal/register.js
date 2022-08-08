$(".js-example-basic-single").select2();

function showImage(event) {
  if (objLength(event.files) > 0) {
    if (event.files[0].size > 100 * 1024) {
      image.style.display = "none";
      picMsg.innerHTML =
        '<div class="failure">Picture should not be more than 100kb</div>';
      return;
    }
    picMsg.innerHTML = "";
    hiddenPic.value = "hasvalue";
    image.src = URL.createObjectURL(event.files[0]);
    image.style.display = "block";
  } else {
    hiddenPic.value = "";
    picMsg.innerHTML = '<div class="failure">Select a Picture</div>';
    image.style.display = "none";
  }
}


function changeContent(userType){
  location.assign('register.php?user_type='+userType);
}

function register(event) {
  event.preventDefault();
  if (validate("regForm", { validateOnSubmit: true })) {
    document.getElementById("userType").value =
      localStorage.getItem("user_type");
    event.target.submit();
  }
}

validate("regForm");