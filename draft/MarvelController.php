<?php

namespace App\Controller;

use App\Entity\LightHero;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("api/marvel")
 */
class MarvelController extends AbstractController
{
	private string $baseUrl = "http://gateway.marvel.com/v1/public/characters";

	private function getHashUrl(): string
	{
		//Should be stored in secure place
		$ts="1";
		$publickey = "5f9fafa4c65f4c31bc15b9301203835f";
		$privateKey = "3e518ba29dc0b6b82a4875cc0a72ed2171aa00d9";

		$toHash = $ts . $privateKey . $publickey;
		$hash = md5($toHash);

		$hashUrl = "?ts=" . $ts . "&apikey=" . $publickey . "&hash=" . $hash;

		return $hashUrl;

	}

	private function request($url, $method)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => "",
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"cache-control: no-cache"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		$result['response'] = $response;
		$result['err'] = $err;

		curl_close($curl);

		return $result;
	}

	/**
	 * [getCharactersList description]
	 * @Route("/getCharactersList", methods={"GET"})
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function getCharactersList(Request $request): Response
	{
		// $routeUrl = "characters";

		$offset = $request->query->get('offset');
		$offset = $offset == null ? 100 : $offset;
		$limit = $request->query->get('limit');
		$limit = $limit == null ? 20 : $limit;

		$url = $this->baseUrl . $this->getHashUrl();
		$url .= "&offset=".$offset;
		$url .= "&limit=".$limit;

		$marvelResponse = $this->request($url, "GET");

		$response = $marvelResponse['response'];
		$err = $marvelResponse['err'];


		if ($err)
			return new JsonResponse(["status" => "Internal error."], 400);
		else 
		{
			//We select only the required fields for the returned reponse
	  		$json = json_decode($response);

	  		//limit and total fields are required for pagination of heroes
	  		$result['total'] = $json->data->total;
	  		$result['limit'] = $limit;
	  		$result['offset'] = $offset;

	  		$result['heroes'] = [];
			$heroes = $json->data->results;
			foreach ($heroes as $hero) {
				$lightHero = new LightHero();
				$lightHero->setId($hero->id)
				      	  ->setName($hero->name)
				      	  ->setDescription($hero->description)
				      	  ->setPath($hero->thumbnail->path)
				      	  ->setExtension($hero->thumbnail->extension);

				$result['heroes'][] = $lightHero->get_object_as_array();
			}
  			return new Response(json_encode($result));
		}
	}


	// /**
	//  * [getCharactersList description]
	//  * @Route("/getCharactersList", methods={"GET"})
	//  * @param  Request $request [description]
	//  * @return [type]           [description]
	//  */
	// public function getCharactersList(Request $request): Response
	// {
	// 	$routeUrl = "characters";

	// 	$offset = $request->query->get('offset');
	// 	$offset = $offset == null ? 100 : $offset;
	// 	$limit = $request->query->get('limit');
	// 	$limit = $limit == null ? 20 : $limit;

	// 	$url = $this->baseUrl . $routeUrl . "?ts=1&apikey=5f9fafa4c65f4c31bc15b9301203835f&hash=c2376eedba988967c6a18a5f9bc4d41a";
	// 	$url .= "&offset=".$offset;
	// 	$url .= "&limit=".$limit;

	// 	$curl = curl_init();

	// 	curl_setopt_array($curl, array(
	// 	  CURLOPT_URL => $url,
	// 	  CURLOPT_RETURNTRANSFER => true,
	// 	  CURLOPT_ENCODING => "",
	// 	  CURLOPT_MAXREDIRS => 10,
	// 	  CURLOPT_TIMEOUT => 30,
	// 	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	// 	  CURLOPT_CUSTOMREQUEST => "GET",
	// 	  CURLOPT_POSTFIELDS => "",
	// 	  CURLOPT_HTTPHEADER => array(
	// 	    "Content-Type: application/json",
	// 	    "cache-control: no-cache"
	// 	  ),
	// 	));

	// 	$response = curl_exec($curl);
	// 	$err = curl_error($curl);

	// 	curl_close($curl);

	// 	if ($err) {
	// 		echo "cURL Error #:" . $err;
	// 	} else {
	// 		//We select only the required fields for the returned reponse
	//   		$json = json_decode($response);

	//   		//limit and total fields are required for pagination of heroes
	//   		$result['total'] = $json->data->total;
	//   		$result['limit'] = $json->data->limit;
	//   		$result['offset'] = $json->data->offset;

	//   		$result['heroes'] = [];
	// 		$heroes = $json->data->results;
	// 		foreach ($heroes as $hero) {
	// 			$lightHero = new LightHero();
	// 			$lightHero->setId($hero->id)
	// 			      	  ->setName($hero->name)
	// 			      	  ->setDescription($hero->description)
	// 			      	  ->setPath($hero->thumbnail->path)
	// 			      	  ->setExtension($hero->thumbnail->extension);

	// 			$result['heroes'][] = $lightHero->get_object_as_array();
	// 		}
 //  			return new Response(json_encode($result));
	// 	}
	// }


	/**
	 * @Route("/getThreeFirstComicsByCharacterId", methods={"GET"})
	 */
	public function getThreeFirstComicsByCharacterId(Request $request): Response
	{
		if ( ($heroId = $request->query->get('id') ) === null)
			return new JsonResponse(["status" => "The character id is missing."], 400);
		else
		{
			// $routeUrl = "characters";

			$url = $this->baseUrl . "/" . $heroId . "/comics" .$this->getHashUrl();
			$url .= "&format=comic";
			$url .= "&formatType=comic";
			$url .= "&orderBy=focDate&limit=3";

			$marvelResponse = $this->request($url, "GET");

			$response = $marvelResponse['response'];
			$err = $marvelResponse['err'];


			if ($err)
				return new JsonResponse(["status" => "Internal error."], 400);
			else
		  		return new Response($response);
		}
		else
		{
			return new JsonResponse(["nope" => "bug"]);
		}
	}

	// /**
	//  * @Route("/getThreeFirstComicsByCharacterId", methods={"GET"})
	//  */
	// public function getThreeFirstComicsByCharacterId(Request $request): Response
	// {
	// 	if ( ($heroId = $request->query->get('id') ) !== null)
	// 	{
	// 		$curl = curl_init();

	// 		curl_setopt_array($curl, array(
	// 		  CURLOPT_PORT => "443",
	// 		  CURLOPT_URL => "https://gateway.marvel.com:443/v1/public/characters/".$heroId."/comics?format=comic&formatType=comic&orderBy=focDate&limit=3&ts=1&apikey=5f9fafa4c65f4c31bc15b9301203835f&hash=c2376eedba988967c6a18a5f9bc4d41a",
	// 		  CURLOPT_RETURNTRANSFER => true,
	// 		  CURLOPT_ENCODING => "",
	// 		  CURLOPT_MAXREDIRS => 10,
	// 		  CURLOPT_TIMEOUT => 30,
	// 		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	// 		  CURLOPT_CUSTOMREQUEST => "GET",
	// 		  CURLOPT_POSTFIELDS => "",
	// 		  CURLOPT_HTTPHEADER => array(
	// 		    "Content-Type: application/json",
	// 		    // "Postman-Token: 7a5f1dc6-5df7-4ea2-8da8-c5ac66cbe7d7",
	// 		    "cache-control: no-cache"
	// 		  ),
	// 		));

	// 		$response = curl_exec($curl);
	// 		$err = curl_error($curl);

	// 		curl_close($curl);

	// 		if ($err) {
	// 		  echo "cURL Error #:" . $err;
	// 		} else {
	// 		  	//echo $response;
	// 		  	//SELECT ONLY IMPORTANT FIELDS FOR JS RETURN
	// 	  		return new Response($response);
	// 		}
	// 	}
	// 	else
	// 	{
	// 		return new JsonResponse(["nope" => "bug"]);
	// 	}
	// }


	/**
	 * @Route("/getCharacterById", methods={"GET"})
	 */
	public function getCharacterById(Request $request)
	{
		if ( ($heroId = $request->query->get('id') ) === null)
			return new JsonResponse(["status" => "The character id is missing."], 400);
		else
		{
			$url = $this->baseUrl . "/" . $heroId . $this->getHashUrl();

			$marvelResponse = $this->request($url, "GET");

			$response = $marvelResponse['response'];
			$err = $marvelResponse['err'];

	  		$resp = json_decode($response);
		

			if ($err) {
			  	return new JsonResponse(["status" => "Internal error."], 400);
			} else {
				$hero = $resp->data->results[0];
			  	$lightHero = new LightHero();
				$lightHero->setId($hero->id)
				      	  ->setName($hero->name)
				      	  ->setDescription($hero->description)
				      	  ->setPath($hero->thumbnail->path)
				      	  ->setExtension($hero->thumbnail->extension);

				$lightHero = $lightHero->get_object_as_array();
		  		return new Response(json_encode($lightHero));
			}
		}
	}



	
	// /**
 //     * @Route("/GetById", name="getheroById")
 //     */
 //    public function GetById()
 //    {
	// 	return new JsonResponse([
 //            [
 //                'title' => 'The Princess Brides',
 //                'count' => 0
 //            ]
 //        ]);
 //    }




	//*********************************************************************
	//
	
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
    }
}
