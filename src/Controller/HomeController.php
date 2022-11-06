<?php

namespace App\Controller;

use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(private readonly SessionService $sessionService)
    {}

    /**
     * S'il y un utilisateur redirige vers la liste de ses tamagotchis
     * Sinon redirige vers la page de connexion
     *
     * @return RedirectResponse
     */
    #[Route('/', name: 'app_home')]
    public function index(): RedirectResponse
    {
        $user = $this->sessionService->getSessionObject('user');

        if ($user) return $this->redirectToRoute("tamagotchi_index", ["id" => $user->toBase58()]);
        else return $this->redirectToRoute("user_login");
    }
}
