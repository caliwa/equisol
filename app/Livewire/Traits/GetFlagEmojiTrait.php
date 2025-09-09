<?php

namespace App\Livewire\Traits;

trait GetFlagEmojiTrait
{
    function getFlagEmoji(string $countryCode): string
    {
        if (strlen($countryCode) !== 2) {
            return '';
        }

        $codePoints = array_map(
            fn($char) => 127397 + ord(strtoupper($char)),
            str_split($countryCode)
        );

        return mb_convert_encoding('&#' . implode(';&#', $codePoints) . ';', 'UTF-8', 'HTML-ENTITIES');
    }
}
