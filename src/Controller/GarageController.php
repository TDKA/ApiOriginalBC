<?php

namespace App\Controller;

use App\Entity\Garage;
use App\Entity\User;
use App\Repository\GarageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/api")
 * 
 */
class GarageController extends AbstractController
{


    /////// FIND ALLL GARAGES --- OK
    /**
     * @Route("/garage", name="garage", methods={"GET"})
     */
    public function index(GarageRepository $repo): Response
    {
        if (!$this->getUser()) {
            return $this->json(["Pas de permission", 200, []]);
        }
        $user = $this->getUser();
        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            $garages = $repo->findAll();





            return $this->json($garages, 200, [], [
                "groups" => ["garage"]
            ]);
        } //Dans le cas d'un ROLE_User on retourne uniuemnet les garages de l'utilisateur conecté
        else if (in_array("ROLE_USER", $user->getRoles()) && $user === $this->getUser()) {
            //findAll by user
            //sql => SELECT * FROM `garage` WHERE `user_id`=9
            $user = $this->getUser();
            $userId = $user->getId();
            $garages = $this->getDoctrine()->getRepository(Garage::class)->findBy(["user" => $userId]);
            return $this->json($garages, 200, [], [
                "groups" => ["garage"]
            ]);
        }
    }


    /////////// FIND ONE GARAGE --- OK
    /**
     * @Route("/show/garage/{id}", name="garageFindOne", methods={"GET"})
     */
    public function showOne(Garage $garage): Response
    {
        return $this->json($garage, 201, [], [
            "groups" => [
                "garage"
            ]
        ]);
    }


    ////////// ADD  GARAGE ---- OK

    /**
     * @Route("/garage/add", name="add_garage", methods={"POST"})
     * 
     */
    public function add(Request $req, SerializerInterface $serializer, EntityManagerInterface $manager): Response
    {

        if (!$this->getUser()) {
            return $this->json(["Désolé vous n avez pas acces a cette information !", 200, []]);
        }

        $user = $this->getUser();
        $garageJson = $req->getContent();

        $garage = $serializer->deserialize($garageJson, Garage::class, 'json');

        $date = new \Datetime();
        $garage->setCreatedAt($date);

        $garage->setUser($user);

        $manager->persist($garage);
        $manager->flush();

        $data = ["message" => "success"];
        return $this->json(
            $data,
            200,
            [],
            [
                "groups" => [
                    "garage"
                ]
            ]
        );
    }


    ////////// DELETE GARAGE ---- OK
    /**
     * @Route("/garage/delete/{id}", name="garageDelete", methods={"DELETE"})
     */
    public function delete(Garage $garage, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $userGarage = $garage->getUser();

        if ($userGarage == $user) {
            $manager->remove($garage);
            $manager->flush();
            $message = "Suppression reusi";
        } else {
            $message = "Ops, il y avait des problems avec la suppression !";
        }

        return $this->json($message, 201);
    }


    ///////////// EDIT GARAGE -------- OK
    /**
     * @Route("/garage/edit/{id}", name="editGarage", methods={"PUT"}, priority=2)
     */
    public function edit(Garage $garage, Request $request, EntityManagerInterface $manager, $id, SerializerInterface $serializer, UserInterface $currentUser, GarageRepository $repo): Response
    {
        $isAdmin = in_array("ROLE_ADMIN", $currentUser->getRoles());
        if ($garage->getUser()->getId() || $isAdmin) {
            $garage = $repo->findOneBy(["id" => $id]);
            $garageEdit = $request->getContent();
            $garageEdit = $serializer->deserialize($garageEdit, Garage::class, 'json');
            $garage->setName($garageEdit->getName());
            $garage->setAddresse($garageEdit->getAddresse());
            $garage->setNbPhone($garageEdit->getNbPhone());

            $date = new \Datetime();
            $garage->setCreatedAt($date);


            $manager->persist($garage);
            $manager->flush();
            return $this->json(["La modification a été réussi"], 200);
        }
        return $this->json(["message" => "Pas de permission !"], 200);
    }


    /**
     * 
     * @Route("/garage/findAllByUser", name="find_all_by_user", methods={"GET"})
     */
    public function findAllByUser(GarageRepository $repo)
    {

        if (!$this->getUser()) {
            return $this->json(['Pas de permission ! ', 200]);
        }

        $user = $this->getUser();
        if (in_array("ROLE_USER", $user->getRoles()) && $user === $this->getUser()) {

            $user = $this->getUser();
            $userId = $user->getId();
            $garages = $this->getDoctrine()->getRepository(Garage::class)->findBy(["user" => $userId]);
            return $this->json($garages, 200, [], [
                "groups" => ["garage"]
            ]);
        }
    }
}
