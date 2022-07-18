<?php

namespace App\Controller;

use App\Entity\Widgets;
use App\Form\WidgetFormType;
use App\Repository\WidgetsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManagerInterface;

class WidgetController extends AbstractController
{
    private $em;

    public function __construct
    (
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        private EventDispatcherInterface $dispatchers
    )
    {
        $this->em = $em;
    }

    #[Route('/widget/new', name: 'widget_new')]
    public function create(Request $request): Response
    { 
        $messageC = " Le widget ";
        $message2 = " a été créer avec succès";

        $widgets = new Widgets();
        $form = $this->createForm(WidgetFormType::class, $widgets);

        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $newWidget = $form->getData(); 
            $this->em->persist($newWidget);
            $this->em->flush();

            $this->addFlash('success2',$messageC . $widgets->getNom(). $message2 );
            return $this->redirectToRoute('widget_new', ['id' => $newWidget->getId()]);
        }
        return $this->render('multiverse/widget/create_widget.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
