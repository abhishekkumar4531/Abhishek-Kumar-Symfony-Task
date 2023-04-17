<?php

namespace App\Controller;

use App\Entity\Posts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Users;
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
   * @return Response
   */
  public function index(EntityManagerInterface $entityManager, Request $request): Response {
    $session = $request->getSession();
    if(($session->get('user_loggedin'))) {
      $userEmail = $session->get('user_loggedin');
      $verify = $entityManager->getRepository(Users::class);
      //Creating the object and fetching the user's info if user's entered email is exits.
      $fetchCredentials = $verify->findOneBy([ 'userEmail' => $userEmail ]);
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
   * @return Response
   */
  public function userPost(EntityManagerInterface $entityManager, Request $request): Response {
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
        $imgName = $_FILES['newFile']['name'];
        $imgTmp = $_FILES['newFile']['tmp_name'];
        $imgType = $_FILES['newFile']['type'];
        $postFile = "";
        if($imgType == "image/png" || $imgType == "image/jpeg" || $imgType == "image/jpg") {
          move_uploaded_file($imgTmp, "assets/uploads/". $imgName);
          $postFile = "assets/uploads/". $imgName;
        }
        else if($imgType == "video/wmv" || $imgType == "video/avi" || $imgType == "video/mpeg"
        || $imgType == "video/mpg" || $imgType == "video/mp4" || $imgType == "image/gif") {
          move_uploaded_file($imgTmp, "assets/videos/". $imgName);
          $postFile = "assets/videos/". $imgName;
        }
        else {
          return $this->redirectToRoute('app_home');
          die();
        }
        $post = new Posts();
        $post->setUserEmail($userEmail);
        $post->setPostComment($postComment);
        $post->setPostFile($postFile);
        $entityManager->persist($post);
        $entityManager->flush();
        return $this->redirectToRoute('app_home');
      }
      else {
        return $this->redirectToRoute('app_home', []);
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
   *
   * @param  mixed $entityManager
   * @param  mixed $request
   * @param  mixed $userId
   * @return void
   */
  public function publicProfile(EntityManagerInterface $entityManager, Request $request, int $userId) {
    $session = $request->getSession();
    if($session->get('user_loggedin')) {
      $verify = $entityManager->getRepository(Users::class);
      $fetchCredentials = $verify->findOneBy([ 'id' => $userId ]);
      //It will fetch all the user's information with the help of userId.
      //If $fetchCredential will not return null then fetch all the data and then,
      //render to the user's profile page with user's shareble inforation
      //*Shareable information : userFullName, userImage, userBio and user all posts.
      if($fetchCredentials) {
        $firstName = $fetchCredentials->getUserFirstName();
        $lastName = $fetchCredentials->getUserLastName();
        $userImage = $fetchCredentials->getUserImage();
        $userEmail = $fetchCredentials->getUserEmail();
        $userBio = $fetchCredentials->getUserBio();
        $verifyPost = $entityManager->getRepository(Posts::class);
        $fetchPost = $verifyPost->findBy([ 'userEmail' => $userEmail ]);
        return $this->render('home/profile.html.twig', [
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
  public function onBodyLoad(EntityManagerInterface $entityManager, Request $request) {
    $session = $request->getSession();
    if(($session->get('user_loggedin'))) {
      $fetchPosts = $entityManager->getRepository(Posts::class);
      $posts = $fetchPosts->findAll();
      $mediaData = $this->arrangePostData($entityManager, $posts, 0);
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
  public function onLoadMore(EntityManagerInterface $entityManager, Request $request) {
    $session = $request->getSession();
    if(($session->get('user_loggedin'))) {
      $fetchPosts = $entityManager->getRepository(Posts::class);
      $posts = $fetchPosts->findAll();
      $count = $session->get('count');
      $mediaData = $this->arrangePostData($entityManager, $posts, $count);
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

  /**
   * arrangePostData - This is for display the user's post with condtion of maximum
   * 10 posts can be load at a time
   *
   * @param [type] $entityManager
   * @param [type] $posts
   * @param [type] $count
   * @return array
   */
  public function arrangePostData($entityManager, $posts, $count) {
    $count = $count + 10;
    $start = 0;
    $mediaData = [];
    foreach($posts as $user) {
      $start++;
      if($start > $count) {
        return $mediaData;
      }
      $users = [];
      $userEmail = $user->getUserEmail();
      $userPostComment = $user->getPostComment();
      $users['postComment'] = $userPostComment;
      $userPostFile = $user->getPostFile();
      $users['postFile'] = $userPostFile;
      $fetchUsers = $entityManager->getRepository(Users::class);
      $userInfo = $fetchUsers->findOneBy([ 'userEmail' => $userEmail ]);
      $userId = $userInfo->getId();
      $users['userId'] = $userId;
      $userImage = $userInfo->getUserImage();
      $users['userImage'] = $userImage;
      $userFirstName = $userInfo->getUserFirstName();
      $users['userFirstName'] = $userFirstName;
      $userLastName = $userInfo->getUserLastName();
      $users['userLastName'] = $userLastName;
      $mediaData[] = $users;
    }
    return $mediaData;
  }

}
