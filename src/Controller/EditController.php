<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class EditController extends AbstractController
{
  #[Route('/edit', name: 'app_edit', methods: ['GET', 'POST', 'HEAD'])]

  /**
   * index
   *
   * @param  mixed $entityManager
   * @param  mixed $request
   * @return Response
   */
  public function index(EntityManagerInterface $entityManager, Request $request): Response {
    /**
     * When user submit the edit profile page then this block will be execute
     */
    if(isset($_POST['updateBtn'])) {
      //$userBio = $_POST['user_bio'];
      //Start the session
      $session = $request->getSession();
      //Fetch the user's Bio from form and filter using 'htmlspecialchars'
      $userBio = htmlspecialchars($_POST['user_bio'], ENT_QUOTES);
      $userFirstName = $_POST['first_name'];
      $userLastName = $_POST['last_name'];
      $userMobile = $_POST['user_mobile'];
      $userEmail = $_POST['user_email'];
      //Fetch the user's earlier image which is store in session
      $userImage = $session->get('user_image');
      //If user want to update their profile then this condition will execute
      if(!empty($_FILES['user_img']['name'])) {
        $imgName = $_FILES['user_img']['name'];
        $imgTmp = $_FILES['user_img']['tmp_name'];
        $imgType = $_FILES['user_img']['type'];
        //If image type will be valid type then execute this statement
        if($imgType == "image/png" || $imgType == "image/jpeg" || $imgType == "image/jpg") {
          //Upload the user's image on uploads folder which is under the assets folder
          move_uploaded_file($imgTmp, "assets/uploads/". $imgName);
          //Now change the $userImage value with user entered value.
          $userImage = "assets/uploads/". $imgName;
        }
        //If image type will not be valid then redirect to home edit page with error message.
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
      /**
       * If $checkEmail will be user's credential then only this statement will be execute.
       * Update the all the updated feilds of user.
       */
      if($checkEmail) {
        $checkEmail->setUserFirstName($userFirstName);
        $checkEmail->setUserLastName($userLastName);
        $checkEmail->setUserMobile($userMobile);
        $checkEmail->setUserBio($userBio);
        $checkEmail->setUserImage($userImage);
        $entityManager->flush();
        //Call the fetchUserProfile function for rendring on edit page with updated values.
        return $this->fetchUserProfile($verify, $request, $userEmail);
      }
      //If somehow any type of error came then redirect to home page.
      else {
        return $this->redirectToRoute('app_home');
      }
    }
    /**
     * If controller get the request from another way the execute this one.
     */
    else {
      $session = $request->getSession();
      //If user logged in then this statement will be execute.
      if($session->get('user_loggedin')) {
        //Fetch the userEmail of logged in user with the help of session
        $userEmail = $session->get('user_loggedin');
        $verify = $entityManager->getRepository(Users::class);
        //Call the fetchUserProfile function for rendring on edit page with updated values.
        return $this->fetchUserProfile($verify, $request, $userEmail);
      }
      //If user not login then redirect to login page.
      else {
        $session->invalidate();
        return $this->redirectToRoute('app_login');
      }
    }
  }

  /**
   * fetchUserProfile
   * This function is communicates with database and also display user's credentials.
   * First this fucntion will get the three varialble as a parameter
   * @param  mixed $verify
   * @param  mixed $request
   * @param  mixed $userEmail
   *
   * After that it will be verify that the is $userEmail is exiting or not
   * if existing then fetch all the user's credentials and
   * then render back to edit profile page with all the credentails.
   * @return void
   */
  private function fetchUserProfile($verify, $request, $userEmail) {
    $session = $request->getSession();
    //Creating the object and fetching the user's info if user's entered email is exits.
    $fetchCredentials = $verify->findOneBy([ 'userEmail' => $userEmail ]);
    //If $fetchCredentials will not return null that means user is exits, then only this statement will be execute.
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
