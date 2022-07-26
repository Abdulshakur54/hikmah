function getSchools(type=1){
    switch(type){
        case 1:
            return{
                'Hikmah College Katako':'HCK',
                'Hikmah College Bauchi Road':'HCB',
                'Hikmah International School':'HIS',
                'Hikmah Academy':'HA',
                'Hikmah Madrasah':'HM',
                'Hikmah Creche Islamiyah':'HCI',
                'Hikmah E-Madrasah':'H E-M',
                'Hikmah College Madrasah':'HCM'
            };
            
        case 2:
            return['HCK','HCB','HIS','HA','HM','HCI','H E-M','HCM'];
            
        case 3:
            return['Hikmah College Katako','Hikmah College Bauchi Road','Hikmah International School','Hikmah Academy','Hikmah Madrasah','Hikmah Creche Islamiyah','Hikmah E-Madrasah','Hikmah College Madrasah'];
            
    }
    
}

function getIslamiyahSchools(type=1){
    switch(type){
        case 1:
            return{
                'Hikmah Madrasah':'HM',
                'Hikmah Creche Islamiyah':'HCI',
                'Hikmah E-Madrasah':'H E-M',
                'Hikmah College Madrasah':'HCM'
            };
            
        case 2:
            return['HM','HCI','H E-M','HCM'];
            
        case 3:
            return['Hikmah Madrasah','Hikmah Creche Islamiyah','Hikmah E-Madrasah','Hikmah College Madrasah'];
            
    }
    
}

function getConvectionalSchools(type=1){
    switch(type){
        case 1:
            return{
                'Hikmah College Katako':'HCK',
                'Hikmah College Bauchi Road':'HCB',
                'Hikmah International School':'HIS',
                'Hikmah Academy':'HA'
            };
            
        case 2:
            return['HCK','HCB','HIS','HA'];
            
        case 3:
            return['Hikmah College Katako','Hikmah College Bauchi Road','Hikmah International School','Hikmah Academy'];
            
    }
    
}

function getBanks(){
    return ['FIRST BANK','ZENITH BANK', 'GUARANTEED TRUST BANK'];
}

function getLevels(sch_abbr){
    let abbr = sch_abbr.toUpperCase();
    switch (abbr){
        case 'HIS':
        case 'HA':
            return{
                'Preparatory':1,
                'Nursery 1':2,
                'Nursery 2':3,
                'Pre-Basic':4,
                'Basic 1':5,
                'Basic 2':6,
                'Basic 3':7,
                'Basic 4':8,
                'Basic 5':9,
            };
        case 'HCK':
        case 'HCB':
            return{
                'J.S.S 1':1,
                'J.S.S 2':2,
                'J.S.S 3':3,
                'S.S 1':4,
                'S.S 2':5,
                'S.S 3':6         
            };
        case 'HCI':
        case 'HM':
            return{
                'KG':1,
                'Creche 1':2,
                'Creche 2':3,
                'Primary 1':4,
                'Primary 2':5,
                'Primary 3':6,
                'Primary 4':7,
                'Primary 5':8 
            };
        case 'H E-M':
        case 'HCM':
            return{
                'J.S.S 1':1,
                'J.S.S 2':2,
                'J.S.S 3':3
            };
    }
}

