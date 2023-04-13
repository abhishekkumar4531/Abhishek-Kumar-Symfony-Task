<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;

class HomeController extends AbstractController
{
  #[Route('/', name: 'app_home')]
  public function index(Request $request): Response {
    $session = $request->getSession();
    if(($session->get('user_loggedin'))) {
      return $this->render('home/index.html.twig', []);
    }
    else {
      //session_destroy();
      $session->invalidate();
      return $this->redirectToRoute('app_login');
    }
  }
}
