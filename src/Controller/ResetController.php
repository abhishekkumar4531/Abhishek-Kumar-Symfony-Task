<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\SendOtp;
use App\Entity\Users;

/**
 * ResetController
 * This is reponsible for reseting the user's password
 */
class ResetController extends AbstractController
{

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
   * userData - It will store the user's personal data.
   *
   * @var array
   */
  private $userData = Array();

  /**
   * __construct - It will initialize the class and store objects in $entityManager,
   * and $userRepo.
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

  #[Route('/reset', name: 'app_reset')]
  /**
   * It is managing three cases
   * First case : It will fetch email from user and generate otp and send to the
   * user registered email.
   * Second case : It will fetch new password from form and update to the database.
   * Third case : Apart from above two cases it will check if user logged in or not,
   * if logged in then redirect to the home page otherwise redirect to login page.
   *
   *   @param  mixed $request
   *     This Request object is to handles the session.
   *
   *   @return Response
   *     If user submit generate otp page then render to reset page.
   *     If user submit reset page and if password reset done then redirect to
   *     login page otherwise render to reset page.
   *     If user direct try to access this controller then if logged in then
   *     redirect to home page otherwise redirect to login page.
   */
  public function index(Request $request): Response {
    //f user submit the reset form then this statement will execute
    if(isset($_POST['sendOtp'])) {
      $userEmail = $_POST['user_email'];
      $fetchCredentials = $this->userRepo->findOneBy([ 'userEmail' => $userEmail ]);
      if($fetchCredentials) {
        $send = new SendOtp();
        $getOtp = $send->getEmail($userEmail);
        $session = $request->getSession();
        $session->set('get_otp', $getOtp);
        $session->set('user_email', $userEmail);
        $firstName = $fetchCredentials->getUserFirstName();
        $lastName = $fetchCredentials->getUserLastName();
        return $this->render('reset/reset.html.twig', [
          'userName' => $firstName . " " . $lastName
        ]);
      }
      else {
        return $this->render('reset/index.html.twig', [
          'userEmail' => $userEmail,
          'invalidEmail' => "Enter valid email!!!"
        ]);
      }
    }
    else if(isset($_POST['resetPwd'])) {
      $this->userData['userName'] = $_POST['userName'];
      $this->userData['enteredOtp'] = $_POST['enterOtp'];
      $this->userData['newPassword'] = $_POST['newPassword'];
      $this->userData['cnfPassword'] = $_POST['cnfPassword'];
      if($this->userData['newPassword'] === $this->userData['cnfPassword']) {
        $session = $request->getSession();
        $getOtp = $session->get('get_otp');
        $userEmail = $session->get('user_email');
        //$session->invalidate();
        if(number_format($this->userData['enteredOtp']) === number_format($getOtp)) {
          $fetchCredentials = $this->userRepo->findOneBy([ 'userEmail' => $userEmail ]);
          if($fetchCredentials) {
            $fetchCredentials->setUserPassword($this->userData['newPassword']);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_login');
          }
        }
        else {
          return $this->render('reset/reset.html.twig', [
            'userData' => $this->userData,
            'userName' => $this->userData['userName'],
            'invalidOtp' => 'Please enter valid OTP'
          ]);
        }
      }
      else {
        return $this->render('reset/reset.html.twig', [
          'userData' => $this->userData,
          'userName' => $this->userData['userName'],
          'invalidPassword' => 'Please enter same password'
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
        return $this->render('reset/index.html.twig');
      }
    }
  }

}
