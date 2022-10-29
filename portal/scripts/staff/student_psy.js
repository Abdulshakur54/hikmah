$(".js-example-basic-single").select2();
function changeStudent() {
  let std_id = _("student").value;
  getPage("staff/student_psy.php?std_id=" + std_id);
}


async function saveChanges() {
  await getPostPageWithUpload(
    "scheduleForm",
    "staff/responses/responses.php",
    "update_student_psy"
  );
  resetInputStyling("scheduleForm", "inputsuccess", "inputfailure");
}
