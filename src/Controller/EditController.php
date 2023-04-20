<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FetchData;

/**
 * EditController
 * This class is extends with AbstractController
 * When user wants to edit or update his/her information then this class will be called.
 */
class EditController extends AbstractController {

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
  }

  #[Route('/edit', name: 'app_edit', methods: ['GET', 'POST', 'HEAD'])]
  /**
   * Index function is performing two type of operations,
   * 1st operation : If user want to update/edit their information
   * 2nd operation : If user want to just view their personal information
   *
   *   @param  mixed $request
   *     This Request object is to handles the session.
   *
   *   @return Response
   *     If user submit edit button then after updating the values it will
   *     redirect to the edit page.
   *     And in other cases it will check user logged in and exits then render to
   *     the edit page or user not logged in then redirect to login page.
   */
  public function index(Request $request): Response {
    $getUserData = new FetchData();
    //When user submit the edit/update form then if statement will be execute.
    //It will fetch all the data from edit/update form and then update the form.
    //If this function will called through link, url or using navbar then else
    //statement will be execute.
    if(isset($_POST['updateBtn'])) {
      $session = $request->getSession();

      $userBio = htmlspecialchars($_POST['user_bio'], ENT_QUOTES);
      $userFirstName = $_POST['first_name'];
      $userLastName = $_POST['last_name'];
      $userMobile = $_POST['user_mobile'];
      $userEmail = $_POST['user_email'];

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
          $this->userData['userImage'] = $userImage;
          $this->userData['userBio'] = $userBio;
          $this->userData['userFirstName'] = $userFirstName;
          $this->userData['userLastName'] = $userLastName;
          $this->userData['userMobile'] = $userMobile;
          $this->userData['userEmail'] =$userEmail;
          return $this->render('edit/index.html.twig', [
            'userData' => $this->userData,
            'imageTypeError' => "Please select valid image"
          ]);
        }
      }

      $fetchData = $this->userRepo->findOneBy([ 'userEmail' => $userEmail ]);

      //If fetchData will be user's credential then only this statement will be execute.
      //Update the all the updated feilds of user.
      if($fetchData) {
        $fetchData->setUserFirstName($userFirstName);
        $fetchData->setUserLastName($userLastName);
        $fetchData->setUserMobile($userMobile);
        $fetchData->setUserBio($userBio);
        $fetchData->setUserImage($userImage);
        $this->entityManager->flush();
        $userData =  $getUserData->fetchUserProfile($this->userRepo, $request, $userEmail);
        return $this->render('edit/index.html.twig', [
          'userData' => $userData
        ]);
      }
      else {
        return $this->redirectToRoute('app_home');
      }
    }
    else {
      $session = $request->getSession();
      if($session->get('user_loggedin')) {
        $userEmail = $session->get('user_loggedin');
        $userData = $getUserData->fetchUserProfile($this->userRepo, $request, $userEmail);
        return $this->render('edit/index.html.twig', [
          'userData' => $userData
        ]);
      }
      else {
        $session->invalidate();
        return $this->redirectToRoute('app_login');
      }
    }
  }

}
