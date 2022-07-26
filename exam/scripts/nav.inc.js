let show = false;
    let = toggleBar = document.getElementById('toggleBar');
    let = toggleBody = document.getElementById('toggleBody');
    let schoolPortal = document.getElementById('schoolPortal');
    let toggleDiv = document.getElementById('toggleDiv');
    toggleBar.addEventListener('click',function(){
        if(!show){
            toggleBody.style.display = 'block';
            schoolPortal.style.display = 'block';
            toggleDiv.style.marginBottom = '50px';
            show = true;
        }else{
            toggleBody.style.display = 'none';
            schoolPortal.style.display = 'none';
            toggleDiv.style.marginBottom = '0px';
            show = false;
        }
    });
    
    //for the full height navigation 
    let fullNav = true;
    let toggleBarLg = document.getElementById('toggleBarLg');
    let nav = document.querySelector('nav');
    let main = document.querySelector('main');
    let header = document.querySelector('header');
    toggleBarLg.addEventListener('click',
        function(){
            if(!fullNav){
                nav.style.width = '270px';
                main.style.marginLeft = '270px';
                header.style.marginLeft = '270px';
                toggleBody.style.display = 'block';
                schoolPortal.style.display = 'block';
                toggleDiv.style.marginBottom = '50px';
                fullNav = true;
            }else{
                nav.style.width = '150px';
                main.style.marginLeft = '150px';
                header.style.marginLeft = '150px';
                toggleBody.style.display = 'none';
                schoolPortal.style.display = 'none';
                toggleDiv.style.marginBottom = '0px';
                fullNav = false;
            }
        }
    );


