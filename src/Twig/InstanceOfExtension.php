<?php

/**
 * File: InstanceOfExtension.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

class InstanceOfExtension extends AbstractExtension
{
    public function getTests()
    {
        return [
            new TwigTest('instanceof', [$this, 'instanceOf']),
        ];
    }

    public function instanceOf($var, $instance): bool
    {
        return $var instanceof $instance;
    }
}
