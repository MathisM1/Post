<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\User;
use App\Form\MediaType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    #[Route('/media', name: 'app_media')]
    public function index(): Response
    {
        return $this->render('media/index.html.twig', [
            'controller_name' => 'MediaController',
        ]);
    }

    #[Route('/media/create', name:'create_media')]
    public function createMedia(HttpFoundationRequest $request, EntityManagerInterface $EntityManager):Response
    {
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media);
        $form -> handleRequest($request);

        $user = $EntityManager->getRepository(User::class)->find(1);
        $media -> setUser($user);

        if ($form->isSubmitted())
        {
            $EntityManager -> persist($media);
            $EntityManager -> flush();
        }
        return $this->render('media/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/media/all', name:'all_media')]
    public function getAllMedia(EntityManagerInterface $EntityManager):Response
    {
        $repository = $EntityManager -> getRepository(media::class);
        $media = $repository-> findAll();
        return $this->render('media/all.html.twig', [
            'medias' => $media
        ]);
    }

    #[Route('/media/update/{id}', name:'update_media')]
    public function updateMedia(HttpFoundationRequest $request, EntityManagerInterface $EntityManager, int $id):Response
    {
        $media = $EntityManager-> getRepository(Media::class) -> find($id);
        if(!$media)
        {
            throw $this->createNotFoundException('Catégorie pas trouvé ^^');
        }
        $form = $this->createForm(MediaType::class, $media);
        $form -> handleRequest($request);
        if($form->isSubmitted())
        {
            $EntityManager->persist($media);
            $EntityManager->flush();
        }
        return $this->render('media/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
