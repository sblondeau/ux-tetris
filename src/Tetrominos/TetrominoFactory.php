<?php

namespace App\Tetrominos;

use App\Tetrominos\Tetromino;
use App\Tetrominos\TetrominoI;
use App\Tetrominos\TetrominoJ;
use App\Tetrominos\TetrominoO;
use App\Tetrominos\TetrominoT;
use App\Tetrominos\TetrominoZ;

class TetrominoFactory
{
    public const TETROMINOS = [
        TetrominoI::class,
        TetrominoJ::class,
        TetrominoL::class,
        TetrominoO::class,
        TetrominoT::class,
        TetrominoS::class,
        TetrominoZ::class,
    ];
        
    public function generate(): Tetromino
    {
        $className = self::TETROMINOS[array_rand(self::TETROMINOS)];
        return new $className();
    }
}