<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;

class RegisterController extends AbstractController {
	#[Route('/register', name: 'app_register', methods:['GET', 'HEAD', 'POST'])]
	/**
	 * index
	 *
	 * @return Response
	 */
	public function index(EntityManagerInterface $entityManager): Response {
		/**
		 * When user will submit the registartion form then first below statement will be execute.
		 * First validating all the input data then also validate the image type.
		 */
		if(isset($_POST['submitRegistration'])) {
			$userFirstName = $_POST['first_name'];
			$userLastName = $_POST['last_name'];
			$userPassword = $_POST['user_password'];
			$userMobile = $_POST['user_mobile'];
      $userEmail = $_POST['user_email'];
			/**
			 * Validate all the input data with the help of @textInput method.
			 */
			/*$userFirstName = $this->testInput($userFirstName);
			$userLastName = $this->testInput($userLastName);
			$userPassword = $this->testInput($userPassword);
			$userMobile = $this->testInput($userMobile);
			$userEmail = $this->testInput($userEmail);*/
			if(isset($_POST['cookie_status'])) {
				$cookieStatus = $_POST['cookie_status'];
			}
			else {
				$cookieStatus = "Denied";
			}
			$imgName = $_FILES['user_img']['name'];
			$imgTmp = $_FILES['user_img']['tmp_name'];
			$imgType = $_FILES['user_img']['type'];
			//To check the image file type
			if($imgType == "image/png" || $imgType == "image/jpeg" || $imgType == "image/jpg") {
				move_uploaded_file($imgTmp, "assets/uploads/". $imgName);
				$userImage = "assets/uploads/". $imgName;
				$verify = $entityManager->getRepository(Users::class);
				$checkEmail = $verify->findOneBy([ 'userEmail' => $userEmail ]);
				$checkMobile = $verify->findOneBy(['userMobile' => $userMobile]);
				if(!$checkEmail && !$checkMobile) {
					$users = new Users();
					$users->setUserFirstName($userFirstName);
					$users->setUserLastName($userLastName);
					$users->setUserPassword($userPassword);
					$users->setUserMobile($userMobile);
					$users->setUserEmail($userEmail);
					$users->setUserImage($userImage);
					$users->setUserBio('User Bio');
					$entityManager->persist($users);
					$entityManager->flush();
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
			//If image type will invalid then send back to the Registration page with error message
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
		/**
		 * if user randomly come to this page.
		 * If user logged in then user will be redirected to the home page.
		 * If user is not logged in then registartion page will be open.
		 */
    else {
			if(isset($_SESSION['user_loggedin'])) {
				return $this->redirectToRoute('app_home');
			}
			else {
				//return $this->redirectToRoute('app_login');
				return $this->render('register/index.html.twig', []);
			}
    }
	}

	/**
	 * testInput
	 *
	 * @param  mixed $data
	 * @return $data
	 */
	public function testInput($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
}
