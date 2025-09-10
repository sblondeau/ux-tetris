<?php

namespace App\Tetrominos;

class Tetromino
{
    public const array COLORS = ['yellow', 'blue', 'red', 'green'];

    public array $bricks = [];

    public function rotationCenters($position = 0): ?array
    {
        $centers = [
            [$this->bricks[2]['x'], $this->bricks[2]['y']],
            [$this->bricks[1]['x'], $this->bricks[1]['y']],
            [$this->bricks[0]['x'], $this->bricks[0]['y']],
            [$this->bricks[3]['x'], $this->bricks[3]['y']],
        ];

        return $centers[$position];
    }

    public function rotate(int $x, int $y, float $angle, int $position = 0): array
    {
        [$rx, $ry] = $this->rotationCenters($position);
        
        return [
            (($x - $rx) * round(cos($angle)) - ($y - $ry) * round(sin($angle))) + $rx,
            (($x - $rx) * round(sin($angle)) + ($y - $ry) * round(cos($angle))) + $ry,
        ];
    }
}
