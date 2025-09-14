<?php

namespace App\Tetrominos;

use App\Tetrominos\Brick;

class TetrominoS extends Tetromino
{
    public const COLOR = '#42b642';
    public array $bricks = [];

    public function __construct()
    {
        $this->bricks[] = new Brick(x: 5, y: 0, color: self::COLOR);
        $this->bricks[] = new Brick(x: 6, y: 0, color: self::COLOR);
        $this->bricks[] = new Brick(x: 4, y: 1, color: self::COLOR);
        $this->bricks[] = new Brick(x: 5, y: 1, color: self::COLOR);
    }

    public function getRotationCenter()
    {
        return [
            $this->bricks[0]->x,
            $this->bricks[0]->y,
        ];
    }

}            
