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
   * __construct - It will update the $entityManager and $verify.
   *
   * @param  mixed $entityManager
   * @param  mixed $request
   * @return void
   */
  public function __construct(EntityManagerInterface $entityManager) {
    $this->entityManager = $entityManager;
    $this->userRepo = $entityManager->getRepository(Users::class);
  }

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
          $userData['userImage'] = $userImage;
          $userData['userBio'] = $userBio;
          $userData['userFirstName'] = $userFirstName;
          $userData['userLastName'] = $userLastName;
          $userData['userMobile'] = $userMobile;
          $userData['userEmail'] =$userEmail;
          return $this->render('edit/index.html.twig', [
            'userData' => $userData,
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
