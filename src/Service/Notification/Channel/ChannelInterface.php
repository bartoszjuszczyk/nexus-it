<?php

/**
 * File: ChannelInterface.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Service\Notification\Channel;

use App\Dto\Notification;

interface ChannelInterface
{
    public function send(Notification $notification): void;
}
