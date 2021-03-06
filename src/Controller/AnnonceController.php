<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Brand;
use App\Repository\UserRepository;
use App\Repository\BrandRepository;
use App\Repository\GarageRepository;
use App\Repository\ModeleRepository;
use App\Repository\AnnonceRepository;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

/**
 *
 * @Route("/api")
 */
class AnnonceController extends AbstractController
{
    //// FIND ALL ANNONCES ---- OK

    // FOR ADMIN & USER PRO
    /**
     * @Route("/annonce", name="annonce", methods={"GET"})
     */
    public function findAll(AnnonceRepository $repo): Response
    {
        if (!$this->getUser()) {
            return $this->json(["Pas de permission !", 200, []]);
        }
        $user = $this->getUser();
        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            $annonces = $repo->findAll();
            return $this->json($annonces, 200, [], [
                "groups" => ["annonce"]
            ]);
        } else if (in_array("ROLE_USER", $user->getRoles()) && $user === $this->getUser()) {
            //findAll by user
            //
            $user = $this->getUser();
            $userId = $user->getId();
            $annonces = $this->getDoctrine()->getRepository(Annonce::class)->findBy(["user" => $userId]);
            return $this->json($annonces, 200, [], [
                "groups" => ["annonce"],
                "message" => "founded "
            ]);
        } else {
            $annonces = $repo->findAll();
            return $this->json($annonces, 200, [], [
                "groups" => ["annonce"]
            ]);
        }
    }

    //// 
    // PUBLIC FINDALL() ---- OK
    /**
     * @Route("/annonce/public"), name="annonce_public", methods={"GET"}
     *
     */
    public function findAllPublic(AnnonceRepository $repo)
    {
        $annonces = $repo->findAll();
        return $this->json($annonces, 200, [], [
            "groups" => ["annonce"]
        ]);
    }

    /////// SHOW ONE ----- OK
    /**
     * @Route("/annonce/{id}", name="showAnnonce", methods={"GET"})
     */
    public function showOne(Annonce $annonce): Response
    {

        return $this->json($annonce, 200, [], ["groups" => "annonce"]);
    }

    /////// DELETE ANNONCE -- OK
    /**
     * @Route("/annonce/delete/{id}", name="annonceDelete", methods={"DELETE"})
     */
    public function delete(
        Annonce $annonce,
        EntityManagerInterface $manager
    ): Response {
        $manager->remove($annonce);
        $manager->flush();

        return $this->json("Delete was successfull", 200, [], ["groups" => "annonce"]);
    }

    ///// ADDDD 



    /**
     * @Route("/annonce/add/", name="add_annonce", methods={"POST"})
     */
    public function add(
        Request $req,
        SerializerInterface $serializer,
        EntityManagerInterface $emi,
        UserRepository $userRepo,
        GarageRepository $garageRepo,
        UserInterface $currentUser,
        BrandRepository $brandRepo,
        ModeleRepository $modelRepo
    ): Response {

        if (!$this->getUser()) {
            return $this->json(["Permission", 200, []]);
        }
        $isAdmin = in_array("ROLE_ADMIN", $this->getUser()->getRoles(), true);


        if ($isAdmin || $this->getUser()) {

            $annonceJson = $req->getContent();
            $annonce = $serializer->deserialize($annonceJson, Annonce::class, 'json');

            $brand = $brandRepo->findOneBy(["id" => $req->toArray('brand')]);
            $modele = $modelRepo->findOneBy(["id" => $req->toArray('modele')]);
            $garage = $garageRepo->findOneBy(["id" => $req->toArray('garage')]);

            // $user = $userRepo->findOneBy(["id" => $req->toArray('user')]);
            $user = $this->getUser();
            $annonce->setUser($user);

            $annonce->setBrand($brand);
            $annonce->setModele($modele);
            $annonce->setGarage($garage);
            $annonce->setCreatedAt(new \DateTime());

            $emi->persist($annonce);
            $emi->flush();
            return $this->json(["message" => "Vous avez modifi?? l'annonce !"], 200, []);
        }
        return $this->json(["message" => "Ops, il y a un problem !"], 200, []);
    }



    /**
     * @Route("/annonce/edit/{id}", name="edit_annonce", methods={"PUT"})
     */
    public function edit(
        Request $req,
        SerializerInterface $serializer,
        EntityManagerInterface $emi,
        UserRepository $userRepo,
        GarageRepository $garageRepo,
        BrandRepository $brandRepo,
        ModeleRepository $modeleRepo,
        Annonce $annonce
    ) { {
            if (!$this->getUser()) {
                return $this->json(["D??sol?? vous n avez pas acces a cette information !", 200, []]);
            }
            $isAdmin = in_array("ROLE_ADMIN", $this->getUser()->getRoles(), true);
            if ($isAdmin || $this->getUser()) {

                $annonceJson = $req->getContent();
                $annonceObj = $serializer->deserialize($annonceJson, Annonce::class, 'json');

                $annonce->setTitle($annonceObj->getTitle());
                $annonce->setDescription($annonceObj->getDescription());
                $annonce->setReference($annonceObj->getReference());
                $annonce->setPrice($annonceObj->getPrice());
                $annonce->setKm($annonceObj->getKm());
                $annonce->setFuel($annonceObj->getFuel());
                $annonce->setYear($annonceObj->getYear());
                $annonce->setCreatedAt(new \DateTime());


                $brand = $brandRepo->findOneBy(["id" => $req->toArray('brand')]);
                $modele = $modeleRepo->findOneBy(["id" => $req->toArray('modele')]);
                $garage = $garageRepo->findOneBy(["id" => $req->toArray('garage')]);
                // $user = $userRepo->findOneBy(["id" => $req->toArray('user')]);
                $user = $this->getUser();
                $annonce->setUser($user);


                $annonce->setBrand($brand);
                $annonce->setModele($modele);
                $annonce->setGarage($garage);

                $emi->persist($annonce);
                $emi->flush();
                return $this->json(["message" => "Modification r??ussi !"], 200, []);
            }
            return $this->json(["message" => "Il y a un problem l?? !"], 200, []);
        }
    }
}
