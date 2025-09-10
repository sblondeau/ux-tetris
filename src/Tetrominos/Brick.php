<?php

namespace App\Tetrominos;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

final class Brick
{
    public function __construct(
        public int $x = 4,
        public int $y = 0,
        public string $color = 'grey',
        public bool $isCurrent = true,
    ) {}

}
