<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/* 
Dans les controllers remplacer 
use Doctrine\Common\Persistence\ObjectManager;
par 
use Doctrine\ORM\EntityManagerInterface;
*/

class BlogController extends AbstractController
{
    /** afficher tous les articles
     * 
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
        /* 
        acceder à ArticleRepository.php qui gère la selection 
        $repo = $this->getDoctrine()->getRepository(Article::class);
        */

        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    /** afficher la page d'acceuil
     * 
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig');
    }

    /** Créer un article
     * 
     * @Route("/blog/new", name="blog_create")
     */
    public function create(Request $request , EntityManagerInterface $manager)
    {
        $article = new Article();

        /*
            créer un objet Form pour faire un formulaire pour les articles
            l'objet form est complexe et possède beaucoup d'éléments dedans 
        */
        $form = $this->createFormBuilder($article)
            ->add('title')
            ->add('content')
            ->add('image')
            ->getForm();//faire le resultat


        /* en post
        analyse la requête http, passer en paramètre 
        si le champ title existe dans $article , c'est bon
        si le champ content existe dans $article , c'est bon
        si le champ image existe dans $article , c'est bon
        */
        $form->handleRequest($request);

        //si le formulaire a été soumi et est valide
        if($form->isSubmitted() && $form->isValid())
        {
            $article->setCreatedAt(new \DateTime());

            $manager->persist($article);
            $manager->flush();

            //redirection
            return $this->redirectToRoute('blog_show', ['id' => $article->getId() ]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView() //envoyer le résultat de la fonction createView() et pas l'objet $form
        ]);
    }

    /** afficher un article précis
     * 
     * route parametrer
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article)
    {
        /* 
        public function show(ArticleRepository $repo, $id)
        {
        acceder à ArticleRepository.php qui gère la selection 
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);
        */

        return $this->render('blog/show.html.twig',[
            'article' => $article
        ]);
    }
}
