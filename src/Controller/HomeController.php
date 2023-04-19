<?php

namespace App\Controller;

use App\Entity\Posts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Users;
use App\Service\FetchData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * HomeController
 * This class is extends with AbstractController class.
 * All the function of this class will be execute after login only.
 * All the function of HomeController is first check is user logged in or not?
 * If logged in then continue otherwise redirect to login page.
 */
class HomeController extends AbstractController {

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
   * userData - It will store the user's personal data.
   *
   * @var array
   */
  private $userData = Array();

  private $arrange;

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
    $this->arrange = new FetchData();
  }

  #[Route('/', name: 'app_home')]
  #[Route('/home', name: 'app_homes')]
  /**
   * index
   * First it will start the session and check is user logged in or not?
   * If logged in then first fetch user's name and image and then render to
   * home page with user's name and image.
   * If user not logged in then destroy the session and redirect to login page.
   *
   * @param  mixed $entityManager
   * @param  mixed $request
   *
   * @return Response
   */
  public function index(Request $request): Response {
    $session = $request->getSession();
    if($session->get('user_loggedin')) {
      $userEmail = $session->get('user_loggedin');
      $fetchCredentials = $this->userRepo->findOneBy([ 'userEmail' => $userEmail ]);
      if($fetchCredentials) {
        $firstName = $fetchCredentials->getUserFirstName();
        $userImage = $fetchCredentials->getUserImage();
        return $this->render('home/index.html.twig', [
          'userFirstName' => $firstName,
          'userImage' => $userImage
        ]);
      }
      else {
        $session->invalidate();
        return $this->redirectToRoute('app_login');
      }
    }
    else {
      $session->invalidate();
      return $this->redirectToRoute('app_login');
    }
  }

  #[Route('/home/post', name: 'app_homepost')]
  /**
   * userPost
   * When user post their post then this function will be called
   * First it will fetch all the input data then check file type
   * Currently only image and video type will be accepted.
   * After file type validations it will store the post's information into database,
   * after that redirect to the home page.
   *
   * @param  mixed $entityManager
   * @param  mixed $request
   *
   * @return Response
   */
  public function userPost(Request $request): Response {
    $session = $request->getSession();
    //If user logged in then it will check if user submit the post's form or not?
    //If submit the post form then fetch the data from form and validate them,
    //after validation it will store in database and then reedirect to home page.
    //If user not submit the form and try to call this function then it will
    //redirect to home page.
    //If user not logged in then it will first distroy the session and then
    //redirect to login page.
    if(($session->get('user_loggedin'))) {
      if(isset($_POST['upload'])) {
        $userEmail = $session->get('user_loggedin');
        $postComment = htmlspecialchars($_POST['newPost'], ENT_QUOTES);
        $fileName = $_FILES['newFile']['name'];
        $fileTmp = $_FILES['newFile']['tmp_name'];
        $fileType = $_FILES['newFile']['type'];
        $postFile = "";
        if($fileType == "image/png" || $fileType == "image/jpeg" ||
        $fileType == "image/jpg" || $fileType == "image/gif") {
          move_uploaded_file($fileTmp, "assets/uploads/". $fileName);
          $postFile = "assets/uploads/". $fileName;
        }
        else if($fileType == "video/wmv" || $fileType == "video/avi" ||
        $fileType == "video/mpeg" || $fileType == "video/mpg" || $fileType == "video/mp4") {
          move_uploaded_file($fileTmp, "assets/videos/". $fileName);
          $postFile = "assets/videos/". $fileName;
        }
        else {
          return $this->redirectToRoute('app_home');
          die();
        }
        $post = new Posts();
        $post->setUserEmail($userEmail);
        $post->setPostComment($postComment);
        $post->setPostFile($postFile);
        $this->entityManager->persist($post);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_home');
      }
      else {
        return $this->redirectToRoute('app_home');
      }
    }
    else {
      $session->invalidate();
      return $this->redirectToRoute('app_login');
    }
  }

  #[Route('/home/profile/{userId}', name: 'app_profile')]
  /**
   * publicProfile - For display other profie with shareable values.
   * It will fetch all the user's information with the help of userId.
   * First it will check user logged in or not?
   * If logged in then fetch the user's data after that fetch the user all the
   * post data render to the user's profile page with user's shareble inforation.
   * *Shareable information : userFullName, userImage, userBio and user all posts.
   *
   * @param  mixed $entityManager
   * @param  mixed $request
   * @param  int $userId
   *
   * @return void
   */
  public function publicProfile(Request $request, int $userId) {
    $session = $request->getSession();
    if($session->get('user_loggedin')) {
      $fetchCredentials = $this->userRepo->findOneBy([ 'id' => $userId ]);

      if($fetchCredentials) {
        $userData['userFirstName'] = $fetchCredentials->getUserFirstName();
        $userData['userLastName'] = $fetchCredentials->getUserLastName();
        $userData['userImage'] = $fetchCredentials->getUserImage();
        $userData['userBio'] = $fetchCredentials->getUserBio();
        $userEmail = $fetchCredentials->getUserEmail();
        $fetchPost = $this->postRepo->findBy([ 'userEmail' => $userEmail ]);
        return $this->render('home/profile.html.twig', [
          'userData' => $userData,
          'fetchPosts' => $fetchPost
        ]);
      }
    }
    else {
      $session->invalidate();
      return $this->redirectToRoute('app_login');
    }
  }

  #[Route('/home/load', name: 'app_loadpost')]
  /**
   * onBodyLoad - For presenting the default 10 post on home page.
   * First it will check is user logged-in or not?
   * After that fetch all the post data from database and store in $posts.
   * And then call the $arrangePostData function, it will arrange the post data with condition.
   * After that render to post display page with post's data $mediaData.
   *
   * @param  mixed $entityManager
   * @param  mixed $request
   * @return Response
   */
  public function displayDefaultPost(Request $request) {
    $session = $request->getSession();
    if(($session->get('user_loggedin'))) {
      $posts = $this->postRepo->findAll();
      $mediaData = $this->arrange->arrangePostData($this->userRepo, $posts, 0);
      $session->set('count', count($mediaData));
      return $this->render('home/post.html.twig', [
        'mediaData' => $mediaData
      ]);
    }
    else {
      $session->invalidate();
      return $this->redirectToRoute('app_login');
    }
  }

  #[Route('/home/loadmore', name: 'app_loadmorepost')]
  /**
   * onLoadMore - For presenting the default 10 post on home page.
   * First it will check is user logged-in or not?
   * After that fetch all the post data from database and store in $posts.
   * And then call the $arrangePostData function, it will arrange the post data with condition.
   * After that render to post display page with post's data $mediaData.
   *
   * @param  mixed $entityManager
   * @param  mixed $request
   * @return Response
   */
  public function displayMorePost(Request $request) {
    $session = $request->getSession();
    if(($session->get('user_loggedin'))) {
      $posts = $this->postRepo->findAll();
      $count = $session->get('count');
      $mediaData = $this->arrange->arrangePostData($this->userRepo, $posts, $count);
      $session->set('count', count($mediaData));
      return $this->render('home/post.html.twig', [
        'mediaData' => $mediaData
      ]);
    }
    else {
      $session->invalidate();
      return $this->redirectToRoute('app_login');
    }
  }

}
