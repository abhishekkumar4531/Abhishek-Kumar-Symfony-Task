<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Users;
use App\Entity\Posts;
use Doctrine\ORM\EntityManagerInterface;

/**
 * AccountController
 * This class is extends with AbstractController
 * This class is user's profile page.
 * In this class only one fuction available - index.
 */
class AccountController extends AbstractController {

  #[Route('/account', name: 'app_account')]
  /**
   * index
   * First it will start the session and first check is user logged in or not?
   * If user not logged in then destroy the session and redirect to the login page.
   * If user logged in then continue.
   * First it will fetch user's data from database and then display data using redering.
   *
   * @param  mixed $entityManager
   * @param  mixed $request
   * @return Response
   */
  public function index(EntityManagerInterface $entityManager, Request $request): Response
  {
    //start the session
    $session = $request->getSession();
    //If user logged in then fetch the user data and render to the display page.
    if($session->get('user_loggedin')) {
      //Fetch userEmail of logged in user
      $userEmail = $session->get('user_loggedin');

      $verify = $entityManager->getRepository(Users::class);

      //Create the object for Users class and fetch user's data whose email is $userEmail.
      $fetchCredentials = $verify->findOneBy([ 'userEmail' => $userEmail ]);

      //If user $fetchCredentials not null.
      if($fetchCredentials) {
        //Fetch the user's id.
        $userId = $fetchCredentials->getId();

        //Fetch the user's first name.
        $firstName = $fetchCredentials->getUserFirstName();

        //Fetch the user's last name.
        $lastName = $fetchCredentials->getUserLastName();

        //Fetch the user's image.
        $userImage = $fetchCredentials->getUserImage();

        //Fetch the user's bio.
        $userBio = $fetchCredentials->getUserBio();

        $verifyPost = $entityManager->getRepository(Posts::class);

        //Create object for Posts class and fetch user's data whose email is $userEmail.
        $fetchPost = $verifyPost->findBy([ 'userEmail' => $userEmail ]);

        //After that render to the account's display page with user's credentails.
        return $this->render('account/index.html.twig', [
          'userId' => $userId,
          'userFirstName' => $firstName,
          'userLastName' => $lastName,
          'userImage' => $userImage,
          'userBio' => $userBio,
          'fetchPosts' => $fetchPost
        ]);
      }
    }
    //If user not logged in.
    else {
      //Destroy the session.
      $session->invalidate();

      //And resirect to the home page
      return $this->redirectToRoute('app_login');
    }
  }
}
