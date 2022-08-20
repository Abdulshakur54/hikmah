position.addEventListener("change", function () {
  let genHtml = "";
  let schools = "";
  switch (position.value) {
    case "5":
      schools = getConvectionalSchools();
      for (let school in schools) {
        genHtml +=
          '<option value="' + schools[school] + '">' + school + "</option>";
      }
      break;
    case "17":
      schools = getIslamiyahSchools();
      for (let school in schools) {
        genHtml +=
          '<option value="' + schools[school] + '">' + school + "</option>";
      }
      break;
    default:
      genHtml += "<option value='All'>All</option>";
  }
  $('#school').html(genHtml);
});

function addToken() {
  if (validate("tokenForm", { validateOnSubmit: true })) {
    ld_startLoading("generatePin");
    const staffname = _("staffname");
    const position = _("position");
    const salary = _("salary");
    const token = _("token");
    const school = _("school");
    ajaxRequest(
      "management/director/responses/add_token.rsp.php",
      addTokenRsp,
      "name=" +
        staffname.value +
        "&rank=" +
        position.value +
        "&sch_abbr=" +
        school.value +
        "&token=" +
        token.value +
        "&salary=" +
        salary.value
    );
  }

  function addTokenRsp() {
    let rsp = JSON.parse(xmlhttp.responseText);
    let msgStyleClass;
    const msgDiv = _("messageContainer");
    if (rsp.success) {
      msgStyleClass = "success m-2";
      msgDiv.innerHTML =
        '<div>Pin successfully generated, Registration details below</div>' +
        '<div class="message">Name: ' +
        staffname.value +
        "<br/>Token: " +
        rsp.createdToken +
        "</div>";
    } else {
      msgStyleClass = "failure m-2";
      msgDiv.innerHTML= '<div>' + rsp.message + '</div>';
    }
    _("token").value = rsp.token;
    msgDiv.className = msgStyleClass;
    emptyInputs(["staffname", "position", "school", "salary"]);
      resetInputStyling("tokenForm", "inputsuccess", "inputfailure");
    ld_stopLoading("generatePin");
  }
  
}
