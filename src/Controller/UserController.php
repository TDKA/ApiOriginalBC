<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 *
 * @Route("/api/admin")
 */
class UserController extends AbstractController
{

    ///////////// ADMIN REGISTER USER ////////////////////  OK
    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher, SerializerInterface $serializer): Response
    {
        $user = $this->getUser();

        if (in_array("ROLE_ADMIN", $user->getRoles(), true)) {
            $data = $request->getContent();

            $user = $serializer->deserialize($data, User::class, 'json');

            if ($user->getPassword() === $user->getPasswordConfirm()) {

                $date = new \Datetime();
                $user->setCreatedAt($date);

                $hashedPassword = $hasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);
                $user->setRoles(["ROLE_USER"]);

                $manager->persist($user);
                $manager->flush();

                return $this->json(["message" => "Parfait! Enregistrement reussi"], 200);
            } else {
                return $this->json(["message" => "Desole pas de permission !"], 200);
            }
        } else {
            return $this->json(["message" => "Desole pas de permission !"], 200);
        }
    }



    ///////////// SHOW ONE USER ///////////////// OK

    // FOR ADMIN
    /**
     *
     * @Route("/show/user/{id}", name="show_user_by_id", methods={"GET"})
     *
     */

    public function show(User $user): Response
    {

        $isAdmin = in_array("ROLE_ADMIN", $user->getRoles());

        $data = ["message" => "Pas de permission !"];

        //Admin + User
        if (
            $user->getUserIdentifier() || $isAdmin
        ) {
            $data = ["message" => $user];
            return $this->json(
                $data,
                200,
                [],
                [
                    "groups" => [
                        "user"
                    ]
                ]
            );
        } else {
            //No permission
            return $this->json(
                $data,
                200
            );
        }
    }

    //For user pro
    /**
     *
     * @Route("/user/show", name="show_user", methods={"GET"})
     * 
     */
    public function showUserPro(User $user = null, UserInterface $currentUser): Response
    {
        if (!$user) {
            $user = $currentUser;
        }


        $isAdmin = in_array("ROLE_ADMIN", $currentUser->getRoles());
        $data = ["user_index" => "SOO SORRY, YOU DON'T HAVE PERMISSION FOR THAT"];

        //A USER HAS ACCESS TO THEIR OWN DATA, AS WEL AS AN ADMIN.
        if ($currentUser->getUserIdentifier() || $isAdmin) {
            $data = ["user_index" => $user];

            //if varified
            return $this->json(
                $data,
                200,
                [],
                [
                    "groups" => [
                        "user"
                    ]
                ]
            );
        } else {
            //if no permissions
            return $this->json(
                $data,
                200
            );
        }
    }


    /////////////////////// FIND ALL USERS ///////////////////// OK
    /**
     *
     * @Route("/findAll", name="find_all_users", methods={"GET"})
     *
     */
    public function findAllUsers(UserRepository $repo): Response
    {
        if (!$this->getUser()) {
            return $this->json(["message" => "Pas de permission !"], 200, []);
        }
        $user = $this->getUser();
        $isAdmin = in_array("ROLE_ADMIN", $user->getRoles(), true);
        if ($isAdmin) {
            $users = $repo->findAll();
            return $this->json($users, 200, [], ["groups" => ["user"]]);
        }
        return $this->json(["message" => "Pas de permission"], 200, []);
    }




    //////////////// EDIT USER ///////////// OKK

    /**
     * 
     * @Route("/edit/{id}", name="edit_user",methods={"PUT"} )
     */
    public function edit(User $user, Request $req, SerializerInterface $serializer, EntityManagerInterface $emi, UserInterface $currentUser, UserPasswordHasherInterface $hasher): Response
    {
        $isAdmin = in_array("ROLE_ADMIN", $currentUser->getRoles());
        //Admin + User

        if ($currentUser->getUserIdentifier() == $user->getUserIdentifier() || $isAdmin) {
            $jsonUser = $req->getContent();
            $userObj = $serializer->deserialize($jsonUser, User::class, 'json');

            $hashedPassword = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $user->setUsername($userObj->getUsername());
            $user->setEmail($userObj->getEmail());
            $user->setFirstname($userObj->getFirstname());
            $user->setLastname($userObj->getLastname());
            $user->setNbPhone($userObj->getNbPhone());

            //test
            $user->setNbSiret($userObj->getNbSiret());


            //only an 
            if (!$isAdmin) {
                //An added layer to protect against json hack to change roles manually.
                $user->setRoles(["ROLE_USER"]);
            }

            $emi->persist($user);
            $emi->flush();

            $data = ["message" => "Reussi"];

            return $this->json(
                $data,
                200,
                [],
                [
                    "groups" => [
                        "user"
                    ]
                ]
            );
        } else {
            $data = ["message" => "Pas de permission"];
            return $this->json(
                $data,
                200
            );
        }
    }


    ///////////// DELETE USER /////////////////   OK

    /**
     * @Route("/delete/{id}", name="delete_user", methods={"DELETE"})
     * 
     */
    public function delete(
        UserRepository $repo,
        EntityManagerInterface $manager,
        $id
    ): Response {
        if (!$this->getUser()) {
            return $this->json(["message" => "Pas de permission !"], 200, []);
        }
        $user = $this->getUser();
        $isAdmin = in_array("ROLE_ADMIN", $user->getRoles(), true);
        if ($isAdmin) {

            $deletedUser = $repo->findBy(["id" => $id]);
            $manager->remove($deletedUser[0]);
            $manager->flush();
            return $this->json(['message' => "<Suppresion reussi!"], 200, []);
        }

        return $this->json(["message" => "Pas de permission !"], 200, []);
    }



    /**
     *
     * @Route("/login",name="login",methods={"POST"})
     */
    public function login(): Response
    {
        if (!$this->getUser()) {
            return $this->json(["message" => "Identifiants Incorrect !"], 200, []);
        }

        return $this->json(["message" => "bienvenue"], 200, ["groups" => "user"]);
    }
    /**
     *
     * @Route("/logout",name="logout",methods={"POST"})
     */
    public function logout(): Response
    {
        if (!$this->getUser()) {
            return $this->json(["message" => "Un probleme est survenue ! !"], 200, []);
        }
        return $this->json(['message' => 'Vous etes deconécté !'], 200, []);
    }


    /**
     * @Route("/currentUser",name="current_user",methods={"GET"})
     */
    public function currentUser()
    {
        if (!$this->getUser()) {
            return $this->json(["message" => "Pas de permission"], 200, []);
        }
        $user = $this->getUser();
        return $this->json($user, 200, [], ["groups" => ["user"]]);
    }







    // /**
    //  * @Route("/registerLocal", name="register")
    //  */
    // public function registerLocal(Request $req, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    // {
    //     $user = new User();
    //     $form = $this->createForm(RegisterType::class, $user);

    //     $form->handleRequest($req);

    //     if ($form->isSubmitted() && $form->isValid()) {

    //         $hashedPassword = $hasher->hashPassword($user, $user->getPassword());
    //         $date = new \Datetime();
    //         $user->setCreatedAt($date);
    //         $user->setPassword($hashedPassword);

    //         $manager->persist($user);
    //         $manager->flush();

    //         return $this->redirectToRoute('login');
    //     }

    //     return $this->render('user/register.html.twig', [
    //         'form' => $form->createView()
    //     ]);
    // }
}
