<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\Common\Persistence\ObjectManager;
//use Doctrine\DBAL\Types\TextType;
//use Doctrine\DBAL\Types\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BlogController extends AbstractController
{
    /**
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

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig');
    }

    /**
     * @Route("/blog/new", name="blog_create")
     */
    public function create(Request $request /*, ObjectManager $manager*/)
    {
        $article = new Article();

        /*
            créer un objet Form pour faire un formulaire pour les articles
            l'objet form est complexe et possède beaucoup d'éléments dedans 
        */
        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, [
                //tableau d'option, attribut html
                'attr' => [
                    'placeholder' => "Titre de l'article"
                ]
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'placeholder' => "Contenu de l'article"
                ]
            ])
            ->add('image', TextType::class, [
                'attr' => [
                    'placeholder' => "Image de l'article"
                ]
            ])
            ->getForm();//faire le resultat
        dump($form);
        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView() //envoyer le résultat de la fonction createView() et pas l'objet $form
        ]);
    }

    /**
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
