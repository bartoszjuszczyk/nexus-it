<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\TicketAttachment;
use App\Form\Type\TicketType;
use App\Repository\TicketRepository;
use App\Service\Ticket\AttachmentUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TicketController extends AbstractController
{
    #[Route('/tickets', name: 'app_ticket_list')]
    public function index(
        TicketRepository $ticketRepository,
    ): Response {
        if ($this->isGranted('ROLE_SUPPORT')) {
            $tickets = $ticketRepository->findAll();
        } else {
            $user = $this->getUser();
            $tickets = $ticketRepository->findByUser($user);
        }

        return $this->render('ticket/index.html.twig', [
            'app_title' => $this->getParameter('app_title'),
            'page_title' => $this->getParameter('app_title').' – Tickets',
            'board_title' => 'Tickets',
            'tickets' => $tickets,
        ]);
    }

    #[Route('/tickets/new', name: 'app_ticket_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        AttachmentUploader $attachmentUploader,
    ): Response {
        $user = $this->getUser();
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $attachments = $form->get('attachments')->getData();
                if ($attachments) {
                    foreach ($attachments as $attachment) {
                        $fileName = $attachmentUploader->upload($attachment);
                        $attachmentEntity = new TicketAttachment();
                        $attachmentEntity->setAuthor($user);
                        $attachmentEntity->setFile($fileName);
                        $ticket->addTicketAttachment($attachmentEntity);
                    }
                }
                $ticket->setAuthor($user);
                $entityManager->persist($ticket);
                $entityManager->flush();

                $this->addFlash('success', 'Ticket created successfully!');

                return $this->redirectToRoute('app_ticket_view', ['id' => $ticket->getId()]);
            } catch (\Exception $exception) {
                $this->addFlash('error', 'There was an error creating the ticket.');

                return $this->redirectToRoute('app_ticket_new');
            }
        }

        return $this->render('ticket/new.html.twig', [
            'app_title' => $this->getParameter('app_title'),
            'page_title' => $this->getParameter('app_title').' – New Ticket',
            'board_title' => 'My tickets',
            'ticketForm' => $form,
        ]);
    }

    #[Route('/tickets/{id}', name: 'app_ticket_view')]
    public function view(
        Ticket $ticket,
    ): Response {
        return $this->render('ticket/view.html.twig', [
            'app_title' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – Ticket: #'.$ticket->getId(),
            'board_title' => 'Ticket #'.$ticket->getId(),
            'ticket' => $ticket,
        ]);
    }
}
