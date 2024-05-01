<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class indexController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }
    /** * @Route("/article/save")*/
#[Route('/article/save', name: 'home')]
    public function save()
    {
        $article = new Article();
        $article->setNom('Article 3');
        $article->setPrix(4000);
        $this->entityManager->persist($article);
        $this->entityManager->flush();
        return new Response('Article enregistré avec id ' . $article->getId());
    }

    /**
 *@Route("/",name="article_list")
 */
#[Route('/', name: 'article_list')]

public function home()
{
// Récupérer tous les articles de la table Article de la BD
// et les mettre dans le tableau $articles
    $articles = $this->entityManager->getRepository(Article::class)->findAll();

    return $this->render('articles/index.html.twig', ['articles' => $articles]);
}
/**
 * @Route("/article/new", name="new_article")
 * @Method({"GET", "POST"})
 */
#[Route('/article/new', name: 'new_article')]
public function new(Request $request)
{
    $article = new Article();
    $form = $this->createFormBuilder($article)
        ->add('nom', TextType::class)
        ->add('prix', TextType::class)
        ->add('save', SubmitType::class, [
            'label' => 'Créer',
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $article = $form->getData();

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $this->redirectToRoute('article_list');
    }
    return $this->render('articles/new.html.twig', ['form' => $form->createView()]);
}



    /**
     * @Route("/article/{id}", name="article_show")
     */
    #[Route('/article/{id}', name: 'article_show')]

    public function show($id) {
        $article = $this->entityManager->getRepository(Article::class)->find($id);

        return $this->render('articles/show.html.twig', ['article' => $article]);
    }

    /**
 * @Route("/article/edit/{id}", name="edit_article")
 * Method({"GET", "POST"})
 */
#[Route('/article/edit/{id}', name: 'edit_article')]
public function edit(Request $request, $id) {
    $entityManager = $this->entityManager;
    $article = $entityManager->getRepository(Article::class)->find($id);
    $form = $this->createFormBuilder($article)
        ->add('nom', TextType::class)
        ->add('prix', TextType::class)
        ->add('save', SubmitType::class, [
            'label' => 'Modifier'
        ])
        ->getForm();
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        return $this->redirectToRoute('article_list');
    }
    return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
}
 

/**
 * @Route("/article/delete/{id}", name="delete_article")
 * @Method({"DELETE"})
 */

 #[Route('/article/delete/{id}', name: 'delete_article')]

public function delete(Request $request, $id, EntityManagerInterface $entityManager)
{
    $article = $entityManager->getRepository(Article::class)->find($id);
    $entityManager->remove($article);
    $entityManager->flush();

    return $this->redirectToRoute('article_list');
}


}