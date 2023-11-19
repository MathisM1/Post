<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    #[Route('/category/create', name:'create_category')]
    public function createCategory(HttpFoundationRequest $request, EntityManagerInterface $EntityManager):Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form -> handleRequest($request);

        if ($form->isSubmitted())
        {
            $EntityManager -> persist($category);
            $EntityManager -> flush();
        }
        return $this->render('category/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/category/all', name:'all_category')]
    public function getAllCategory(EntityManagerInterface $EntityManager):Response
    {
        $repository = $EntityManager -> getRepository(category::class);
        $category = $repository-> findAll();
        return $this->render('category/all.html.twig', [
            'categories' => $category
        ]);
    }

    #[Route('/category/update/{id}', name:'update_category')]
    public function updateCategory(HttpFoundationRequest $request, EntityManagerInterface $EntityManager, int $id):Response
    {
        $category = $EntityManager-> getRepository(Category::class) -> find($id);
        if(!$category)
        {
            throw $this->createNotFoundException('Catégorie pas trouvé ^^');
        }
        $form = $this->createForm(CategoryType::class, $category);
        $form -> handleRequest($request);
        if($form->isSubmitted())
        {
            $EntityManager->persist($category);
            $EntityManager->flush();
        }
        return $this->render('category/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
