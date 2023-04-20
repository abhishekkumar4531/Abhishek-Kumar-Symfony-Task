<?php

namespace App\Service;
// require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

class SendOtp {
  public function getEmail($userEmail) {
    $email = $userEmail;
    $otp = rand(100000, 999999);

    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = "abhi31kr45@gmail.com";
    $mail->Password = "ylagckqsadjtgigz";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom("abhi31kr45@gmail.com");
    $mail->addAddress($email);
    $mail->Subject = "Reset Password!!!";
    $mail->isHTML(TRUE);
    $mail->Body = "<b>Mail content:</b> Your OTP => $otp";
    $mail->send();
    return $otp;
  }
}

?>
