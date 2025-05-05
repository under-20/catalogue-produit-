<?php
class Category
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists()
    {
        try {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS categories (
                    id_cat INT AUTO_INCREMENT PRIMARY KEY,
                    nom_cat VARCHAR(100) NOT NULL,
                    slug VARCHAR(100) NOT NULL UNIQUE,
                    parentid INT NULL,
                    description TEXT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                    CONSTRAINT fk_parent 
                        FOREIGN KEY (parentid) 
                        REFERENCES categories(id_cat) 
                        ON DELETE SET NULL
                        ON UPDATE CASCADE,
                    INDEX idx_slug (slug),
                    INDEX idx_parent (parentid)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création de la table: " . $e->getMessage());
        }
    }

    public function getAll(): array
    {
        try {
            return $this->pdo->query("
                WITH RECURSIVE category_tree AS (
                    SELECT id_cat, nom_cat, slug, parentid, description, 0 AS level
                    FROM categories
                    WHERE parentid IS NULL
                    
                    UNION ALL
                    
                    SELECT c.id_cat, c.nom_cat, c.slug, c.parentid, c.description, ct.level + 1
                    FROM categories c
                    JOIN category_tree ct ON c.parentid = ct.id_cat
                )
                SELECT * FROM category_tree
                ORDER BY level, nom_cat
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des catégories: " . $e->getMessage());
        }
    }

    public function getParentCategories(): array
    {
        try {
            return $this->pdo->query("
                SELECT id_cat, nom_cat 
                FROM categories 
                WHERE parentid IS NULL 
                ORDER BY nom_cat
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des catégories parentes: " . $e->getMessage());
        }
    }

    public function getById(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM categories 
                WHERE id_cat = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération de la catégorie: " . $e->getMessage());
        }
    }

    public function create(array $data): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO categories (nom_cat, slug, parentid, description) 
                VALUES (:nom_cat, :slug, :parentid, :description)
            ");
            return $stmt->execute([
                ':nom_cat' => $data['nom_cat'],
                ':slug' => $data['slug'],
                ':parentid' => $data['parentid'] ?? null,
                ':description' => $data['description'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new Exception("Erreur lors de la création de la catégorie");
        }
    }

    public function update(int $id, array $data): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE categories 
                SET nom_cat = :nom_cat, 
                    slug = :slug, 
                    parentid = :parentid,
                    description = :description
                WHERE id_cat = :id
            ");
            return $stmt->execute([
                ':nom_cat' => $data['nom_cat'],
                ':slug' => $data['slug'],
                ':parentid' => $data['parentid'] ?? null,
                ':description' => $data['description'] ?? null,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour de la catégorie: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool
    {
        try {
            $this->pdo->beginTransaction();

            // Mise à jour des sous-catégories
            $stmt = $this->pdo->prepare("
                UPDATE categories 
                SET parentid = NULL 
                WHERE parentid = ?
            ");
            $stmt->execute([$id]);

            // Suppression de la catégorie
            $stmt = $this->pdo->prepare("
                DELETE FROM categories 
                WHERE id_cat = ?
            ");
            $result = $stmt->execute([$id]);

            $this->pdo->commit();
            return $result;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new Exception("Erreur lors de la suppression de la catégorie: " . $e->getMessage());
        }
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM categories WHERE slug = ?";
            $params = [$slug];

            if ($excludeId !== null) {
                $sql .= " AND id_cat != ?";
                $params[] = $excludeId;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la vérification du slug: " . $e->getMessage());
        }
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM categories WHERE nom_cat = ?";
            $params = [$name];

            if ($excludeId !== null) {
                $sql .= " AND id_cat != ?";
                $params[] = $excludeId;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la vérification du nom de catégorie: " . $e->getMessage());
        }
    }

    public function hasChildren(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM categories 
                WHERE parentid = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la vérification des sous-catégories: " . $e->getMessage());
        }
    }

    /**
     * Get all categories with hierarchical structure
     */
    public function getAllWithHierarchy(): array
    {
        try {
            return $this->pdo->query("
                WITH RECURSIVE category_tree AS (
                    SELECT id_cat, nom_cat, slug, parentid, description, 0 AS level, CAST(id_cat AS CHAR(200)) AS path
                    FROM categories
                    WHERE parentid IS NULL
                    
                    UNION ALL
                    
                    SELECT c.id_cat, c.nom_cat, c.slug, c.parentid, c.description, ct.level + 1, CONCAT(ct.path, ',', c.id_cat)
                    FROM categories c
                    JOIN category_tree ct ON c.parentid = ct.id_cat
                )
                SELECT * FROM category_tree
                ORDER BY path
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des catégories hiérarchiques: " . $e->getMessage());
        }
    }

    /**
     * Count all categories
     */
    public function countAll(): int
    {
        try {
            return $this->pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des catégories: " . $e->getMessage());
        }
    }

    /**
     * Check if a category exists
     */
    public function exists(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM categories WHERE id_cat = ?");
            $stmt->execute([$id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la vérification de l'existence de la catégorie: " . $e->getMessage());
        }
    }

    /**
     * Count children of a category
     */
    public function countChildren(int $id): int
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM categories WHERE parentid = ?");
            $stmt->execute([$id]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des sous-catégories: " . $e->getMessage());
        }
    }

    /**
     * Get subcategories of a category
     */
    public function getByParentId(int $parentId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM categories 
                WHERE parentid = ? 
                ORDER BY nom_cat
            ");
            $stmt->execute([$parentId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des sous-catégories: " . $e->getMessage());
        }
    }

    /**
     * Get recent categories
     */
    public function getRecent(int $limit = 5): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM categories 
                ORDER BY created_at DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des catégories récentes: " . $e->getMessage());
        }
    }

    /**
     * Get paginated categories
     */
    public function getPaginated(int $page = 1, int $perPage = 5): array
    {
        try {
            $offset = ($page - 1) * $perPage;

            // Get categories for the current page
            $stmt = $this->pdo->prepare("
                WITH RECURSIVE category_tree AS (
                    SELECT id_cat, nom_cat, slug, parentid, 0 AS level
                    FROM categories
                    WHERE parentid IS NULL
                    
                    UNION ALL
                    
                    SELECT c.id_cat, c.nom_cat, c.slug, c.parentid, ct.level + 1
                    FROM categories c
                    JOIN category_tree ct ON c.parentid = ct.id_cat
                )
                SELECT * FROM category_tree
                ORDER BY level, nom_cat
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get total count for pagination
            $totalCount = $this->countAll();

            return [
                'categories' => $categories,
                'totalCount' => $totalCount,
                'totalPages' => ceil($totalCount / $perPage),
                'currentPage' => $page,
                'perPage' => $perPage
            ];
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des catégories paginées: " . $e->getMessage());
        }
    }

    /**
     * Get products belonging to a specific category
     * @param int $categoryId The ID of the category
     * @return array Array of products in the category
     */
    public function getProducts(int $categoryId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, c.nom_cat as category_name
                FROM produit p
                LEFT JOIN categories c ON p.id_cat = c.id_cat
                WHERE p.id_cat = ?
                ORDER BY p.titre
            ");
            $stmt->execute([$categoryId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des produits de la catégorie: " . $e->getMessage());
        }
    }
}