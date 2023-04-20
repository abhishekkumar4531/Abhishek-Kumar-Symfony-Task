<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * LogoutController
 * It is responsible for user logout.
 * It will destroy the session.
 */
class LogoutController extends AbstractController
{
  #[Route('/logout', name: 'app_logout')]
  /**
   * It will destroy the session and redirect to login page.
   *
   *   @param  mixed $request
   *     This Request object is to handles the session.
   *
   *   @return Response
   *     After destroy the session redirect to login page.
   */
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
