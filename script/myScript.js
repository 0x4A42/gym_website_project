/**
 * Activates burger bar menu
 * Taken from https://bulma.io/documentation/components/navbar/
 */
$(document).ready(function () {
  // Check for click events on the navbar burger icon
  $(".navbar-burger").click(function () {
    // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
    $(".navbar-burger").toggleClass("is-active");
    $(".navbar-menu").toggleClass("is-active");
  });

  /**
   * Tracks when a user clicks out of the Username input of the login up form.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#usernameOrEmailLogIn").focusout(function () {
    var username = $("#usernameOrEmailLogIn");
    if (username.val() == "") {
      $("#usernameOrEmailLogIn").addClass("is-danger");
      $("#usernameOrEmailLogInWarn").html(
        "This field is mandatory. Please enter your username or email address."
      );
    } else if (!username.val() == "") {
      $("#usernameOrEmailLogIn").removeClass("is-danger");
      $("#usernameOrEmailLogInWarn").html("");
      $("#usernameOrEmailLogIn").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the password input of the login up form.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#passwordLogin").focusout(function () {
    var passwordLogin = $("#passwordLogin");
    if (passwordLogin.val() == "") {
      $("#passwordLogin").addClass("is-danger");
      $("#passwordLoginWarn").html(
        "This field is mandatory. Please enter your password."
      );
    } else if (!passwordLogin.val() == "") {
      $("#passwordLogin").removeClass("is-danger");
      $("#passwordLoginWarn").html("");
      $("#passwordLogin").addClass("is-success");
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   */
  $("#loginForm").keyup(function () {
    if (
      $("#usernameOrEmailLogIn").val() == "" ||
      $("#passwordLogin").val() == ""
    ) {
      // if any field is empty, disables access to the button
      $(".loginButtonIfValid").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".loginButtonIfValid").prop("disabled", false);
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   * Regex adapted from https://stackoverflow.com/questions/23476532/check-if-string-contains-only-letters-in-javascript
   */
  $("#signUpForm").keyup(function () {
    var userSign = $("#userNameSign");
    var realName = $("#realNameSign");
    var emailSign = $("#emailSign");
    var confirmPasswordSign = $("#confirmPasswordSign");
    var passwordSign = $("#passwordSign");
    var realDOB = $("#dob");
    var phoneSignUp = $("#telephoneNo").val();
    var parsedTelephone = parseInt(phoneSignUp);
    var addressLine = $("#addressLine");
    var city = $("#city");
    var postc = $("#postCode");
    if (
      $("#userNameSign").val() == "" ||
      $("#realNameSign").val() == "" ||
      $("#emailSign").val() == "" ||
      $("#passwordSign").val() == "" ||
      $("#confirmPasswordSign").val() == "" ||
      $("#dob").val() == "" ||
      $("#telephoneNo").val() == "" ||
      $("#addressLine").val() == "" ||
      $("#city").val() == "" ||
      $("#postCode").val() == ""
    ) {
      // if any field is empty, disables access to the button
      $(".buttonSignUpIfValid").prop("disabled", true);
    } else if (!Number.isInteger(parsedTelephone)) {
      // if phone number is not numeric
      $(".buttonSignUpIfValid").prop("disabled", true);
    } else if (
      !/^[a-zA-Z\s]+$/.test(city.val()) ||
      !/^[a-zA-Z\s]+$/.test(realName.val())
    ) {
      $(".buttonSignUpIfValid").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".buttonSignUpIfValid").prop("disabled", false);
    }
  });

  /**
   * Tracks when a user clicks out of the Username change field.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#usernameChange").focusout(function () {
    var usernameChange = $("#usernameChange");
    if (usernameChange.val() == "") {
      $("#usernameChange ").addClass("is-danger");
      $("#usernameChangeWarn").html("This field is mandatory.");
    } else if (!usernameChange.val() == "") {
      $("#usernameChange ").removeClass("is-danger");
      $("#usernameChangeWarn").html("");
      $("#usernameChange ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the Name input of the form.
   * If empty or not alphabetical, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#nameContact").focusout(function () {
    var name = $("#nameContact");
    if (name.val() == "") {
      $("#nameContact").addClass("is-danger");
      $("#nameContactWarn").html("This field is mandatory.");
    } else if (!/^[a-zA-Z\s]+$/.test(name.val())) {
      $("#nameContact").addClass("is-danger");
      $("#nameContactWarn").html("Name must be alphabetical.");
    } else if (!name.val() == "") {
      $("#nameContact").removeClass("is-danger");
      $("#nameContactWarn").html("");
      $("#nameContact").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the Email input of the form.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#emailContact").focusout(function () {
    var email = $("#emailContact");
    if (email.val() == "") {
      $("#emailContact ").addClass("is-danger");
      $("#emailContactWarn").html("This field is mandatory.");
    } else if (!email.val() == "") {
      $("#emailContact").removeClass("is-danger");
      $("#emailContactWarn").html("");
      $("#emailContact").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the Subject input of the form.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#subjectContact").focusout(function () {
    var subjectContact = $("#subjectContact");
    if (subjectContact.val() == "") {
      $("#subjectContact ").addClass("is-danger");
      $("#subjectContactWarn").html("This field is mandatory.");
    } else if (!subjectContact.val() == "") {
      $("#subjectContact").removeClass("is-danger");
      $("#subjectContactWarn").html("");
      $("#subjectContact").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the Question input of the form.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#questionContact").focusout(function () {
    var questionContact = $("#questionContact");
    if (questionContact.val() == "") {
      $("#questionContact ").addClass("is-danger");
      $("#questionContactWarn").html("This field is mandatory.");
    } else if (!questionContact.val() == "") {
      $("#questionContact").removeClass("is-danger");
      $("#questionContactWarn").html("");
      $("#questionContact").addClass("is-success");
    }
  });

  /**
   * Checks if there is any input within the fields of the contact form
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   * Regex adapted from https://stackoverflow.com/questions/23476532/check-if-string-contains-only-letters-in-javascript
   */
  $("#actualContactForm").focusout(function () {
    var name = $("#nameContact");

    if (
      $("#nameContact").val() == "" ||
      $("#emailContact").val() == "" ||
      $("#subjectContact").val() == "" ||
      $("#questionContact").val() == ""
    ) {
      // if any field is empty, disables access to the button
      $(".contactIfValid").prop("disabled", true);
    } else if (!/^[a-zA-Z\s]+$/.test(name.val())) {
      $(".contactIfValid").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".contactIfValid").prop("disabled", false);
    }
  });

  /**
   * Tracks when a user clicks out of the Username sign up field.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#userNameSign").focusout(function () {
    var userSign = $("#userNameSign");
    if (userSign.val() == "") {
      $("#userNameSign ").addClass("is-danger");
      $("#userWarn").html("This field is mandatory.");
    } else if (!userSign.val() == "") {
      $("#userNameSign ").removeClass("is-danger");
      $("#userWarn").html("");
      $("#userNameSign ").addClass("is-success");
    }
  });
  /**
   * Tracks when a user clicks out of the email sign up field.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#emailSign").focusout(function () {
    var emailSign = $("#emailSign");
    if (emailSign.val() == "") {
      $("#emailSign ").addClass("is-danger");
      $("#emailWarn").html("This field is mandatory.");
    } else if (!emailSign.val() == "") {
      $("#emailSign ").removeClass("is-danger");
      $("#emailWarn").html("");
      $("#emailSign ").addClass("is-success");
    }
  });

  /**
   *
   * Tracks when a user clicks out of the password sign up field.
   * If empty or does not adhere to regex, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   *
   * Regexes adapted from
   * https://stackoverflow.com/questions/32311081/check-for-special-characters-in-string
   * https://stackoverflow.com/questions/42467243/regex-strong-password-the-special-characters
   */
  $("#passwordSign").focusout(function () {
    var passwordSign = $("#passwordSign");
    if (passwordSign.val() == "") {
      $("#passwordSign ").addClass("is-danger");
      $("#passWarn").html("This field is mandatory.");
    } else if (!passwordSign.val().match(/\d/)) {
      $("#passwordSign ").addClass("is-danger");
      $("#passWarn").html("Password must contain one digit.");
    } else if (!passwordSign.val().match(/[A-Z]/)) {
      $("#passwordSign ").addClass("is-danger");
      $("#passWarn").html(
        "Password should contain at least one uppercase letter."
      );
    } else if (!passwordSign.val().match(/[a-z]/)) {
      $("#passwordSign ").addClass("is-danger");
      $("#passWarn").html(
        "Password must contain at least one lowercase letter."
      );
    } else if (
      !/[ `!@#$%^&£*()_+\-=\[\]{};':"\\|,.<>\/?~]/.test(passwordSign.val())
    ) {
      $("#passwordSign ").addClass("is-danger");
      $("#passWarn").html(
        "Password should contain at least one special character."
      );
    } else if (
      passwordSign.val().length < 8 ||
      passwordSign.val().length > 16
    ) {
      $("#passwordSign ").addClass("is-danger");
      $("#passWarn").html("Password should be between 8 - 16 characters.");
    } else if (!passwordSign.val() == "") {
      $("#passwordSign ").removeClass("is-danger");
      $("#passWarn").html("");
      $("#passwordSign ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the confirm password sign up field.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#confirmPasswordSign").focusout(function () {
    var confirmPasswordSign = $("#confirmPasswordSign");
    var passwordSign = $("#passwordSign");
    if (confirmPasswordSign.val() != passwordSign.val()) {
      $("#confirmPasswordSign").addClass("is-danger");
      $("#confirmPassWarn").html("Passwords do not match.");
    } else if (confirmPasswordSign.val() == "") {
      $("#confirmPasswordSign ").addClass("is-danger");
      $("#confirmPassWarn").html("This field is mandatory.");
    } else if (
      !confirmPasswordSign.val() == "" &&
      confirmPasswordSign.val() == passwordSign.val()
    ) {
      $("#confirmPasswordSign ").removeClass("is-danger");
      $("#confirmPassWarn").html("");
      $("#confirmPasswordSign ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the date of birth  sign up field.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#dob").focusout(function () {
    var realDOB = $("#dob");
    if (realDOB.val() == "") {
      $("#dob ").addClass("is-danger");
      $("#dobWarn").html("This field is mandatory.");
    } else if (!realDOB.val() == "") {
      $("#dob ").removeClass("is-danger");
      $("#dobWarn").html("");
      $("#dob ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the name sign up field.
   * If empty or not alphabetical, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#realNameSign").focusout(function () {
    var realName = $("#realNameSign");
    if (realName.val() == "") {
      $("#realNameSign ").addClass("is-danger");
      $("#nameWarn").html("This field is mandatory.");
    } else if (!/^[a-zA-Z\s]+$/.test(realName.val())) {
      $("#realNameSign ").addClass("is-danger");
      $("#nameWarn").html("Name must be alphabetical.");
    } else if (!realName.val() == "") {
      $("#realNameSign ").removeClass("is-danger");
      $("#nameWarn").html("");
      $("#realNameSign ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the telephone number sign up field.
   * If empty or not numerical, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#telephoneNo").focusout(function () {
    var phoneSignUp = $("#telephoneNo").val();
    var parsedTelephone = parseInt(phoneSignUp);
    if (!phoneSignUp == "" && !Number.isInteger(parsedTelephone)) {
      $("#telephoneNo").addClass("is-danger");
      $("#telephoneWarn").addClass("is-danger");
      $("#telephoneWarn").html("Input must be numerical");
    } else if (
      $("#telephoneNo").val().length < 10 ||
      $("#telephoneNo").val().length > 13
    ) {
      $("#telephoneNo").removeClass("is-success");
      $("#telephoneNo").addClass("is-danger");
      $("#telephoneWarn").html("Must be between 10 - 13 digits.");
    } else if (
      !phoneSignUp == "" &&
      Number.isInteger(parsedTelephone) &&
      $("#telephoneNo").val().length >= 10 &&
      $("#telephoneNo").val().length <= 13
    ) {
      $("#telephoneNo ").removeClass("is-danger");
      $("#telephoneWarn").html("");
      $("#telephoneNo ").addClass("is-success");
    } else if (phoneSignUp == "") {
      $("#telephoneNo ").removeClass("is-success");
      $("#telephoneNo ").addClass("is-danger");
      $("#telephoneWarn").html("This field is mandatory.");
    }
  });

  /**
   * Tracks when a user clicks out of the address sign up field.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#addressLine").focusout(function () {
    var addressLine = $("#addressLine");
    if (addressLine.val() == "") {
      $("#addressLine").addClass("is-danger");
      $("#addressWarn").html("This field is mandatory.");
    } else if (!addressLine.val() == "") {
      $("#addressLine").removeClass("is-danger");
      $("#addressWarn").html("");
      $("#addressLine").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the city sign up field.
   * If empty or not alphabetical, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#city").focusout(function () {
    var city = $("#city");
    if (city.val() == "") {
      $("#city ").addClass("is-danger");
      $("#cityWarn").html("This field is mandatory.");
    } else if (!/^[a-zA-Z\s]+$/.test(city.val())) {
      $("#city ").addClass("is-danger");
      $("#cityWarn").html("City must be alphabetical.");
    } else if (!city.val() == "") {
      $("#city ").removeClass("is-danger");
      $("#cityWarn").html("");
      $("#city ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the postcode sign up field.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#postCode").focusout(function () {
    var postc = $("#postCode");
    if (postc.val() == "") {
      $("#postCode ").addClass("is-danger");
      $("#postcodeWarn").html("This field is mandatory.");
    } else if (!postc.val() == "") {
      $("#postCode ").removeClass("is-danger");
      $("#postcodeWarn").html("");
      $("#postCode ").addClass("is-success");
    }
  });

  /**
   * When the reset button is clicked, clears all fields.
   */
  $("#resetSign").click(function () {
    $("#userNameSign").val("");
    $("#emailSign").val("");
    $("#passwordSign").val("");
    $("#confirmPasswordSign").val("");
    $("#realNameSign").val("");
    $("#dob").val("");
    $("#telephoneNo").val("");
    $("#addressLine").val("");
    $("#city").val("");
    $("#postCode").val("");
    $("#userNameSign ").removeClass("is-danger");
    $("#userWarn").html("");
    $("#userNameSign ").removeClass("is-success");
    $("#emailSign ").removeClass("is-danger");
    $("#emailWarn").html("");
    $("#emailSign ").removeClass("is-success");
    $("#passwordSign ").removeClass("is-danger");
    $("#passWarn").html("");
    $("#passwordSign ").removeClass("is-success");
    $("#confirmPasswordSign ").removeClass("is-danger");
    $("#confirmPassWarn").html("");
    $("#confirmPasswordSign ").removeClass("is-success");
    $("#dob ").removeClass("is-danger");
    $("#dobWarn").html("");
    $("#dob ").removeClass("is-success");
    $("#realNameSign ").removeClass("is-danger");
    $("#nameWarn").html("");
    $("#realNameSign ").removeClass("is-success");
    $("#telephoneNo ").removeClass("is-success");
    $("#telephoneNo ").removeClass("is-danger");
    $("#telephoneWarn").html("");
    $("#addressLine").removeClass("is-danger");
    $("#addressWarn").html("");
    $("#addressLine").removeClass("is-success");
    $("#city ").removeClass("is-danger");
    $("#cityWarn").html("");
    $("#city ").removeClass("is-success");
    $("#postCode ").removeClass("is-danger");
    $("#postcodeWarn").html("");
    $("#postCode ").removeClass("is-success");
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   * Regex adapted from https://stackoverflow.com/questions/23476532/check-if-string-contains-only-letters-in-javascript
   */
  $("#signUpForm").focusout(function () {
    var userSign = $("#userNameSign");
    var realName = $("#realNameSign");
    var emailSign = $("#emailSign");
    var confirmPasswordSign = $("#confirmPasswordSign");
    var passwordSign = $("#passwordSign");
    var realDOB = $("#dob");
    var phoneSignUp = $("#telephoneNo").val();
    var parsedTelephone = parseInt(phoneSignUp);
    var addressLine = $("#addressLine");
    var city = $("#city");
    var postc = $("#postCode");
    if (
      $("#userNameSign").val() == "" ||
      $("#realNameSign").val() == "" ||
      $("#emailSign").val() == "" ||
      $("#passwordSign").val() == "" ||
      $("#confirmPasswordSign").val() == "" ||
      $("#dob").val() == "" ||
      $("#telephoneNo").val() == "" ||
      $("#addressLine").val() == "" ||
      $("#city").val() == "" ||
      $("#postCode").val() == ""
    ) {
      // if any field is empty, disables access to the button
      $(".buttonSignUpIfValid").prop("disabled", true);
    } else if (!Number.isInteger(parsedTelephone)) {
      // if phone number is not numeric
      $(".buttonSignUpIfValid").prop("disabled", true);
    } else if (
      !/^[a-zA-Z\s]+$/.test(city.val()) ||
      !/^[a-zA-Z\s]+$/.test(realName.val())
    ) {
      $(".buttonSignUpIfValid").prop("disabled", true);
    } else if (!passwordSign.val().match(/\d/)) {
      $(".buttonSignUpIfValid").prop("disabled", true);
    } else if (!passwordSign.val().match(/[A-Z]/)) {
      $(".buttonSignUpIfValid").prop("disabled", true);
    } else if (!passwordSign.val().match(/[a-z]/)) {
      $(".buttonSignUpIfValid").prop("disabled", true);
    } else if (
      !/[ `!@#$%^&£*()_+\-=\[\]{};':"\\|,.<>\/?~]/.test(passwordSign.val())
    ) {
      $(".buttonSignUpIfValid").prop("disabled", true);
    } else if ($("#passwordSign").val() != $("#confirmPasswordSign").val()) {
      $(".buttonSignUpIfValid").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".buttonSignUpIfValid").prop("disabled", false);
    }
  });

  /**
   * Tracks when a user clicks out of the username change field.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#usernameChange").focusout(function () {
    var usernameChange = $("#usernameChange");
    if (usernameChange.val() == "") {
      $("#usernameChange ").addClass("is-danger");
      $("#usernameChangeWarn").html("This field is mandatory.");
    } else if (!usernameChange.val() == "") {
      $("#usernameChange ").removeClass("is-danger");
      $("#usernameChangeWarn").html("");
      $("#usernameChange ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the dob change  field.
   * If empty or not alphabetical, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   *
   * Regex adapted from https://stackoverflow.com/questions/23476532/check-if-string-contains-only-letters-in-javascript
   */
  $("#realNameChange").focusout(function () {
    var realNameChange = $("#realNameChange");
    if (realNameChange.val() == "") {
      $("#realNameChange ").addClass("is-danger");
      $("#realNameChangeWarn").html("This field is mandatory.");
    } else if (!/^[a-zA-Z\s]+$/.test(realNameChange.val())) {
      $("#realNameChange ").addClass("is-danger");
      $("#realNameChangeWarn").html("Name must be alphabetical.");
    } else if (!realNameChange.val() == "") {
      $("#realNameChange ").removeClass("is-danger");
      $("#realNameChangeWarn").html("");
      $("#realNameChange ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the dob change  field.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#dateChange").focusout(function () {
    var dateChange = $("#dateChange");
    if (dateChange.val() == "") {
      $("#dateChange ").addClass("is-danger");
      $("#dateChangeWarn").html("This field is mandatory.");
    } else if (!dateChange.val() == "") {
      $("#dateChange ").removeClass("is-danger");
      $("#dateChangeWarn").html("");
      $("#dateChange ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the email change  field.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#emailChange").focusout(function () {
    var emailChange = $("#emailChange");
    if (emailChange.val() == "") {
      $("#emailChange ").addClass("is-danger");
      $("#emailChangeWarn").html("This field is mandatory.");
    } else if (!emailChange.val() == "") {
      $("#emailChange ").removeClass("is-danger");
      $("#emailChangeWarn").html("");
      $("#emailChange ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the phone change  field.
   * If empty, puts a red outline and displays an error message.
   * If not numeric, error and red border.
   * If not empty, puts a green border.
   */
  $("#phoneChange").focusout(function () {
    var phoneChanged = $("#phoneChange").val();
    var parsedTelephone = parseInt(phoneChanged);
    if (phoneChanged == "") {
      $("#phoneChange").removeClass("is-success");
      $("#phoneChange").addClass("is-danger");
      $("#phoneChangeWarn").html("This field is mandatory.");
    } else if (!phoneChanged == "" && Number.isInteger(parsedTelephone)) {
      $("#phoneChange").removeClass("is-danger");
      $("#phoneChangeWarn").html("");
      $("#phoneChange").addClass("is-success");
    } else if (!phoneChange == "" && !Number.isInteger(parsedTelephone)) {
      $("#phoneChange").addClass("is-danger");
      $("#phoneChangeWarn").addClass("is-danger");
      $("#phoneChangeWarn").html("Input must be numerical");
    }
  });

  $("#addressChange").focusout(function () {
    var addressChange = $("#addressChange");
    if (addressChange.val() == "") {
      $("#addressChange ").addClass("is-danger");
      $("#addressChangeWarn").html("This field is mandatory.");
    } else if (!addressChange.val() == "") {
      $("#addressChange ").removeClass("is-danger");
      $("#addressChangeWarn").html("");
      $("#addressChange ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the city change  field.
   * If empty or not alphabetical, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * Regex adapted from https://stackoverflow.com/questions/23476532/check-if-string-contains-only-letters-in-javascript
   */
  $("#cityChange").focusout(function () {
    var cityChange = $("#cityChange");
    if (cityChange.val() == "") {
      $("#cityChange ").addClass("is-danger");
      $("#cityChangeWarn").html("This field is mandatory.");
    } else if (!/^[a-zA-Z\s]+$/.test(cityChange.val())) {
      $("#cityChange ").addClass("is-danger");
      $("#cityChangeWarn").html("City must be alphabetical.");
    } else if (!cityChange.val() == "") {
      $("#cityChange ").removeClass("is-danger");
      $("#cityChangeWarn").html("");
      $("#cityChange ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the postcode change  field.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#postcodeChange").focusout(function () {
    var postcodeChange = $("#postcodeChange");
    if (postcodeChange.val() == "") {
      $("#postcodeChange ").addClass("is-danger");
      $("#postcodeChangeWarn").html("This field is mandatory.");
    } else if (!postcodeChange.val() == "") {
      $("#postcodeChange ").removeClass("is-danger");
      $("#postcodeChangeWarn").html("");
      $("#postcodeChange ").addClass("is-success");
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   * Regex adapted from https://stackoverflow.com/questions/23476532/check-if-string-contains-only-letters-in-javascript
   */
  $("#changeDetailsForm").keyup(function () {
    var phoneChanged = $("#phoneChange").val();
    var parsedTelephone = parseInt(phoneChanged);
    var cityChange = $("#cityChange");
    var realNameChange = $("#realNameChange");
    if (
      $("#usernameChange").val() == "" ||
      $("#realNameChange").val() == "" ||
      $("#emailChange").val() == "" ||
      $("#phoneChange").val() == "" ||
      $("#addressChange").val() == "" ||
      $("#cityChange").val() == "" ||
      $("#postcodeChange").val() == ""
    ) {
      // if any field is empty, disables access to the button
      $(".detailsIfValid").prop("disabled", true);
    } else if (!Number.isInteger(parsedTelephone)) {
      // if phone number is not numeric
      $(".detailsIfValid").prop("disabled", true);
    } else if (
      !/^[a-zA-Z\s]+$/.test(cityChange.val()) ||
      !/^[a-zA-Z\s]+$/.test(realNameChange.val())
    ) {
      $(".detailsIfValid").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".detailsIfValid").prop("disabled", false);
    }
  });

  /**
   * Tracks when a user clicks out of the current pass field.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   */
  $("#currentPass").focusout(function () {
    var currentPass = $("#currentPass");
    if (currentPass.val() == "") {
      $("#currentPass ").addClass("is-danger");
      $("#currentPassWarn").html("This field is mandatory.");
    } else if (!currentPass.val() == "") {
      $("#currentPass ").removeClass("is-danger");
      $("#currentPassWarn").html("");
      $("#currentPass ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the new pass field.
   * If empty or does not adhere to regex, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * Regexes adapted from
   * https://stackoverflow.com/questions/32311081/check-for-special-characters-in-string
   * https://stackoverflow.com/questions/42467243/regex-strong-password-the-special-characters
   */
  $("#newPass").focusout(function () {
    var newPass = $("#newPass");
    var currentPass = $("#currentPass");
    if (newPass.val() == "") {
      $("#newPass ").addClass("is-danger");
      $("#newPassWarn").html("This field is mandatory.");
    } else if (!newPass.val().match(/\d/)) {
      $("#newPass ").addClass("is-danger");
      $("#newPassWarn").html("Password must contain one digit.");
    } else if (!newPass.val().match(/[A-Z]/)) {
      $("#newPass ").addClass("is-danger");
      $("#newPassWarn").html(
        "Password should contain at least one uppercase letter."
      );
    } else if (!newPass.val().match(/[a-z]/)) {
      $("#newPass ").addClass("is-danger");
      $("#newPassWarn").html(
        "Password must contain at least one lowercase letter."
      );
    } else if (
      !/[ `!@#$%^&£*()_+\-=\[\]{};':"\\|,.<>\/?~]/.test(newPass.val())
    ) {
      $("#newPass ").addClass("is-danger");
      $("#newPassWarn").html(
        "Password should contain at least one special character."
      );
    } else if (currentPass.val() == newPass.val()) {
      $("#newPass").addClass("is-danger");
      $("#newPassWarn").html("New password same as entered current password.");
    } else if (newPass.val().length < 8 || newPass.val().length > 16) {
      $("#newPass ").addClass("is-danger");
      $("#newPassWarn").html("Password should be between 8 - 16 characters.");
    } else if (!newPass.val() == "" && currentPass.val() != newPass.val()) {
      $("#newPass ").removeClass("is-danger");
      $("#newPassWarn").html("");
      $("#newPass ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the confirm new pass field.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * Displays errors if the confirm password does not adhere to certain criteria
   */
  $("#confirmNewPass").focusout(function () {
    var currentPass = $("#currentPass");
    var confirmNewPass = $("#confirmNewPass");
    if (confirmNewPass.val() == "") {
      $("#confirmNewPass ").addClass("is-danger");
      $("#confirmNewPassWarn").html("This field is mandatory.");
    } else if (confirmNewPass.val() != $("#newPass").val()) {
      $("#confirmNewPass ").addClass("is-danger");
      $("#confirmNewPassWarn").html("New passwords do not match.");
    } else if (currentPass.val() == confirmNewPass.val()) {
      $("#confirmNewPass ").addClass("is-danger");
      $("#confirmNewPassWarn").html(
        "Confirmed new password same as entered current password."
      );
    } else if (
      confirmNewPass.val().length < 8 ||
      confirmNewPass.val().length > 16
    ) {
      $("#confirmNewPass ").addClass("is-danger");
      $("#confirmNewPassWarn").html(
        "Password should be between 8 - 16 characters."
      );
    } else if (
      !confirmNewPass.val() == "" &&
      confirmNewPass.val() == $("#newPass").val()
    ) {
      $("#confirmNewPass ").removeClass("is-danger");
      $("#confirmNewPassWarn").html("");
      $("#confirmNewPass ").addClass("is-success");
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   */
  $("#changePassForm").focusout(function () {
    var confirmNewPass = $("#confirmNewPass");
    var newPass = $("#newPass");
    if (
      $("#confirmNewPass").val() == "" ||
      $("#newPass").val() == "" ||
      $("#currentPass").val() == ""
    ) {
      // if any field is empty, disables access to the button
      $(".passwordIfValid").prop("disabled", true);
    } else if ($("#confirmNewPass").val() != $("#newPass").val()) {
      $(".passwordIfValid").prop("disabled", true);
    } else if (!$("#newPass").val().match(/\d/)) {
      $(".passwordIfValid").prop("disabled", true);
    } else if (!$("#newPass").val().match(/[A-Z]/)) {
      $(".passwordIfValid").prop("disabled", true);
    } else if (!$("#newPass").val().match(/[a-z]/)) {
      $(".passwordIfValid").prop("disabled", true);
    } else if (
      !/[ `!@#$%^&£*()_+\-=\[\]{};':"\\|,.<>\/?~]/.test($("#newPass").val())
    ) {
      $(".passwordIfValid").prop("disabled", true);
    } else if (
      $("#newPass").val().length < 8 ||
      $("#newPass").val().length > 16
    ) {
      $(".passwordIfValid").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".passwordIfValid").prop("disabled", false);
    }
  });

  /**
   * Tracks when a user clicks out of the testimonial body field.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * */
  $("#testimonialTextBody").focusout(function () {
    var testimonialTextBody = $("#testimonialTextBody");
    if (testimonialTextBody.val() == "") {
      $("#testimonialTextBody ").addClass("is-danger");
      $("#testiTextWarn").html("This field is mandatory.");
    } else if (!testimonialTextBody.val() == "") {
      $("#testimonialTextBody ").removeClass("is-danger");
      $("#testiTextWarn").html("");
      $("#testimonialTextBody ").addClass("is-success");
    }
  });
  /**
   * Tracks when a user clicks out of the testimonial title field.
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * */
  $("#testimonialTitle").focusout(function () {
    var testimonialTitle = $("#testimonialTitle");
    if (testimonialTitle.val() == "") {
      $("#testimonialTitle ").addClass("is-danger");
      $("#testiTitleWarn").html("This field is mandatory.");
    } else if (!testimonialTitle.val() == "") {
      $("#testimonialTitle ").removeClass("is-danger");
      $("#testiTitleWarn").html("");
      $("#testimonialTitle ").addClass("is-success");
    }
  });
  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   */
  $("#updateTestimonialForm").focusout(function () {
    if (
      $("#testimonialTitle").val() == "" ||
      $("#testimonialTextBody").val() == ""
    ) {
      $(".currentTestiIfValid").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".currentTestiIfValid").prop("disabled", false);
    }
  });

  /**
   * Tracks when a user clicks out of the current password field for deleting
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * */
  $("#delCurrentPassword").focusout(function () {
    var delCurrentPassword = $("#delCurrentPassword");
    if (delCurrentPassword.val() == "") {
      $("#delCurrentPassword ").addClass("is-danger");
      $("#delCurrentPasswordWarn").html("This field is mandatory.");
    } else if (!delCurrentPassword.val() == "") {
      $("#delCurrentPassword ").removeClass("is-danger");
      $("#delCurrentPasswordWarn").html("");
      $("#delCurrentPassword ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the confirm current password field for deleting
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * */
  $("#delConfirmPassword").focusout(function () {
    var delConfirmPassword = $("#delConfirmPassword");
    if (delConfirmPassword.val() == "") {
      $("#delConfirmPassword ").addClass("is-danger");
      $("#delConfirmPasswordWarn").html("This field is mandatory.");
    } else if (
      $("#delConfirmPassword").val() != $("#delCurrentPassword").val()
    ) {
      $("#delConfirmPassword ").addClass("is-danger");
      $("#delConfirmPasswordWarn").html("Passwords do not match.");
    } else if (!delConfirmPassword.val() == "") {
      $("#delConfirmPassword ").removeClass("is-danger");
      $("#delConfirmPasswordWarn").html("");
      $("#delConfirmPassword ").addClass("is-success");
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   */
  $("#deleteAccountForm").focusout(function () {
    if (
      $("#delCurrentPassword").val() == "" ||
      $("#delConfirmPassword").val() == ""
    ) {
      $(".deleteAccIfValid").prop("disabled", true);
    } else if (
      $("#delCurrentPassword").val() != $("#delConfirmPassword").val()
    ) {
      $(".deleteAccIfValid").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".deleteAccIfValid").prop("disabled", false);
    }
  });

  /**
   * Tracks when a user clicks out of the change height field
   * If empty or not numeric, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * */
  $("#height").focusout(function () {
    var height = $("#height").val();
    var parsedHeight = parseInt(height);
    if (height == "") {
      $("#height ").addClass("is-danger");
      $("#heightWarn").html("This field is mandatory.");
    } else if (!height == "" && !Number.isInteger(parsedHeight)) {
      $("#height ").addClass("is-danger");
      $("#heightWarn").html("Height must be numeric.");
    } else if (!(height == "") && Number.isInteger(parsedHeight)) {
      $("#height ").removeClass("is-danger");
      $("#heightWarn").html("");
      $("#height ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the change starting weight field
   * If empty or not numeric, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * */
  $("#startingWeight").focusout(function () {
    var sWeight = $("#startingWeight").val();
    var parsedSWeight = parseInt(sWeight);
    if (sWeight == "") {
      $("#startingWeight ").addClass("is-danger");
      $("#startingWeightWarn").html("This field is mandatory.");
    } else if (!sWeight == "" && !Number.isInteger(parsedSWeight)) {
      $("#startingWeight ").addClass("is-danger");
      $("#startingWeightWarn").html("Starting weight must be numeric.");
    } else if (!(sWeight == "") && Number.isInteger(parsedSWeight)) {
      $("#startingWeight ").removeClass("is-danger");
      $("#startingWeightWarn").html("");
      $("#startingWeight ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the change current weight field
   * If empty or not numeric, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * */
  $("#currentWeight").focusout(function () {
    var weight = $("#currentWeight").val();
    var parsedWeight = parseInt(weight);
    if (weight == "") {
      $("#currentWeight ").addClass("is-danger");
      $("#currentWeightWarn").html("This field is mandatory.");
    } else if (!weight == "" && !Number.isInteger(parsedWeight)) {
      $("#currentWeight ").addClass("is-danger");
      $("#currentWeightWarn").html("Current weight must be numeric.");
    } else if (!(weight == "") && Number.isInteger(parsedWeight)) {
      $("#currentWeight ").removeClass("is-danger");
      $("#currentWeightWarn").html("");
      $("#currentWeight ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the change current bmi field
   * If empty or not numeric, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * */
  $("#currentBMI").focusout(function () {
    var currentBMI = $("#currentBMI").val();
    var parsedCurrentBMI = parseInt(currentBMI);
    if (currentBMI == "") {
      $("#currentBMI ").addClass("is-danger");
      $("#currentBMIWarn").html("This field is mandatory.");
    } else if (!currentBMI == "" && !Number.isInteger(parsedCurrentBMI)) {
      $("#currentBMI ").addClass("is-danger");
      $("#currentBMIWarn").html("BMI must be numeric.");
    } else if (!(currentBMI == "") && Number.isInteger(parsedCurrentBMI)) {
      $("#currentBMI ").removeClass("is-danger");
      $("#currentBMIWarn").html("");
      $("#currentBMI ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the change body fat field
   * If empty or not numeric, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * */
  $("#currentBodyFat").focusout(function () {
    var currentBodyFat = $("#currentBodyFat").val();
    var parsedCurrentBodyFat = parseInt(currentBodyFat);
    if (currentBodyFat == "") {
      $("#currentBodyFat ").addClass("is-danger");
      $("#currentBodyFatWarn").html("This field is mandatory.");
    } else if (
      !currentBodyFat == "" &&
      !Number.isInteger(parsedCurrentBodyFat)
    ) {
      $("#currentBodyFat ").addClass("is-danger");
      $("#currentBodyFatWarn").html("Body Fat must be numeric.");
    } else if (
      !(currentBodyFat == "") &&
      Number.isInteger(parsedCurrentBodyFat)
    ) {
      $("#currentBodyFat ").removeClass("is-danger");
      $("#currentBodyFatWarn").html("");
      $("#currentBodyFat ").addClass("is-success");
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   */

  $("#currentProgressForm").keyup(function () {
    var height = $("#height").val();
    var parsedHeight = parseInt(height);
    var weight = $("#currentWeight").val();
    var parsedWeight = parseInt(weight);
    var sWeight = $("#startingWeight").val();
    var parsedSWeight = parseInt(sWeight);
    var currentBMI = $("#currentBMI").val();
    var parsedCurrentBMI = parseInt(currentBMI);
    var currentBodyFat = $("#currentBodyFat").val();
    var parsedCurrentBodyFat = parseInt(currentBodyFat);
    if (
      !Number.isInteger(parsedHeight) ||
      !Number.isInteger(parsedWeight) ||
      !Number.isInteger(parsedSWeight) ||
      !Number.isInteger(parsedCurrentBMI) ||
      !Number.isInteger(parsedCurrentBodyFat)
    ) {
      $(".currentValsIfValid").prop("disabled", true);
    } else if (
      $("#height").val() == "" ||
      $("#startingWeight").val() == "" ||
      $("#currentWeight").val() == "" ||
      $("#currentBMI").val() == "" ||
      $("#currentBodyFat").val() == ""
    ) {
      // if any field is empty, disables access to the button
      $(".currentValsIfValid").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".currentValsIfValid").prop("disabled", false);
    }
  });

  /**
   * Tracks when a user clicks out of the change goal weight field
   * If empty or not numeric, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * */
  $("#goalWeight").focusout(function () {
    var goalWeight = $("#goalWeight").val();
    var parsedGoalWeight = parseInt(goalWeight);
    if (goalWeight == "") {
      $("#goalWeight ").addClass("is-danger");
      $("#goalWeightWarn").html("This field is mandatory.");
    } else if (!goalWeight == "" && !Number.isInteger(parsedGoalWeight)) {
      $("#goalWeight ").addClass("is-danger");
      $("#goalWeightWarn").html("Goal weight must be numeric.");
    } else if (!(goalWeight == "") && Number.isInteger(parsedGoalWeight)) {
      $("#goalWeight ").removeClass("is-danger");
      $("#goalWeightWarn").html("");
      $("#goalWeight ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the change goal bmi field
   * If empty or not numeric, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * */
  $("#goalBMI").focusout(function () {
    var goalBMI = $("#goalBMI").val();
    var parsedGoalBMI = parseInt(goalBMI);
    if (goalBMI == "") {
      $("#goalBMI ").addClass("is-danger");
      $("#goalBMIWarn").html("This field is mandatory.");
    } else if (!goalBMI == "" && !Number.isInteger(parsedGoalBMI)) {
      $("#goalBMI ").addClass("is-danger");
      $("#goalBMIWarn").html("Goal BMI must be numeric.");
    } else if (!(goalBMI == "") && Number.isInteger(parsedGoalBMI)) {
      $("#goalBMI ").removeClass("is-danger");
      $("#goalBMIWarn").html("");
      $("#goalBMI ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the change goal body fat field
   * If empty or not numeric, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * */
  $("#goalBodyFat").focusout(function () {
    var goalBodyFat = $("#goalBodyFat").val();
    var parsedGoalBodyFat = parseInt(goalBodyFat);
    if (!goalBodyFat == "" && !Number.isInteger(parsedGoalBodyFat)) {
      $("#goalBodyFat ").addClass("is-danger");
      $("#goalBodyFatWarn").html("Goal Body Fat must be numeric.");
    } else if (currentBodyFat == "") {
      $("#goalBodyFat ").addClass("is-danger");
      $("#goalBodyFatWarn").html("This field is mandatory.");
    } else if (!(goalBodyFat == "") && Number.isInteger(parsedGoalBodyFat)) {
      $("#goalBodyFat ").removeClass("is-danger");
      $("#goalBodyFatWarn").html("");
      $("#goalBodyFat ").addClass("is-success");
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   */
  $("#goalsForm").keyup(function () {
    var goalWeight = $("#goalWeight").val();
    var parsedGoalWeight = parseInt(goalWeight);
    var goalBMI = $("#goalBMI").val();
    var parsedGoalBMI = parseInt(goalBMI);
    var goalBodyFat = $("#goalBodyFat").val();
    var parsedGoalBodyFat = parseInt(goalBodyFat);

    if (
      !Number.isInteger(parsedGoalWeight) ||
      !Number.isInteger(parsedGoalBMI) ||
      !Number.isInteger(parsedGoalBodyFat)
    ) {
      $(".goalsIfValid").prop("disabled", true);
    } else if (
      $("#goalWeight").val() == "" ||
      $("#goalBMI").val() == "" ||
      $("#goalBodyFat").val() == ""
    ) {
      // if any field is empty, disables access to the button
      $(".goalsIfValid").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".goalsIfValid").prop("disabled", false);
    }
  });

  /**
   * When click on update personal button, shows this modal.
   */
  $("#updatePersonal").click(function () {
    $("#editPersonalDetails").addClass("is-active");
  });

   /**
   * Tracks when a user clicks out of the appointment description field when booking an appt 
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * */
  $("#apptDesc").focusout(function () {
    var apptDesc = $("#apptDesc").val();
    if (apptDesc == "") {
      $("#apptDesc ").addClass("is-danger");
      $("#descWarn").html("This field is mandatory.");
    } else if (!(timepicker == "")) {
      $("#apptDesc ").removeClass("is-danger");
      $("#descWarn").html("");
      $("#apptDesc ").addClass("is-success");
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   */

  $("#makeAppts").focusout(function () {
    if ($("#apptDesc").val() == "") {
      // if any field is empty, disables access to the button
      $(".appointmentSubmit").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".appointmentSubmit").prop("disabled", false);
    }
  });

   /**
   * Tracks when a user clicks out of the coach comment section of the log 
   * If empty, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * */
  $("#coachComments").focusout(function () {
    var coachComments = $("#coachComments").val();
    if (coachComments == "") {
      $("#coachComments ").addClass("is-danger");
      $("#coachCommentsWarn").html("This field is mandatory.");
    } else if (!(coachComments == "")) {
      $("#coachComments ").removeClass("is-danger");
      $("#coachCommentsWarn").html("");
      $("#coachComments ").addClass("is-success");
    }
  });

  /**
   * Tracks when a user clicks out of the coach comment section of the log 
   * If empty or not numeric, puts a red outline and displays an error message.
   * If not empty, puts a green border.
   * */ // come here
  $("#coachRating").focusout(function () {
    var coachRating = $("#coachRating").val();
    if (coachRating == "") {
      $("#coachRating ").addClass("is-danger");
      $("#coachRatingWarn").html("This field is mandatory.");
    } else if (!(coachRating == "")) {
      $("#coachRating ").removeClass("is-danger");
      $("#coachRatingWarn").html("");
      $("#coachRating ").addClass("is-success");
    }
  });

  $("#userComments").focusout(function () {
    var userComments = $("#userComments").val();

    if (userComments == "") {
      $("#userComments ").addClass("is-danger");
      $("#userCommentsWarn").html("This field is mandatory.");
    } else if (!(userComments == "")) {
      $("#userComments ").removeClass("is-danger");
      $("#userCommentsWarn").html("");
      $("#userComments ").addClass("is-success");
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   */

  $("#coachLog").focusout(function () {
    if (
      $("#coachComments").val() == "" ||
      $("#coachRating").val() == "" ||
      $("#userComments").val() == ""
    ) {
      // if any field is empty, disables access to the button
      $(".logIfValid").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".logIfValid").prop("disabled", false);
    }
  });

  /**
   * Closes the modals if the X button is pressed
   */
  $(".cancelUpdate").click(function () {
    $("#editPersonalDetails").removeClass("is-active");
    $("#editGoals").removeClass("is-active");
    $("#editCurrentProgress").removeClass("is-active");
    $("#editPassword").removeClass("is-active");
    $("#addLog").removeClass("is-active");
    $("#editLog").removeClass("is-active");
    $("#replyMsg").removeClass("is-active");
    $("#editRegime").removeClass("is-active");
    $("#editTestimonial").removeClass("is-active");
    $("#readUserMessage").removeClass("is-active");
    $("#createNewGrp").removeClass("is-active");
    $("#editClientRegime").removeClass("is-active");
    $("#attemptReset").removeClass("is-active");
    $("#editTestimonial").removeClass("is-active");
    $("#deleteAcc").removeClass("is-active");
    $("#deleteGroup").removeClass("is-active");
    $("#generateCode").removeClass("is-active");
    $("#attemptReset").removeClass("is-active");
    $("#updateDesc").removeClass("is-active");
    $("#editCoachDetailsModal").removeClass("is-active");
  });

  /**
   * Closes the modals if esc is pressed
   */
  $(document).keyup(function (e) {
    if (e.key === "Escape") {
      // escape key maps to keycode `27`

      $("#editPersonalDetails").removeClass("is-active");
      $("#editGoals").removeClass("is-active");
      $("#editCurrentProgress").removeClass("is-active");
      $("#editPassword").removeClass("is-active");
      $("#addLog").removeClass("is-active");
      $("#editLog").removeClass("is-active");
      $("#replyMsg").removeClass("is-active");
      $("#editRegime").removeClass("is-active");
      $("#editTestimonial").removeClass("is-active");
      $("#readUserMessage").removeClass("is-active");
      $("#createNewGrp").removeClass("is-active");
      $("#editClientRegime").removeClass("is-active");
      $("#attemptReset").removeClass("is-active");
      $("#editTestimonial").removeClass("is-active");
      $("#deleteAcc").removeClass("is-active");
      $("#deleteGroup").removeClass("is-active");
      $("#editLog").removeClass("is-active");
      $("#generateCode").removeClass("is-active");
      $("#attemptReset").removeClass("is-active");
      $("#updateDesc").removeClass("is-active");
      $("#editCoachDetailsModal").removeClass("is-active");
    }
  });

  $(".updatePassword").click(function () {
    $("#editGoals").removeClass("is-active");
    $("#editPersonalDetails").removeClass("is-active");
    $("#editCurrentProgress").removeClass("is-active");
    $("#editRegime").removeClass("is-active");
    $("#editTestimonial").removeClass("is-active");
    $("#deleteAcc").removeClass("is-active");
    $("#editPassword").addClass("is-active");
  });

  $(".updateGoalsButton").click(function () {
    $("#editPersonalDetails").removeClass("is-active");
    $("#editCurrentProgress").removeClass("is-active");
    $("#editPassword").removeClass("is-active");
    $("#editRegime").removeClass("is-active");
    $("#editTestimonial").removeClass("is-active");
    $("#deleteAcc").removeClass("is-active");
    $("#editGoals").addClass("is-active");
  });

  $(".updateRegimeButton").click(function () {
    $("#editPersonalDetails").removeClass("is-active");
    $("#editCurrentProgress").removeClass("is-active");
    $("#editPassword").removeClass("is-active");
    $("#editGoals").removeClass("is-active");
    $("#editTestimonial").removeClass("is-active");
    $("#deleteAcc").removeClass("is-active");
    $("#editRegime").addClass("is-active");
  });

  $(".updateCurrentProgressButton").click(function () {
    $("#editGoals").removeClass("is-active");
    $("#editPersonalDetails").removeClass("is-active");
    $("#editPassword").removeClass("is-active");
    $("#editRegime").removeClass("is-active");
    $("#editTestimonial").removeClass("is-active");
    $("#deleteAcc").removeClass("is-active");
    $("#editCurrentProgress").addClass("is-active");
  });

  $(".updatePersonalDetailsButton").click(function () {
    $("#editCurrentProgress").removeClass("is-active");
    $("#editGoals").removeClass("is-active");
    $("#editPassword").removeClass("is-active");
    $("#editRegime").removeClass("is-active");
    $("#editTestimonial").removeClass("is-active");
    $("#deleteAcc").removeClass("is-active");
    $("#editPersonalDetails").addClass("is-active");
  });

  $("#changePassword").click(function () {
    $("#editRegime").removeClass("is-active");
    $("#editCurrentProgress").removeClass("is-active");
    $("#editGoals").removeClass("is-active");
    $("#editPersonalDetails").removeClass("is-active");
    $("#editTestimonial").removeClass("is-active");
    $("#deleteAcc").removeClass("is-active");
    $("#editCoachDetailsModal").removeClass("is-active");
    $("#editPassword").addClass("is-active");
  });

  $(".updateTestimonial").click(function () {
    $("#editRegime").removeClass("is-active");
    $("#editCurrentProgress").removeClass("is-active");
    $("#editGoals").removeClass("is-active");
    $("#editPersonalDetails").removeClass("is-active");
    $("#editPassword").removeClass("is-active");
    $("#deleteAcc").removeClass("is-active");
    $("#editTestimonial").addClass("is-active");
  });

  $(".delAccount").click(function () {
    $("#editRegime").removeClass("is-active");
    $("#editCurrentProgress").removeClass("is-active");
    $("#editGoals").removeClass("is-active");
    $("#editPersonalDetails").removeClass("is-active");
    $("#editPassword").removeClass("is-active");
    $("#editTestimonial").removeClass("is-active");
    $("#deleteAcc").addClass("is-active");
  });

  $("#syncDetails").click(function () {
    $("#refreshName").load(" #refreshName");
    $("#refreshFirstCol").load(" #refreshFirstCol");
    $("#refreshSecondCol").load(" #refreshSecondCol");
    $("#refreshThirdCol").load(" #refreshThirdCol");
  });

  /**
   * Checks input of subject fields, if empty displays a red border and error text
   */
  $("#subjectInput").focusout(function () {
    var subjectInput = $("#subjectInput").val();
    if (subjectInput == "") {
      $("#subjectInput ").addClass("is-danger");
      $(".subjectWarn").html("This field is mandatory.");
    } else if (!(subjectInput == "")) {
      $("#subjectInput ").removeClass("is-danger");
      $(".subjectWarn").html("");
      $("#subjectInput ").addClass("is-success");
    }
  });

  /**
   * Checks input of message body fields, if empty displays a red border and error text
   */
  $("#messageBody").focusout(function () {
    var messageBody = $("#messageBody").val();
    if (messageBody == "") {
      $("#messageBody").addClass("is-danger");
      $(".messageWarn").html("This field is mandatory.");
    } else if (!(messageBody == "")) {
      $("#messageBody").removeClass("is-danger");
      $(".messageWarn").html("");
      $("#messageBody").addClass("is-success");
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   */
  $("#userMsgCoach ").focusout(function () {
    if ($("#messageBody").val() == "" || $("#subjectInput").val() == "") {
      // if any field is empty, disables access to the button
      $(".userMessageCoachButton").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".userMessageCoachButton").prop("disabled", false);
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   */
  $("#clientReplyToMsg ").focusout(function () {
    if ($("#messageBody").val() == "") {
      // if any field is empty, disables access to the button
      $(".clientReplyToMsgButton").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".clientReplyToMsgButton").prop("disabled", false);
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   */
  $("#coachMessageUser").focusout(function () {
    if ($("#messageBody").val() == "" || $("#subjectInput").val() == "") {
      // if any field is empty, disables access to the button
      $(".coachMessageUserButton").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".coachMessageUserButton").prop("disabled", false);
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   */
  $("#coachMsgAllUser").focusout(function () {
    if ($("#messageBody").val() == "" || $("#subjectInput").val() == "") {
      // if any field is empty, disables access to the button
      $(".coachMsgAllUserButton").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".coachMsgAllUserButton").prop("disabled", false);
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   */
  $("#coachReplyToUser").focusout(function () {
    if ($("#messageBody").val() == "") {
      // if any field is empty, disables access to the button
      $(".coachReplyToUserButton").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".coachReplyToUserButton").prop("disabled", false);
    }
  });

  /**
   * Checks input of edit comment field, if empty displays a red border and error text
   */
  $("#userComments").focusout(function () {
    var userComments = $("#userComments").val();
    if (userComments == "") {
      $("#userComments").addClass("is-danger");
      $("#commentWarn").html("This field is mandatory.");
    } else if (!(userComments == "")) {
      $("#userComments").removeClass("is-danger");
      $("#commentWarn").html("");
      $("#userComments").addClass("is-success");
    }
  });

  /**
   * Checks input of message body fields, if empty displays a red border and error text
   */
  $("#userRating").focusout(function () {
    var userRating = $("#userRating").val();
    if (userRating == "") {
      $("#userRating").addClass("is-danger");
      $("#ratingWarn").html("This field is mandatory.");
    } else if (!(userRating == "")) {
      $("#userRating").removeClass("is-danger");
      $("#ratingWarn").html("");
      $("#userRating").addClass("is-success");
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   */
  $("#addALog").focusout(function () {
    if ($("#userComments").val() == "" || $("#userRating").val() == "") {
      // if any field is empty, disables access to the button
      $(".submitLogBut").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".submitLogBut").prop("disabled", false);
    }
  });

  /**
   * Checks input of edit comment field, if empty displays a red border and error text
   */
  $("#editUserComments").focusout(function () {
    var editUserComments = $("#editUserComments").val();
    if (editUserComments == "") {
      $("#editUserComments").addClass("is-danger");
      $("#editCommentsWarn").html("This field is mandatory.");
    } else if (!(editUserComments == "")) {
      $("#editUserComments").removeClass("is-danger");
      $("#editCommentsWarn").html("");
      $("#editUserComments").addClass("is-success");
    }
  });

  /**
   * Checks input of message body fields, if empty displays a red border and error text
   */
  $("#editUserRating").focusout(function () {
    var editUserRating = $("#editUserRating").val();
    if (editUserRating == "") {
      $("#editUserRating").addClass("is-danger");
      $("#editRatingWarn").html("This field is mandatory.");
    } else if (!(editUserRating == "")) {
      $("#editUserRating").removeClass("is-danger");
      $("#editRatingWarn").html("");
      $("#editUserRating").addClass("is-success");
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   */
  $("#editALog").focusout(function () {
    if (
      $("#editUserComments").val() == "" ||
      $("#editUserRating").val() == ""
    ) {
      // if any field is empty, disables access to the button
      $(".editLogBut").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".editLogBut").prop("disabled", false);
    }
  });

  /**
   * Checks input of message body fields, if empty displays a red border and error text
   */
  $("#newGroupName").focusout(function () {
    var newGroupName = $("#newGroupName").val();
    if (newGroupName == "") {
      $("#newGroupName").addClass("is-danger");
      $("#newGroupNameWarn").html("This field is mandatory.");
    } else if (!(newGroupName == "")) {
      $("#newGroupName").removeClass("is-danger");
      $("#newGroupNameWarn").html("");
      $("#newGroupName").addClass("is-success");
    }
  });

  /**
   * Checks input of message body fields, if empty displays a red border and error text
   */
  $("#newGroupDesc").focusout(function () {
    var newGroupDesc = $("#newGroupDesc").val();
    if (newGroupDesc == "") {
      $("#newGroupDesc").addClass("is-danger");
      $("#newGroupDescWarn").html("This field is mandatory.");
    } else if (!(newGroupDesc == "")) {
      $("#newGroupDesc").removeClass("is-danger");
      $("#newGroupDescWarn").html("");
      $("#newGroupDesc").addClass("is-success");
    }
  });

  /**
   * Checks input of message body fields, if empty displays a red border and error text
   */
  $("#newGroupSessionOne").focusout(function () {
    var newGroupSessionOne = $("#newGroupSessionOne").val();
    if (newGroupSessionOne == "") {
      $("#newGroupSessionOne").addClass("is-danger");
      $("#newGroupSessionOneWarn").html("This field is mandatory.");
    } else if (!(newGroupSessionOne == "")) {
      $("#newGroupSessionOne").removeClass("is-danger");
      $("#newGroupSessionOneWarn").html("");
      $("#newGroupSessionOne").addClass("is-success");
    }
  });

  /**
   * Checks input of message body fields, if empty displays a red border and error text
   */
  $("#newGroupSessionTwo").focusout(function () {
    var newGroupSessionTwo = $("#newGroupSessionTwo").val();
    if (newGroupSessionTwo == "") {
      $("#newGroupSessionTwo").addClass("is-danger");
      $("#newGroupSessionTwoWarn").html("This field is mandatory.");
    } else if (!(newGroupSessionTwo == "")) {
      $("#newGroupSessionTwo").removeClass("is-danger");
      $("#newGroupSessionTwoWarn").html("");
      $("#newGroupSessionTwo").addClass("is-success");
    }
  });

  /**
   * Checks input of message body fields, if empty displays a red border and error text
   */
  $("#newGroupSessionThree").focusout(function () {
    var newGroupSessionThree = $("#newGroupSessionThree").val();
    if (newGroupSessionThree == "") {
      $("#newGroupSessionThree").addClass("is-danger");
      $("#newGroupSessionThreeWarn").html("This field is mandatory.");
    } else if (!(newGroupSessionThree == "")) {
      $("#newGroupSessionThree").removeClass("is-danger");
      $("#newGroupSessionThreeWarn").html("");
      $("#newGroupSessionThree").addClass("is-success");
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   *
   */
  $("#newGroupForm").focusout(function () {
    if (
      $("#newGroupName").val() == "" ||
      $("#newGroupDesc").val() == "" ||
      $("#newGroupSessionOne").val() == "" ||
      $("#newGroupSessionTwo").val() == "" ||
      $("#newGroupSessionThree").val() == ""
    ) {
      // if any field is empty, disables access to the button
      $(".detailsIfValid").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".detailsIfValid").prop("disabled", false);
    }
  });

  /**
   * Displays calendar only on the appointments page.
   */
  if (document.location.pathname == "/gymafi/appointments.php") {
    $("#evoCalendar").evoCalendar({
      calendarEvents: myEvents,
      todayHighlight: true,
      canAddEvent: false,
    });

    $("#evoCalendar").evoCalendar({
      format: "mm/dd/yyyy",

      titleFormat: "MM yyyy",

      eventHeaderFormat: "MM d, yyyy",

      dates: {
        en: {
          days: [
            "Sunday",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
          ],

          daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],

          daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],

          months: [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December",
          ],

          monthsShort: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
          ],
        },
      },
    });

    $(function () {
      $("#datepicker").datepicker({ minDate: 0, maxDate: 7 });
    });

    $("#timepicker").timepicker({
      timeFormat: "HH:mm",
      interval: 30,
      minTime: "09",
      maxTime: "20",
      defaultTime: "now",
      startTime: "09",
      dynamic: false,
      dropdown: true,
      scrollbar: true,
    });
  }

  /**
   * Only works on this specific div -  posts the data to create an appointment without refreshing the page.
   * Successfully posts the data, but error/success messages etc do not show. 
   * adapted from this online tutorial <https://www.youtube.com/watch?v=GrycH6F-ksY&list=LLUsAJ_g6sA2g7KaU8SXkrYg>
   
  $("#makeAppts").on("submit", function () {
    var that = $(this),
      url = that.attr("action"),
      type = that.attr("method"),
      data = {};

    that.find("[name]").each(function (index, value) {
      var that = $(this),
        name = that.attr("name"),
        value = that.val();
      data[name] = value;
    });

    $.ajax({
      url: url,
      type: type,
      data: data,
      success: function (data) {
        $("#makeAppts").trigger("reset");

        $("#success").fadeIn().html(data);
      },
    });

    return false;
  });
  *
   * */

  $("#editCoachDetails").click(function () {
    $("#editCoachDetailsModal").addClass("is-active");
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   * Regex adapted from https://stackoverflow.com/questions/23476532/check-if-string-contains-only-letters-in-javascript
   */
  $("#changeCoachDetailsForm").keyup(function () {
    if (
      $("#usernameChange").val() == "" ||
      $("#realNameChange").val() == "" ||
      $("#emailChange").val() == ""
    ) {
      // if any field is empty, disables access to the button
      $(".editCoachDetailsIfValid").prop("disabled", true);
    } else if (!/^[a-zA-Z\s]+$/.test($("#realNameChange").val())) {
      $(".editCoachDetailsIfValid").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".editCoachDetailsIfValid").prop("disabled", false);
    }
  });

  $("#coachArea").focusout(function () {
    var coachArea = $("#coachArea");
    if (coachArea.val() == "") {
      $("#coachArea").addClass("is-danger");
      $("#coachAreaWarn").html("This field is mandatory.");
    } else if (!coachArea.val() == "") {
      $("#coachArea").removeClass("is-danger");
      $("#coachAreaWarn").html("");
      $("#coachArea").addClass("is-success");
    }
  });

  /**
   * Checks if there is any input within the fields of the forms
   * If any mandatory field is empty, disables access to the submit button.
   * Adapted from online tutorial:
   * <https://blog.revillweb.com/jquery-disable-button-disabling-and-enabling-buttons-with-jquery-5e3ffe669ece>
   * Regex adapted from https://stackoverflow.com/questions/23476532/check-if-string-contains-only-letters-in-javascript
   */
  $("#editCoachForm").keyup(function () {
    if (
      $("#usernameChange").val() == "" ||
      $("#realNameChange").val() == "" ||
      $("#emailChange").val() == "" ||
      $("#coachArea").val() == ""
    ) {
      // if any field is empty, disables access to the button
      $(".newCoachDetailsIfValid").prop("disabled", true);
    } else if (!/^[a-zA-Z\s]+$/.test($("#realNameChange").val())) {
      $(".newCoachDetailsIfValid").prop("disabled", true);
    } else {
      // If all fields have some sort of valid input, enables button.
      $(".newCoachDetailsIfValid").prop("disabled", false);
    }
  });
});
