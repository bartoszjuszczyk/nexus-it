<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\Ticket\TicketAttachment;
use App\Entity\User;
use App\Event\TicketClosedEvent;
use App\Event\TicketEventCreated;
use App\Exception\NotificationException;
use App\Form\Type\CommentType;
use App\Form\Type\StatusChangeType;
use App\Form\Type\TicketAssignType;
use App\Form\Type\TicketFilterType;
use App\Form\Type\TicketType;
use App\Repository\Ticket\TicketEventRepository;
use App\Repository\Ticket\TicketPriorityRepository;
use App\Repository\Ticket\TicketStatusRepository;
use App\Repository\TicketRepository;
use App\Service\Notification\NotificationManager;
use App\Service\Ticket\AttachmentUploader;
use App\Service\Ticket\TicketEvent\TicketEventManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TicketController extends AbstractController
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[Route('/tickets', name: 'app_ticket_list', methods: ['GET'])]
    public function index(
        TicketRepository $ticketRepository,
        Request $request,
    ): Response {
        $filterForm = $this->createForm(TicketFilterType::class);
        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted() && $filterForm->isValid() && $filterForm->get('filter')->isClicked()) {
            $filters = $filterForm->getData();

            $cleanFilters = array_filter($filters);
            $cleanFilters = array_map(function ($filter) {
                return $filter->getId();
            }, $cleanFilters);

            return $this->redirectToRoute('app_ticket_list', $cleanFilters);
        }

        $filters = $request->query->all();
        /** @var User $user */
        $user = $this->getUser();
        if ($this->isGranted('ROLE_SUPPORT')) {
            $tickets = $ticketRepository->findWithFilters($filters);
        } else {
            $tickets = $ticketRepository->findWithFilters($filters, $user);
        }

        return $this->render('ticket/index.html.twig', [
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – Tickets',
            'boardTitle' => 'Tickets',
            'tickets' => $tickets,
            'filterForm' => $filterForm,
            'usedFilters' => $filters,
        ]);
    }

    #[Route('/tickets/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        AttachmentUploader $attachmentUploader,
        TicketEventManager $ticketEventManager,
        TicketStatusRepository $ticketStatusRepository,
        NotificationManager $notificationManager,
    ): Response {
        /** @var User $user */
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
                        $ticketEventManager->createAttachmentEvent($ticket, $user, $attachmentEntity);
                    }
                }
                $ticket->setAuthor($user);
                $ticketStatus = $ticketStatusRepository->findOneBy(['code' => 'new']);
                $ticket->setStatus($ticketStatus);
                $newTicketEvent = $ticketEventManager->createNewTicketEvent($ticket, $user);

                $entityManager->persist($ticket);
                $entityManager->flush();
                $notificationManager->process($newTicketEvent);
                $this->dispatchTicketEventCreatedEvent($newTicketEvent);
                $this->addFlash('success', 'Ticket created successfully!');

                return $this->redirectToRoute('app_ticket_view', ['id' => $ticket->getId()]);
            } catch (\Exception $exception) {
                $this->addFlash('error', 'There was an error creating the ticket.');

                return $this->redirectToRoute('app_ticket_new');
            }
        }

        return $this->render('ticket/new.html.twig', [
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – New Ticket',
            'boardTitle' => 'New ticket',
            'ticketForm' => $form,
        ]);
    }

    #[Route('/tickets/{id}', name: 'app_ticket_view', methods: ['GET'])]
    public function view(
        Ticket $ticket,
        TicketEventRepository $ticketEventRepository,
        TicketPriorityRepository $ticketPriorityRepository,
    ): Response {
        $priorities = $ticketPriorityRepository->findAll();
        $commentForm = $this->createForm(CommentType::class, options: [
            'action' => $this->generateUrl('app_ticket_add_comment', ['id' => $ticket->getId()]),
            'method' => 'POST',
        ]);
        $statusChangeForm = null;
        if ($this->isGranted('ROLE_SUPPORT')) {
            $statusChangeForm = $this->createForm(StatusChangeType::class, options: [
                'action' => $this->generateUrl('app_ticket_change_status', ['id' => $ticket->getId()]),
                'method' => 'POST',
            ]);
        }
        $assignForm = null;
        if ($this->isGranted('ROLE_ADMIN')) {
            $assignForm = $this->createForm(TicketAssignType::class, options: [
                'action' => $this->generateUrl('app_ticket_assign_worker', ['id' => $ticket->getId()]),
                'method' => 'POST',
            ]);
        }

        $ticketEvents = $ticketEventRepository->findBy(
            ['ticket' => $ticket],
            ['createdAt' => 'DESC']
        );

        if (!$this->isGranted('ROLE_SUPPORT')) {
            $ticketEvents = array_filter($ticketEvents, function ($ticketEvent) {
                return !($ticketEvent instanceof Ticket\TicketEvent\InternalCommentEvent);
            });
        }

        return $this->render('ticket/view.html.twig', [
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – Ticket: #'.$ticket->getId(),
            'boardTitle' => 'Ticket #'.$ticket->getId(),
            'ticket' => $ticket,
            'commentForm' => $commentForm,
            'ticketEvents' => $ticketEvents,
            'statusChangeForm' => $statusChangeForm,
            'assignForm' => $assignForm,
            'priorities' => $priorities,
        ]);
    }

    #[Route('/tickets/{id}/comment', name: 'app_ticket_add_comment', methods: ['POST'])]
    public function addComment(
        Ticket $ticket,
        Request $request,
        EntityManagerInterface $entityManager,
        TicketEventManager $ticketEventManager,
        AttachmentUploader $attachmentUploader,
        NotificationManager $notificationManager,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $comment = $form->get('comment')->getData();
                $isSupport = $this->isGranted('ROLE_SUPPORT');
                $isInternal = false;
                if ($form->has('is_internal')) {
                    $isInternal = $form->get('is_internal')->getData();
                }

                if ($isSupport && $isInternal) {
                    $ticketEvent = $ticketEventManager->createInternalCommentEvent($ticket, $user, $comment);
                } elseif ($isSupport) {
                    $ticketEvent = $ticketEventManager->createSupportCommentEvent($ticket, $user, $comment);
                } else {
                    $ticketEvent = $ticketEventManager->createCommentEvent($ticket, $user, $comment);
                }
                $ticketAttachmentEvents = [];
                $attachments = $form->get('attachments')->getData();
                if ($attachments) {
                    foreach ($attachments as $attachment) {
                        $fileName = $attachmentUploader->upload($attachment);
                        $attachmentEntity = new TicketAttachment();
                        $attachmentEntity->setAuthor($user);
                        $attachmentEntity->setFile($fileName);
                        $ticket->addTicketAttachment($attachmentEntity);
                        $ticketAttachmentEvents[] = $ticketEventManager->createAttachmentEvent($ticket, $user, $attachmentEntity);
                    }
                }
                $entityManager->flush();
                $this->addFlash('success', 'Comment created successfully!');
                $this->dispatchTicketEventCreatedEvent($ticketEvent);
                foreach ($ticketAttachmentEvents as $ticketAttachmentEvent) {
                    $this->dispatchTicketEventCreatedEvent($ticketAttachmentEvent);
                }
                $notificationManager->process($ticketEvent);
            } catch (NotificationException $notificationException) {
                $this->addFlash('error', 'There was an error sending the notification.');
            } catch (\Exception $exception) {
                $this->addFlash('error', 'There was an error creating the comment.');
            }
        }

        return $this->redirectToRoute('app_ticket_view', ['id' => $ticket->getId()]);
    }

    #[Route('/tickets/{id}/change-status', name: 'app_ticket_change_status', methods: ['POST'])]
    public function changeStatus(
        Ticket $ticket,
        Request $request,
        TicketEventManager $ticketEventManager,
        EntityManagerInterface $entityManager,
        NotificationManager $notificationManager,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(StatusChangeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $oldStatus = $ticket->getStatus();
                /** @var Ticket\TicketStatus $newStatus */
                $newStatus = $form->get('newStatus')->getData();
                $ticket->setStatus($newStatus);
                $entityManager->persist($ticket);
                $ticketEvent = $ticketEventManager->createStatusChangeEvent($ticket, $user, $oldStatus, $newStatus);
                if ('closed' === $newStatus->getCode()) {
                    $event = new TicketClosedEvent($ticket, $ticketEvent);
                    $this->eventDispatcher->dispatch($event);
                }
                $entityManager->flush();
                $this->addFlash('success', 'Status updated successfully!');
                $this->dispatchTicketEventCreatedEvent($ticketEvent);
                $notificationManager->process($ticketEvent);
            } catch (NotificationException $notificationException) {
                $this->addFlash('error', 'There was an error sending the notification.');
            } catch (\Exception $exception) {
                $this->addFlash('error', 'There was an error changing the status.');
            }
        }

        return $this->redirectToRoute('app_ticket_view', ['id' => $ticket->getId()]);
    }

    #[Route('/tickets/{id}/assign-worker', name: 'app_ticket_assign_worker', methods: ['POST'])]
    public function assignWorker(
        Ticket $ticket,
        Request $request,
        TicketEventManager $ticketEventManager,
        EntityManagerInterface $entityManager,
        NotificationManager $notificationManager,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(TicketAssignType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $assignedWorker = $form->get('assignedWorker')->getData();
                $ticket->setAssignedTo($assignedWorker);
                $entityManager->persist($ticket);
                $ticketEvent = $ticketEventManager->createAssignEvent($ticket, $user, $assignedWorker);
                $entityManager->flush();
                $this->addFlash('success', 'Worker assigned successfully!');
                $this->dispatchTicketEventCreatedEvent($ticketEvent);
                $notificationManager->process($ticketEvent);
            } catch (NotificationException $notificationException) {
                $this->addFlash('error', 'There was an error sending the notification.');
            } catch (\Exception $exception) {
                $this->addFlash('error', 'There was an error assigning the worker.');
            }
        }

        return $this->redirectToRoute('app_ticket_view', ['id' => $ticket->getId()]);
    }

    #[Route('/tickets/{id}/rate/{rate}', name: 'app_ticket_rate', methods: ['GET'])]
    public function rate(
        Ticket $ticket,
        int $rate,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        try {
            $ticketRating = new Ticket\TicketRating();
            $ticketRating->setTicket($ticket);
            $ticketRating->setRate($rate);
            $ticketRating->setAuthor($ticket->getAuthor());
            $ticketRating->setSourceIp($request->getClientIp());
            $entityManager->persist($ticketRating);
            $entityManager->flush();
            $this->addFlash('success', 'Thank you for your feedback!');
        } catch (\Exception $exception) {
            $this->addFlash('error', 'There was an error rating the ticket.');
        } finally {
            return $this->redirectToRoute('app_ticket_list');
        }
    }

    #[Route('/tickets/{id}/set_priority/none', name: 'app_ticket_set_priority_none', methods: ['GET'])]
    public function setPriorityToNone(
        Ticket $ticket,
        EntityManagerInterface $entityManager,
    ): Response {
        try {
            $ticket->setPriority(null);
            $entityManager->persist($ticket);
            $entityManager->flush();
            $this->addFlash('success', 'Priority changed successfully!');
        } catch (\Exception $exception) {
            $this->addFlash('error', 'There was an error changing the priority.');
        }

        return $this->redirectToRoute('app_ticket_view', ['id' => $ticket->getId()]);
    }

    #[Route('/tickets/{id}/set_priority/{ticketPriority}', name: 'app_ticket_set_priority', methods: ['GET'])]
    public function setPriority(
        Ticket $ticket,
        Ticket\TicketPriority $ticketPriority,
        EntityManagerInterface $entityManager,
    ): Response {
        try {
            $ticket->setPriority($ticketPriority);
            $entityManager->persist($ticket);
            $entityManager->flush();
            $this->addFlash('success', 'Priority changed successfully!');
        } catch (\Exception $exception) {
            $this->addFlash('error', 'There was an error changing the priority.');
        }

        return $this->redirectToRoute('app_ticket_view', ['id' => $ticket->getId()]);
    }

    private function dispatchTicketEventCreatedEvent(Ticket\TicketEvent $ticketEvent): void
    {
        $ticketEventCreatedEvent = new TicketEventCreated($ticketEvent);
        $this->eventDispatcher->dispatch($ticketEventCreatedEvent);
    }
}
