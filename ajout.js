document.getElementById("theme-toggle").addEventListener("change", function() {
    document.body.classList.toggle("dark", this.checked);
});

/* dark mood */
const toggle = document.getElementById("theme-toggle");

toggle.addEventListener("change", function () {
  if (toggle.checked) {
    document.body.classList.add("dark-mode");
  } else {
    document.body.classList.remove("dark-mode");
  }
});



/* quantite */
<input type="number" id="quantity" name="quantity" min="1" max="50" placeholder="Ex : 25" required>




const form = document.getElementById("bookForm");
const preview = document.getElementById("preview");

form.addEventListener("submit", function(e) {
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
      preview.innerHTML = `
        <h3>📚 Livre publié avec succès</h3>
        <img src="${event.target.result}" alt="Couverture du livre" style="width:150px; border-radius:10px;">
        <p><strong>Référence :</strong> ${ref}</p>
        <p><strong>Titre :</strong> ${title}</p>
        <p><strong>Description :</strong> ${description}</p>
        <p><strong>Prix :</strong> ${price} dt</p>
        <p><strong>Quantité :</strong> ${quantity}</p>
        <p><strong>État :</strong> ${etat}</p>
      `;
    };
    reader.readAsDataURL(imageFile);
  }
});
