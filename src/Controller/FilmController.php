<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\TemplateRenderer;
use App\Entity\Film;
use App\Repository\FilmRepository;

class FilmController
{
    private TemplateRenderer $renderer;

    public function __construct()
    {
        $this->renderer = new TemplateRenderer();
    }

    public function list(array $queryParams)
    {
        $filmRepository = new FilmRepository();
        $films = $filmRepository->findAll();

        /* $filmEntities = [];
        foreach ($films as $film) {
            $filmEntity = new Film();
            $filmEntity->setId($film['id']);
            $filmEntity->setTitle($film['title']);
            $filmEntity->setYear($film['year']);
            $filmEntity->setType($film['type']);
            $filmEntity->setSynopsis($film['synopsis']);
            $filmEntity->setDirector($film['director']);
            $filmEntity->setCreatedAt(new \DateTime($film['created_at']));
            $filmEntity->setUpdatedAt(new \DateTime($film['updated_at']));

            $filmEntities[] = $filmEntity;
        } */

        //dd($films);

        echo $this->renderer->render('film/list.html.twig', [
            'films' => $films,
        ]);

        // header('Content-Type: application/json');
        // echo json_encode($films);
    }

    public function create(): void
    {
        $message = null; // Message à afficher en cas d'erreur ou de succès
        $messageType = null; // Type de message (success, danger)
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération et validation des données
            $data = [
                'title' => !empty($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : null,
                'year' => !empty($_POST['year']) && is_numeric($_POST['year']) ? (int)$_POST['year'] : null,
                'type' => !empty($_POST['type']) ? htmlspecialchars(trim($_POST['type'])) : null,
                'director' => !empty($_POST['director']) ? htmlspecialchars(trim($_POST['director'])) : null,
                'synopsis' => !empty($_POST['synopsis']) ? htmlspecialchars(trim($_POST['synopsis'])) : null,
            ];
    
            // Validation simple des champs obligatoires
            if (array_search(null, $data, true) !== false) {
                $message = 'Veuillez remplir tous les champs correctement.';
                $messageType = 'danger';
            } else {
                // Création de l'entité Film
                $film = $this->entityMapperService->mapToEntity($data, Film::class);
    
                // Enregistrement via le repository
                $filmRepository = new FilmRepository();
                if ($filmRepository->save($film)) {
                    header('Location: /films?success=Le film "' . $film->getTitle() . '" a été ajouté avec succès.');
                    exit;
                } else {
                    $message = 'Une erreur est survenue lors de l\'enregistrement.';
                    $messageType = 'danger';
                }
            }
        }
    
        // Rendu du formulaire pour la méthode GET
        echo $this->renderer->render('film/create.html.twig', [
            'message' => $message,
            'messageType' => $messageType,
        ]);
    }
       

    public function read(array $queryParams): void
    {
        // Validation de l'ID fourni dans les paramètres
        $id = $queryParams['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            header('Location: /films?error=ID invalide ou manquant');
            exit;
        }
    
        // Récupération du film via le FilmRepository
        $filmRepository = new FilmRepository();
        $film = $filmRepository->find((int)$id);
    
        // Vérification si le film existe
        if (!$film) {
            header('Location: /films?error=Film introuvable');
            exit;
        }
    
        // Rendu du template Twig pour afficher les détails du film
        echo $this->renderer->render('film/read.html.twig', [
            'film' => $film,
            'error' => null, // Pas d'erreur à ce stade
        ]);
    }
    
    

    public function update(array $queryParams): void
    {
        // Validation de l'ID fourni dans les paramètres
        $id = $queryParams['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            header('Location: /films?error=ID invalide ou manquant');
            exit;
        }
    
        $filmRepository = new FilmRepository();
        $film = $filmRepository->find((int)$id);
    
        // Vérification si le film existe
        if (!$film) {
            header('Location: /films?error=Film introuvable');
            exit;
        }
    
        $message = null;
        $messageType = null;
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération des données POST avec validation basique
            $data = [
                'title' => $_POST['title'] ?? $film->getTitle(),
                'year' => isset($_POST['year']) && is_numeric($_POST['year']) ? (int)$_POST['year'] : $film->getYear(),
                'type' => $_POST['type'] ?? $film->getType(),
                'director' => $_POST['director'] ?? $film->getDirector(),
                'synopsis' => $_POST['synopsis'] ?? $film->getSynopsis(),
            ];
    
            if (array_search(null, $data, true) !== false) {
                $message = 'Tous les champs sont obligatoires.';
                $messageType = 'danger';
            } else {
                // Mise à jour des informations du film
                $film->setTitle($data['title']);
                $film->setYear($data['year']);
                $film->setType($data['type']);
                $film->setDirector($data['director']);
                $film->setSynopsis($data['synopsis']);
                $film->setUpdatedAt(new \DateTime());
    
                if ($filmRepository->update($film)) {
                    header('Location: /films?success=Le film "' . $film->getTitle() . '" a bien été mis à jour.');
                    exit;
                } else {
                    $message = 'Une erreur est survenue lors de la mise à jour.';
                    $messageType = 'danger';
                }
            }
        }
    
        // Affichage du formulaire avec les données actuelles du film
        echo $this->renderer->render('film/update.html.twig', [
            'film' => $film,
            'message' => $message,
            'messageType' => $messageType,
        ]);
    }
    
    

    public function delete(array $queryParams): void
    {
        // Vérification que l'ID est fourni
        $id = $queryParams['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            header('Location: /films?error=ID invalide ou manquant');
            exit;
        }
    
        // Récupération du film via le repository
        $filmRepository = new FilmRepository();
        $film = $filmRepository->find((int)$id);
    
        if (!$film) {
            header('Location: /films?error=Film introuvable');
            exit;
        }
    
        // Suppression du film
        if ($filmRepository->delete((int)$id)) {
            header('Location: /films?success=Le film a été supprimé avec succès.');
            exit;
        } else {
            header('Location: /films?error=Une erreur est survenue lors de la suppression.');
            exit;
        }
    }
    
}