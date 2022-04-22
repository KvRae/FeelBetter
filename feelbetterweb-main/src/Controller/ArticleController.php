<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\ArticleType;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="app_article_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager
            ->getRepository(Article::class)
            ->findAll();

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/articles", name="app_article_indexFront", methods={"GET"})
     */
    public function indexFront(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager
            ->getRepository(Article::class)
            ->findAll();

        return $this->render('article/indexFront.html.twig', [
            'articles' => $articles,
        ]);
    }



    /**
     * @Route("/new", name="app_article_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $article->setDate(new \DateTime());
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idarticle}", name="app_article_show", methods={"GET"})
     */
    public function show(EntityManagerInterface $entityManager,Article $article): Response
    {
        $commentaires = $entityManager
            ->getRepository(Commentaire::class)
            ->findAll();
        return $this->render('article/show.html.twig', [
            'article' => $article, 'commentaires' => $commentaires,
        ]);
    }


    /**
     * @Route("/{idarticle}/edit", name="app_article_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $article->setDate(new \DateTime());
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idarticle}", name="app_article_delete", methods={"POST"})
     */
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getIdarticle(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/approuver/{id}", name="article_approuver", methods={"GET"})
     */
    public function approuver(ManagerRegistry $doctrine, int $id): Response
{
    $entityManager = $doctrine->getManager();
    $article = $entityManager->getRepository(Article::class)->find($id);

    if (!$article) {
        throw $this->createNotFoundException(
            'No article found for id '.$id
        );
    }
    if($article->getApprouver() == 'non'){
    $article->setapprouver('oui');
    $entityManager->flush();
    }

    else{
    $article->setapprouver('non');
    $entityManager->flush();
    }


    return $this->redirectToRoute('app_article_index', [
        'id' => $article->getIdarticle()
    ]);
}

    /**
     * @Route("/pdf/{idarticle}", name="article.pdf")
     */
    public function generateArticlePdf(Article $article = null, PdfService $pdf){
        $commentaires= null;
        $html = $this->render('article/show.html.twig',['article'=>$article,'commentaires'=>$commentaires]);
        $pdf->showPdfFile($html);

    }

    /**
     * @Route("/approuver/", name="indexApprouver", methods={"GET"})
     */
    public function indexApprouver(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager
            ->getRepository(Article::class)
            ->findAll();

        return $this->render('article/indexApprouver.html.twig', [
            'articles' => $articles,
        ]);
    }

}
