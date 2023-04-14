<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SendOtp;
use App\Entity\Users;

class ResetController extends AbstractController
{
  #[Route('/reset', name: 'app_reset')]
  /**
   * index
   *
   * @param  mixed $entityManager
   * @param  mixed $request
   * @return Response
   */
  public function index(EntityManagerInterface $entityManager, Request $request): Response {
    /**
     * If user submit the reset form then this statement will execute
     */
    if(isset($_POST['sendOtp'])) {
      $userEmail = $_POST['user_email'];
      $verify = $entityManager->getRepository(Users::class);
      //Creating the object and fetching the user's info if user's entered email is exits.
      $fetchCredentials = $verify->findOneBy([ 'userEmail' => $userEmail ]);
      //If $fetchCredentials will not return null that means user is exits, then only this statement will be execute.
      if($fetchCredentials) {
        $send = new SendOtp();
        //Call the method getEmail with $userEmail as a parameter for otp sending to user's email.
        $getOtp = $send->getEmail($userEmail);
        //Store the otp value in session.
        $session = $request->getSession();
        $session->set('get_otp', $getOtp);
        $session->set('user_email', $userEmail);
        //Fetch the user's $firstName and $lastName from database.
        $firstName = $fetchCredentials->getUserFirstName();
        $lastName = $fetchCredentials->getUserLastName();
        // $userName = $firstName ." ". $lastName;
        //Render to the Reset-Page where user can reset password
        return $this->render('reset/reset.html.twig', [
          'userName' => $firstName ." ". $lastName
        ]);
      }
      //If user enter invalid email for otp sending to the user's email
      else {
        //render to the send otp page with error message.
        return $this->render('reset/index.html.twig', [
          'userEmail' => $userEmail,
          'invalidEmail' => "Enter valid email!!!"
        ]);
      }
    }
    else if(isset($_POST['resetPwd'])) {
      //Fetching all the input values from Reset password form
      $userName = $_POST['userName'];
      $enterOtp = $_POST['enterOtp'];
      $newPassword = $_POST['newPassword'];
      $cnfPassword = $_POST['cnfPassword'];
      //If both newPassword and confirmPassword will be same the execute
      if($newPassword === $cnfPassword) {
        $session = $request->getSession();
        $getOtp = $session->get('get_otp');
        $userEmail = $session->get('user_email');
        $session->invalidate();
        if(number_format($enterOtp) === number_format($getOtp)) {
          $verify = $entityManager->getRepository(Users::class);
          $fetchCredentials = $verify->findOneBy([ 'userEmail' => $userEmail ]);
          if($fetchCredentials) {
            $fetchCredentials->setUserPassword($newPassword);
            $entityManager->flush();
            return $this->redirectToRoute('app_login');
          }
        }
        else {
          return $this->render('reset/reset.html.twig', [
            'userName' => $userName,
            'enteredOtp' => $enterOtp,
            'newPassword' => $newPassword,
            'cnfPassword' => $cnfPassword,
            'invalidOtp' => 'Please enter valid OTP'
          ]);
        }
      }
      //If both the password is not same
      else {
        return $this->render('reset/reset.html.twig', [
          'userName' => $userName,
          'enteredOtp' => $enterOtp,
          'newPassword' => $newPassword,
          'cnfPassword' => $cnfPassword,
          'invalidPassword' => 'Please enter same password'
        ]);
      }
    }
    else {
      $session = $request->getSession();
      if($session->get('user_loggedin')) {
        return $this->redirectToRoute('app_home');
      }
      else {
        $session->invalidate();
        return $this->render('reset/index.html.twig', []);
      }
    }
  }
}
