<?php

namespace App\Controller;

use App\Entity\Pages;
use App\Entity\Categories;
use App\Entity\Users;
use App\Form\PageFormType;
use App\Form\CategorieFormType;
use App\Repository\CategoriesRepository;
use App\Repository\PagesRepository;
use App\Repository\UsersRepository;
use App\Service\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Event\ListAllPagesEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PageController extends AbstractController
{
    private $em;
    private $pagesRepository;
    public function __construct
    (
        PagesRepository $pagesRepository,
        UsersRepository $usersRepository,
        EntityManagerInterface $em,
        UploaderService $uploaderService,
        EventDispatcherInterface $dispatcher,
        private EventDispatcherInterface $dispatchers
    )
    
    {
        $this->UploaderService = $uploaderService;
        $this->pagesRepository = $pagesRepository;
        $this->usersRepository = $usersRepository;
        $this->em = $em;
    }

    #[Route('/page/new', name: 'page_new')]
    public function create(Request $request, UploaderService $uploaderService,): Response
    { 
        $messageP = " L'univers ";
        $messageC = " La catégorie ";
        $message2 = " a été créer avec succès";

        $page = new Pages();
        $form = $this->createForm(PageFormType::class, $page);

        $categories = new Categories();
        $form2 = $this->createForm(CategorieFormType::class, $categories);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $newPage = $form->getData();
            $images = $form->get('images')->getData();
            if ($images) {
                $directory = $this->getParameter('page_directory');
                $page->setImages($uploaderService->uploadFile($images, $directory));
            }
            /** @var \App\Entity\Users $currentUser */
            $currentUser = $this->getUser();
            $cuId = $currentUser->getId();
            $newPage->setAuthor($cuId);
            $newPage->setDate(new \DateTime());
            $newPage->setStatus(0);
            $this->em->persist($newPage);
            $this->em->flush(); 
            $this->addFlash('success',$messageP . $page->getTitle(). $message2 );
            return $this->redirectToRoute('page_new', ['id' => $newPage->getId()]);
        }
        
        $form2->handleRequest($request);
        if($form2->isSubmitted() && $form2->isValid()) {
            $newCategorie = $form2->getData(); 
            $this->em->persist($newCategorie);
            $this->em->flush();

            $this->addFlash('success2',$messageC . $categories->getNom(). $message2 );
            return $this->redirectToRoute('page_new', ['id' => $newCategorie->getId()]);
        }
        return $this->render('multiverse/create.html.twig', [
            'form' => $form->createView(),
            'form2' => $form2->createView(),
        ]);
    }

    #[
        Route('pages/alls/{page?1}/{nbre?8}', name: 'pages.list.alls'),
        IsGranted("ROLE_USER")
    ]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbre): Response {

        $currentUser = $this->getUser();
        $user = $currentUser->getId();

        $repository = $doctrine->getRepository(Pages::class);
        $nbPages = $repository->count([]);
        $nbrePage = ceil($nbPages / $nbre) ;

        $pages = $repository->findBy(['author' => $user], [],$nbre, ($page - 1 ) * $nbre);
        $listAllPagesEvent = new ListAllPagesEvent(count($pages));
        $this->dispatchers->dispatch($listAllPagesEvent, ListAllPagesEvent::LIST_ALL_Pages_EVENT);

        return $this->render('multiverse/pages_list.html.twig', [
            'pages' => $pages,
            'isPaginated' => true,
            'nbrePage' => $nbrePage,
            'page' => $page,
            'nbre' => $nbre
        ]);
    }

    #[
        Route('pages/delete/{id}', name: 'pages.delete'),
        IsGranted("ROLE_USER")
    ]
    public function deletePages(Pages $pages = null, ManagerRegistry $doctrine): RedirectResponse {
        // Récupérer la produits
        if ($pages) {
            // Si la produits existe => le supprimer et retourner un flashMessage de succés
            $manager = $doctrine->getManager();
            // Ajoute la fonction de suppression dans la transaction
            $manager->remove($pages);
            // Exécuter la transacition
            $manager->flush();
            $this->addFlash('success', "La page a été supprimé avec succès");
        } else {
            //Sinon  retourner un flashMessage d'erreur
            $this->addFlash('error', "Page innexistante");
        }
        return $this->redirectToRoute('pages.list.alls');
    }

    #[Route('/edit/{id?0}', name: 'pages.edit')]
    public function addPages(
        Pages $pages = null,
        ManagerRegistry $doctrine,
        Request $request,
        UploaderService $uploaderService,
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $new = false;
        //$this->getDoctrine() : Version Sf <= 5
        if (!$pages) {
            $new = true;
            $pages = new Pages();
        }

        $form = $this->createForm(PageFormType::class, $pages);
        $form->remove('createdAt');
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('images')->getData();
            if ($image) {
                $directory = $this->getParameter('page_directory');
                $pages->setImages($uploaderService->uploadFile($image, $directory));
            }
            if($new) {
                $message = " a été ajouté avec succès";;
            } else {
                $message = " a été mis à jour avec succès";
            }
            $manager = $doctrine->getManager();
            $manager->persist($pages);

            $manager->flush();
            // Afficher un mssage de succès
            if($new) {
                // On a créer notre evenenement
                $addPagesEvent = new AddPagesEvent($pages);
                // On va maintenant dispatcher cet événement
                $this->dispatcher->dispatch($addProduitsEvent, AddPagesEvent::ADD_PAGES_EVENT);
            }
            $this->addFlash('success',$pages->getTitle(). $message );
            // Rediriger verts la liste des produits
            return $this->redirectToRoute('pages.list.alls');
        } else {
            //Sinon
            //On affiche notre formulaire
            return $this->render('multiverse/edit-pages.html.twig', [
                'form' => $form->createView()
            ]);
        }

    }
   
    #[Route('pages/update/{id}/{title}}', name: 'pages.update')]
    public function updatePages(Pages $pages = null, ManagerRegistry $doctrine, $title, $categories, $content) {
        //Vérifier que la produits à mettre à jour existe
        if ($pages) {
            // Si la produits existe => mettre a jour notre produits + message de succes
            $pages->setTitle($title);
            $pages->setCategories($categories);
            $pages->setContent($content);
            $manager = $doctrine->getManager();
            $manager->persist($pages);

            $manager->flush();
            $this->addFlash('success', "La pages a été mis à jour avec succès");
        }  else {
            //Sinon  retourner un flashMessage d'erreur
            $this->addFlash('error', "Pages innexistante");
        }
        return $this->redirectToRoute('page.list.alls');
    }

    #[Route('/page/{id<\d+>}', name: 'pages.vue')]
    public function detail(Pages $pages = null): Response {
        if(!$pages) {
            $this->addFlash('erreur', "La pages n'existe pas ");
            return $this->redirectToRoute('app_multiverse');
        }

        return $this->render('multiverse/page.html.twig', ['pages' => $pages]);
    }

}
