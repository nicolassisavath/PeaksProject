<?php

namespace App\Tests;

// use PHPUnit\Framework\TestCase;
use App\Repository\UserRepository;
use App\Repository\HeroRepository;


class UserControllerTest extends CoreTestController
{

    /**
     * @dataProvider UserLoginDataProvider
     */
	public function testLogin($user)
	{
		$route = "user/login";

		$data = [
			"login" => $user['login'],
			"password" => $user['password']
		];

		$response = $this->curlRequest("POST", $route, $data);

		$this->assertEquals($user['expected_code'], $response['http_code']);
	}

	/**
	 * Data provider for testLogin
	 * @return array
	 */
	public function UserLoginDataProvider()
	{
		$userNothing['login'] = '';
		$userNothing['password'] = '';
		$userNothing['expected_code'] = 400;

		$userNologin['login'] = '';
		$userNologin['password'] = 'peaks';
		$userNologin['expected_code'] = 400;

		$userNoPwd['login'] = 'peaks';
		$userNoPwd['password'] = '';
		$userNoPwd['expected_code'] = 400;

		$userPeaks['login'] = 'peaks';
		$userPeaks['password'] = 'peaks';
		$userPeaks['expected_code'] = 200;

		return array(
			[$userNothing],
			[$userNologin],
			[$userNoPwd],
			[$userPeaks]
		);
	}

	/**
     * @dataProvider UserCreateDataProvider
     */
	public function testCreate($user)
	{
		$route = "user/create";

		$data = [
			"login" => $user['login'],
			"password" => $user['password']
		];

		$response = $this->curlRequest("POST", $route, $data);

		$this->assertEquals($user['expected_code'], $response['http_code']);
	}

	/**
	 * Data provider for testCreate
	 * @return array
	 */
	public function UserCreateDataProvider()
	{
		$userNothing['login'] = '';
		$userNothing['password'] = '';
		$userNothing['expected_code'] = 400;

		$userNologin['login'] = '';
		$userNologin['password'] = 'peaks';
		$userNologin['expected_code'] = 400;

		$userNoPwd['login'] = 'peaks';
		$userNoPwd['password'] = '';
		$userNoPwd['expected_code'] = 400;

		$userPeaks['login'] = 'peaks';
		$userPeaks['password'] = 'peaks';
		$userPeaks['expected_code'] = 400; //allready present in db

		$userSymfo['login'] = 'symfo';
		$userSymfo['password'] = 'symfo';
		$userSymfo['expected_code'] = 200; 

		return array(
			[$userNothing],
			[$userNologin],
			[$userNoPwd],
			[$userPeaks],
			[$userSymfo]
		);
	}


	/**
     * @dataProvider UserAddToFavouritesDataProvider
     */
	public function testAddToFavourites($data)
	{
		$route = "user/addToFavourites";

		$postedData = [
			"userId" => $data['userId'],
			"heroId" => $data['heroId']
		];

		$response = $this->curlRequest("POST", $route, $postedData);

		$this->assertEquals($data['expected_code'], $response['http_code']);
	}

	/**
	 * Data provider for testAddToFavourites
	 * @return array
	 */
	public function UserAddToFavouritesDataProvider()
	{
		$dataNothing['userId'] = '';
		$dataNothing['heroId'] = '';
		$dataNothing['expected_code'] = 400;

		$dataNoUserId['userId'] = '';
		$dataNoUserId['heroId'] = '12345';
		$dataNoUserId['expected_code'] = 400;

		$dataNoHeroId['userId'] = '1';
		$dataNoHeroId['heroId'] = '';
		$dataNoHeroId['expected_code'] = 400;

		$user1['userId'] = '2';
		$user1['heroId'] = '1010763';
		$user1['expected_code'] = 200;

		$user2['userId'] = '2';
		$user2['heroId'] = '1010743';
		$user2['expected_code'] = 200;

		$user3['userId'] = '2';
		$user3['heroId'] = '1009610';
		$user3['expected_code'] = 200;

		$user4['userId'] = '2';
		$user4['heroId'] = '1009189';
		$user4['expected_code'] = 200;

		$user5['userId'] = '2';
		$user5['heroId'] = '1009282';
		$user5['expected_code'] = 200;

		$user6['userId'] = '2';
		$user6['heroId'] = '1009652';
		$user6['expected_code'] = 400; // 5 Favourites achieved


		return array(
			[$dataNothing],
			[$dataNoUserId],
			[$dataNoHeroId],
			[$user1],
			[$user2],
			[$user3],
			[$user4],
			[$user5],
			[$user6]
		);
	}

	/**
     * @dataProvider UserRemoveFromFavouritesDataProvider
     */
	public function testRemoveFromFavourites($data)
	{
		$route = "user/removeFromFavourites";

		$postedData = [
			"userId" => $data['userId'],
			"heroId" => $data['heroId']
		];

		$response = $this->curlRequest("POST", $route, $postedData);

		$this->assertEquals($data['expected_code'], $response['http_code']);
	}

	/**
	 * Data provider for testRemoveFromFavourites
	 * @return array
	 */
	public function UserRemoveFromFavouritesDataProvider()
	{
		$dataNothing['userId'] = '';
		$dataNothing['heroId'] = '';
		$dataNothing['expected_code'] = 400;

		$dataNoUserId['userId'] = '';
		$dataNoUserId['heroId'] = '12345';
		$dataNoUserId['expected_code'] = 400;

		$dataNoHeroId['userId'] = '1';
		$dataNoHeroId['heroId'] = '';
		$dataNoHeroId['expected_code'] = 400;

		$hero1['userId'] = '2';
		$hero1['heroId'] = '1010763';
		$hero1['expected_code'] = 200;

		$hero2['userId'] = '2';
		$hero2['heroId'] = '1010743';
		$hero2['expected_code'] = 200;

		$hero3['userId'] = '2';
		$hero3['heroId'] = '1009610';
		$hero3['expected_code'] = 200;

		$hero4['userId'] = '2';
		$hero4['heroId'] = '1009189';
		$hero4['expected_code'] = 200;

		$hero5['userId'] = '2';
		$hero5['heroId'] = '1009282';
		$hero5['expected_code'] = 200;

		$hero6['userId'] = '2';
		$hero6['heroId'] = '12345';
		$hero6['expected_code'] = 400; // hero marvel id does not exist


		return array(
			[$dataNothing],
			[$dataNoUserId],
			[$dataNoHeroId],
			[$hero1],
			[$hero2],
			[$hero3],
			[$hero4],
			[$hero5],
			[$hero6]
		);
	}
}