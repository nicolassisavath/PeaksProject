<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Hero;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\HeroRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("api/user")
 */
class UserController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserRepository
     */
    private $heroRepository;

    public function __construct(UserRepository $repository, HeroRepository $heroRepository)
    {
        $this->userRepository = $repository;
        $this->heroRepository = $heroRepository;
    }

    /**
     * API : SignUp
     * @Route("/create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $post = json_decode(
            $request->getContent(),
            true
        ); 

        if( is_null($login = $post['login']) || is_null($pwd = $post['password']) )
            return new JsonResponse(["status" => "Some data are missing."], 400);
        else
        {
            //Control if a user already has the same login
            if ( !is_null( $dbUser = $this->userRepository->findOneBy(["login" => $login]) ))
                return new JsonResponse(["status" => "This login already exists."], 400);
            else
            {
                //init the new user
                $user = new User();
                $user->setLogin($login)
                     ->setPassword(password_hash($pwd, PASSWORD_DEFAULT))
                     ->setFavoritesNumber(0);

                //persist the user in db
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return new JsonResponse(["status" => "The account was ceated successfully"]);
            }
        }
    }

    /**
     * API : Login
     * @Route("/login", methods={"POST"})
     */
    public function login(Request $request): Response
    {
        $post = json_decode(
            $request->getContent(),
            true
        );    

        if ( is_null($login = $post['login']) || is_null($pwd = $post['password']))
            return new JsonResponse(["status" => "Some data are missing."], 400);
        else
        {
            $dbUser = $this->userRepository->findOneBy(["login" => $login]);

            if($dbUser !== null && password_verify($pwd, $dbUser->getPassword()))
            {
                //If the user exists, return his favourites characters id in an array
                $fav = [];
                if (count($dbUser->getHeroes()) > 0)
                {
                    foreach ($dbUser->getHeroes() as $favorite) 
                    {
                        $fav[] = $favorite->getMarvelId();
                    }
                }
                return new JsonResponse( ["status" => "Connected.", "userId" => $dbUser->getId(), "favourites" => $fav] );
            }
            else
                return new JsonResponse(["status" => "Bad credentials."], 400);
        }
    }

    /**
     * API : Add a hero to user favourites
     * @Route("/addToFavourites", methods={"POST"})
     */
    public function addToFavourites(Request $request): Response
    {
        $post = json_decode(
            $request->getContent(),
            true
        );  

        if ( is_null($userId = $post['userId']) || is_null($heroId = $post['heroId']) )
                return new JsonResponse(["response" => "Bad request."], 400);
        else
        {
            $user = $this->userRepository->findOneBy(["id" => $userId]);
            if (is_null($user))
                return new JsonResponse(["response" => "User not found."], 400);

            if(count($user->getHeroes()) === 5)
                return new JsonResponse(["response" => "You achieved the max number of favourites."], 400);

            $dbHero = $this->heroRepository->findOneBy(["marvelId" => $heroId]);
            $em = $this->getDoctrine()->getManager();

            //if the hero is not in hero table yet, we save his id hero table
            if ( $dbHero === null )
            {
                $dbHero = new Hero($heroId);
                $em->persist($dbHero);
                $em->flush();
            }

            //join the hero to the user and persist him
            $user->addHero($dbHero);
            $user->incrementFavoritesNumber();
            $em->persist($user);
            $em->flush();

            return new JsonResponse(["response" => "Favourite added", "addedFavouriteId" => $dbHero->getMarvelId()]);
        }            
    }

    /**
     * API : Remove a hero to user favourites
     * @Route("/removeFromFavourites", methods={"POST"})
     */
    public function removeFromFavourites(Request $request): Response
    {
        $post = json_decode(
            $request->getContent(),
            true
        );  

        if ( is_null($userId = $post['userId']) || is_null($heroId = $post['heroId']) )
                return new JsonResponse(["response" => "Bad request."], 400);
        else
        {
            $user = $this->userRepository->findOneBy(["id" => $userId]);
            if (is_null($user))
                return new JsonResponse(["response" => "User not found."], 400);

            //verify if the hero is in hero table
            $dbHero = $this->heroRepository->findOneBy(["marvelId" => $heroId]);
            //Should not happen, except if db is corrupted
            if ( is_null($dbHero) )
                return new JsonResponse(["response" => "Hero not found."], 400);
            else
            {
                //Remove the join between hero and user and persit him
                $user->removeHero($dbHero);
                $user->decrementFavoritesNumber();
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }

            return new JsonResponse(["response" => "Favourite added",  "removedFavouriteId" => $dbHero->getMarvelId()]);
        }            
    }
}
