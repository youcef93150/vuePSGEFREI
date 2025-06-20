<template>
    <div>
      <!-- Barre de navigation -->
      <nav class="navbar">
        <div class="logo">
            <router-link to="/AccueilAdmin">
                <img src="@/assets/images/psg.png" alt="PSG Logo" class="logo-img" />
            </router-link>
          
        </div>
        <div class="nav-links">
            
            <router-link to="/JoueursAdmin">Gérer les joueurs du club</router-link>
        </div>
        <button class="logout-button" @click="logout">Déconnexion</button>
      </nav>
  
      <!-- Gestion de la boutique -->
      <div class="boutique-admin">
        <center><h1>Gestion de la Boutique</h1></center>
  
        <form @submit.prevent="addProduct">
          <input v-model="newProduct.nom" type="text" placeholder="Nom du produit" required />
          <input v-model="newProduct.prix" type="number" step="0.01" placeholder="Prix (€)" required />
          <select v-model="newProduct.taille">
            <option disabled value="">Sélectionnez une taille</option>
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
          </select>
          <input v-model="newProduct.couleur" type="text" placeholder="Couleur" required />
          <textarea v-model="newProduct.description" placeholder="Description" required></textarea>
          <input v-model="newProduct.stock" type="number" placeholder="Stock" required />
          <button type="submit">Ajouter Produit</button>
        </form>
  
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nom</th>
              <th>Prix</th>
              <th>Taille</th>
              <th>Couleur</th>
              <th>Description</th>
              <th>Stock</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id">
              <td>{{ product.id }}</td>
              <td>{{ product.nom }}</td>
              <td>{{ product.prix }}</td>
              <td>{{ product.taille || '-' }}</td>
              <td>{{ product.couleur }}</td>
              <td>{{ product.description }}</td>
              <td>
                <input
                  type="number"
                  v-model.number="product.stock"
                  @change="updateStock(product.id, product.stock)"
                />
              </td>
              <td>
                <button @click="deleteProduct(product)">Supprimer</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        newProduct: {
          nom: "",
          prix: "",
          taille: "",
          couleur: "",
          description: "",
          stock: "",
        },
        products: [],
      };
    },
    methods: {
      logout() {
        localStorage.removeItem("authToken");
        this.$router.push("/login");
      },
      async addProduct() {
        try {
          const response = await axios.post("http://localhost:8000/api/produits", this.newProduct);
          alert(response.data.message);
          this.products.push({ id: response.data.id, ...this.newProduct });
          this.newProduct = { nom: "", prix: "", taille: "", couleur: "", description: "", stock: "" };
        } catch (error) {
          console.error("Erreur lors de l'ajout du produit :", error);
        }
      },
      async updateStock(productId, newStock) {
        try {
          await axios.put(`http://localhost:8000/api/produits/${productId}/stock`, { stock: newStock });
          alert("Stock mis à jour avec succès !");
        } catch (error) {
          console.error("Erreur lors de la mise à jour du stock :", error);
        }
      },
      async deleteProduct(product) {
        const confirmDelete = confirm(`Êtes-vous sûr de vouloir supprimer le produit "${product.nom}" ?`);
        if (!confirmDelete) return;
  
        try {
          await axios.delete(`http://localhost:8000/api/produits/${product.id}`);
          this.products = this.products.filter((p) => p.id !== product.id);
          alert("Produit supprimé avec succès !");
        } catch (error) {
          console.error("Erreur lors de la suppression du produit :", error);
        }
      },
      async loadProducts() {
        try {
          const response = await axios.get("http://localhost:8000/api/produits");
          this.products = response.data;
        } catch (error) {
          console.error("Erreur lors du chargement des produits :", error);
        }
      },
    },
    mounted() {
      this.loadProducts();
    },
  };
  </script>
  
  <style scoped>
  /* Barre de navigation */
  .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #002b5c;
    padding: 15px 25px;
    color: white;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  }
  
  .logo-img {
    height: 50px;
  }
  
  .nav-links {
    display: flex;
    gap: 20px;
  }
  
  .nav-links a {
    color: white;
    text-decoration: none;
    font-size: 18px;
    font-weight: 500;
  }
  
  .nav-links a:hover {
    text-decoration: underline;
    color: #c8102e;
  }
  
  .logout-button {
    background-color: #c8102e;
    border: none;
    padding: 12px 18px;
    color: white;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  
  .logout-button:hover {
    background-color: #9b0d20;
  }
  
  /* Formulaire */
  form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: space-between;
    margin-bottom: 20px;
  }
  
  form input,
  form select,
  form textarea,
  form button {
    flex: 1 1 calc(48% - 10px);
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 5px;
  }
  
  form textarea {
    height: 80px;
    resize: none;
  }
  
  form button {
    background-color: #002b5c;
    color: white;
    font-weight: bold;
    cursor: pointer;
    border: none;
    transition: background-color 0.3s;
  }
  
  form button:hover {
    background-color: #c8102e;
  }
  
  /* Table */
  table {
    width: 100%;
    border-collapse: collapse;
  }
  
  th,
  td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
  }
  
  th {
    background-color: #002b5c;
    color: white;
  }
  
  input[type="number"] {
    width: 60px;
    text-align: center;
  }
  </style>
  