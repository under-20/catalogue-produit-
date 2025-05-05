</main>
    <footer>
        <style>
        /* === CSS COMPLET DU FOOTER === */
        :root {
            --primary-dark: #27445D;
            --primary-medium: #497D74;
            --primary-light: #71BBB2;
            --background-light: #EFE9D5;
            --text-light: #FBFBFB;
        }

        footer {
            background-color: var(--primary-dark);
            color: var(--text-light);
            padding: 3rem 0 1.5rem;
            margin-top: 2rem;
            font-size: 1rem;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2.5rem;
        }

        .footer-about {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .footer-logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-light);
            text-decoration: none;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            color: var(--text-light);
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: var(--primary-light);
        }

        .footer-links h3 {
            color: var(--primary-light);
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer-links h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background-color: var(--primary-light);
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .footer-links a {
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary-light);
            padding-left: 5px;
        }

        .footer-links .fas {
            color: var(--primary-light);
            width: 1.2rem;
        }

        .copyright {
            text-align: center;
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .footer-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .footer-links {
                margin-top: 1.5rem;
            }
        }
        </style>

        <div class="footer-container">
            <div class="footer-about">
                <a href="/" class="footer-logo">Léalivre</a>
                <p>Votre librairie en ligne préférée depuis 2024.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            
            <div class="footer-links">
                <h3>Navigation</h3>
                <ul>
                    <li><a href="index.php?action=index&controller=product">Produits</a></li>
                    <li><a href="index.php?action=index&controller=category">Catégories</a></li>
                </ul>
            </div>
            
            <div class="footer-links">
                <h3>Contact</h3>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> ESPRIT – École Supérieure Privée d'Ingénierie et de Technologies  , Tunis</li>
                    <li><i class="fas fa-phone"></i> (+216) 27430670</li>
                    <li><i class="fas fa-envelope"></i> contact@bookshop.fr</li>
                </ul>
            </div>
        </div>
        
        <div class="copyright">
            <p>&copy; <?= date('Y') ?> Bookshop - Tous droits réservés</p>
        </div>
    </footer>
    
    <!-- Chargement des icônes Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="/assets/js/script.js"></script>
</body>
</html>