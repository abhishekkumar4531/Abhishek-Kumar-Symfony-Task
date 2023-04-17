<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends AbstractController {
  #[Route('/login', name: 'app_login', methods:['GET', 'HEAD', 'POST'])]
  /**
   * index
   * When user will submit their login form then first if block will be execute
   * If user send the request to the controller using other way then else block
   * of first if block will be execute.
   *
   * @param  mixed $entityManager
   * @return Response
   */
  public function index(EntityManagerInterface $entityManager, Request $request): Response {
    //If User will submit the login form then this statement will be execute.
    //First it will fetch userEmail and userPassword from login form.
    //After that it will validate, if user exits then it will redirect to home page.
    //If user filled invalid credentials then it will again render to the login page
    //with error message.
    if(isset($_POST['submitLogin'])) {
      $userEmail = $_POST['useremail'];
      $userPassword = $_POST['userpassword'];
      $verify = $entityManager->getRepository(Users::class);
      $fetchCredentials = $verify->findOneBy([ 'userEmail' => $userEmail ]);
      if($fetchCredentials) {
        $checkPassword = $fetchCredentials->getUserPassword();
        if($checkPassword === $userPassword) {
          $session = $request->getSession();
          $session->set('user_loggedin', $userEmail);
          return $this->redirectToRoute('app_home');
        }
        else {
          return $this->render('login/index.html.twig', [
            'useremail' => $userEmail,
            'userpassword' => $userPassword,
            'invalidPassword' => "Please enter valid password"
          ]);
        }
      }
      else {
        return $this->render('login/index.html.twig', [
          'useremail' => $userEmail,
          'userpassword' => $userPassword,
          'invalidEmail' => "Please enter valid email"
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
        return $this->render('login/index.html.twig', []);
      }
    }
  }
}
