<?php

namespace App\Tetrominos;

use App\Tetrominos\Brick;

class TetrominoI extends Tetromino
{
    public const COLOR = '#31c7ef';
    public array $bricks = [];
    public function __construct()
    {
        $this->bricks[] = new Brick(x: 4, y: 0, color: self::COLOR);
        $this->bricks[] = new Brick(x: 5, y: 0, color: self::COLOR);
        $this->bricks[] = new Brick(x: 6, y: 0, color: self::COLOR);
        $this->bricks[] = new Brick(x: 7, y: 0, color: self::COLOR);
    }
        
    public function rotationCenter(): ?array
    {
        return [$this->bricks[3]->x, $this->bricks[3]->y];
    }

}            
