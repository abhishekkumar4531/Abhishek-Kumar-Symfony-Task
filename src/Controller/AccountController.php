<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AccountController extends AbstractController
{
  #[Route('/account', name: 'app_account')]
  public function index(Request $request): Response
  {
    $session = $request->getSession();
    if($session->get('user_loggedin')) {
      return $this->render('account/index.html.twig', []);
    }
    else {
      $session->invalidate();
      return $this->redirectToRoute('app_login');
    }
  }
}
