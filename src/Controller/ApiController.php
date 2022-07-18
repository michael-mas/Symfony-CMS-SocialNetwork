<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Users;
use App\Entity\Friendships;
use App\Entity\Reactions;
use App\Entity\Comments;
use App\Repository\PostsRepository;
use App\Repository\UsersRepository;
use App\Repository\FriendshipsRepository;
use App\Repository\ReactionsRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private $em;
    private $postsRepository;
    private $usersRepository;
    private $friendshipsRepository;
    private $reactionsRepository;
    private $commentsRepository;
    public function __construct(PostsRepository $postsRepository, UsersRepository $usersRepository, FriendshipsRepository $friendshipsRepository, ReactionsRepository $reactionsRepository, CommentsRepository $commentsRepository, EntityManagerInterface $em)
    {
        $this->postsRepository = $postsRepository;
        $this->usersRepository = $usersRepository;
        $this->friendshipsRepository = $friendshipsRepository;
        $this->reactionsRepository = $reactionsRepository;
        $this->commentsRepository = $commentsRepository;
        $this->em = $em;
    }

    #[Route('/api/friends/{uid}', name: 'api_friends', defaults:['uid'=>'0'])]
    public function friends($uid): Response
    {
        if(intval($uid) <= 0)
        {
            /** @var \App\Entity\Users $currentUser */
            if($currentUser = $this->getUser()) $uid = $currentUser->getId();
        }

        $friendList = $this->friendshipsRepository->getFriendList(intval($uid));
        $friends = [];
        foreach($friendList as $friendBasic) {
            if($friendBasic['uid']==$uid) continue;
            $friend = $this->usersRepository->find($friendBasic['uid']);
            $friends[] = [
                'id' => $friend->getId(),
                'username' => $friend->getUsername(),
                'pfp' => $friend->getPfp(),
                'checked_me' => $friendBasic['checked_me'],
                'last_checked' => $friendBasic['last_checked'],
                'isBlocked' => $friendBasic['status'] == 4,
            ];
        }
        //sort by username
        usort($friends, fn($a, $b) => strcmp($a['username'], $b['username']));

        return $this->json($friends);
    }

    #[Route('/api/blocked/{uid}', name: 'api_blocked', defaults:['uid'=>'0'])]
    public function blocked($uid): Response
    {
        $blockedList = $this->friendshipsRepository->getBlockedList(intval($uid));
        $blocked = [];
        foreach($blockedList as $blockedBasic) {
            if($blockedBasic['uid']==$uid) continue;
            $user = $this->usersRepository->find($blockedBasic['uid']);
            $blocked[] = [
                'id' => $blockedBasic['uid'],
                'username' => $user->getUsername(),
                'pfp' => $user->getPfp(),
                'isFriend' => $blockedBasic['status'] == 4,
            ];
        }
        //sort by username
        usort($blocked, fn($a, $b) => strcmp($a['username'], $b['username']));

        return $this->json($blocked);
    }

    #[Route('/api/pending/{uid}', name: 'api_pending', defaults:['uid'=>'0'])]
    public function pending($uid): Response
    {
        $pendingList = $this->friendshipsRepository->getPendingList(intval($uid));
        $pending = [];
        foreach($pendingList as $pendingBasic) {
            if($pendingBasic['uid']==$uid) continue;
            $user = $this->usersRepository->find($pendingBasic['uid']);
            $pending[] = [
                'id' => $pendingBasic['uid'],
                'username' => $user->getUsername(),
                'pfp' => $user->getPfp()
            ];
        }

        return $this->json($pending);
    }

    #[Route('/api/requests/{uid}', name: 'api_requests', defaults:['uid'=>'0'])]
    public function requests($uid): Response
    {
        $requestList = $this->friendshipsRepository->getRequestList(intval($uid));
        $requests = [];
        foreach($requestList as $requestBasic) {
            if($requestBasic['uid']==$uid) continue;
            $user = $this->usersRepository->find($requestBasic['uid']);
            $requests[] = [
                'id' => $requestBasic['uid'],
                'username' => $user->getUsername(),
                'pfp' => $user->getPfp()
            ];
        }

        return $this->json($requests);
    }

    #[Route('/api/userposts', name: 'api_userposts')]
    public function userposts(Request $request): Response
    {
        $uid = intval($request->request->get('uid'));
        $page = intval($request->request->get('page',1));
        if($page<1) $page = 1;

        $limit = 10;
        $offset = ($page-1)*$limit;
        $posts = $this->postsRepository->getUserPosts($uid,$limit,$offset);

        $postCount = 0;
        foreach($posts as $post) {
            $posts[$postCount]['reactions'] = $this->reactionsRepository->getPostReactions($post['id']);
            $posts[$postCount]['comments'] = $this->commentsRepository->getCommentCount($post['id']);
            $postCount++;
        }

        if($postCount<$limit) $posts[] = 'end';

        return $this->json($posts);
    }

    #[Route('/api/posts', name: 'api_posts')]
    public function posts(Request $request): Response
    {
        /** @var \App\Entity\Users $currentUser */
        if($currentUser = $this->getUser()) $uid = $currentUser->getId();
        else return $this->json(['error'=>'not logged in'],403);

        $page = $request->request->get('page',1);
        if($page<1) $page = 1;

        $friendList = $this->friendshipsRepository->getFriendList(intval($uid));
        $idList = [];
        $idList[] = intval($uid);
        foreach($friendList as $friendBasic) {
            if($friendBasic['uid']==$uid) continue;
            $idList[] = $friendBasic['uid'];
        }

        $limit = 10;
        if(intval($page)<1) $page = 1;
        $offset = ($page-1)*$limit;
        $posts = $this->postsRepository->getPosts($idList,$limit,$offset);

        $postCount = 0;
        foreach($posts as $post){
            $postAuthor = $this->usersRepository->find($post['author']);
            $posts[$postCount]['author_username'] = $postAuthor->getUsername();
            $posts[$postCount]['author_pfp'] = $postAuthor->getPfp();
            $posts[$postCount]['reactions'] = $this->reactionsRepository->getPostReactions($post['id']);
            $posts[$postCount]['comments'] = $this->commentsRepository->getCommentCount($post['id']);

            $cuReaction = $this->reactionsRepository->FindOneBy(['pid'=>$post['id'],'uid'=>$uid,'type'=>0]);
            if($cuReaction) $posts[$postCount]['cuReaction'] = $cuReaction->getValue();
            else $posts[$postCount]['cuReaction'] = 0;

            $postCount++;
        }

        if($postCount<$limit) $posts[] = 'end';

        return $this->json($posts);
    }

    #[Route('/api/react', name: 'api_react')]
    public function react(Request $request): Response
    {
        $postId = intval($request->request->get('postId'));
        $reaction = intval($request->request->get('reaction'));
        if(!$reaction) $reaction = 0;
        $type = intval($request->request->get('type'));
        /* 0: post, 1: comment */

        if(!$postId || $postId<1) return $this->json(['error'=>'invalid_request'],400);

        if($reaction!=1 && $reaction!=0 && $reaction!=-1) return $this->json(['error'=>'invalid_reaction'],400);

        if($type!=0 && $type!=1) return $this->json(['error'=>'invalid_type'],400);

        /** @var \App\Entity\Users $currentUser */
        if($currentUser = $this->getUser()) $uid = $currentUser->getId();
        else return $this->json(['error'=>'not logged in'],403);

        //check if reaction exists, update if exists
        $exReact = $this->reactionsRepository->findOneBy(['pid'=>$postId,'uid'=>$uid,'type'=>$type]);
        if($exReact){
            if($reaction==0){
                $this->em->remove($exReact);
                $this->em->flush();
                return $this->json(['status'=>'reaction_removed']);
            }else{
                $exReact->setValue($reaction);
                $this->em->flush();
                return $this->json(['status'=>'reaction_updated']);
            }
        }

        //create new reaction, if it doesn't exist
        $addReaction = new Reactions();
        $addReaction->setPid($postId);
        $addReaction->setUid($uid);
        $addReaction->setValue($reaction);
        $addReaction->setDate(new \DateTime());
        $addReaction->setType($type);

        $this->em->persist($addReaction);
        $this->em->flush();

        return $this->json(['status'=>'success']);
    }

    #[Route('/api/comment', name: 'api_comment')]
    public function comment(Request $request): Response
    {
        $postId = intval($request->request->get('postId'));
        $action = $request->request->get('action');
        $comment = $request->request->get('comment');

        if($action=='edit' || $action=='del') $comId = intval($request->request->get('commentId'));

        /* Actions:
            * add - add comment
            * edit - edit comment
            * del - delete comment
           Comment status:
            * 0 - normal
            * 1 - edited
            * 2 - deleted
        */
        
        if(!$postId || $postId<1) return $this->json(['error'=>'invalid_request']);

        if($action!='add' && $action!='edit' && $action!='del') return $this->json(['error'=>'invalid_action']);

        if(!$comment){
            if($action!='del') return $this->json(['error'=>'invalid_comment']);
        }

        /** @var \App\Entity\Users $currentUser */
        if($currentUser = $this->getUser()) $uid = $currentUser->getId();
        else return $this->json(['error'=>'not logged in']);
        
        //create new comment
        if($action=='add'){
            $addComment = new Comments();
            $addComment->setPid($postId);
            $addComment->setUid($uid);
            $addComment->setContent($comment);
            $addComment->setDate(new \DateTime());
            $addComment->setStatus(0);
            $this->em->persist($addComment);
            $this->em->flush();
        }
        else if($action=='edit'){
            $editComment = $this->commentsRepository->findOneBy(['id'=>$comId,'uid'=>$uid]);
            if(!$editComment) return $this->json(['error'=>'comment_not_found']);
            $editComment->setContent($comment);
            $editComment->setStatus(1);
            $this->em->flush();
        }
        else if($action=='del'){
            $delComment = $this->commentsRepository->findOneBy(['id'=>$comId,'uid'=>$uid]);
            if(!$delComment) return $this->json(['error'=>'comment_not_found']);
            $delComment->setStatus(2);
            $this->em->flush();
        }

        return $this->json(['status'=>'success']);
    }

    #[Route('/api/comments', name: 'api_comments')]
    public function comments(Request $request): Response
    {
        $postId = intval($request->request->get('postId'));

        if(!$postId || $postId<1) return $this->json(['error'=>'invalid_request']);

        /** @var \App\Entity\Users $currentUser */
        if($currentUser = $this->getUser()) $uid = $currentUser->getId();
        else $uid = 0;

        $comments = $this->commentsRepository->getComments($postId);

        $commentCount = 0;
        foreach($comments as $comment){
            $getCuReaction = $this->reactionsRepository->findOneBy(['pid'=>$comment['id'],'uid'=>$uid,'type'=>1]);
            if($getCuReaction) $cuReaction = $getCuReaction->getValue();
            else $cuReaction = 0;

            $commentAuthor = $this->usersRepository->find($comment['uid']);
            $comments[$commentCount]['author_username'] = $commentAuthor->getUsername();
            $comments[$commentCount]['author_pfp'] = $commentAuthor->getPfp();
            $comments[$commentCount]['isAuthor'] = $comment['uid']==$uid;
            $comments[$commentCount]['reactions'] = $this->reactionsRepository->getCommentReactions($comment['id']);
            $comments[$commentCount]['cuReaction'] = $cuReaction;
            if($comment['status']==1) $comments[$commentCount]['edited'] = true;
            else $comments[$commentCount]['edited'] = false;
            $commentCount++;
        }

        return $this->json($comments);
    }

    #[Route('/api/postimg/{pid}', name: 'api_postimg')]
    public function postimg($pid): Response
    {
        $post = $this->postsRepository->find($pid);
        if(!$post) return $this->json(['error'=>'post_not_found']);

        $postImg = $this->postsRepository->find($pid)->getImages();

        return $this->json($postImg);
    }

    #[Route('/api/upload/{type}', name: 'api_upload', defaults:['type'=>'0'])]
    public function upload($type,Request $request): Response
    {
        /* TYPES
            * 0 = profile picture
            * 1 = post picture
        */
        $types = ['/pfps','/uploads'];
        $type = intval($type);
        if($type > count($types)-1 || $type < 0) return $this->json(['error'=>'invalid upload type']);
        $fDir = $types[$type];

        $file = $request->files->get('file');
        if($file){
            $fileName = uniqid() . '.' . $file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('kernel.project_dir') . '/public' . $fDir,
                    $fileName
                );
            } catch (FileException $e) {
                return $this->json(['error'=>'File upload failed']);
            }
            return $this->json(['url'=>$fDir.'/'.$fileName]);
        }
        return $this->json(['error'=>'No file uploaded']);
    }

    #[Route('/api/searchUsers', name: 'api_searchUsers')]
    public function searchUsers(Request $request): Response
    {
        $query = $request->request->get('query');
        if(!$query) return $this->json(['error'=>'invalid_query']);

        $stage = intval($request->request->get('stage'));
        if($stage<1) $stage = 1;
        $offset = ($stage-1)*15;

        $users = $this->usersRepository->searchUsers($query,$offset);

        $userCount = 0;
        foreach($users as $user){
            if($user['pfp']==null) $users[$userCount]['pfp'] = '/pfps/default.jpg';
            $userCount++;
        }
        if($userCount<15) $users[] = 'end';

        return $this->json($users);
    }

    #[Route('/api/searchPosts', name: 'api_searchPosts')]
    public function searchPosts(Request $request): Response
    {
        $query = $request->request->get('query');
        if(!$query) return $this->json(['error'=>'invalid_query']);

        $stage = intval($request->request->get('stage'));
        if($stage<1) $stage = 1;
        $offset = ($stage-1)*10;

        $posts = $this->postsRepository->searchPosts($query,$offset);

        $postCount = 0;
        foreach($posts as $post){
            $postAuthor = $this->usersRepository->find($post['author']);
            $posts[$postCount]['author_username'] = $postAuthor->getUsername();
            $posts[$postCount]['author_pfp'] = $postAuthor->getPfp();
            $posts[$postCount]['reactions'] = $this->reactionsRepository->getPostReactions($post['id']);
            $posts[$postCount]['comments'] = $this->commentsRepository->getCommentCount($post['id']);
            $postCount++;
        }
        if($postCount<10) $posts[] = 'end';

        return $this->json($posts);
    }

    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        /** @var \App\Entity\Users $currentUser */
        if($currentUser = $this->getUser()) $cuId = $currentUser->getId();
        else $cuId = 0;

        return $this->json(['working'=>true, 'auth'=>$cuId],200);
    }
}
