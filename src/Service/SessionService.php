<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class SessionService
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack) {
        $this->requestStack = $requestStack;
    }

    /**
     * Récupère l'élément lié à la clé donnée dans la session
     *
     * @param string $name
     * @return mixed
     */
    public function getSessionObject(string $name): mixed
    {
        $session = $this->requestStack->getSession();

        return $session->get($name);
    }

    /**
     * Ajoute un élément dans la session avec sa clé pour le retrouver
     *
     * @param string $key
     * @param $value
     * @return void
     */
    public function setSessionObject(string $key, $value): void
    {
        $session = $this->requestStack->getSession();
        $session->set($key, $value);
    }

    /**
     * Supprime tous les éléments de la session
     *
     * @return void
     */
    public function clear():void
    {
        $session = $this->requestStack->getSession();
        $session->clear();
    }

    /**
     * Ajoute un message flash du type donné avec le message donné
     *
     * @param string $type
     * @param string $message
     * @return void
     */
    public function addFlash(string $type, string $message): void
    {
        $this->setSessionObject("flashes", [$type => $message]);
    }
}