<?php
require_once __DIR__ . '/../models/Category.php';

class AdminCategoryController
{
    private $categoryModel;
    private $activePage = 'categories'; // For active menu item highlighting

    public function __construct()
    {
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $this->categoryModel = new Category($database->getConnection());
    }

    /**
     * List all categories for administration
     */
    public function list()
    {
        $activePage = $this->activePage;

        // Get pagination parameters
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page = max(1, $page); // Ensure page is at least 1
        $perPage = 5; // Show 5 items per page

        // Get paginated data
        $data = $this->categoryModel->getPaginated($page, $perPage);

        // Extract data for the view
        $categories = $data['categories'];
        $pagination = [
            'currentPage' => $data['currentPage'],
            'totalPages' => $data['totalPages'],
            'totalCount' => $data['totalCount'],
            'perPage' => $data['perPage']
        ];

        // Load the admin categories list view
        require __DIR__ . '/../views/admin/categories/list.php';
    }

    /**
     * Show the form to add a new category
     */
    public function create()
    {
        $activePage = $this->activePage;

        // Fetch all categories to populate the parent dropdown
        $categories = $this->categoryModel->getAll();

        // Pass any validation errors that might exist
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        // Load the add category view
        require __DIR__ . '/../views/admin/categories/add.php';
    }

    /**
     * Generate a URL-friendly slug from a string
     */
    private function generateSlug($text)
    {
        // Character map for accented characters
        $char_map = [
            // Latin
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'AE',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ð' => 'D',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ő' => 'O',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ű' => 'U',
            'Ý' => 'Y',
            'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'æ' => 'ae',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'd',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ő' => 'o',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'ű' => 'u',
            'ý' => 'y',
            'þ' => 'th',
            'ÿ' => 'y',

            // French
            'œ' => 'oe',
            'Œ' => 'OE',

            // Other special characters
            '&' => 'et',
            '@' => 'at',
            '#' => 'hash',
            '$' => 'dollar',
            '%' => 'percent'
        ];

        // Replace accented characters with ASCII equivalents
        $text = strtr($text, $char_map);

        // Convert to lowercase
        $text = mb_strtolower($text, 'UTF-8');

        // Replace non-alphanumeric characters with hyphens
        $text = preg_replace('/[^a-z0-9]+/i', '-', $text);

        // Remove duplicate hyphens
        $text = preg_replace('/-+/', '-', $text);

        // Trim hyphens from beginning and end
        return trim($text, '-');
    }

    /**
     * Store a new category in the database
     */
    public function store()
    {
        $errors = [];

        // Get and validate data
        $data = [
            'nom_cat' => trim($_POST['nom_cat'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'parentid' => !empty($_POST['parentid']) ? (int) $_POST['parentid'] : null
        ];

        // Validation
        if (empty($data['nom_cat'])) {
            $errors[] = "Le nom de la catégorie est obligatoire";
        } else if ($this->categoryModel->nameExists($data['nom_cat'])) {
            $errors[] = "Une catégorie avec ce nom existe déjà";
        }

        // Validate parent category if selected
        if (!empty($data['parentid'])) {
            if (!$this->categoryModel->exists($data['parentid'])) {
                $errors[] = "La catégorie parente sélectionnée n'existe pas";
            }
        }

        if (empty($errors)) {
            // Generate slug from name
            $data['slug'] = $this->generateSlug($data['nom_cat']);

            // Check if slug exists and make it unique if necessary
            $baseSlug = $data['slug'];
            $counter = 1;
            while ($this->categoryModel->slugExists($data['slug'])) {
                $data['slug'] = $baseSlug . '-' . $counter++;
            }

            if ($this->categoryModel->create($data)) {
                $_SESSION['message'] = "Catégorie ajoutée avec succès";
                header('Location: index.php?action=list&controller=admin_category');
                exit;
            } else {
                $errors[] = "Erreur lors de la création de la catégorie";
            }
        }

        // If errors occurred, store them and redirect back to form
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        header('Location: index.php?action=create&controller=admin_category');
        exit;
    }

    /**
     * Show the form to edit an existing category
     */
    public function edit($id)
    {
        $activePage = $this->activePage;

        // Get the category
        $category = $this->categoryModel->getById($id);
        if (!$category) {
            $_SESSION['error'] = "Catégorie introuvable";
            header('Location: index.php?action=list&controller=admin_category');
            exit;
        }

        // Fetch all categories to populate the parent dropdown
        $categories = $this->categoryModel->getAll();

        // Get parent category name if applicable
        $parentName = null;
        if (!empty($category['parentid'])) {
            $parentCategory = $this->categoryModel->getById($category['parentid']);
            if ($parentCategory) {
                $parentName = $parentCategory['nom_cat'];
            }
        }

        // Count child categories
        $childCount = $this->categoryModel->countChildren($id);

        // Count products in this category
        $products = $this->categoryModel->getProducts($id);
        $productCount = count($products);

        // Pass any validation errors that might exist
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        // Load the edit category view
        require __DIR__ . '/../views/admin/categories/edit.php';
    }

    /**
     * Update an existing category in the database
     */
    public function update($id)
    {
        $errors = [];

        // Verify category exists
        $category = $this->categoryModel->getById($id);
        if (!$category) {
            $_SESSION['error'] = "Catégorie introuvable";
            header('Location: index.php?action=list&controller=admin_category');
            exit;
        }

        // Get and validate data
        $data = [
            'nom_cat' => trim($_POST['nom_cat'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'parentid' => !empty($_POST['parentid']) ? (int) $_POST['parentid'] : null
        ];

        // Validation
        if (empty($data['nom_cat'])) {
            $errors[] = "Le nom de la catégorie est obligatoire";
        } else if ($this->categoryModel->nameExists($data['nom_cat'], $id)) {
            $errors[] = "Une catégorie avec ce nom existe déjà";
        }

        // Validate parent category if selected
        if (!empty($data['parentid'])) {
            if (!$this->categoryModel->exists($data['parentid'])) {
                $errors[] = "La catégorie parente sélectionnée n'existe pas";
            }

            // Prevent circular references
            if ($data['parentid'] == $id) {
                $errors[] = "Une catégorie ne peut pas être sa propre catégorie parent";
            }
        }

        if (empty($errors)) {
            // Generate slug from name
            $data['slug'] = $this->generateSlug($data['nom_cat']);

            // Check if slug exists and make it unique if necessary
            if ($data['slug'] !== $category['slug']) {
                $baseSlug = $data['slug'];
                $counter = 1;
                while ($this->categoryModel->slugExists($data['slug'], $id)) {
                    $data['slug'] = $baseSlug . '-' . $counter++;
                }
            }

            if ($this->categoryModel->update($id, $data)) {
                $_SESSION['message'] = "Catégorie modifiée avec succès";
                header('Location: index.php?action=list&controller=admin_category');
                exit;
            } else {
                $errors[] = "Erreur lors de la mise à jour de la catégorie";
            }
        }

        // If errors occurred, store them and redirect back to form
        $_SESSION['errors'] = $errors;
        header('Location: index.php?action=edit&controller=admin_category&id=' . $id);
        exit;
    }

    /**
     * Display category details
     */
    public function view($id)
    {
        $activePage = $this->activePage;

        // Get the category
        $category = $this->categoryModel->getById($id);
        if (!$category) {
            $_SESSION['error'] = "Catégorie introuvable";
            header('Location: index.php?action=list&controller=admin_category');
            exit;
        }

        // Get products in this category
        $products = $this->categoryModel->getProducts($id);

        // Load the view category view
        require __DIR__ . '/../views/admin/categories/list.php';
    }

    /**
     * Delete a category
     */
    public function delete($id)
    {
        // Verify category exists
        $category = $this->categoryModel->getById($id);
        if (!$category) {
            $_SESSION['error'] = "Catégorie introuvable";
            header('Location: index.php?action=list&controller=admin_category');
            exit;
        }

        // Check if category has products
        $products = $this->categoryModel->getProducts($id);
        if (!empty($products)) {
            $_SESSION['error'] = "Cette catégorie contient des produits et ne peut pas être supprimée. Veuillez d'abord supprimer ou réaffecter les produits.";
            header('Location: index.php?action=list&controller=admin_category');
            exit;
        }

        if ($this->categoryModel->delete($id)) {
            $_SESSION['message'] = "Catégorie supprimée avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression de la catégorie";
        }

        header('Location: index.php?action=list&controller=admin_category');
        exit;
    }
}