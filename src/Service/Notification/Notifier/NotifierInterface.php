<?php

/**
 * File: NotifierInterface.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Service\Notification\Notifier;

use App\Dto\Notification;
use App\Entity\Ticket\TicketEvent;

interface NotifierInterface
{
    public function supports(TicketEvent $event): bool;

    public function createNotification(TicketEvent $event): Notification;
}
