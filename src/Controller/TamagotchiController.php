<?php

namespace App\Controller;

use App\Entity\Tamagotchi;
use App\Form\TamagotchiType;
use App\Repository\TamagotchiRepository;
use App\Service\SessionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/{ownerId}', name: 'index', methods: ['GET'])]
    public function index(int $ownerId): Response
    {
        $tamagotchis = $this->tamagotchiRepository->findAliveTamagotchis($ownerId);
        dd($tamagotchis);
        return $this->render('tamagotchi/index.html.twig', [
            'owner' => $ownerId,
            'tamagotchis' => $tamagotchis
        ]);
    }

    #[Route('/{ownerId}/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $ownerId): Response
    {
        $tamagotchi = new Tamagotchi();
        $form = $this->createForm(TamagotchiType::class, $tamagotchi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tamagotchi->setOwner($ownerId);
            $this->tamagotchiRepository->save($tamagotchi, true);

            return $this->redirectToRoute('tamagotchi_index', ['id' => $ownerId], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tamagotchi/new.html.twig', [
            'user' => $ownerId,
            'tamagotchi' => $tamagotchi,
            'form' => $form,
        ]);
    }

    #[Route('/{ownerId}/cimetière', name: 'cemetery', methods: ['GET'])]
    public function cemetery(int $ownerId): Response
    {

        return $this->render('tamagotchi/cemetery.html.twig', [
            'user' => $ownerId,
            'tamagotchis' => $this->tamagotchiRepository->findDeadTamagotchis($ownerId)
        ]);
    }

    #[Route('/{ownerId}/{name}', name: 'show', methods: ['GET'])]
    public function show(int $ownerId, string $name): Response
    {
        if (!$tamagotchi->isAlive()) return $this->redirectToRoute('tamagotchi_cemetery', ['id' => $user->getId()->toBase58()]);

        return $this->render('tamagotchi/show.html.twig', [
            'user' => $ownerId,
            'tamagotchi' => $tamagotchi,
        ]);
    }

    #[Route('/{ownerId}/{name}/manger', name: 'eat', methods: ['GET'])]
    public function eat(int $ownerId, string $name): RedirectResponse
    {
        $this->tryAction($tamagotchi, "eat");
        return $this->redirectToRoute('tamagotchi_show', ["id" => $user->getId()->toBase58(), "name" => $tamagotchi->getName()]);
    }

    #[Route('/{id}/{name}/boire', name: 'drink', methods: ['GET'])]
    public function drink(User $user, Tamagotchi $tamagotchi): RedirectResponse
    {
        $this->tryAction($tamagotchi, "drink");
        return $this->redirectToRoute('tamagotchi_show', ["id" => $user->getId()->toBase58(), "name" => $tamagotchi->getName()]);
    }

    #[Route('/{id}/{name}/dormir', name: 'sleep', methods: ['GET'])]
    public function sleep(User $user, Tamagotchi $tamagotchi): RedirectResponse
    {
        $this->tryAction($tamagotchi, "sleep");
        return $this->redirectToRoute('tamagotchi_show', ["id" => $user->getId()->toBase58(), "name" => $tamagotchi->getName()]);
    }

    #[Route('/{id}/{name}/jouer', name: 'play', methods: ['GET'])]
    public function play(User $user, Tamagotchi $tamagotchi): RedirectResponse
    {
        $this->tryAction($tamagotchi, "play");
        return $this->redirectToRoute('tamagotchi_show', ["id" => $user->getId()->toBase58(), "name" => $tamagotchi->getName()]);
    }

    #[Route('/{id}/{name}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,  User $user, Tamagotchi $tamagotchi): Response
    {
        $form = $this->createForm(TamagotchiType::class, $tamagotchi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tamagotchiRepository->save($tamagotchi, true);

            return $this->redirectToRoute('tamagotchi_index', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tamagotchi/edit.html.twig', [
            'user' => $user,
            'tamagotchi' => $tamagotchi,
            'form' => $form,
        ]);
    }

    #[Route('/{ownerId}/{name}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request,  int $ownerId, string $name): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tamagotchi->getId(), $request->request->get('_token'))) {
            if (!$tamagotchi->isFirst()) {
                $this->tamagotchiRepository->remove($tamagotchi, true);
            } else {
                $this->sessionService->setSessionObject("flashes", ['error' => 'Impossible de supprimer votre premier tamagotchi']);
            }
        }

        return $this->redirectToRoute('tamagotchi_index', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
    }

    private function tryAction(Tamagotchi $tamagotchi, string $action)
    {
        switch ($action) {
            case "eat":
                $action = $tamagotchi->eat();
                break;
            case "drink":
                $action = $tamagotchi->drink();
                break;
            case "sleep":
                $action = $tamagotchi->goSleep();
                break;
            case "play":
                $action = $tamagotchi->play();
                break;
        }

        if ($action) {
            if ($tamagotchi->getHunger() == 0 || $tamagotchi->getThirst() == 0 || $tamagotchi->getSleep() == 0 || $tamagotchi->getBoredom() == 0) {
                $tamagotchi
                    ->setAlive(false)
                    ->setDiedOn(new \DateTimeImmutable("now", new \DateTimeZone("Europe/Paris")))
                ;
                $this->sessionService->setSessionObject("flashes", ["error" => "Votre tamagotchis est mort"]);
            }
            $this->tamagotchiRepository->save($tamagotchi, true);
        } else {
            $this->sessionService->setSessionObject("flashes", ["error" => "Impossible de faire cette action"]);
        }
    }
}
