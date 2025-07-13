<?php

/**
 * File: NotificationManager.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Service\Notification;

use App\Entity\Ticket\TicketEvent;
use App\Service\Notification\Channel\ChannelInterface;
use App\Service\Notification\Notifier\NotifierInterface;

class NotificationManager
{
    /**
     * @param iterable<NotifierInterface> $notifiers
     * @param iterable<ChannelInterface>  $channels
     */
    public function __construct(
        private iterable $notifiers,
        private iterable $channels,
    ) {
    }

    public function process(TicketEvent $event): void
    {
        foreach ($this->notifiers as $notifier) {
            if ($notifier->supports($event)) {
                $notification = $notifier->createNotification($event);

                foreach ($this->channels as $channel) {
                    $channel->send($notification);
                }
            }
        }
    }
}
