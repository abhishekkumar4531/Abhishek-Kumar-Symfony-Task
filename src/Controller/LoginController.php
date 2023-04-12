<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController {
  #[Route('/login', name: 'app_login', methods:['GET', 'HEAD', 'POST'])]
  public function index(): Response {
    if(isset($_POST['submitLogin'])) {
      $userEmail = $_POST['useremail'];
      $userPassword = $_POST['userpassword'];
      if($userEmail === 'abhikrjha45@gmail.com') {
        return $this->json([
          'User Email' => $userEmail,
          'User Password' => $userPassword
        ]);
      }
      else {
        return $this->render('login/index.html.twig', [
          'useremail' => $userEmail,
          'userpassword' => $userPassword
        ]);
      }
    }
    else {
      if(isset($_SESSION['user_loggedin'])) {
        return $this->redirectToRoute('app_home');
      }
      else {
        //return $this->redirectToRoute('app_login');
        return $this->render('login/index.html.twig', []);
      }
    }
  }
}
