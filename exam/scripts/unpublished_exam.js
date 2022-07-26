let token = _('token');

function deleteExam(examId){
    let cont = confirm('Are you sure you want to delete');
    if(cont){
        location.assign('unpublished_exams.php?examid='+examId+'&token='+token.value);
    }
    
}