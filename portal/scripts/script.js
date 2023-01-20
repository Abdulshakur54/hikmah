function _(id) {
  return document.getElementById(id);
}
function __(className) {
  return document.getElementsByClassName(className);
}

//this function returns the lenght of an object by counting its keys
function objLength(obj) {
  return Object.keys(obj).length;
}

//this function returns the lenght of an object by counting its values
function objLen(obj) {
  return Object.values(obj).length;
}

//this function checks if 2 strings are equal and returns a boolean
function equal(str1, str2) {
  if (str1.toLowerCase() === str2.toLowerCase()) {
    return true;
  }
  return false;
}

//this function expects and input dom element as its parameter
function empty(strval) {
  if (strval.value.trim().length > 0) {
    return false;
  }
  return true;
}

//this expects a string value as its parameter
function emp(val) {
  if (val.trim().length > 0) {
    return false;
  }
  return true;
}

function capitalizeFirstLetter(word) {
  const firstLetter = word.substr(0, 1);
  return firstLetter.toUpperCase() + word.substr(1, word.length - 1);
}
function capitalizeWords(sentence) {
  const words = sentence.split(" ");
  let capitalizedWordsInSentence = "";
  for (let word of words) {
    capitalizedWordsInSentence += " " + capitalizeFirstLetter(word);
  }
  return capitalizedWordsInSentence.trim();
}

function formatName(fname, oname, lname, shorten_other_name = true) {
  if (oname.length > 0) {
    if (shorten_other_name) {
      return capitalizeWords(fname + " "+oname.substr(0, 1) + ". "+lname);
    }
    return capitalizeWords(fname + " " + oname + " " + lname);
  }
  return capitalizeWords(fname + " " + lname);
}
function round(number,dp){
  return Math.round(number * (10**dp))/(10**dp);
}
function formatMoney(money){
  const result = Intl.NumberFormat('en-US',{style: 'currency','currency':'USD'}).format(money);
  return result.substring(1,result.length-1);
}

