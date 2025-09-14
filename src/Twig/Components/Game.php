<?php

namespace App\Twig\Components;

use App\Tetrominos\Tetromino;
use App\Tetrominos\TetrominoFactory;
use App\Tetrominos\TetrominoGarbage;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent]
final class Game
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    public const WIDTH = 10;
    public const HEIGHT = 22;

    public const BASE_SCORE = 10;
    public const CHANGE_LEVEL = 3;

    public int $width = self::WIDTH;
    public int $height = self::HEIGHT;

    #[LiveProp]
    public int $score = 0;

    #[LiveProp]
    public int $otherScore = 0;

    #[LiveProp]
    public int $lines = 0;

    #[LiveProp]
    public int $level = 1;

    #[LiveProp(updateFromParent: true)]
    public ?string $player = null;

    #[LiveProp(updateFromParent: true)]
    public ?string $gameName;


    #[LiveProp]
    public ?string $otherPlayer = null;

    #[LiveProp(updateFromParent: true)]
    public bool $onPlay = true;

    /** @var Tetromino[] */
    #[LiveProp(useSerializerForHydration: true)]
    public array $tetrominos = [];

    #[LiveProp(useSerializerForHydration: true)]
    public ?Tetromino $nextTetromino = null;

    public function __construct(
        private TetrominoFactory $tetrominoFactory,
        private HubInterface $hub,
    ) {}

    public function mount(?string $gameName, ?string $player)
    {
        $this->gameName = $gameName;
        $this->player = $player;
        $this->generateTetromino();
    }





    private function generateTetromino(): void
    {
        $this->tetrominos[] = $this->nextTetromino ?? $this->tetrominoFactory->generate();
        $this->nextTetromino = $this->tetrominoFactory->generate();

        $this->gameOver();
    }



    private function addGarbageLine(int $garbage): void
    {
        if ($garbage <= 0) {
            return;
        }

        $currentTetromino = end($this->tetrominos);

        foreach ($this->tetrominos as $tetromino) {
            foreach ($tetromino->bricks as $key => $brick) {
                if ($tetromino !== $currentTetromino) {
                    $tetromino->bricks[$key]['y'] -= $garbage;
                }
            }
        }

        $garbageLines = new TetrominoGarbage($garbage);
        array_unshift($this->tetrominos, $garbageLines);
    }

    private function gameOver(): void
    {
        $currentTetromino = end($this->tetrominos);
        foreach ($currentTetromino->bricks as $brick) {
            if (is_array($brick) && !$this->isEmpty($brick['x'], $brick['y'])) {
                $this->emit('stop');
            }
        }
    }

    #[LiveAction]
    public function otherScoreUp(#[LiveArg] ?string $gameName, #[LiveArg] ?string $player, #[LiveArg] int $score, #[LiveArg] int $garbage): void
    {
    if ($gameName !== $this->gameName) {
            return;
        }

        $this->otherPlayer = $player;
        if ($player !== $this->player) {
    $this->otherScore = $score;

            $this->addGarbageLine($garbage);
        }
    }

    private function sendScoreUp(int $newLines)
    {
        if ($newLines === 1) {
            $newLines = 0;
        } elseif ($newLines >= 2  && $newLines <= 3) {
            $newLines--;
        }

        $update = new Update(
            'game',
            json_encode(
                [
                    'gameName' => $this->gameName,
                    'player' => $this->player,
                    'score' => $this->score,
                    'garbage' => $newLines,
                ]
            )
        );

        $this->hub->publish($update);
    }


    private function scoreUp()
    {
        $this->lines++;

        if ($this->lines % self::CHANGE_LEVEL === 0) {
            $this->level++;
            $this->emit('accelerate');
        }

        $this->score += $this->level * self::BASE_SCORE;
    }

    private function removeLine(): void
    {
        $previousLines = $this->lines;
        $lines = [];

        foreach ($this->tetrominos as $tetromino) {
            foreach ($tetromino->bricks as $brick) {
                $lines[$brick['y']][$brick['x']] = 1;
            }
        }

        for ($y = 0; $y < self::HEIGHT; $y++) {
            if (isset($lines[$y]) && array_sum($lines[$y]) === self::WIDTH) {
                // a priori bug css (ce met sur le coté)
                for ($x = 0; $x < self::WIDTH; $x++) {
                    $this->removeBrick($x, $y);
                }
                $this->downLine($y);
                $this->scoreUp();
            }
        }

        $newLines = $this->lines - $previousLines;
        if ($newLines > 0) {
            $this->sendScoreUp($newLines);
        }
    }

    private function removeBrick(int $x, int $y)
    {
        $searchedBrick = $this->getBrick($x, $y);
        if (null !== $searchedBrick) {
            foreach ($this->tetrominos as $tetromino) {
                foreach ($tetromino->bricks as $key => $brick) {
                    if ($brick === $searchedBrick) {
                        unset($tetromino->bricks[$key]);
                    }
                }
            }
        }
    }

    private function downLine(int $removedLine)
    {
        for ($y = $removedLine - 1; $y > 0; $y--) {
            for ($x = 0; $x < self::WIDTH; $x++) {
                $searchedBrick = $this->getBrick($x, $y);
                if (null !== $searchedBrick) {
                    foreach ($this->tetrominos as $tetromino) {
                        foreach ($tetromino->bricks as $key => $brick) {
                            if ($brick === $searchedBrick) {
                                $tetromino->bricks[$key]['y']++;
                            }
                        }
                    }
                }
            }
        }
    }

    #[LiveAction]
    public function moveLeft(): void
    {
        $this->move(-1);
    }

    #[LiveAction]
    public function moveRight(): void
    {


        $this->move(1);
    }

    private function move($direction = 1): void
    {
        $currentTetromino = end($this->tetrominos);
        foreach ($currentTetromino->bricks as $brick) {
            if (!$this->isEmpty($brick['x'] + $direction, $brick['y'])) {
                return;
            }
        }

        foreach ($currentTetromino->bricks as $key => $brick) {
            $currentTetromino->bricks[$key]['x'] += $direction;
        }
    }

    #[LiveAction]
    public function rotateLeft(): void
    {
        $this->rotate(-M_PI_2);
    }

    #[LiveAction]
    public function rotateRight(): void
    {
        $this->rotate(M_PI_2);
    }

    private function rotate($angle):  void
    {
        $currentTetromino = end($this->tetrominos);

        $position = 0;
        while($position <= 3) {
            $isRotatable = true;
            foreach ($currentTetromino->bricks as $key => $brick) {
                [$newX, $newY] = $currentTetromino->rotate($brick['x'], $brick['y'], $angle, $position);
                if (!$this->isEmpty($newX, $newY)) {
                    $isRotatable = false;
                    $position++;
                }
            }
            
            if($isRotatable) {
                foreach ($currentTetromino->bricks as $key => $brick) {
                    [$newX, $newY] = $currentTetromino->rotate($brick['x'], $brick['y'], $angle, $position);
                    $currentTetromino->bricks[$key]['x'] = $newX;
                    $currentTetromino->bricks[$key]['y'] = $newY;
                }
            }
            
            return;
        }
    }

    #[LiveListener('down')]
    #[LiveAction]
    public function moveDown(): void
    {
        $currentTetromino = end($this->tetrominos);

        foreach ($currentTetromino->bricks as $key => $brick) {
            if (is_array($brick) && !$this->isEmpty($brick['x'], $brick['y'] + 1)) {
                $this->removeLine();
                $this->generateTetromino();

                return;
            }
        }

        foreach ($currentTetromino->bricks as $key => $brick) {
            if (is_array($brick)) {
                $currentTetromino->bricks[$key]['y']++;
            }
        }
    }

    private function isEmpty(int $x, int $y): bool
    {
        $brick = $this->getBrick($x, $y);
        $currentTetromino = end($this->tetrominos);

        return (null === $brick || in_array($brick, $currentTetromino->bricks)) && $x >= 0 && $x < Game::WIDTH && $y >= 0 && $y < Game::HEIGHT;
    }

    private function getBrick(int $x, int $y): ?array
    {
        foreach ($this->tetrominos as $tetromino) {
            foreach ($tetromino->bricks as $brick) {
                if ($brick['x'] === $x && $brick['y'] === $y) {
                    return $brick;
                }
            }
        }

        return null;
    }
}
