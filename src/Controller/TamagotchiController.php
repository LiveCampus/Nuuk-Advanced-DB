<?php

namespace App\Controller;

use App\Entity\Tamagotchi;
use App\Entity\User;
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
        private readonly TamagotchiRepository $tamagotchiRepository,
        private SessionService $sessionService
    )
    {}

    #[Route('/{id}', name: 'index', methods: ['GET'])]
    public function index(User $user): Response
    {
        return $this->render('tamagotchi/index.html.twig', [
            'user' => $user,
            'tamagotchis' => $this->tamagotchiRepository->findBy(['owner' => $user, 'alive' => true]),
        ]);
    }

    #[Route('/{id}/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, User $user): Response
    {
        $tamagotchi = new Tamagotchi();
        $form = $this->createForm(TamagotchiType::class, $tamagotchi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tamagotchi->setOwner($user);
            $this->tamagotchiRepository->save($tamagotchi, true);

            return $this->redirectToRoute('tamagotchi_index', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tamagotchi/new.html.twig', [
            'user' => $user,
            'tamagotchi' => $tamagotchi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/cimetière', name: 'cemetery', methods: ['GET'])]
    public function cemetery(User $user): Response
    {

        return $this->render('tamagotchi/cemetery.html.twig', [
            'user' => $user,
            'tamagotchis' => $this->tamagotchiRepository->findBy(['owner' => $user, 'alive' => false]),
        ]);
    }

    #[Route('/{id}/{name}', name: 'show', methods: ['GET'])]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    #[ParamConverter('tamagotchi', options: ['mapping' => ['name' => 'name']])]
    public function show(User $user, Tamagotchi $tamagotchi): Response
    {
        if (!$tamagotchi->isAlive()) return $this->redirectToRoute('tamagotchi_cemetery', ['id' => $user->getId()->toBase58()]);

        return $this->render('tamagotchi/show.html.twig', [
            'user' => $user,
            'tamagotchi' => $tamagotchi,
        ]);
    }

    #[Route('/{id}/{name}/manger', name: 'eat', methods: ['GET'])]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    #[ParamConverter('tamagotchi', options: ['mapping' => ['name' => 'name']])]
    public function eat(User $user, Tamagotchi $tamagotchi): RedirectResponse
    {
        $this->tryAction($tamagotchi, "eat");
        return $this->redirectToRoute('tamagotchi_show', ["id" => $user->getId()->toBase58(), "name" => $tamagotchi->getName()]);
    }

    #[Route('/{id}/{name}/boire', name: 'drink', methods: ['GET'])]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    #[ParamConverter('tamagotchi', options: ['mapping' => ['name' => 'name']])]
    public function drink(User $user, Tamagotchi $tamagotchi): RedirectResponse
    {
        $this->tryAction($tamagotchi, "drink");
        return $this->redirectToRoute('tamagotchi_show', ["id" => $user->getId()->toBase58(), "name" => $tamagotchi->getName()]);
    }

    #[Route('/{id}/{name}/dormir', name: 'sleep', methods: ['GET'])]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    #[ParamConverter('tamagotchi', options: ['mapping' => ['name' => 'name']])]
    public function sleep(User $user, Tamagotchi $tamagotchi): RedirectResponse
    {
        $this->tryAction($tamagotchi, "sleep");
        return $this->redirectToRoute('tamagotchi_show', ["id" => $user->getId()->toBase58(), "name" => $tamagotchi->getName()]);
    }

    #[Route('/{id}/{name}/jouer', name: 'play', methods: ['GET'])]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    #[ParamConverter('tamagotchi', options: ['mapping' => ['name' => 'name']])]
    public function play(User $user, Tamagotchi $tamagotchi): RedirectResponse
    {
        $this->tryAction($tamagotchi, "play");
        return $this->redirectToRoute('tamagotchi_show', ["id" => $user->getId()->toBase58(), "name" => $tamagotchi->getName()]);
    }

    #[Route('/{id}/{name}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    #[ParamConverter('tamagotchi', options: ['mapping' => ['name' => 'name']])]
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

    #[Route('/{id}/{name}', name: 'delete', methods: ['POST'])]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    #[ParamConverter('tamagotchi', options: ['mapping' => ['name' => 'name']])]
    public function delete(Request $request,  User $user, Tamagotchi $tamagotchi): Response
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
