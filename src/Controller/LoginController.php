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
   *
   * @param  mixed $entityManager
   * @return Response
   */
  public function index(EntityManagerInterface $entityManager, Request $request): Response {
    /**
     * If User will submit the login form then only this statement will be execute.
     */
    if(isset($_POST['submitLogin'])) {
      $userEmail = $_POST['useremail'];
      $userPassword = $_POST['userpassword'];
      $verify = $entityManager->getRepository(Users::class);
      //Creating the object and fetching the user's info if user's entered email is exits.
      $fetchCredentials = $verify->findOneBy([ 'userEmail' => $userEmail ]);
      //If $fetchCredentials will not return null that means user is exits, then only this statement will be execute.
      if($fetchCredentials) {
        // $checkEmail = $fetchCredentials->getUserEmail();
        //Now fetching the user's password
        $checkPassword = $fetchCredentials->getUserPassword();
        /**
         * Comparing the user's entered password with $checkPassword which is fetched from database
         * If both password will be same then only this statement will be execute.
         */
        if($checkPassword === $userPassword) {
          // session_start();
          // $_SESSION['user_loggedin'] = $userEmail;
          $session = $request->getSession();
          $session->set('user_loggedin', $userEmail);
          //And now redirect to home page.
          return $this->redirectToRoute('app_home');
        }
        else {
          //If user entered invalid password then back to the login page with error message.
          return $this->render('login/index.html.twig', [
            'useremail' => $userEmail,
            'userpassword' => $userPassword,
            'invalidPassword' => "Please enter valid password"
          ]);
        }
      }
      else {
        //If user entered invalid email then back to the login page with error message.
        return $this->render('login/index.html.twig', [
          'useremail' => $userEmail,
          'userpassword' => $userPassword,
          'invalidEmail' => "Please enter valid email"
        ]);
      }
    }
    /**
     * If login controller get request from other ways.
     * Then first it will be check, if user is logged or not.
     * If user logged in then redirect to the home page else stay on login page.
     */
    else {
      //session_start();
      $session = $request->getSession();
      if($session->get('user_loggedin')) {
        return $this->redirectToRoute('app_home');
      }
      else {
        //session_destroy();
        $session->invalidate();
        return $this->render('login/index.html.twig', []);
      }
    }
  }
}
