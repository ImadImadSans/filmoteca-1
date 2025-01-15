<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\DatabaseConnection;
use App\Service\EntityMapper;
use App\Entity\Film;

class FilmRepository
{
    private \PDO $db;
    private EntityMapper $entityMapperService;

    public function __construct()
    {
        $this->db = DatabaseConnection::getConnection();
        $this->entityMapperService = new EntityMapper();
    }

    // Récupérer tous les films
    public function findAll(): array
    {
        $query = 'SELECT * FROM film WHERE deleted_at IS NULL ORDER BY created_at DESC';
        $stmt = $this->db->query($query);
        $films = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (!$films) {
            return [];
        }

        return $this->entityMapperService->mapToEntities($films, Film::class);
    }

    // Récupérer un film par son identifiant
    public function find(int $id): ?Film
    {
        $query = 'SELECT * FROM film WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);

        $film = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$film) {
            return null;
        }

        return $this->entityMapperService->mapToEntity($film, Film::class);
    }

    // Créer un nouveau film
    public function create(Film $film): bool
    {
        $query = 'INSERT INTO film (title, year, type, synopsis, director, created_at) 
                  VALUES (:title, :year, :type, :synopsis, :director, :createdAt)';
        $stmt = $this->db->prepare($query);

        return $stmt->execute([
            'title' => $film->getTitle(),
            'year' => $film->getYear(),
            'type' => $film->getType(),
            'synopsis' => $film->getSynopsis(),
            'director' => $film->getDirector(),
            'createdAt' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    // Mettre à jour un film
    public function update(Film $film): bool
    {
        $query = "
            UPDATE film
            SET 
                title = :title,
                year = :year,
                type = :type,
                director = :director,
                synopsis = :synopsis,
                updated_at = :updatedAt
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id' => $film->getId(),
            'title' => $film->getTitle(),
            'year' => $film->getYear(),
            'type' => $film->getType(),
            'director' => $film->getDirector(),
            'synopsis' => $film->getSynopsis(),
            'updatedAt' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    // Supprimer un film en définissant la date de suppression
    public function delete(int $id): bool
    {
        $query = 'UPDATE film SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL';
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['id' => $id]);
    }
}
