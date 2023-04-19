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
   * It will be the object of EntityManagerInterfaced.
   *
   * @var mixed
   */
  private $entityManager;

  /**
   * It will be store the repository of Users class.
   *
   * @var mixed
   */
  private $userRepo;

  /**
   * It will be store the repository of Users class.
   *
   * @var mixed
   */
  private $postRepo;

  /**
   * __construct - It will update the $entityManager and $userRepo
   * and postRepo.
   *
   * @param  mixed $entityManager
   * @param  mixed $request
   *
   * @return void
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
   * @return Response
   */
  public function index(Request $request): Response
  {
    $session = $request->getSession();

    //If user logged in then fetch the user data and render to the display page.
    //If user not logged in then destroy the session and redirect to login page.
    if($session->get('user_loggedin')) {
      $userEmail = $session->get('user_loggedin');

      $fetchCredentials = $this->userRepo->findOneBy([ 'userEmail' => $userEmail ]);

      //If user $fetchCredentials not null fetch all the data from database and
      //render to the display page with values;
      if($fetchCredentials) {
        $userId = $fetchCredentials->getId();
        $firstName = $fetchCredentials->getUserFirstName();
        $lastName = $fetchCredentials->getUserLastName();
        $userImage = $fetchCredentials->getUserImage();
        $userBio = $fetchCredentials->getUserBio();

        $fetchPost = $this->postRepo->findBy([ 'userEmail' => $userEmail ]);
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
    else {
      $session->invalidate();
      return $this->redirectToRoute('app_login');
    }
  }
}
