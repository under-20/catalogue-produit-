<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookshop Administration</title>
    <style>
        /* === CSS COMPLET DU HEADER === */
        :root {
            --primary-dark: #27445D;
            --primary-medium: #497D74;
            --primary-light: #71BBB2;
            --background-light: #EFE9D5;
            --text-light: #FBFBFB;
            --background-white: #FBFBFB;
            --error-color: #dc3545;
            --success-color: #28a745;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--background-light);
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: var(--primary-dark);
            color: var(--text-light);
            padding: 1.2rem 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-light);
        }

        .logo-icon {
            font-size: 1.8rem;
            transition: transform 0.3s ease;
        }

        .logo:hover .logo-icon {
            transform: rotate(15deg);
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        nav a {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem;
            padding: 0.5rem 0;
            position: relative;
            transition: all 0.3s ease;
        }

        nav a:hover {
            color: var(--primary-light);
        }

        nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary-light);
            transition: width 0.3s ease;
        }

        nav a:hover::after {
            width: 100%;
        }

        nav a.active {
            color: var(--primary-light);
        }

        nav a.active::after {
            width: 100%;
        }

        /* Alert messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Admin specific header styling */
        .admin-badge {
            background-color: var(--primary-light);
            color: white;
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-left: 0.5rem;
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            nav ul {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <header>
        <div class="header-container">
            <a href="/bookshop/public/index.php?action=dashboard&controller=admin"
                class="logo">
                <i class="fas fa-book-open logo-icon"></i>
                <span>Bookshop</span>
                <span class="admin-badge">Admin</span>
            </a>
            <nav>
                <ul>
                    <li><a href="/bookshop/public/index.php?action=dashboard&controller=admin"
                            class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
                            <i class="fas fa-tachometer-alt"></i> Tableau de
                            bord
                        </a></li>
                    <li><a href="/bookshop/public/index.php?action=list&controller=admin_product"
                            class="<?= $activePage === 'products' ? 'active' : '' ?>">
                            <i class="fas fa-book"></i> Produits
                        </a></li>
                    <li><a href="/bookshop/public/index.php?action=list&controller=admin_category"
                            class="<?= $activePage === 'categories' ? 'active' : '' ?>">
                            <i class="fas fa-tags"></i> Catégories
                        </a></li>
                    <li><a href="/bookshop/public/index.php"
                            class="<?= $activePage === 'site' ? 'active' : '' ?>">
                            <i class="fas fa-globe"></i> Voir le site
                        </a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>