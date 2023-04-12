reg_obj = new UserValidity();
function checkFname() {
  var user_name = document.getElementById('first_name').value;
  var status = reg_obj.checkName(user_name);
  if(status) {
    document.getElementById("invalid_fname").innerHTML = `Enter only alphabets`;
    document.getElementById("submitBtn").disabled = true;
  }
  else {
    document.getElementById("invalid_fname").innerHTML = '';
    document.getElementById("submitBtn").disabled = false;
  }
}
function checkLname() {
  var user_name = document.getElementById('last_name').value;
  var status = reg_obj.checkName(user_name);
  if(status) {
    document.getElementById("invalid_lname").innerHTML = `Enter only alphabets`;
    document.getElementById("submitBtn").disabled = true;
  }
  else {
    document.getElementById("invalid_lname").innerHTML = '';
    document.getElementById("submitBtn").disabled = false;
  }
}
function checkPhoneNo() {
  var user_mobile = document.getElementById('mobile').value;
  var status = reg_obj.checkPhone(user_mobile);
  if(status) {
    document.getElementById("invalid_mobile").innerText = `Enter valid mobile number`;
    document.getElementById("submitBtn").disabled = true;
  }
  else {
    document.getElementById("invalid_mobile").innerText = '';
    document.getElementById("submitBtn").disabled = false;
  }
}
function checkEmailStatus() {
  var user_email = document.getElementById('email').value;
  var status = reg_obj.checkEmail(user_email);
  if(status) {
    document.getElementById("email_success").innerText = ``;
    document.getElementById("email_status").innerText = `Enter valid email`;
    document.getElementById("submitBtn").disabled = true;
  }
  else {
    document.getElementById("email_status").innerText = ``;
    document.getElementById("email_success").innerText = `Valid email`;
    document.getElementById("submitBtn").disabled = false;
  }
}
function checkPasswordStatus() {
  var user_pwd = document.getElementById('pwd').value;
  var status = reg_obj.checkPasswords(user_pwd);
  if(status) {
    document.getElementById("pwd_success").innerText = ``;
    document.getElementById("pwd_status").innerText = `Enter valid password`;
    document.getElementById("submitBtn").disabled = true;
  }
  else {
    document.getElementById("pwd_status").innerText = ``;
    document.getElementById("pwd_success").innerText = `Valid password`;
    document.getElementById("submitBtn").disabled = false;
  }
}
function confirmPassword() {
  var new_pwd = document.getElementById('pwd').value;
  var cnf_pwd = document.getElementById('cnfPwd').value;
  var status = reg_obj.samePasswords(new_pwd, cnf_pwd);
  if(status) {
    document.getElementById("cnfPwd_status").innerText = ``;
    document.getElementById("submitBtn").disabled = false;
  }
  else {
    document.getElementById("cnfPwd_status").innerText = `Please enter same password`;
    document.getElementById("submitBtn").disabled = true;
  }
}
