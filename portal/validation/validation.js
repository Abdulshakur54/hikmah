var msgElement = "block";
var defaultOptions = {
  validateOnSubmit: false,
  display: "block",
  instant: true,
  successMessageClass: "success",
  failureMessageClass: "failure",
  successInputClass: "inputsuccess",
  failureInputClass: "inputfailure",
  displaySuccessMessage: false,
  successMessageText: "valid",
  minCharForSuccessMessageText: 3,
  numberIncludedForMinCharForSuccessMessageText: false,
};
var defaultMessages = {
  required: "val_Title is required",
  email: "Enter a valid Email",
  pattern: "Invalid val_Title entered",
  minlength: "Minimum of val_RuleValue characters allowed for val_Title",
  maxlength: "Maximum of val_RuleValue characters allowed for val_Title",
  size: "val_Title must be val_RuleValue characters long",
  max: "val_Title should not be greater than val_RuleValue",
  min: "val_Title should not be less than val_RuleValue",
};

function validate(formId, options = {}, customMessages = {}) {
  options = { ...defaultOptions, ...options };
  customMessages = { ...defaultMessages, ...customMessages };
  const form = document.getElementById(formId);
  const textInput = form.querySelectorAll(
    "input[type=text], textarea, input[type=number]"
  );
  const numberInput = form.querySelectorAll("input[type=number]");
  const radioInput = form.querySelectorAll("input[type=radio]");
  const checkInput = form.querySelectorAll("input[type=checkbox]");
  const selectInput = form.querySelectorAll("select");
  const otherTextInput = form.querySelectorAll(
    "input[type=email],[type=date],[type=number],[type=password],[type=month],[type=tel],[type=time],[type=url],[type=week]"
  );

  //form is to be submitted by setting validateOnSubmit property of options to true;
  if (options.validateOnSubmit) {
    const allElements = [
      ...textInput,
      ...radioInput,
      ...checkInput,
      ...selectInput,
      ...otherTextInput,
      ...numberInput,
    ];
    let valid = true;
    for (let eachElement of allElements) {
      if (!sanitize(eachElement, true)) {
        valid = false;
      }
    }
    return valid;
  }

  //end of form is to be submitted
  //the rest of the code will run if form is not submitted

  msgElement = "block" ? "div" : "span";
  //add Event Listeners to the inputs if enabled

  if (options.instant) {
    addEvents(textInput, "keyup");
    addEvents(numberInput, "change");
    addEvents(radioInput);
    addEvents(checkInput);
    addEvents(selectInput);
    addEvents(otherTextInput);
    addEvents(otherTextInput, "keyup");

    addEvents(textInput, "blur");
    addEvents(numberInput, "blur");
    addEvents(radioInput, "blur");
    addEvents(checkInput, "blur");
    addEvents(selectInput, "blur");
    addEvents(otherTextInput, "blur");
    addEvents(otherTextInput, "blur");
  }

  function addEvents(controls, event = "change") {
    for (let control of controls) {
      control.addEventListener(event, sanitize);
    }
  }

  function sanitize(event, eventIsElement = false) {
    let element = "";
    if (eventIsElement) {
      element = event;
    } else {
      element = event.target;
    }
    const userInput = element.value.trim();
    const inputType = element.type;
    if (element.required) {
      switch (inputType) {
        case "radio":
        case "checkbox":
          const inputGroup = document.querySelectorAll(
            `input[name=${element.name}]`
          );
          let inputGroupChecked = false;
          for (let input of inputGroup) {
            if (input.checked) {
              inputGroupChecked = true;
              break;
            }
          }
          if (!inputGroupChecked) {
            outputMessage(element, "failure", "required");
            return false;
          }
          break;
        default:
          if (userInput.length < 1) {
            outputMessage(element, "failure", "required");
            return false;
          }
      }
    }

    //validation valid only for input fields with tagname of input

    if (element.tagName.toLowerCase() === "input") {
      //this would remove textarea from the elements
      if (element.maxLength !== -1 && userInput.length > 0) {
        const max = element.maxLength;
        if (userInput.length > max) {
          outputMessage(element, "failure", "maxlength", { ruleValue: max });
          return false;
        }
      }

      if (element.minLength !== -1 && userInput.length > 0) {
        const min = element.minLength;
        if (userInput.length < min) {
          outputMessage(element, "failure", "minlength", { ruleValue: min });
          return false;
        }
      }

      if (element.max.length > 0 && userInput.length > 0) {
        const max = parseInt(element.max);
        if (parseInt(userInput) > max) {
          outputMessage(element, "failure", "max", { ruleValue: max });
          return false;
        }
      }

      if (element.min.length > 0 && userInput.length > 0) {
        const min = parseInt(element.min);
        if (parseInt(userInput) < min) {
          outputMessage(element, "failure", "min", { ruleValue: min });
          return false;
        }
      }

      if (element.pattern.length > 0 && userInput.length > 0) {
        if (!match(userInput, element.pattern)) {
          if (element.getAttribute("data-error-message") !== null) {
            outputMessage(element, "failure", "pattern", undefined, {
              error: element.getAttribute("data-error-message"),
              success: "",
            });
          } else {
            outputMessage(element, "failure", "pattern");
          }
          return false;
        }
      }

      if (element.pattern.length == 0 && userInput.length > 0) {
        //validate input type only when nothing is entered for pattern, this would help use the default form validation types in html to help fake filler fill appropriately
        switch (inputType) {
          case "email":
            if (
              !match(userInput, "/^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+$/")
            ) {
              outputMessage(element, "failure", "email");
              return false;
            }
            break;
          case "phone":
            if (!match(userInput, "^(080|070|090|081|091|071)[0-9]{8}$")) {
              outputMessage(element, "failure", "phone");
              return false;
            }
            break;
        }
      }
    }

    if (!options.numberIncludedForMinCharForSuccessMessageText) {
      //this will help validate number input below minChar
      if (element.getAttribute("data-success-message") !== null) {
        outputMessage(element, "success", undefined, undefined, {
          success: element.getAttribute("data-success-message"),
          error: "",
        });
      } else {
        outputMessage(element, "success");
      }
    } else {
      if (userInput.length >= options.minCharForSuccessMessageText) {
        if (element.getAttribute("data-success-message") !== null) {
          outputMessage(element, "success", undefined, undefined, {
            success: element.getAttribute("data-success-message"),
            error: "",
          });
        } else {
          outputMessage(element, "success");
        }
      }
    }

    return true;
  }

  function outputMessage(
    element,
    state,
    valType = null,
    placeholders = { ruleValue: "" },
    messages = { success: "", error: "" }
  ) {
    const parent = element.parentElement;
    let messageWrapper = document.getElementById(element.title + "_msg");
    if (messageWrapper === null) {
      messageWrapper = document.createElement(msgElement);
      messageWrapper.id = element.title + "_msg";
    }
    title = element.title;
    if (state === "success") {
      if (options.displaySuccessMessage) {
        if (messages.success.length > 0) {
          messageWrapper.innerHTML = messages.success;
        } else {
          messageWrapper.innerHTML = options.successMessageText;
        }
      } else {
        messageWrapper.innerHTML = "";
      }
    } else {
      if (messages.error.length > 0) {
        messageWrapper.innerHTML = messages.error;
      } else {
        messageWrapper.innerHTML = customMessages[valType]
          .replace("val_Title", title)
          .replace("val_RuleValue", placeholders.ruleValue);
      }
    }

    parent.appendChild(messageWrapper);
    element.classList.remove(options.successInputClass);
    element.classList.remove(options.failureInputClass);
    if (state === "success") {
      messageWrapper.className = options.successMessageClass;
      element.classList.add(options.successInputClass);
    } else {
      messageWrapper.className = options.failureMessageClass;
      element.classList.add(options.failureInputClass);
    }
  }
}

function match(valuestring, pattern) {
  let flag = "";
  if (pattern.indexOf("/") === 0) {
    if (pattern.charAt(pattern.length - 1) != "/") {
      flag = pattern.charAt(pattern.length - 1);
      pattern = pattern.substr(1, pattern.length - 3);
    } else {
      pattern = pattern.substr(1, pattern.length - 2);
    }
  }
  pattern = new RegExp(pattern);
  return pattern.test(valuestring);
}

function resetInputStyling(formId, firstClass, secondClass = "") {
  const allElements = getAllElements(formId);
  for (let element of allElements) {
    element.classList.remove(firstClass);
    element.classList.remove(secondClass);
  }
}

function getFormData(formId, returnType = "str") {
  let form = document.getElementById(formId);
  const hiddenElements = form.querySelectorAll("input[type=hidden]");
  const elements = getAllElements(formId);
  const allElements = [...elements, ...hiddenElements];
  if (returnType == "obj") {
    const outputObject = {};
    for (let element of allElements) {
      if (element.name !== undefined) {
        if (element.name in outputObject) {
          if (Array.isArray(outputObject[element.name])) {
            outputObject[element.name].push(element.value);
          } else {
            outputObject[element.name] = [element.value];
          }
        } else {
          outputObject[element.name] = element.value;
        }
      }
    }
    return outputObject;
  } else {
    let outputString = "";
    for (let element of allElements) {
      if (element.name !== undefined) {
        outputString += `${element.name}=${element.value}&`;
      }
    }
    return outputString.substring(0, outputString.length - 1);
  }
}

function getAllElements(formId) {
  const form = document.getElementById(formId);
  const textInput = form.querySelectorAll(
    "input[type=text], textarea, input[type=number]"
  );
  const numberInput = form.querySelectorAll("input[type=number]");
  const radioInput = form.querySelectorAll("input[type=radio]");
  const checkInput = form.querySelectorAll("input[type=checkbox]");
  const selectInput = form.querySelectorAll("select");
  const otherTextInput = form.querySelectorAll(
    "input[type=email],[type=date],[type=number],[type=password],[type=month],[type=tel],[type=time],[type=url],[type=week]"
  );

  const allElements = [
    ...textInput,
    ...radioInput,
    ...checkInput,
    ...selectInput,
    ...otherTextInput,
    ...numberInput,
  ];
  return allElements;
}
