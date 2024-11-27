<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\FilmRepository;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class FilmController
{
    private Environment $twig;

    public function __construct()
    {
        // Initialise le moteur de template Twig avec le bon chemin vers les vues
        $loader = new FilesystemLoader(__DIR__ . '/../views'); // Chemin vers les vues
        $this->twig = new Environment($loader);
    }

    public function list(array $queryParams)
    {
        $filmRepository = new FilmRepository();
        $films = $filmRepository->findAll();

        // Utilise Twig pour rendre le template avec les films
        echo $this->twig->render('list.html.twig', ['films' => $films]);
    }

    public function create()
    {
        echo "Création d'un film";
    }

    public function read(array $queryParams)
    {
        $filmRepository = new FilmRepository();
        $film = $filmRepository->find((int) $queryParams['id']);

        dd($film);
    }

    public function update()
    {
        echo "Mise à jour d'un film";
    }

    public function delete()
    {
        echo "Suppression d'un film";
    }
}