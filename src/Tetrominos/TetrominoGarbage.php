<?php

namespace App\Tetrominos;

use App\Tetrominos\Brick;
use App\Twig\Components\Game;

class TetrominoGarbage extends Tetromino
{
    public const COLOR = 'grey';
    public array $bricks = [];
    public function __construct(int $lines)
    {
        // set a column with a hole
        $cols = range(0, Game::WIDTH -1);
        $randomCol = rand(0, Game::WIDTH -4);
        unset($cols[$randomCol]);

        // set lines (with holes at the same place)
        for($y = 1 ; $y <= $lines; $y++) {
            foreach($cols as $col) {
                $this->bricks[] = new Brick(x: $col, y: Game::HEIGHT - $y, color: self::COLOR);
            }
        }
    }
}            
