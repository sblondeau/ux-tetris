<?php

namespace App\Tetrominos;

use App\Tetrominos\Brick;

class TetrominoJ extends Tetromino
{
    public const COLOR = '#5a65ad';
    public array $bricks = [];
    public function __construct()
    {
        $this->bricks[] = new Brick(x: 4, y: 0, color: self::COLOR);
        $this->bricks[] = new Brick(x: 4, y: 1, color: self::COLOR);
        $this->bricks[] = new Brick(x: 5, y: 1, color: self::COLOR);
        $this->bricks[] = new Brick(x: 6, y: 1, color: self::COLOR);
    }

    public function rotationCenter(): ?array
    {
        return [$this->bricks[3]->x, $this->bricks[3]->y];
    }

}            
