<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class LogoutController extends AbstractController
{
  #[Route('/logout', name: 'app_logout')]
  public function index(Request $request): Response
  {
    session_start();
    $session = $request->getSession();
    $session->invalidate();
    session_unset();
    session_destroy();
    return $this->redirectToRoute('app_login');
  }
}
