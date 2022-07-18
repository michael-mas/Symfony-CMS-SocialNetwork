<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Users;
use App\Entity\Reactions;
use App\Form\PostFormType;
use App\Repository\PostsRepository;
use App\Repository\UsersRepository;
use App\Repository\ReactionsRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private $em;
    private $postsRepository;
    public function __construct(PostsRepository $postsRepository, UsersRepository $usersRepository, ReactionsRepository $reactionsRepository, CommentsRepository $commentsRepository, EntityManagerInterface $em)
    {
        $this->postsRepository = $postsRepository;
        $this->usersRepository = $usersRepository;
        $this->reactionsRepository = $reactionsRepository;
        $this->commentsRepository = $commentsRepository;
        $this->em = $em;
    }

    #[Route('/post/new', name: 'post_new')]
    public function create(Request $request): Response
    {
        $post = new Posts();
        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $newPost = $form->getData();

            /** @var \App\Entity\Users $currentUser */
            $currentUser = $this->getUser();
            $cuId = $currentUser->getId();

            $newPost->setAuthor($cuId);
            $newPost->setDate(new \DateTime());
            $newPost->setStatus(0);
            $newPost->setImages(json_decode($form->get('images')->getData()));
            
            $this->em->persist($newPost);
            $this->em->flush();

            return $this->redirectToRoute('post_show', ['id' => $newPost->getId()]);
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/post/edit/{id}', name: 'post_edit', defaults:['id'=>0])]
    public function edit($id, Request $request): Response
    {
        $post = $this->postsRepository->find($id);

        if(!$post) return $this->render('error.html.twig', [
            'title' => 'Błąd',
            'error' => 'Nie znaleziono posta'
        ]);
        else if($post->getStatus() == 2) return $this->render('error.html.twig', [
            'title' => 'Treść usunięta',
            'error' => 'Post został usunięty przez autora/moderatora'
        ]);

        /** @var \App\Entity\Users $currentUser */
        $currentUser = $this->getUser();
        if($currentUser) {
            $cuId = $currentUser->getId();
            if($cuId != $post->getAuthor()) return $this->render('error.html.twig', [
                'title' => 'Brak uprawnień',
                'error' => 'Nie jesteś autorem tego posta'
            ]);
        } else return $this->render('error.html.twig', [
            'title' => 'Błąd',
            'error' => 'Nie jesteś zalogowany'
        ]);

        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $post->setTitle($form->get('title')->getData());
            $post->setContent($form->get('content')->getData());
            $post->setStatus(1);
            $post->setImages(json_decode($form->get('images')->getData()));

            $this->em->flush();
            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'pid' => $id
        ]);
    }

    #[Route('/post/delete/{id}', name: 'post_delete', defaults:['id'=>0])]
    public function delete($id): Response
    {
        $post = $this->postsRepository->find($id);

        if(!$post) return $this->render('error.html.twig', [
            'title' => 'Błąd',
            'error' => 'Nie znaleziono posta'
        ]);

        /** @var \App\Entity\Users $currentUser */
        $currentUser = $this->getUser();
        if($currentUser) {
            $cuId = $currentUser->getId();
            if($cuId != $post->getAuthor()) return $this->render('error.html.twig', [
                'title' => 'Brak uprawnień',
                'error' => 'Nie jesteś autorem tego posta'
            ]);
        } else return $this->render('error.html.twig', [
            'title' => 'Błąd',
            'error' => 'Nie jesteś zalogowany'
        ]);

        $post->setStatus(2);
        $this->em->flush();

        return $this->redirectToRoute('app_home');
    }

    #[Route('/post/{id}', name: 'post_show', defaults:['id'=>0], methods:['GET','HEAD'])]
    public function index($id): Response
    {
        $post = $this->postsRepository->find($id);

        if(!$post) return $this->render('error.html.twig', [
            'title' => 'Błąd',
            'error' => 'Nie znaleziono posta'
        ]);
        else if($post->getStatus() == 2) return $this->render('error.html.twig', [
            'title' => 'Treść usunięta',
            'error' => 'Post został usunięty przez autora/moderatora'
        ]);
        // return $this->json($post);

        $aId = $post->getAuthor();
        $aAnchor = $this->usersRepository->find($aId);
        $author = $aAnchor->getUsername();
        $authorPfp = $aAnchor->getPfp();

        $reactions = $this->reactionsRepository->getPostReactions($id);

        /** @var \App\Entity\Users $currentUser */
        $currentUser = $this->getUser();
        if($currentUser) {
            $cuId = $currentUser->getId();
            if($cuId == $aId) $cuIsAuthor = true;
            else $cuIsAuthor = false;

            $cuReaction = $this->reactionsRepository->findOneBy(['pid' => $id, 'uid' => $cuId, 'type' => 0]);
            if($cuReaction) $cuReaction = $cuReaction->getValue();
            else $cuReaction = 0;
        } else {
            $cuIsAuthor = false;
            $cuReaction = 0;
        }

        return $this->render('post/index.html.twig', [
            'postid' => $id,
            'post' => $post,
            'author' => $author,
            'authorPfp' => $authorPfp,
            'cDate' => new \DateTime(),
            'cuIsAuthor' => $cuIsAuthor,
            'reactions' => $reactions,
            'cuReaction' => $cuReaction,
            'comments' => $this->commentsRepository->getCommentCount($id)
        ]);
    }
}
