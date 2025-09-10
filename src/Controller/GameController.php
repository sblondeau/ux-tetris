<?php

namespace App\Controller;

use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

final class GameController extends AbstractController
{
    #[Route('/', name: 'app_game')]
    public function publish(HubInterface $hub, 
        // #[MapQueryParameter] ?string $gameName=null, 
        // #[MapQueryParameter] ?string $player=null,
    ): Response
    {
        return $this->render('game/index.html.twig', [
            // 'multiplayer' => $multiplayer,
            // 'player' => $player,
            // 'gameName' => $gameName,
        ]);
    }
}
