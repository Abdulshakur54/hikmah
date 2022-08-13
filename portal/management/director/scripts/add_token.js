position.addEventListener("change", function () {
    let genHtml = '';
  switch (position.value) {
    case "5":
      break;
      for (let school in schools) {
        genHtml +=
          '<option value="' + schools[school] + '">' + school + "</option>";
      }
    case "17":
      schools = getIslamiyahSchools();
      for (let school in schools) {
        genHtml +=
          '<option value="' + schools[school] + '">' + school + "</option>";
      }
      break;
    default:
      genHtml +=
       "<option value='All'>All</option>";
  }
  _('school').innerHTML = genHtml;
});
