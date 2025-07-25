<?php

/**
 * File: WordSliceExtension.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class WordSliceExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('wordSlice', [$this, 'wordSlice']),
        ];
    }

    public function wordSlice(string $text, int $charLimit = 0): string
    {
        if (mb_strlen($text, 'UTF-8') <= $charLimit) {
            return $text;
        }

        $words = preg_split('/\s+/', $text);
        $wordsToKeep = [];
        $currentLength = 0;

        foreach ($words as $word) {
            $wordLength = mb_strlen($word, 'UTF-8');
            if ($currentLength + $wordLength > $charLimit) {
                break;
            }

            $wordsToKeep[] = $word;
            $currentLength += $wordLength + 1;
        }

        if (empty($wordsToKeep)) {
            return '...';
        }

        return implode(' ', $wordsToKeep).'...';
    }
}
