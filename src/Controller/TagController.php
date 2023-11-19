<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    #[Route('/tag', name: 'app_tag')]
    public function index(): Response
    {
        return $this->render('tag/index.html.twig', [
            'controller_name' => 'TagController',
        ]);
    }

    #[Route('/tag/create', name:'create_tag')]
    public function createTag(HttpFoundationRequest $request, EntityManagerInterface $EntityManager):Response
    {
        // $tag = new Tag();
        // $tag -> setName('test');
    
        // $EntityManager -> persist($tag);
        // $EntityManager -> flush();
        // return $this->render('tag/index.html.twig');

        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form -> handleRequest($request);

        if ($form->isSubmitted())
        {
            $EntityManager -> persist($tag);
            $EntityManager -> flush();
        }
        return $this->render('tag/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/tag/all', name:'all_tag')]
    public function getAllTag(EntityManagerInterface $EntityManager):Response
    {
        $repository = $EntityManager -> getRepository(Tag::class);
        $tag = $repository-> findAll();
        return $this->render('tag/all.html.twig', [
            'tags' => $tag
        ]);
    }
}
