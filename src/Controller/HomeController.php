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

class HomeController extends AbstractController
{
  #[Route('/', name: 'app_home')]
  #[Route('/home', name: 'app_homes')]
  /**
   * index
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
          $postFile = "assets/uploads/". $imgName;
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
        return $this->redirectToRoute('app_home');
      }
    }
    else {
      $session->invalidate();
      return $this->redirectToRoute('app_login');
    }
  }

  #[Route('/home/load', name: 'app_loadpost')]
  public function onBodyLoad(EntityManagerInterface $entityManager, Request $request) {
    $session = $request->getSession();
    if(($session->get('user_loggedin'))) {
      $fetchPosts = $entityManager->getRepository(Posts::class);
      $posts = $fetchPosts->findAll();
      //return $this->json($posts);
      return $this->render('home/post.html.twig', [
        'userPosts' => $posts
      ]);
    }
    else {
      $session->invalidate();
      return $this->redirectToRoute('app_login');
    }
  }

}
