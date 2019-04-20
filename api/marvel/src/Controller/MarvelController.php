<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
                'title' => 'The Princess Brides',
                'count' => 0
            ]
        ]);
    }

     /**
     * @Route("/test", name="test")
     */
    public function test()
    {
    	$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "http://gateway.marvel.com/v1/public/characters?ts=1&apikey=5f9fafa4c65f4c31bc15b9301203835f&hash=c2376eedba988967c6a18a5f9bc4d41a&offset=100&limit=20",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_POSTFIELDS => "",
		  CURLOPT_HTTPHEADER => array(
		    "Content-Type: application/json",
		    //"Postman-Token: b3e60971-24b9-4265-9f1b-6e1e7ca2d728",
		    "cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  return new Response($response);
		  //return new JsonResponse([$response]);
		}

		// return new JsonResponse([
  //           [
  //               'title' => 'The Princess plouf',
  //               'count' => 0
  //           ]
  //       ]);
    }
}
