<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
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
    private $repository;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
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

        if(($login = $post['login']) !== null && ($pwd = $post['password']) != null)
        {
            if ( ($dbUser = $this->repository->findOneBy(["login" => $login])) !== null )
                return new JsonResponse(["status" => "This login already exists."], 400);
            else
                $user = new User();
                $user->setLogin($login)
                     ->setPassword(password_hash($pwd, PASSWORD_DEFAULT))
                     ->setFavoritesNumber(0);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return new JsonResponse(["status" => "The account was ceated successfully"]);
            //retourné le client aussi
        }
        else
            return new JsonResponse(["status" => "Bad credentials."]);
    }

    /**
     * API : Login
     * @Route("/login", methods={"POST"})
     */
    public function login(Request $request, UserRepository $userRepository): Response
    {
        $post = json_decode(
            $request->getContent(),
            true
        );    

        if ( ($login = $post['login']) === null|| ($pwd = $post['password']) == null)
            return new JsonResponse(["status" => "Some data are missing."], 400);
        else
        {
            $dbUser = $this->repository->findOneBy(["login" => $login]);

            if($dbUser !== null && password_verify($pwd, $dbUser->getPassword()))
                return new JsonResponse(["status" => "Connected.", "userId" => $dbUser->getId()]);
            else
                return new JsonResponse(["status" => "Bad credentials."], 400);
        }
    }

//     /**
//      * API : Login
//      * @Route("/compare", methods={"POST"})
//      */
//     public function gjfd(Request $request)
//     {
//         $response = new JsonResponse(["statut" => "données manquantes"]);
//         $response->setPublic();
//             // $response->headers->set('Content-Type', 'xml');
//             // $response->headers->set('Access-Control-Allow-Headers', 'origin, content-type, accept');
//     // $response->headers->set('Access-Control-Allow-Origin', '*');
//     // $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');
// // $response->headers[] = ['Access-Control-Allow-Headers'=> 'origin, content-type, accept'];
// // $response->headers[] = ['Access-Control-Allow-Origin', '*'];
// // $response->headers[] = ['Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS'];

// // var_dump($response);
//         return $response;
//         // return new JsonResponse(["statut" => "données manquantes"]);
//     }



    //***************************************************
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }


    // /**
    //  * @Route("/new", name="user_new", methods={"GET","POST"})
    //  */
    // public function new(Request $request): Response
    // {
    //     $user = new User();
    //     $data = json_decode(
    //         $request->getContent(),
    //         true
    //     );

    //     var_dump($request->query);
    //     var_dump($request->request);//do ot work with json data

    //     $info = $request->getContent();

    //     var_dump($info);
    //     var_dump($data);
    //     var_dump($data["login"]);

    //     var_dump($request);

    //     // $user->setLogin($request->request->get('login'));
    //     // // $clearPwd = $request->get('password');
    //     // // $user->setPassword(password_hash($clearPwd, PASSWORD_DEFAULT));
    //     // // $user->setFavoritesNumber(0);
    //     // $form = $this->createForm(UserType::class, $user);
    //     // $form->handleRequest($request);

    //     // // if ($form->isSubmitted() && $form->isValid()) {
    //     // //     $entityManager = $this->getDoctrine()->getManager();
    //     //     $entityManager->persist($user);
    //     //     $entityManager->flush();

    //     // //     return $this->redirectToRoute('user_index');
    //     // // }

    //     // return $this->render('user/new.html.twig', [
    //     //     'user' => $user,
    //     //     'request' => $request,
    //     //     'form' => $form->createView(),
    //     // ]);
    //     return new Response([["ok" => "yep"]]);
        
    // }

    // /**
    //  * @Route("/{id}", name="user_show", methods={"GET"})
    //  */
    // public function show(User $user): Response
    // {
    //     return $this->render('user/show.html.twig', [
    //         'user' => $user,
    //     ]);
    // }

    // /**
    //  * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
    //  */
    // public function edit(Request $request, User $user): Response
    // {
    //     $form = $this->createForm(UserType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $this->getDoctrine()->getManager()->flush();

    //         return $this->redirectToRoute('user_index', [
    //             'id' => $user->getId(),
    //         ]);
    //     }

    //     return $this->render('user/edit.html.twig', [
    //         'user' => $user,
    //         'form' => $form->createView(),
    //     ]);
    // }

    // /**
    //  * @Route("/{id}", name="user_delete", methods={"DELETE"})
    //  */
    // public function delete(Request $request, User $user): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->remove($user);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('user_index');
    // }

    /***************

    //     /**
    //  * @Route("/new", name="create_user", methods={"POST"})
    //  */
    // public function CreateNew(Request $request): Response
    // {
    //     $user = new User();
    //     $form = $this->createForm(UserType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($user);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('user_index');
    //     }

    //     return $this->render('user/new.html.twig', [
    //         'user' => $user,
    //         'form' => $form->createView(),
    //     ]);
    // }
}
