<?php

namespace App\Controller;

use App\Entity\Content;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Tag;
use App\Form\ArticleType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name:'app_admin_')]
class AdminController extends AbstractController
{
    #[Route('/admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/articles', name: 'article_list')]
    public function articleList(EntityManagerInterface $entityManager): Response
    {
        $articleList = $entityManager->getRepository(Post::class)->findBy([
            'type' => 'article',
            'deletedAt' => null
        ])
        ;
        return $this->render('admin/article/list.html.twig', ['articleList' => $articleList]);
    }

    #[Route('/admin/articles/create', name: 'article_create')]
    public function articleCreate(Request $request, EntityManagerInterface $entityManager): Response
    {
        //$user = $this->getUser();
        $user = $entityManager->getRepository(User::class)->find(1);
        // $tag = $entityManager->getRepository(Tag::class)->find(1);


        $post = new Post();
        $post->setCreatedAt(new \DateTimeImmutable('now'));
        $post->setType('article');
        $post->setAuthor($user);
        // $post->addTag($tag->getName());

        $form = $this->createForm(ArticleType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // traitement des données reçues
            $data = $form->get('data')->getData();
            
            $content = new Content();
            $content->setData($data);
            $content->setPost($post);
            
            $entityManager->persist($post);
            $entityManager->persist($content);
            $entityManager->flush($post);

            return $this->redirectToRoute('app_admin_article_list');
        }

        return $this->render('admin/article/create.html.twig', [
            'createArticleForm' => $form
        ]);
    }

    #[Route('/admin/articles/{id}/update', name: 'article_update')]
    public function articleUpdate($id,Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = $entityManager->getRepository(Post::class)->find(intval($id));
        $contentParent = $article->getContents()->isEmpty() === false? $article->getContents()->last(): null;

        $form = $this->createForm(ArticleType::class, $article, [
            'contentData' => $contentParent !== null ? $contentParent->getData() : ""
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //traitement des données
            $data = $form->get('data')->getData();
            
            $content = new Content();
            $content->setData($data);
            $content->setPost($article);

            $content->setParent($contentParent);
            
            $entityManager->persist($article);
            $entityManager->persist($content);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_admin_article_view', ['id' => $id]);
        }
        
        return $this->render('admin/articles/update.html.twig', ['updateArticleForm'=>$form]);
    }
}