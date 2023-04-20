<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * RegisterController
 * It is responsible for user Registration.
 */
class RegisterController extends AbstractController {

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
   * and $userRepo
   *
   *   @param  mixed $entityManager
   *     It is to manage persistance and retriveal Entity object from Database.
   *   @return void
   */
  public function __construct(EntityManagerInterface $entityManager) {
    $this->entityManager = $entityManager;
    $this->userRepo = $entityManager->getRepository(Users::class);
  }

	#[Route('/register', name: 'app_register', methods:['GET', 'HEAD', 'POST'])]

	/**
	 * When user submit their registration form then it will fetch the data and
	 * store in the database after that redirect to the login page.
	 * If their is any issues during registartion then it will render to registartion
	 * page with error message.
	 * If someone directly try to access this url then first if user logged in then
	 * redirect to home page other wise render to registartion page.
	 *
	 *   @param  mixed $request
   *     This Request object is to handles the session.
	 *
	 *   @return Response
	 *     If registration form submitted then after registration it will redirect
	 *     to login page, if any issue during registartion then it will render to
	 *     registartion page with error message.
	 *     If user logged in then try to access then it will redirect to home page
	 *     otherwise render to the registration page.
	 */
	public function index(Request $request): Response {

		// When user will submit the registartion form then first below statement will be execute.
		// First validating all the input data then also validate the image type.
		if(isset($_POST['submitRegistration'])) {
			$userFirstName = $_POST['first_name'];
			$userLastName = $_POST['last_name'];
			$userPassword = $_POST['user_password'];
			$userMobile = $_POST['user_mobile'];
      $userEmail = $_POST['user_email'];
			/*$userFirstName = $this->testInput($userFirstName);
			$userLastName = $this->testInput($userLastName);
			$userPassword = $this->testInput($userPassword);
			$userMobile = $this->testInput($userMobile);
			$userEmail = $this->testInput($userEmail);*/
			/*if(isset($_POST['cookie_status'])) {
				$cookieStatus = $_POST['cookie_status'];
			}
			else {
				$cookieStatus = "Denied";
			}*/
			$imgName = $_FILES['user_img']['name'];
			$imgTmp = $_FILES['user_img']['tmp_name'];
			$imgType = $_FILES['user_img']['type'];

			// To check the image file type
			if($imgType == "image/png" || $imgType == "image/jpeg" || $imgType == "image/jpg") {
				move_uploaded_file($imgTmp, "assets/uploads/". $imgName);
				$userImage = "assets/uploads/". $imgName;
				$checkEmail = $this->userRepo->findOneBy([ 'userEmail' => $userEmail ]);
				$checkMobile = $this->userRepo->findOneBy(['userMobile' => $userMobile]);
				if(!$checkEmail && !$checkMobile) {
					$users = new Users();
					$users->setUserFirstName($userFirstName);
					$users->setUserLastName($userLastName);
					$users->setUserPassword($userPassword);
					$users->setUserMobile($userMobile);
					$users->setUserEmail($userEmail);
					$users->setUserImage($userImage);
					$users->setUserBio('User Bio');
					$this->entityManager->persist($users);
					$this->entityManager->flush();
					return $this->redirectToRoute('app_login');
				}
				else {
					if($checkEmail && $checkMobile){
						$emailError = "Please check your email";
						$mobileError = "Please check your mobile";
					}
					else if($checkEmail) {
						$emailError = "Please check your email";
						$mobileError = "";
					}
					else {
						$emailError = "";
						$mobileError = "Please check your mobile";
					}
					return $this->render('register/index.html.twig', [
						'userfname' => $userFirstName,
						'userlname' => $userLastName,
						'userpassword' => $userPassword,
						'usermobile' => $userMobile,
						'useremail' => $userEmail,
						'emailError' => $emailError,
						'mobileError' => $mobileError
					]);
				}
			}
      else {
        return $this->render('register/index.html.twig', [
					'userfname' => $userFirstName,
					'userlname' => $userLastName,
					'userpassword' => $userPassword,
					'usermobile' => $userMobile,
					'useremail' => $userEmail,
					'imageError' => "Please upload valid image type"
				]);
      }
    }
    else {
			$session = $request->getSession();
      if($session->get('user_loggedin')) {
        return $this->redirectToRoute('app_home');
      }
      else {
        $session->invalidate();
        return $this->render('register/index.html.twig', []);
      }
    }
	}

	/**
	 * It is validate the input data.
	 *
	 *   @param string $data
	 *     It is string type user entered data.
	 *   @return $data
	 *     After all the operation a string type data will return
	 */
	public function testInput($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

}
