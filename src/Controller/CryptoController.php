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
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class CryptoController extends AbstractController
{
    /**
     * @Route("/articles", name="articles")
     */
    public function mainArticles(ArticleRepository $repository){

        $articles = $repository->findAll();

        // Article::SetUserRepository($userRepo);

        return $this->render('crypto/articles.html.twig', [
            "articles" => $articles,
        ]);
    }

    /**
     * @Route ("/articles/creer", name="create_article")
     * @Route ("/articles/{id}/editer", name="edit_article")
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager, Security $security){

        if(!$article){
            $article = new Article();
        }
        $user = $security->getUser();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }

            $article->setAuthor($user);
            $manager->persist($article);
            $manager->flush();
            

            return $this->redirectToRoute('articles_show', ['id' => $article->getId()]);
        };

        return $this->render('crypto/create.html.twig',[
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
            
        ]);
    }

    /**
     * @Route ("/articles/{id}", name="articles_show")
     */
    public function comment(Article $article,Request $request, ObjectManager $manager)
    {

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           $comment->setArticle($article->getId());
           $comment->setCreatedAt(new \DateTime());

           $manager->persist($comment);
           $manager->flush();

        return $this->redirectToRoute('articles', ['id' => $article->getId()]);
        }

        return $this->render('crypto/show.html.twig', [
            'article' => $article,


        ]);
    }

    /**
     * @Route ("/", name="home")
     */
    public function home(){
        return $this->render('crypto/home.html.twig', [
            'crypto' => $this->getData(),
            
        ]);

    }

    /**
     * @Route ("/getcrypto", name="getcrypto")
     */
    public function GetCrypto() {
        $httpClient = HttpClient::create();
        $cryptoDataResponse = $httpClient->request('GET', 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest?CMC_PRO_API_KEY=d3e5529a-6b80-48a6-b10f-b0d92e24ceab');
        $cryptoDataJson = $cryptoDataResponse->getContent();

        return JsonResponse::fromJsonString($cryptoDataJson);
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

         return $this->render('crypto/exchanges.html.twig');

     }

    /**
     * @Route("/mining", name="mining") 
     */

     public function mining(){

        return $this->render('crypto/mining.html.twig');
    }

     /**
     * @Route("/trading", name="trading") 
     */
    public function trading(){

        return $this->render('crypto/trading.html.twig');

    }
};




