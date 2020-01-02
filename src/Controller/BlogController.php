<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Form\ArticleType;
use App\Entity\Comment;
use App\Form\CommentType;

use Doctrine\ORM\EntityManagerInterface;

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

    /** Créer ou modifier un article 
     * cette fonction peut être appeler par 2 route différentes
     * 
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request , EntityManagerInterface $manager)
    {
        if(!$article){
            $article = new Article();
        }
        

        /*
            créer un objet Form pour faire un formulaire pour les articles
            l'objet form est complexe et possède beaucoup d'éléments dedans 
        
            si je crée moi meme
        $form = $this->createFormBuilder($article)
            ->add('title')
            ->add('content')
            ->add('image')
            ->getForm();//faire le resultat

            sinon si je le crée avec la CLI
        */
        $form = $this->createForm(ArticleType::class, $article);

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
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }
            

            $manager->persist($article);
            $manager->flush();

            //redirection
            return $this->redirectToRoute('blog_show', ['id' => $article->getId() ]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(), //envoyer le résultat de la fonction createView() et pas l'objet $form
            'editMode' => ($article->getId() !== null)
        ]);
    }

    /** afficher un article précis
     * 
     * route parametrer
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article, Request $request, EntityManagerInterface $manager)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){

            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }


        return $this->render('blog/show.html.twig',[
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }
}
