<?php

namespace App\Twig\Components;

use Exception;
use Symfony\Config\TurboConfig;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
final class Loop
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    public const int DELAY_STEP_PERCENT = 10;

    #[LiveProp]
    public int $delay = 800;

    #[LiveProp(writable: true)]
    public bool $pause = true;

    #[LiveProp(writable: true)]
    public ?string $gameName = null;

    #[LiveProp()]
    public bool $gameOver = false;

    #[LiveProp()]
    public ?string $player = null;

    #[LiveProp()]
    public ?string $otherPlayer = null;

    #[LiveProp(writable: true, onUpdated: 'setMultiplayer')]
    public bool $multiplayer = false;

    #[LiveProp()]
    public bool $config = false;

    public function __construct(
        private HubInterface $hub,
    ) {
    }

    #[LiveAction]
    public function configuration(): void
    {
        if ($this->multiplayer === false) {
            $this->pause = false;
            $this->config = true;
        } else {
            if (!empty($this->gameName) && !empty($this->player)) {
                $this->config = true;
            $this->startMultiplayer();
            }
        }

        if (!$this->config) {
            throw new Exception('Wrong configuration');
        }
    }

    public function setMultiplayer()
    {
        $this->player = 'P' . uniqid();
        $this->otherPlayer = null;
        if ($this->multiplayer) {
            $this->gameName = uniqid();
        }
    }

    #[LiveAction]
    public function loop(): void
    {
        if ($this->onPlay()) {
            $this->emit('down');
        }
    }

    #[ExposeInTemplate]
    public function onPlay(): bool
    {
        if ($this->gameOver) {
            return false;
        }

        return !$this->pause;
    }

    #[LiveAction()]
    public function startMultiplayer(): void
    {
        if (null === $this->gameName) {
            return;
        }

        $update = new Update(
            'start',
            json_encode(
                [
                    'player' => $this->player,
                    'gameName' => $this->gameName,
                    'shouldStart' => null !== $this->otherPlayer,
                ]
            )
        );

        $this->hub->publish($update);
    }

    #[LiveListener('accelerate')]
    public function accelerate(): void
    {
        $this->delay -= ($this->delay * (self::DELAY_STEP_PERCENT / 100));
    }

    #[LiveAction]
    public function start(#[LiveArg] string $player, #[LiveArg] string $gameName, #[LiveArg] $shouldStart = false ): void
    {
        if ($gameName === $this->gameName && $player !== $this->player) {
            $this->otherPlayer = $this->player;
        }
        $this->pause = !$shouldStart;
        
    }

    #[LiveListener('stop')]
    public function stop(): void
    {
        $this->gameOver = true;
    }
}
