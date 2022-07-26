let token = _('token');

function deleteExam(examId){
    let cont = confirm('Are you sure you want to delete. \nAll data related to this exam including examinee result will be deleted');
    if(cont){
        location.assign('published_exams.php?examid='+examId+'&token='+token.value);
    }
    
}