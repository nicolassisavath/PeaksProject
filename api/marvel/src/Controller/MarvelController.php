<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class MarvelController extends AbstractController
{
    /**
     * @Route("/marvel", name="marvel")
     */
    public function index()
    {
        return $this->render('marvel/index.html.twig', [
            'controller_name' => 'MarvelController',
        ]);
    }


     /**
     * @Route("/mouais", name="mouais")
     */
    public function mouais()
    {
		return new JsonResponse([
            [
                'title' => 'The Princess Bride',
                'count' => 0
            ]
        ]);
    }
}
