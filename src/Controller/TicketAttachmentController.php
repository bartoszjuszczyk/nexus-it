<?php

namespace App\Controller;

use App\Entity\Ticket\TicketAttachment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TicketAttachmentController extends AbstractController
{
    #[Route('/attachment/download/{id}', name: 'app_attachment_download', methods: ['GET'])]
    public function index(
        TicketAttachment $attachment,
    ): Response {
        $dirPath = $this->getParameter('attachment_directory');
        $fileName = $attachment->getFile();

        return $this->file($dirPath.'/'.$fileName, $fileName);
    }
}
