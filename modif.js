document.getElementById("theme-toggle").addEventListener("change", function() {
    document.body.classList.toggle("dark-mode", this.checked);
  });
  
  // Recherche par référence
  document.getElementById("searchForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const ref = document.getElementById("search-ref").value;
  
    // Simuler une recherche dans la base de données (ou un tableau d'exemple)
    const books = [
      {
        ref: "REF-1234",
        title: "L'Étranger",
        description: "Un livre emblématique de la littérature française.",
        price: "27.90",
        quantity: "25",
        etat: "stock",
        image: "https://example.com/image1.jpg"
      },
      // Ajoute d'autres livres ici
    ];
  
    // Chercher le livre correspondant à la référence
    const book = books.find(b => b.ref === ref);
  
    if (book) {
      // Remplir les champs du formulaire avec les données du livre trouvé
      document.getElementById("ref").value = book.ref;
      document.getElementById("title").value = book.title;
      document.getElementById("description").value = book.description;
      document.getElementById("price").value = book.price;
      document.getElementById("quantity").value = book.quantity;
      document.getElementById("etat").value = book.etat;
  
      // Afficher le formulaire de modification
      document.getElementById("bookForm").style.display = "block";
  
      // Afficher un aperçu de l'image (si disponible)
      const preview = document.getElementById("preview");
      preview.innerHTML = `<h3>Référence trouvée</h3><img src="${book.image}" alt="Couverture du livre" style="width:150px; border-radius:10px;">`;
  
    } else {
      alert("Livre non trouvé.");
    }
  });
  
  // Gérer la soumission du formulaire de modification
  document.getElementById("bookForm").addEventListener("submit", function(e) {
    e.preventDefault();
  
    const ref = document.getElementById("ref").value;
    const title = document.getElementById("title").value;
    const description = document.getElementById("description").value;
    const price = document.getElementById("price").value;
    const quantity = document.getElementById("quantity").value;
    const etat = document.getElementById("etat").value;
    const imageInput = document.getElementById("image");
    const imageFile = imageInput.files[0];
  
    if (imageFile) {
      const reader = new FileReader();
      reader.onload = function(event) {
        alert("Livre modifié avec succès");
        // Affichage d'un message de succès ou enregistrement des modifications en base de données
      };
      reader.readAsDataURL(imageFile);
    } else {
      alert("Modification réussie sans nouvelle image");
    }
  });
  