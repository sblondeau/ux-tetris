<?php

namespace App\Tetrominos;

use App\Tetrominos\Brick;

class TetrominoO extends Tetromino
{
    public const string COLOR = '#f7d308';
    public array $bricks = [];
    public function __construct()
    {
        $this->bricks[] = new Brick(x: 4, y: 0, color: self::COLOR);
        $this->bricks[] = new Brick(x: 4, y: 1, color: self::COLOR);
        $this->bricks[] = new Brick(x: 5, y: 0, color: self::COLOR);
        $this->bricks[] = new Brick(x: 5, y: 1, color: self::COLOR);
    }
    
    public function rotationCenter(): ?array
    {
        return null;
    }

}            
