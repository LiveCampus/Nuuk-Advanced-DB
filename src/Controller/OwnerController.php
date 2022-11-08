<?php

namespace App\Controller;

use App\Repository\OwnerRepository;
use App\Repository\TamagotchiRepository;
use App\Service\SessionService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/utilisateur', name: 'owner_')]
class OwnerController extends AbstractController
{
    public function __construct(
        private readonly SessionService         $sessionService,
        private readonly OwnerRepository        $ownerRepository,
        private readonly TamagotchiRepository   $tamagotchiRepository,
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

        try {
            $owner = $this->ownerRepository->getIdByName($ownerName);
        } catch (Exception) {
            $this->sessionService->addFlash("error", "Une erreur est survenu... Réessayer ultérieurement");
            return $this->redirectToRoute('owner_login');
        }

        if ($owner) {
            try {
                $isFirstTamagotchi = (bool)$this->tamagotchiRepository->findFirstTamagotchiByName($owner, $firstTamagotchi);
            } catch (Exception) {
                $this->sessionService->addFlash("error", "Une erreur est survenu... Réessayer ultérieurement");
                return $this->redirectToRoute('owner_login');
            }

            if ($isFirstTamagotchi) {
                $this->sessionService->setSessionObject('owner', $owner);
                return $this->redirectToRoute('app_home');
            }
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
            try {
                $owner = $this->ownerRepository->createOwnerWithFirstTamagotchi($ownerName, $firstTamagotchi);
            } catch (Exception) {
                $this->sessionService->addFlash("error", "Une erreur est survenue... Réessayer ultérieurement");
                return $this->redirectToRoute('owner_signup');
            }

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
