<?php

namespace App\Tetrominos;

use App\Tetrominos\Brick;

class TetrominoZ extends Tetromino
{
    public const COLOR = '#ef2029';
    public array $bricks = [];
    public function __construct()
    {
        $this->bricks[] = new Brick(x: 4, y: 0, color: self::COLOR);
        $this->bricks[] = new Brick(x: 5, y: 0, color: self::COLOR);
        $this->bricks[] = new Brick(x: 5, y: 1, color: self::COLOR);
        $this->bricks[] = new Brick(x: 6, y: 1, color: self::COLOR);
    }

    public function rotationCenter(): ?array
    {
        return [$this->bricks[1]->x, $this->bricks[1]->y];
    }
}            
