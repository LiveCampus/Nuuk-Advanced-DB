<?php

namespace App\Controller;

use App\Service\SessionService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/utilisateur', name: 'user_')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly SessionService $sessionService
    ) {}

    #[Route('/connexion', name: 'login')]
    public function index(): Response
    {
        return $this->render('user/login.html.twig');
    }

    #[Route('/connecter', name: 'connect')]
    public function connect(Request $request): RedirectResponse
    {
        $username = $request->get('username');
        $firstTamagotchi = $request->get('firstTamagotchi');

        $user = $this->userService->existingUser($username);

        if ($user) {
            $tamagotchis = $user->getTamagotchis();

            foreach ($tamagotchis as $tamagotchi) {
                if ($firstTamagotchi == $tamagotchi->getName()) {
                    $this->sessionService->setSessionObject('user', $user->getId());

                    return $this->redirectToRoute('app_home');
                }
            }
        }

        $this->sessionService->setSessionObject("flashes", ["error" => "Identifiant incorrect"]);
        return $this->redirectToRoute('user_login');
    }

    #[Route('/inscription', name: 'signup')]
    public function signup(): Response
    {
        return $this->render('user/signup.html.twig');
    }

    #[Route('/creer', name: 'create')]
    public function create(Request $request): RedirectResponse
    {
        $username = $request->get('username');
        $firstTamagotchi = $request->get('firstTamagotchi');

        if ($username && $firstTamagotchi) {
            $user = $this->userService->createUser($username, $firstTamagotchi);
            $this->sessionService->setSessionObject('user', $user);

            return $this->redirectToRoute('app_home');
        }

        $this->sessionService->setSessionObject("flashes", ["error" => "Tous les champs sont obligatoires"]);
        return $this->redirectToRoute('user_signup');
    }

    #[Route('/deconnexion', name: 'logout')]
    public function logout(): RedirectResponse
    {
        $this->sessionService->clear();

        return $this->redirectToRoute('app_home');
    }
}
