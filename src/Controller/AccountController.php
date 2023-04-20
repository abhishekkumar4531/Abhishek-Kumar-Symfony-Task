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
 * It is responsible for showing the user's profile page with user's image, fullName
 * and all the post which is posted be user.
 */
class AccountController extends AbstractController {

  /**
   * It stores the object of EntityManagerInterface
   * It is also manage persistance and retriveal Entity object from Database.
   *
   *   @var mixed
   */
  private $entityManager;

  /**
   * It store the object of UserRepository class and also fetch data from database.
   *
   *   @var mixed
   */
  private $userRepo;

  /**
   * It store the object of PostRepository class and also fetch data from database.
   *
   *   @var mixed
   */
  private $postRepo;

  /**
   * It will store the user's personal data.
   *
   *   @var array
   */
  private $userData = [];

  /**
   * __construct - It will initialize the class and store objects in $entityManager,
   * $userRepo, $postRepo and $arrange.
   *
   *   @param  mixed $entityManager
   *     It is to manage persistance and retriveal Entity object from Database.
   *
   *   @return void
   */
  public function __construct(EntityManagerInterface $entityManager) {
    $this->entityManager = $entityManager;
    $this->userRepo = $entityManager->getRepository(Users::class);
    $this->postRepo = $entityManager->getRepository(Posts::class);
  }

  #[Route('/account', name: 'app_account')]
  /**
   * First it will start the session and check is user logged in or not?
   * If user not logged in then destroy the session and redirect to the login page.
   * If user logged in then continue.
   * First it will fetch user's data from database and then display data using redering.
   *
   *   @param  mixed $request
   *     This Request object is to handles the session.
   *
   *   @return Response
   *     If user logged in and user exits then int will render to user's persobal
   *     profile page.
   *     If user not logged in then redirect to the login page.
   */
  public function index(Request $request): Response
  {
    $session = $request->getSession();

    // If user logged in then fetch the user data and render to the display page.
    // If user not logged in then destroy the session and redirect to login page.
    if($session->get('user_loggedin')) {
      $userEmail = $session->get('user_loggedin');

      $fetchCredentials = $this->userRepo->findOneBy([ 'userEmail' => $userEmail ]);

      // If user $fetchCredentials not null fetch all the data from database and
      // render to the display page with values;
      if($fetchCredentials) {
        $this->userData['userId'] = $fetchCredentials->getId();
        $this->userData['firstName'] = $fetchCredentials->getUserFirstName();
        $this->userData['lastName'] = $fetchCredentials->getUserLastName();
        $this->userData['userImage'] = $fetchCredentials->getUserImage();
        $this->userData['userBio'] = $fetchCredentials->getUserBio();

        $fetchPost = $this->postRepo->findBy([ 'userEmail' => $userEmail ]);
        return $this->render('account/index.html.twig', [
          'userData' => $this->userData,
          'fetchPosts' => $fetchPost
        ]);
      }
    }
    else {
      $session->invalidate();
      return $this->redirectToRoute('app_login');
    }
  }

}
