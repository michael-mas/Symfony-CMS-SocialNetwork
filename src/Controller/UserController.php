<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Friendships;
use App\Form\UserFormType;
use App\Repository\UsersRepository;
use App\Repository\FriendshipsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class UserController extends AbstractController
{
    private $em;
    private $usersRepository;
    private $friendshipsRepository;
    public function __construct(UsersRepository $usersRepository, FriendshipsRepository $friendshipsRepository, EntityManagerInterface $em)
    {
        $this->usersRepository = $usersRepository;
        $this->friendshipsRepository = $friendshipsRepository;
        $this->em = $em;
    }

    /* Status codes:
     * 0* - Not friends
     * 1 - Pending request
     * 2 - Friends
     * 3 - Blocked, not friends
     * 4 - Blocked, friends
     * 5* - Self
     * 6* - Not Logged In
     * 
     * * - not stored in database
    */

    #[Route('/user/invite/{id}', name: 'user_invite')]
    public function invite($id): Response
    {
        /** @var \App\Entity\Users $currentUser */
        $currentUser = $this->getUser();
        if(!$currentUser) return $this->redirectToRoute('app_login');
        $cuId = $currentUser->getId();
        
        $user = $this->usersRepository->find($id);
        if(!$user) return $this->redirectToRoute('user_show');
        $uId = $user->getId();

        if($cuId==$id) return $this->redirectToRoute('user_show');

        $friendship = $this->friendshipsRepository->findOneBy(['user1' => $cuId, 'user2' => $uId]);
        if(!$friendship) $friendship = $this->friendshipsRepository->findOneBy(['user1' => $uId, 'user2' => $cuId]);
        if($friendship) return $this->redirectToRoute('user_show');

        $friendship = new Friendships();
        $friendship->setUser1($cuId);
        $friendship->setUser2($uId);
        $friendship->setU1last(new \DateTime());
        $friendship->setU2last(new \DateTime());
        $friendship->setStatus(1);
        $this->em->persist($friendship);
        $this->em->flush();

        return $this->redirectToRoute('user_show', ['id' => $uId]);
    }

    #[Route('/user/accept/{id}', name: 'user_accept')]
    public function accept($id): Response
    {
        /** @var \App\Entity\Users $currentUser */
        $currentUser = $this->getUser();
        if(!$currentUser) return $this->redirectToRoute('app_login');
        $cuId = $currentUser->getId();
        
        if($cuId==$id) return $this->redirectToRoute('user_show');

        $user = $this->usersRepository->find($id);
        if(!$user) return $this->redirectToRoute('user_show');
        $uId = $user->getId();

        $friendship = $this->friendshipsRepository->findOneBy(['user1' => $uId, 'user2' => $cuId]);
        if(!$friendship) return $this->redirectToRoute('user_show');
        if($friendship->getStatus()!=1) return $this->redirectToRoute('user_show');

        $friendship->setStatus(2);
        $this->em->persist($friendship);
        $this->em->flush();

        return $this->redirectToRoute('user_show', ['id' => $uId]);
    }

    #[Route('/user/unfriend/{id}', name: 'user_unfriend')]
    public function unfriend($id): Response
    {
        /** @var \App\Entity\Users $currentUser */
        $currentUser = $this->getUser();
        if(!$currentUser) return $this->redirectToRoute('app_login');
        $cuId = $currentUser->getId();

        $user = $this->usersRepository->find($id);
        if(!$user) return $this->redirectToRoute('user_show');
        $uId = $user->getId();

        $friendship = $this->friendshipsRepository->findOneBy(['user1' => $cuId, 'user2' => $uId]);
        if(!$friendship) $friendship = $this->friendshipsRepository->findOneBy(['user1' => $uId, 'user2' => $cuId]);
        if(!$friendship) return $this->redirectToRoute('user_show');
        if($friendship->getStatus()!=4) {
            $this->em->remove($friendship);
            $this->em->flush();
        } else {
            $friendship->setStatus(3);
            $this->em->persist($friendship);
            $this->em->flush();
        }

        return $this->redirectToRoute('user_show', ['id' => $uId]);
    }

    #[Route('/user/cancel/{id}', name: 'user_cancel')]
    public function cancel($id): Response
    {
        return $this->redirectToRoute('user_unfriend', ['id' => $id]);
    }

    #[Route('/user/deny/{id}', name: 'user_deny')]
    public function deny($id): Response
    {
        return $this->redirectToRoute('user_unfriend', ['id' => $id]);
    }

    #[Route('/user/block/{id}', name: 'user_block')]
    public function block($id): Response
    {
        /** @var \App\Entity\Users $currentUser */
        $currentUser = $this->getUser();
        if(!$currentUser) return $this->redirectToRoute('app_login');
        $cuId = $currentUser->getId();

        if($cuId==$id) return $this->redirectToRoute('user_show');
        
        $user = $this->usersRepository->find($id);
        if(!$user) return $this->redirectToRoute('user_show');
        $uId = $user->getId();

        $friendship = $this->friendshipsRepository->findOneBy(['user1' => $cuId, 'user2' => $uId]);
        if(!$friendship) $friendship = $this->friendshipsRepository->findOneBy(['user1' => $uId, 'user2' => $cuId]);
        if(!$friendship) {
            $friendship = new Friendships();
            $friendship->setUser1($cuId);
            $friendship->setUser2($uId);
            $friendship->setU1last(new \DateTime());
            $friendship->setU2last(new \DateTime());
            $friendship->setStatus(3);
            $this->em->persist($friendship);
            $this->em->flush();
        } else {
            $friendship->setUser1($cuId);
            $friendship->setUser2($uId);
            if($friendship->getStatus()!=1) $friendship->setStatus(4);
            else $friendship->setStatus(3);
            $this->em->persist($friendship);
            $this->em->flush();
        }

        return $this->redirectToRoute('user_show', ['id' => $uId]);
    }

    #[Route('/user/unblock/{id}', name: 'user_unblock')]
    public function unblock($id): Response
    {
        /** @var \App\Entity\Users $currentUser */
        $currentUser = $this->getUser();
        if(!$currentUser) return $this->redirectToRoute('app_login');
        $cuId = $currentUser->getId();

        if($cuId==$id) return $this->redirectToRoute('user_show');
        
        $user = $this->usersRepository->find($id);
        if(!$user) return $this->redirectToRoute('user_show');
        $uId = $user->getId();

        $friendship = $this->friendshipsRepository->findOneBy(['user1' => $cuId, 'user2' => $uId]);
        if(!$friendship) return $this->redirectToRoute('user_show');
        if($friendship->getStatus()==4){
            $friendship->setStatus(2);
            $this->em->persist($friendship);
            $this->em->flush();
        } else if ($friendship->getStatus()==3) {
            $this->em->remove($friendship);
            $this->em->flush();
        } else {
            return $this->redirectToRoute('user_show');
        }

        return $this->redirectToRoute('user_show', ['id' => $uId]);
    }

    #[Route('/user/edit', name: 'user_edit')]
    public function edit(Request $request): Response
    {
        /** @var \App\Entity\Users $currentUser */
        $currentUser = $this->getUser();
        if(!$currentUser) return $this->redirectToRoute('app_login');
        $cuId = $currentUser->getId();

        $user = $this->usersRepository->find($cuId);
        if(!$user) return $this->redirectToRoute('user_show');

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('user_show');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/friends/{id}', name: 'user_friends', defaults: ['id'=>'me'])]
    public function friends($id): Response
    {
        /** @var \App\Entity\Users $currentUser */
        $currentUser = $this->getUser();
    
        if($id=='me') {
            if($currentUser!=null) $id = $currentUser->getId();
            else return $this->redirectToRoute('app_login');
        }

        $user = $this->usersRepository->find($id);
        if(!$user) return $this->render('error.html.twig', [
            'title' => 'Opps!',
            'error' => 'User not found'
        ]);

        return $this->render('user/friends.html.twig', [
            'uid' => $user->getId(),
            'name' => $user->getUsername(),
            'pfp' => $user->getPfp(),
            'isOwner' => $id==$currentUser->getId()
        ]);
    }

    #[Route('/user/{id}', name: 'user_show', defaults:['id'=>'me'], methods:['GET','HEAD'])]
    public function index($id): Response
    {
        /** @var \App\Entity\Users $currentUser */
        $currentUser = $this->getUser();

        if($id == 'me') {
            if($currentUser!=null) $id = $currentUser->getId();
            else return $this->redirectToRoute('app_login');
        }
        $user = $this->usersRepository->find($id);

        if(!$user) return $this->render('error.html.twig', [
            'title' => 'Błąd',
            'error' => 'Nie znaleziono użytkownika'
        ]);

        $isMgr = 1;
        if($currentUser!=null){
            $cuId = $currentUser->getId();
            $isOwner = $cuId == $user->getId();
            if($isOwner) $fstatus = 5;
            else {
                $friendship = $this->friendshipsRepository->findOneBy(['user1' => $cuId, 'user2' => $id]);
                if(!$friendship){
                    $friendship = $this->friendshipsRepository->findOneBy(['user1' => $id, 'user2' => $cuId]);
                    $isMgr = 0;
                }
                if($friendship) $fstatus = $friendship->getStatus();
                else $fstatus = 0;
            }
        } else {
            $isOwner = 0;
            $fstatus = 6;
        }

        if(!$isMgr && ($fstatus==3 || $fstatus==4)) return $this->render('error.html.twig', [
            'title' => 'Brak dostępu',
            'error' => 'Nie możesz zobaczyć profilu tego użytkownika'
        ]);
        
        return $this->render('user/index.html.twig', [
            'uid' => $user->getId(),
            'name' => $user->getUsername(),
            'email' => $user->getEmail(),
            'pfp' => $user->getPfp(),
            'bio' => $user->getInfo(),
            'isOwner' => $isOwner,
            'isMgr' => $isMgr,
            'friendship' => $fstatus
        ]);
    }
}
