<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Entity\Users;
use App\Entity\Friendships;
use App\Repository\MessagesRepository;
use App\Repository\UsersRepository;
use App\Repository\FriendshipsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessengerController extends AbstractController
{
    private $em;
    private $messagesRepository;
    private $usersRepository;
    private $friendshipsRepository;
    public function __construct(MessagesRepository $messagesRepository, UsersRepository $usersRepository, FriendshipsRepository $friendshipsRepository, EntityManagerInterface $em)
    {
        $this->messagesRepository = $messagesRepository;
        $this->usersRepository = $usersRepository;
        $this->friendshipsRepository = $friendshipsRepository;
        $this->em = $em;
    }

    #[Route("/messenger/friends", name: "messenger_friends")]
    public function msgFriends(): Response
    {
        /** @var \App\Entity\Users $currentUser */
        if($currentUser = $this->getUser()) $uid = $currentUser->getId();
        else return $this->json(['error' => 'You are not logged in.']);

        $friendList = $this->friendshipsRepository->getFriendList(intval($uid));
        $friends = [];
        foreach($friendList as $friendBasic) {
            if($friendBasic['uid']==$uid) continue;
            $friend = $this->usersRepository->find($friendBasic['uid']);

            $getLastMessageStr = $this->messagesRepository->getLastMessageDate($uid, $friendBasic['uid']);
            if($getLastMessageStr) $lastMessage = $getLastMessageStr;
            else $lastMessage = '0000-00-00 00:00:00';

            $friends[] = [
                'id' => $friend->getId(),
                'username' => $friend->getUsername(),
                'pfp' => $friend->getPfp(),
                'checked_me' => $friendBasic['checked_me'],
                'last_checked' => $friendBasic['last_checked'],
                'last_message' => $lastMessage,
                'isBlocked' => $friendBasic['status'] == 4,
            ];
        }
        //sort by last message date
        usort($friends, function($a, $b) {
            return strtotime($b['last_message']) - strtotime($a['last_message']);
        });

        return $this->json($friends);
    }

    #[Route("/messenger/messages", name: "messenger_messages")]
    public function msgMessages(Request $request): Response
    {
        /** @var \App\Entity\Users $currentUser */
        if($currentUser = $this->getUser()) $uid = $currentUser->getId();
        else return $this->json(['error' => 'You are not logged in.']);

        $fid = $request->request->get('fid');
        if(!$fid) return $this->json(['error' => 'No friend id provided.']);

        $messages = $this->messagesRepository->getMessages($uid, $fid);

        foreach($messages as &$message) {
            $message['me'] = $message['sender'] == $uid;
        }

        //update last checked
        if($lastChecked = $this->friendshipsRepository->findOneBy(['user1' => $uid, 'user2' => $fid])) {
            $lastChecked->setU1last(new \DateTime());
        } else if($lastChecked = $this->friendshipsRepository->findOneBy(['user1' => $fid, 'user2' => $uid])) {
            $lastChecked->setU2last(new \DateTime());
        }
        $this->em->persist($lastChecked);
        $this->em->flush();

        return $this->json($messages);
    }

    #[Route("/messenger/cfu", name: "messenger_checkforupdates")]
    public function msgCheckForUpdates(Request $request): Response
    {
        /** @var \App\Entity\Users $currentUser */
        if($currentUser = $this->getUser()) $uid = $currentUser->getId();
        else return $this->json(['error' => 'You are not logged in.']);

        $fid = $request->request->get('fid');
        if(!$fid) return $this->json(['error' => 'No friend id provided.']);

        $last = $request->request->get('last');
        if(!$last) return $this->json(['error' => 'No last date provided.']);

        //update last checked
        if($lastChecked = $this->friendshipsRepository->findOneBy(['user1' => $uid, 'user2' => $fid])) {
            $lastChecked->setU1last(new \DateTime());
        } else if($lastChecked = $this->friendshipsRepository->findOneBy(['user1' => $fid, 'user2' => $uid])) {
            $lastChecked->setU2last(new \DateTime());
        }
        $this->em->persist($lastChecked);
        $this->em->flush();

        $messages = $this->messagesRepository->getMessagesSince($uid, $fid, $last);
        if(!$messages) return $this->json(['info' => 'no-new']);

        foreach($messages as &$message) {
            $message['me'] = $message['sender'] == $uid;
        }

        return $this->json($messages);
    }

    #[Route("/messenger/send", name: "messenger_send")]
    public function msgSend(Request $request): Response
    {
        /** @var \App\Entity\Users $currentUser */
        if($currentUser = $this->getUser()) $uid = $currentUser->getId();
        else return $this->json(['error' => 'You are not logged in.']);

        $fid = $request->request->get('fid');
        if(!$fid) return $this->json(['error' => 'No friend id provided.']);

        $message = $request->request->get('msg');
        if(!$message) return $this->json(['error' => 'No message provided.']);

        $message = htmlspecialchars($message);

        $sendMessage = $this->messagesRepository->sendMessage($uid, $fid, $message);
        if(!$sendMessage) return $this->json(['error' => 'Message could not be sent.']);

        return $this->json(['status' => 'ok']);
    }

    #[Route('/messenger', name: 'app_messenger')]
    public function index(): Response
    {
        return $this->json(['status' => 'ok']);
    }
}
