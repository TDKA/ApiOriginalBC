<?php

namespace App\Controller;

use App\Repository\ModeleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * 
 * @Route("/api/modele")
 */
class ModeleController extends AbstractController
{
    /**
     * @Route("/findAll", name="model_fidnAll",methods={"GET"})
     */
    public function findAll(ModeleRepository $repo): Response
    {
        $models = $repo->findAll();
        return $this->json($models, 200, [], [
            "groups" => ["modele"]
        ]);
    }

    /**
     * @Route("/findOne/{modelId}", name="model_fidnOne",methods={"GET"}, requirements={"id":"\d+"})
     */
    public function findOne(ModeleRepository $repo, $modelId): Response
    {
        $model = $repo->findBy(["id" => $modelId]);
        return $this->json($model, 200, [], [
            "groups" => ["modele"]
        ]);
    }
}
