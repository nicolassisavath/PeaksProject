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

	private $baseUrl = "http://gateway.marvel.com/v1/public/characters";

	/**
	 * return a character according to the id sent in the request
	 * @Route("/getCharacterById", methods={"GET"})
	 */
	public function getCharacterById(Request $request)
	{
		if ( ($heroId = $request->query->get('id') ) === null)
			return new JsonResponse(["status" => "The character id is missing."], 400);
		else
		{
			//Construct the url
			$url = $this->baseUrl . "/" . $heroId . $this->getHashUrl();

			//Call the marvel api
			$marvelResponse = $this->request($url, "GET");

			$response = $marvelResponse['response'];
			$err = $marvelResponse['err'];

	  		$resp = json_decode($response);

			if ($err)
			  	return new JsonResponse(["status" => "Internal error."], 400);
			else 
			{
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

	/**
	 * return the list of characters
	 * @Route("/getCharactersList", methods={"GET"})
	 */
	public function getCharactersList(Request $request): Response
	{
		//get the offset and the limit in the request or set defaul tvalue if null
		$offset = $request->query->get('offset');
		$offset = $offset == null ? 100 : $offset;
		$limit = $request->query->get('limit');
		$limit = $limit == null ? 20 : $limit;

		//Construct the url
		$url = $this->baseUrl . $this->getHashUrl();
		$url .= "&offset=".$offset;
		$url .= "&limit=".$limit;

		//Call the marvel api
		$marvelResponse = $this->request($url, "GET");

		$response = $marvelResponse['response'];
		$err = $marvelResponse['err'];


		if ($err)
			return new JsonResponse(["status" => "Internal error."], 400);
		else 
		{
			//We select only the required fields for the returned reponse
	  		$resp = json_decode($response);

	  		//limit and total fields are required for pagination of heroes
	  		$result['total'] = $resp->data->total;
	  		$result['limit'] = $resp->data->limit;
	  		$result['offset'] = $resp->data->offset;

	  		$result['heroes'] = [];
			$heroes = $resp->data->results;
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

	/**
	 * return the three first comics appearances of a character
	 * @Route("/getThreeFirstComicsByCharacterId", methods={"GET"})
	 */
	public function getThreeFirstComicsByCharacterId(Request $request): Response
	{
		if ( ($heroId = $request->query->get('id') ) === null)
			return new JsonResponse(["status" => "The character id is missing."], 400);
		else
		{
			//Construct the url
			$url = $this->baseUrl . "/" . $heroId . "/comics" .$this->getHashUrl();
			$url .= "&format=comic";
			$url .= "&formatType=comic";
			$url .= "&orderBy=focDate";
			$url .= "&limit=3";

			//Call the marvel api
			$marvelResponse = $this->request($url, "GET");

			$response = $marvelResponse['response'];
			$err = $marvelResponse['err'];

			if ($err)
				return new JsonResponse(["status" => "Internal error."], 400);
			else
		  		return new Response($response);
		}
	}

	/*
	 * return the hash of ts/privateKey/publicKey
	 */
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

	/**
	 * Make the request to the marvel api
	 * return the response and the error in result
	 */
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
}
