<?php

namespace App\Controller;

use App\Repository\TamagotchiRepository;
use App\Service\SessionService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tamagotchi', name: 'tamagotchi_')]
class TamagotchiController extends AbstractController
{
    public function __construct(
        private readonly TamagotchiRepository   $tamagotchiRepository,
        private readonly SessionService         $sessionService
    )
    {}

    /**
     * @throws Exception
     */
    #[Route('/{ownerId}', name: 'index', methods: ['GET'])]
    public function index(int $ownerId): Response
    {
        return $this->render('tamagotchi/index.html.twig', [
            'owner' => $ownerId,
            'tamagotchis' => $this->tamagotchiRepository->findAliveTamagotchis($ownerId)
        ]);
    }

    #[Route('/{ownerId}/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(int $ownerId, Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'required' => true,
                'label' => "Son nom"
            ])
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->tamagotchiRepository->createTamagotchi($ownerId, $form->getData()["name"]);
            } catch (Exception) {
                $this->sessionService->addFlash("error", "Échec lors de la création de votre tamagotchi... Réessayez ultérieurement");
                return $this->redirectToRoute('tamagotchi_index', ["ownerId" => $ownerId]);
            }

            $this->sessionService->addFlash("success", "Votre tamagotchi a été crée avec succès");
            return $this->redirectToRoute('tamagotchi_index', ['ownerId' => $ownerId], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tamagotchi/new.html.twig', [
            'owner' => $ownerId,
            'form' => $form,
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/{ownerId}/cimetière', name: 'cemetery', methods: ['GET'])]
    public function cemetery(int $ownerId): Response
    {

        return $this->render('tamagotchi/cemetery.html.twig', [
            'owner' => $ownerId,
            'tamagotchis' => $this->tamagotchiRepository->findDeadTamagotchis($ownerId)
        ]);
    }

    #[Route('/{ownerId}/{id}-{name}', name: 'show', methods: ['GET'])]
    public function show(int $ownerId, int $id, string $name): Response
    {
        try {
            $tamagotchi = $this->tamagotchiRepository->findOneById($id, $ownerId);
        } catch (Exception) {
            $this->sessionService->addFlash('error', "Impossible de récupérer les informations du tamagotchi... Réessayez ultérieurement");
            return $this->redirectToRoute('tamagotchi_index', ['ownerId' => $ownerId]);
        }

        if (!$tamagotchi['alive']) return $this->redirectToRoute('tamagotchi_cemetery', ['ownerId' => $ownerId]);

        return $this->render('tamagotchi/show.html.twig', [
            'owner' => $ownerId,
            'tamagotchi' => $tamagotchi,
        ]);
    }

    #[Route('/{ownerId}/{id}-{name}/manger', name: 'eat', methods: ['GET'])]
    public function eat(int $ownerId, int $id, string $name): RedirectResponse
    {
        try {
            $this->tryAction($id, $ownerId, "eat");
        } catch (Exception) {
            $this->sessionService->addFlash("error", "Impossible de l'action... Réessayez ultérieurement");
        }

        return $this->redirectToRoute('tamagotchi_show', ["ownerId" => $ownerId, "id" => $id, "name" => $name]);
    }

    #[Route('/{ownerId}/{id}-{name}/boire', name: 'drink', methods: ['GET'])]
    public function drink(int $ownerId, int $id, string $name): RedirectResponse
    {
        try {
            $this->tryAction($id, $ownerId, "drink");
        } catch (Exception) {
            $this->sessionService->addFlash("error", "Impossible de l'action... Réessayez ultérieurement");
        }

        return $this->redirectToRoute('tamagotchi_show', ["ownerId" => $ownerId, "id" => $id, "name" => $name]);
    }

    #[Route('/{ownerId}/{id}-{name}/dormir', name: 'sleep', methods: ['GET'])]
    public function sleep(int $ownerId, int $id, string $name): RedirectResponse
    {
        try {
            $this->tryAction($id, $ownerId, "sleep");
        } catch (Exception) {
            $this->sessionService->addFlash("error", "Impossible de l'action... Réessayez ultérieurement");
        }

        return $this->redirectToRoute('tamagotchi_show', ["ownerId" => $ownerId, "id" => $id, "name" => $name]);
    }

    #[Route('/{ownerId}/{id}-{name}/jouer', name: 'play', methods: ['GET'])]
    public function play(int $ownerId, int $id, string $name): RedirectResponse
    {
        try {
            $this->tryAction($id, $ownerId, "play");
        } catch (Exception) {
            $this->sessionService->addFlash("error", "Impossible de l'action... Réessayez ultérieurement");
        }

        return $this->redirectToRoute('tamagotchi_show', ["ownerId" => $ownerId, "id" => $id, "name" => $name]);
    }

    #[Route('/{ownerId}/{id}-{name}/modification', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(int $ownerId, int $id, string $name, Request $request): Response
    {
        $form = $this->createFormBuilder(["name" => $name])
            ->add('name', TextType::class, [
                'required' => true,
                'label' => "Son nom"
            ])
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->tamagotchiRepository->updateTamagotchi($id, $form->getData()["name"], $ownerId);
            } catch (Exception) {
                $this->sessionService->addFlash("error", "Impossible de modifier votre tamagotchi... Réessayez ultérieurement");
                return $this->redirectToRoute('tamagotchi_index', ["ownerId" => $ownerId]);
            }

            $this->sessionService->addFlash("success", "Tamagotchi modifié avec succès");
            return $this->redirectToRoute('tamagotchi_index', ['ownerId' => $ownerId], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tamagotchi/edit.html.twig', [
            'owner' => $ownerId,
            'form' => $form,
        ]);
    }

    #[Route('/{ownerId}/{id}', name: 'delete', methods: ['POST'])]
    public function delete(int $ownerId, int $id, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$id, $request->request->get('_token'))) {
            try {
                $tamagotchi = $this->tamagotchiRepository->findOneById($id, $ownerId);
            } catch (Exception) {
                $this->sessionService->addFlash("error", "Impossible de trouver votre tamagotchi");
                return $this->redirectToRoute('tamagotchi_index', ["ownerId" => $ownerId]);
            }


            if (!$tamagotchi["first"]) {
                try {
                    $this->tamagotchiRepository->removeTamagotchi($id, $ownerId);
                } catch (Exception) {
                    $this->sessionService->setSessionObject("flashes", ['error' => 'Impossible de supprimer votre tamagotchi... Réessayez ultérieurement']);
                }
            } else {
                $this->sessionService->setSessionObject("flashes", ['error' => 'Impossible de supprimer votre premier tamagotchi']);
            }
        }

        return $this->redirectToRoute('tamagotchi_index', ['ownerId' => $ownerId], Response::HTTP_SEE_OTHER);
    }

    /**
     * @throws Exception
     */
    private function tryAction(int $id, int $ownerId, string $action)
    {
        switch ($action) {
            case "eat":
                $action = $this->tamagotchiRepository->eat($id, $ownerId);
                break;
            case "drink":
                $action = $this->tamagotchiRepository->drink($id, $ownerId);
                break;
            case "sleep":
                $action = $this->tamagotchiRepository->sleep($id, $ownerId);
                break;
            case "play":
                $action = $this->tamagotchiRepository->play($id, $ownerId);
                break;
        }

        $tamagotchi = $this->tamagotchiRepository->findOneById($id, $ownerId);

        if ($action) {
            if ($tamagotchi["hunger"] == 0 || $tamagotchi["thirst"] == 0 || $tamagotchi["sleep"] == 0 || $tamagotchi["boredom"] == 0) {
                $this->tamagotchiRepository->killTamagotchi($id, $ownerId);
                $this->sessionService->setSessionObject("flashes", ["error" => "Votre tamagotchis est mort"]);
            }
        } else {
            $this->sessionService->setSessionObject("flashes", ["error" => "Impossible de faire cette action"]);
        }
    }
}
