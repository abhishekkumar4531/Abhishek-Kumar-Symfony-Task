<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SendOtp;
use App\Entity\Users;

/**
 * ResetController
 */
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
    //f user submit the reset form then this statement will execute
    if(isset($_POST['sendOtp'])) {
      $userEmail = $_POST['user_email'];
      $verify = $entityManager->getRepository(Users::class);
      $fetchCredentials = $verify->findOneBy([ 'userEmail' => $userEmail ]);
      if($fetchCredentials) {
        $send = new SendOtp();
        $getOtp = $send->getEmail($userEmail);
        $session = $request->getSession();
        $session->set('get_otp', $getOtp);
        $session->set('user_email', $userEmail);
        $firstName = $fetchCredentials->getUserFirstName();
        $lastName = $fetchCredentials->getUserLastName();
        return $this->render('reset/reset.html.twig', [
          'userName' => $firstName ." ". $lastName
        ]);
      }
      else {
        return $this->render('reset/index.html.twig', [
          'userEmail' => $userEmail,
          'invalidEmail' => "Enter valid email!!!"
        ]);
      }
    }
    else if(isset($_POST['resetPwd'])) {
      $userName = $_POST['userName'];
      $enterOtp = $_POST['enterOtp'];
      $newPassword = $_POST['newPassword'];
      $cnfPassword = $_POST['cnfPassword'];
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
