<?php

namespace App\Controller;

use App\Service\SessionService;
use App\Service\OwnerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/utilisateur', name: 'owner_')]
class OwnerController extends AbstractController
{
    public function __construct(
        private readonly OwnerService   $ownerService,
        private readonly SessionService $sessionService
    ) {}

    #[Route('/connexion', name: 'login')]
    public function index(): Response
    {
        return $this->render('owner/login.html.twig');
    }

    #[Route('/connecter', name: 'connect')]
    public function connect(Request $request): RedirectResponse
    {
        $username = $request->get('owner');
        $firstTamagotchi = $request->get('firstTamagotchi');

        $user = $this->ownerService->existingUser($username);

        if ($user) {
            $tamagotchis = $user->getTamagotchis();

            foreach ($tamagotchis as $tamagotchi) {
                if ($firstTamagotchi == $tamagotchi->getName()) {
                    $this->sessionService->setSessionObject('owner', $user->getId());

                    return $this->redirectToRoute('app_home');
                }
            }
        }

        $this->sessionService->setSessionObject("flashes", ["error" => "Identifiant incorrect"]);
        return $this->redirectToRoute('owner_login');
    }

    #[Route('/inscription', name: 'signup')]
    public function signup(): Response
    {
        return $this->render('owner/signup.html.twig');
    }

    #[Route('/creer', name: 'create')]
    public function create(Request $request): RedirectResponse
    {
        $username = $request->get('username');
        $firstTamagotchi = $request->get('firstTamagotchi');

        if ($username && $firstTamagotchi) {
            $user = $this->ownerService->createUser($username, $firstTamagotchi);
            $this->sessionService->setSessionObject('user', $user);

            return $this->redirectToRoute('app_home');
        }

        $this->sessionService->setSessionObject("flashes", ["error" => "Tous les champs sont obligatoires"]);
        return $this->redirectToRoute('owner_signup');
    }

    #[Route('/deconnexion', name: 'logout')]
    public function logout(): RedirectResponse
    {
        $this->sessionService->clear();

        return $this->redirectToRoute('app_home');
    }
}
