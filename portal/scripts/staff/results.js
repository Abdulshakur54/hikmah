function viewResult() {
  let classId = _("class").value;
  let url = _("url").value;

  ld_startLoading("resultBtn");
  let postData =
    "op=view_results&class_id=" +
    classId +
    "&school=" +
    _("school").value +
    "&token=" +
    _("token").value;
  ajaxRequest(
    "management/hos/responses/responses.php",
    viewResultRsp,
    postData
  );

  function viewResultRsp() {
    const rsp = JSON.parse(xmlhttp.responseText);
    let successCodes = [200, 201, 204];
    _("token").value = rsp.token;
    if (successCodes.includes(rsp.status)) {
      let results = rsp.data;
      ld_stopLoading("resultBtn");
      if (results.length > 0) {
        let students = [];
        results.forEach((element) => {
          students.push(element.std_id);
        });
        location.assign(
          url +
            "&token=" +
            rsp.token +
            "&student_ids=" +
            JSON.stringify(students)
        );
      } else {
        swalNotify("Result is not ready for the class", "info");
      }
    }
  }
}

$(".js-example-basic-single").select2();
