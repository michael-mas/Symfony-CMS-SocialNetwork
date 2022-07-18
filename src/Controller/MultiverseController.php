<?php

namespace App\Controller;

use App\Entity\Pages;
use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use App\Event\ListAllPagesEvent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class MultiverseController extends AbstractController
{
    public function __construct(
        private EventDispatcherInterface $dispatcher
    )
    {}

    #[Route('/multiverse', name: 'app_multiverse')]
    public function index(): Response
    {
        return $this->render('multiverse/index.html.twig', [
            'controller_name' => 'MultiverseController',
        ]);
    }

    #[Route('/testMultiverse', name: 'test_multiverse')]
    public function test(): Response
    {
        return $this->render('multiverse/test.html.twig', [
            'controller_name' => 'MultiverseController',
        ]);
    }

    #[Route('/choice', name: 'choice_multiverse')]
    public function choice(): Response
    {
        
        return $this->render('multiverse/choice.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('pages/categories', name: 'cat')]
    public function cat(CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('multiverse/categories.html.twig', [
            'categories' => $categoriesRepository->findBy([], ['id' => 'asc']),
        ]);
    }

    #[Route('pages/categories/{slug}', name: 'pages_list')]
    public function list(Categories $categories): Response
    {
        $pages = $categories->getPages();
        
        return $this->render('multiverse/PagesList.html.twig', compact('categories', 'pages'));
        // Syntaxe alternative
        // return $this->render('categories/list.html.twig', [
        //     'category' => $category,
        //     'products' => $products
        // ]);
    }

    #[
        Route('pageslist/alls/{page?1}/{nbre?8}', name: 'pageslist.alls'),
    ]
    public function indexPagesAlls(ManagerRegistry $doctrine, $page, $nbre): Response {

        $repository = $doctrine->getRepository(Pages::class);
        $nbPages = $repository->count([]);
        $nbrePage = ceil($nbPages / $nbre) ;

        $pages = $repository->findBy([], [],$nbre, ($page - 1 ) * $nbre);
        $listAllPagesEvent = new ListAllPagesEvent(count($pages));
        $this->dispatcher->dispatch($listAllPagesEvent, ListAllPagesEvent::LIST_ALL_Pages_EVENT);

        return $this->render('multiverse/all-pages.html.twig', [
            'pages' => $pages,
            'isPaginated' => true,
            'nbrePage' => $nbrePage,
            'page' => $page,
            'nbre' => $nbre
        ]);
    }

    #[Route('/sport', name: 'sport_multiverse')]
    public function sport(): Response
    {
        
        return $this->render('multiverse/sport.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/nutrition', name: 'nutrition_multiverse')]
    public function nutrition(): Response
    {
        
        return $this->render('multiverse/nutrition.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/adaptation', name: 'adaptation_multiverse')]
    public function adaptation(): Response
    {
        
        return $this->render('multiverse/adaptation.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/enigme', name: 'enigme_multiverse')]
    public function enigme(): Response
    {
        
        return $this->render('multiverse/enigme.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/memoire', name: 'memoire_multiverse')]
    public function memoire(): Response
    {
        
        return $this->render('multiverse/memoire.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/cours', name: 'cours_multiverse')]
    public function cours(): Response
    {
        
        return $this->render('multiverse/cours.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/dons', name: 'dons_multiverse')]
    public function dons(): Response
    {
        
        return $this->render('multiverse/dons.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/conaissances', name: 'conaissances_multiverse')]
    public function conaissances(): Response
    {
        
        return $this->render('multiverse/conaissances.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/jeux', name: 'jeux_multiverse')]
    public function jeux(): Response
    {
        
        return $this->render('multiverse/jeux.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/jeuxvideo', name: 'jeuxvideo_multiverse')]
    public function jeuxvideo(): Response
    {
        
        return $this->render('multiverse/jeux_page/jeuxvideo.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/videogame1', name: 'videogame1_multiverse')]
    public function videogame1(): Response
    {
        
        return $this->render('multiverse/jeux_page/videogame1.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/videogame2', name: 'videogame2_multiverse')]
    public function videogame2(): Response
    {
        
        return $this->render('multiverse/jeux_page/videogame2.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/videogame3', name: 'videogame3_multiverse')]
    public function videogame3(): Response
    {
        
        return $this->render('multiverse/jeux_page/videogame3.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/art', name: 'art_multiverse')]
    public function art(): Response
    {
        
        return $this->render('multiverse/art.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/gallery1', name: 'gallery1_multiverse')]
    public function gallery1(): Response
    {
        
        return $this->render('multiverse/gallery_art/gallery1.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/gallery2', name: 'gallery2_multiverse')]
    public function gallery2(): Response
    {
        
        return $this->render('multiverse/gallery_art/gallery2.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/gallery3', name: 'gallery3_multiverse')]
    public function gallery3(): Response
    {
        
        return $this->render('multiverse/gallery_art/gallery3.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/gallery4', name: 'gallery4_multiverse')]
    public function gallery4(): Response
    {
        
        return $this->render('multiverse/gallery_art/gallery4.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/gallery5', name: 'gallery5_multiverse')]
    public function gallery5(): Response
    {
        
        return $this->render('multiverse/gallery_art/gallery5.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }

    #[Route('/gallery6', name: 'gallery6_multiverse')]
    public function gallery6(): Response
    {
        
        return $this->render('multiverse/gallery_art/gallery6.html.twig', [
            'controller_name' => 'MultiverseController'
        ]);
    }


}
