/**
 * Validity class is responsible for Form-Validation using JavaScript.
 */
class UserValidity{

  //check_valid variable is type of RegeX variable and used for user name validations.
  //simlarlly, check_phone for user phone number and check_email for user email.
  check_valid = /^[A-Za-z]+$/;
  check_phone = /^(\+91)[0-9]{10}$/;
  check_email = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
  check_pwd = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/;

  /**
   * 'checkName/Phone/Email/Passwords' is parametrised method;
   * 'invalidName/Phone/Email/Passwords' is a variable which hold 'id' of a html tag where error msg should show;
   * 'submitBtn' is a variable which hold 'id' of submit button;
   * 'type' is a vraible which hold color;

   * checkName(userName/Phone, invalidName/Phone, submitBtn, type){
   * if userName/Phone is not matched with RegeX expression then :
      At first error message should show.
      And then disable the submit button.
   * if matched then :
      Error message should not show.
      And enable the submit button.
   * }

   * checkEmail/Passwords's working process is same only change in message display like :
     if condition is true then display success message and if false then display error message.
  */

  /**
   * checkName is a parametrised method which is responsible for the validation
   * of user name.
   *
   *   @param string userName
   *     It is string type which store the user first/last name.
   *
   *   @returns boolean
   *     If userName invalid then return true otherwise false
   */
  checkName(userName){
    if(!(userName.match(this.check_valid))) {
      return true;
    }
    return false;
  }

  /**
   * checkPhone is a parametrised method which is responsible for the validation
   * of user phone number.
   *
   *   @param string userPhone
   *     It stores the user phone number.
   *
   *   @returns boolean
   *     If userPhone invalid then return true otherwise false
   */
  checkPhone(userPhone){
    if(!(userPhone.match(this.check_phone))) {
      return true;
    }
    return false;
  }

  /**
   * checkEmail is a parametrised method which is responsible for the validation
   * of user email.
   *
   *   @param string userEmail
   *     It stores the user phone number.
   *
   *   @returns boolean
   *     If userPhone invalid then return true otherwise false
   */
  checkEmail(userEmail){
    if(!(userEmail.match(this.check_email))) {
      return true;
    }
    return false;
  }

  /**
   * checkPasswords is a parametrised method which is responsible for the
   * validation of user passwords.
   *
   *   @param string userPwd
   *     It stores the user password.
   *   @returns boolean
   *     If userPwd invalid then return true otherwise false
   */
  checkPasswords(userPwd){
    if(!(userPwd.match(this.check_pwd))) {
      return true;
    }
    return false;
  }

  /**
   * comparePasswords is a parametrised method which is comparing user entered
   * passwords.
   * User's old password and new password should't be same.
   * If old password and new password will be same then it will return false
   * otherwise it will return true.
   *
   *   @param string currentPassword
   *     It store the user current password
   *   @param string newPassword
   *     It store the user entered new password
   *
   *   @returns boolean
   *     If both password same then return false otherwise true.
   */
  diffPasswords(currentPassword, newPassword) {
    if(currentPassword === newPassword){
      return false;
    }
    return true;
  }

  /**
   * samePasswords is a parametrised method which is comparing user entered
   * new passwords.
   * User's new password and confirm password should be same.
   * If new password and confirm password will be same then it will return true
   * otherwise it will return false.
   *
   *   @param string newPassword
   *     It store the user entered new password
   *   @param string cnfPassword
   *     It store the user entered cnfirm password
   *
   *   @returns boolean
   *     If both password same then return true otherwise false.
   */
  samePasswords(newPassword, cnfPassword) {
    if(newPassword === cnfPassword){
      return true;
    }
    return false;
  }
}
