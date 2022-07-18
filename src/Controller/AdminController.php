<?php

namespace App\Controller;

use App\Entity\Users;
use App\Event\ListAllUsersEvent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends AbstractController
{

    public function __construct(
        private EventDispatcherInterface $dispatcher
    )
    {}

    #[
    Route('/admin', name: 'panel_admin'),
    IsGranted('ROLE_ADMIN')
    ]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $new = false;
        return $this->render('admin/home.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

     #[
        Route('/alls/{page?1}/{nbre?3}', name: 'users.list.alls'),
        IsGranted("ROLE_ADMIN")
    ]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbre): Response 
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $new = false;

        $repository = $doctrine->getRepository(Users::class);
        $nbUsers = $repository->count([]);

        $nbrePage = ceil($nbUsers / $nbre) ;

        $Users = $repository->findBy([], [],$nbre, ($page - 1 ) * $nbre);
        $listAllUsersEvent = new ListAllUsersEvent(count($Users));
        $this->dispatcher->dispatch($listAllUsersEvent, ListAllUsersEvent::LIST_ALL_Users_EVENT);

        return $this->render('admin/users.html.twig', [
            'users' => $Users,
            'isPaginated' => true,
            'nbrePage' => $nbrePage,
            'page' => $page,
            'nbre' => $nbre
        ]);
    }

    #[
        Route('/{id<\d+>}', name: 'users.detail'),
        IsGranted('ROLE_ADMIN')
    ]
    public function detail(Users $users = null): Response 
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $new = false;

        if(!$users) {
            $this->addFlash('error', "L'utilisateur n'existe pas ");
            return $this->redirectToRoute('users.list.alls');
        }

        return $this->render('admin/detail.html.twig', ['users' => $users]);
    }

    #[
        Route('/delete/{id}', name: 'users.delete'),
        IsGranted('ROLE_ADMIN')
    ]
    public function deleteUsers(Users $users = null, ManagerRegistry $doctrine): RedirectResponse 
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $new = false;
        // Récupérer l'utilisateur'
        if ($users) {
            // Si l'utilisateur existe => le supprimer et retourner un flashMessage de succès
            $manager = $doctrine->getManager();
            // Ajoute la fonction de suppression dans la transaction
            $manager->remove($users);
            // Exécuter la transaction
            $manager->flush();
            $this->addFlash('success', "L'utilisateur' a été supprimé avec succès");
        } else {
            //Sinon  retourner un flashMessage d'erreur
            $this->addFlash('error', "Utilisateur innexistant");
        }
        return $this->redirectToRoute('users.list.alls');
    }
}
