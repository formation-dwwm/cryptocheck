<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;





class CryptoController extends AbstractController
{
    // http://www.keiruaprod.fr/symblog-fr/docs/configuration-et-templates.html

    private $params;

    public function __construct(){
        $this->params = [
            'title' => "CryptoCheck",
            'crypto' => $this->getData(),
        ];
    }

    /**
     * @Route("/articles", name="articles")
     */
    public function index(ArticleRepository $repository){

        $articles = $repository->findAll();


        return $this->render('crypto/articles.html.twig', [
            "articles" => $articles,
            "title" => "CryptoCheck",
        ]);
    }

    /**
     * @Route ("/articles/creer", name="create_article")
     * @Route ("/articles/{id}/editer", name="edit_article")
     */
    public function form(Article $articles = null, Request $request, ObjectManager $manager){

        if(!$articles){
            $articles = new Article();
        }
        
        $form = $this->createForm(ArticleType::class, $articles);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$articles->getId()){
                $articles->setCreatedAt(new \DateTime());
            }

            $manager->persist($articles);
            $manager->flush();

            return $this->redirectToRoute('articles_show', ['id' => $articles->getId()]);
        };

        return $this->render('crypto/create.html.twig',[
            "title" => "CryptoCheck",
            'formArticle' => $form->createView(),
            'editMode' => $articles->getId() !== null
            
        ]);
    }

    /**
     * @Route ("/articles/{id}", name="articles_show")
     */

    public function show(Article $articles, Comment $comment = null, Request $request, ObjectManager $manager){

        if(!$comment){
            $comment = new Comment();
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$comment->getId()){
                $comment->setCreatedAt(new \DateTime());
            }

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('articles', ['id' => $comment->getId()]);
        };

        return $this->render('crypto/show.html.twig',  [
            "articles" => $articles,
            "title" => "CryptoCheck",
            "form" => $form->createView(),
            'editMode' => $comment->getId() !== null
        ]);
    }

    /**
     * @Route ("/", name="home")
     */
    public function home(){

        return $this->render('crypto/home.html.twig', $this->params);

    }

    private function getData(){
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest?CMC_PRO_API_KEY=d3e5529a-6b80-48a6-b10f-b0d92e24ceab');
        $content = json_decode($response->getContent());
        return $content->data;
    }

   /**
     * @Route ("/exchanges", name="exchanges")
     */

     public function exchanges(){

         return $this->render('crypto/exchanges.html.twig', $this->params);

     }

    /**
     * @Route("/mining", name="mining") 
     */

     public function mining(){

        return $this->render('crypto/mining.html.twig', $this->params);
    }

};




