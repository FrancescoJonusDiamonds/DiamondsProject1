<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\UserController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="user_index")
     */
    public function index(): Response
    {
       $userController = new UserController();
	   
	   return $this->render('user/list.html.twig', ['data' => $userController.getUserAction(),]);
	   
    }

    
}

?>