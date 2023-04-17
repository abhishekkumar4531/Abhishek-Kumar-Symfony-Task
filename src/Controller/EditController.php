<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * EditController
 * This class is extends with AbstractController
 * When user wants to edit or update his/her information then this class will be called.
 */
class EditController extends AbstractController {

  #[Route('/edit', name: 'app_edit', methods: ['GET', 'POST', 'HEAD'])]

  /**
   * index
   * index function is performing two type of operations,
   * 1st operation : If user want to update/edit their information
   * 2nd operation : If user want to just view their personal information
   *
   * @param  mixed $entityManager
   * @param  mixed $request
   * @return Response
   */
  public function index(EntityManagerInterface $entityManager, Request $request): Response {
    //When user submit the edit/update form then if statement will be execute.
    //It will fetch all the data from edit/update form and then update the form.
    //If this function will called through link, url or using navbar then else
    //statement will be execute.
    if(isset($_POST['updateBtn'])) {
      $session = $request->getSession();

      //Fetch the user's Bio from form and filter using 'htmlspecialchars'.
      $userBio = htmlspecialchars($_POST['user_bio'], ENT_QUOTES);
      $userFirstName = $_POST['first_name'];
      $userLastName = $_POST['last_name'];
      $userMobile = $_POST['user_mobile'];
      $userEmail = $_POST['user_email'];

      //Fetch the user's earlier image which is store in session.
      $userImage = $session->get('user_image');

      //If user want to also update their image then this statement will be execute.
      //It will check image type and then update the value of $userImage.
      if(!empty($_FILES['user_img']['name'])) {
        $imgName = $_FILES['user_img']['name'];
        $imgTmp = $_FILES['user_img']['tmp_name'];
        $imgType = $_FILES['user_img']['type'];

        //To check the image type, if condotion will be satisfied then
        //store the image in <uploads> folder.
        //If condition will not be satisfied then redirect to edit/update page with error message.
        if($imgType == "image/png" || $imgType == "image/jpeg" || $imgType == "image/jpg") {
          move_uploaded_file($imgTmp, "assets/uploads/". $imgName);
          $userImage = "assets/uploads/". $imgName;
        }
        else {
          return $this->render('edit/index.html.twig', [
            'userImage' => $userImage,
            'userBio' => $userBio,
            'userFirstName' => $userFirstName,
            'userLastName' => $userLastName,
            'userMobile' => $userMobile,
            'userEmail' => $userEmail,
            'imageTypeError' => "Please select valid image"
          ]);
        }
      }
      $verify = $entityManager->getRepository(Users::class);
      //Creating object of @USers class and checking the is email already available?
      $checkEmail = $verify->findOneBy([ 'userEmail' => $userEmail ]);

      //If $checkEmail will be user's credential then only this statement will be execute.
      //Update the all the updated feilds of user.
      if($checkEmail) {
        $checkEmail->setUserFirstName($userFirstName);
        $checkEmail->setUserLastName($userLastName);
        $checkEmail->setUserMobile($userMobile);
        $checkEmail->setUserBio($userBio);
        $checkEmail->setUserImage($userImage);
        $entityManager->flush();
        return $this->fetchUserProfile($verify, $request, $userEmail);
      }
      else {
        return $this->redirectToRoute('app_home');
      }
    }
    else {
      $session = $request->getSession();
      if($session->get('user_loggedin')) {
        $userEmail = $session->get('user_loggedin');
        $verify = $entityManager->getRepository(Users::class);
        return $this->fetchUserProfile($verify, $request, $userEmail);
      }
      else {
        $session->invalidate();
        return $this->redirectToRoute('app_login');
      }
    }
  }

  /**
   * fetchUserProfile
   * This function is communicates with database and also display user's credentials.
   *
   * @param  mixed $verify
   * @param  mixed $request
   * @param  mixed $userEmail
   *
   * It will be verify that the is $userEmail is exiting or not
   * if exists then fetch all the user's credentials and
   * then render back to edit profile page with all the credentails.
   * @return void
   */
  private function fetchUserProfile($verify, $request, $userEmail) {
    $session = $request->getSession();
    $fetchCredentials = $verify->findOneBy([ 'userEmail' => $userEmail ]);

    //If $fetchCredentials will not return null that means user exits,
    //then fetch all the data after that render to the edit profile page with values.
    //If due to any reason $fetchCredentails return null then redirect to the home page.
    if($fetchCredentials) {
      $fetchImage = $fetchCredentials->getUserImage();
      $session->set('user_image', $fetchImage);
      $fetchBio = $fetchCredentials->getUserBio();
      $fetchFirstName = $fetchCredentials->getUserFirstName();
      $fetchLastName = $fetchCredentials->getUserLastName();
      $fetchMobile = $fetchCredentials->getUserMobile();
      $fetchEmail = $fetchCredentials->getUserEmail();
      return $this->render('edit/index.html.twig', [
        'userImage' => $fetchImage,
        'userBio' => $fetchBio,
        'userFirstName' => $fetchFirstName,
        'userLastName' => $fetchLastName,
        'userMobile' => $fetchMobile,
        'userEmail' => $fetchEmail
      ]);
    }
    else {
      return $this->redirectToRoute('app_home');
    }
  }
}
