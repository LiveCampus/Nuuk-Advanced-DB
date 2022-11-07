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
        $ownerName = $request->get('owner');
        $firstTamagotchi = $request->get('firstTamagotchi');

        $owner = $this->ownerService->existingOwner($ownerName);

        if ($owner) {
            $isFirstTamagotchi = $this->ownerService->goodFirstTamagotchi($owner, $firstTamagotchi);

            if ($isFirstTamagotchi) return $this->redirectToRoute('app_home');
        }

        $this->sessionService->addFlash("error", "Identifiant incorrect");
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
        $ownerName = $request->get('username');
        $firstTamagotchi = $request->get('firstTamagotchi');

        if ($ownerName && $firstTamagotchi) {
            $owner = $this->ownerService->createOwner($ownerName, $firstTamagotchi);
            $this->sessionService->setSessionObject('owner', $owner);

            return $this->redirectToRoute('app_home');
        }

        $this->sessionService->addFlash("error", "Tous les champs sont obligatoires");
        return $this->redirectToRoute('owner_signup');
    }

    #[Route('/deconnexion', name: 'logout')]
    public function logout(): RedirectResponse
    {
        $this->sessionService->clear();

        return $this->redirectToRoute('app_home');
    }
}
