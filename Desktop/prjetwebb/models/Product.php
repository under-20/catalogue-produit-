<?php
class Product
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->ensureIdCatColumnExists();
    }

    /**
     * Ensure the id_cat column exists in the produit table
     * This is needed for the category integration
     */
    private function ensureIdCatColumnExists()
    {
        try {
            // Check if id_cat column exists in produit table
            $columnExists = false;
            $columns = $this->pdo->query("SHOW COLUMNS FROM produit")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($columns as $column) {
                if (strtolower($column) === 'id_cat') {
                    $columnExists = true;
                    break;
                }
            }

            // Add the column if it doesn't exist
            if (!$columnExists) {
                $this->pdo->exec("ALTER TABLE produit ADD COLUMN id_cat INT NULL");
                // Add foreign key if possible
                try {
                    $this->pdo->exec("
                        ALTER TABLE produit 
                        ADD CONSTRAINT fk_product_category 
                        FOREIGN KEY (id_cat) 
                        REFERENCES categories(id_cat) 
                        ON DELETE SET NULL 
                        ON UPDATE CASCADE
                    ");
                } catch (PDOException $e) {
                    // Silently skip if we can't add the foreign key constraint
                    error_log("Could not add foreign key constraint: " . $e->getMessage());
                }
            }
        } catch (PDOException $e) {
            error_log("Error checking/adding id_cat column: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        return $this->pdo->query("
            SELECT p.*, c.nom_cat as category_name
            FROM produit p
            LEFT JOIN categories c ON p.id_cat = c.id_cat
            ORDER BY p.titre
        ")->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.nom_cat as category_name  
            FROM produit p
            LEFT JOIN categories c ON p.id_cat = c.id_cat
            WHERE p.id_prod = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $sql = "
            INSERT INTO produit 
            (ref, titre, descrip, prix, quantite, etat, image, id_cat)
            VALUES (:ref, :titre, :descrip, :prix, :quantite, :etat, :image, :id_cat)
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data)
    {
        $sql = "
            UPDATE produit SET
            ref = :ref,
            titre = :titre,
            descrip = :descrip,
            prix = :prix,
            quantite = :quantite,
            etat = :etat,
            image = :image,
            id_cat = :id_cat
            WHERE id_prod = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM produit WHERE id_prod = ?");
        return $stmt->execute([$id]);
    }

    public function refExists($ref, $excludeId = null)
    {
        $sql = "SELECT id_prod FROM produit WHERE ref = ?";
        $params = [$ref];

        if ($excludeId) {
            $sql .= " AND id_prod != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function getByCategory($categoryId)
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.nom_cat as category_name
            FROM produit p
            LEFT JOIN categories c ON p.id_cat = c.id_cat
            WHERE p.id_cat = ?
            ORDER BY p.titre
        ");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all products with their categories
     */
    public function getAllWithCategories()
    {
        return $this->pdo->query("
            SELECT p.*, c.nom_cat as category_name
            FROM produit p
            LEFT JOIN categories c ON p.id_cat = c.id_cat
            ORDER BY p.titre
        ")->fetchAll();
    }

    /**
     * Count all products
     */
    public function countAll()
    {
        return $this->pdo->query("SELECT COUNT(*) FROM produit")->fetchColumn();
    }

    /**
     * Count products by category
     */
    public function countByCategory($categoryId)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM produit WHERE id_cat = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetchColumn();
    }

    /**
     * Get recent products
     */
    public function getRecent($limit = 5)
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.nom_cat as category_name
            FROM produit p
            LEFT JOIN categories c ON p.id_cat = c.id_cat
            ORDER BY p.id_prod DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get products with low stock
     */
    public function getLowStock($threshold = 5)
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.nom_cat as category_name
            FROM produit p
            LEFT JOIN categories c ON p.id_cat = c.id_cat
            WHERE p.quantite <= ?
            ORDER BY p.quantite ASC
        ");
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }

    /**
     * Get paginated products
     */
    public function getPaginated($page = 1, $perPage = 5)
    {
        $offset = ($page - 1) * $perPage;

        // Get products for current page
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.nom_cat as category_name
            FROM produit p
            LEFT JOIN categories c ON p.id_cat = c.id_cat
            ORDER BY p.titre
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll();

        // Get total count for pagination
        $totalCount = $this->countAll();

        return [
            'products' => $products,
            'totalCount' => $totalCount,
            'totalPages' => ceil($totalCount / $perPage),
            'currentPage' => $page,
            'perPage' => $perPage
        ];
    }
}