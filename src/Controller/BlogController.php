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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
                ],
                'label' => 'Titre'
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'placeholder' => "Contenu de l'article"
                ],
                'label' => 'Contenu'
            ])
            ->add('image', TextType::class, [
                'attr' => [
                    'placeholder' => "Image de l'article"
                ],
                'label' => 'Image'
            ])
            /* ajout du button submit
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])*/
            ->getForm();//faire le resultat

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
