<?php

/**
 * File: EventType.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Config\Event;

enum EventType: int
{
    case COMMENT = 1;
    case INTERNAL_COMMENT = 2;
    case SUPPORT_COMMENT = 3;
    case STATUS_CHANGE = 4;
    case ASSIGN = 5;
    case ATTACHMENT = 6;
}
