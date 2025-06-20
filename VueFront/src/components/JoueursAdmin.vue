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
        <router-link to="/BoutiqueAdmin">Gérer les stocks de la boutique</router-link>
        
      </div>
      <button class="logout-button" @click="logout">Déconnexion</button>
    </nav>

    <!-- Contenu principal -->
    <div class="joueurs-container">
      <h1>Gestion des Joueurs</h1>
      <table class="joueurs-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Poste</th>
            <th>Pays d'Origine</th>
            <th>Âge</th>
            <th>Taille (cm)</th>
            <th>Poids (kg)</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="joueur in joueurs" :key="joueur.id">
            <td>{{ joueur.id }}</td>
            <td>{{ joueur.nom }}</td>
            <td>{{ joueur.prenom }}</td>
            <td>{{ joueur.poste }}</td>
            <td>{{ joueur.pays_origine }}</td>
            <td>{{ joueur.age }}</td>
            <td>{{ joueur.taille_cm }}</td>
            <td>{{ joueur.poids_kg }}</td>
            <td>
              <button @click="deleteJoueur(joueur.id)" class="delete-btn">Supprimer</button>
              <button @click="editJoueur(joueur)" class="edit-btn">Modifier</button>
            </td>
          </tr>
        </tbody>
      </table>

      <h2>Ajouter / Modifier un Joueur</h2>
      <form @submit.prevent="saveJoueur" class="joueur-form">
        <input v-model="form.nom" placeholder="Nom" required class="form-input" />
        <input v-model="form.prenom" placeholder="Prénom" required class="form-input" />

        <select v-model="form.poste" required class="form-input">
          <option value="" disabled>Choisir un poste</option>
          <option v-for="poste in postes" :key="poste" :value="poste">{{ poste }}</option>
        </select>

        <select v-model="form.pays_origine" required class="form-input">
          <option value="" disabled>Choisir un pays</option>
          <option v-for="pays in pays" :key="pays" :value="pays">{{ pays }}</option>
        </select>

        <input v-model="form.age" type="number" placeholder="Âge" required class="form-input" />
        <input v-model="form.taille_cm" type="number" placeholder="Taille (cm)" required class="form-input" />
        <input v-model="form.poids_kg" type="number" placeholder="Poids (kg)" required class="form-input" />
        <input v-model="form.numero_maillot" type="number" placeholder="Numéro de Maillot" required class="form-input" />
        <button type="submit" class="submit-btn">Enregistrer</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      joueurs: [],
      form: {
        id: null,
        nom: "",
        prenom: "",
        poste: "",
        pays_origine: "",
        age: "",
        taille_cm: "",
        poids_kg: "",
        numero_maillot: "",
        equipe: "PSG",
      },
      postes: ["Gardien", "Défenseur", "Milieu", "Attaquant"],
      pays: [
        "France", "Brésil", "Allemagne", "Argentine", "Espagne", "Italie", "Angleterre", "Portugal", "Pays-Bas", "Belgique"
        
      ],
    };
  },
  methods: {
    logout() {
      localStorage.removeItem("authToken");
      this.$router.push("/login");
    },
    fetchJoueurs() {
      axios.get("http://localhost:8000/api/joueurs").then((response) => {
        this.joueurs = response.data;
      });
    },
    saveJoueur() {
      const url = this.form.id
        ? `http://localhost:8000/api/joueurs/${this.form.id}`
        : "http://localhost:8000/api/joueurs";
      const method = this.form.id ? "put" : "post";

      axios[method](url, this.form).then(() => {
        this.fetchJoueurs();
        this.resetForm();
      });
    },
    deleteJoueur(id) {
      axios.delete(`http://localhost:8000/api/joueurs/${id}`).then(() => {
        this.fetchJoueurs();
      });
    },
    editJoueur(joueur) {
      this.form = { ...joueur };
    },
    resetForm() {
      this.form = {
        id: null,
        nom: "",
        prenom: "",
        poste: "",
        pays_origine: "",
        age: "",
        taille_cm: "",
        poids_kg: "",
        numero_maillot: "",
        equipe: "PSG",
      };
    },
  },
  mounted() {
    this.fetchJoueurs();
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

/* Couleur de fond beige pour toute la page */
.joueurs-container {
  max-width: 900px;
  margin: 0 auto;
  padding: 20px;
  text-align: center;
  background-color: #eeeeee;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Table des joueurs */
.joueurs-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

.joueurs-table th,
.joueurs-table td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: center;
}

.joueurs-table th {
  background-color: #eeeeee;
  font-weight: bold;
}

.joueurs-table tr:hover {
  background-color: #f9f9f9;
}

/* Formulaire des joueurs */
.joueur-form {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  justify-content: center;
}

.form-input {
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
  width: calc(33% - 20px);
  box-sizing: border-box;
  background-color: #fff;
}

.form-input:focus {
  border-color: #007bff;
  outline: none;
}

/* Boutons */
.submit-btn {
  padding: 10px 20px;
  font-size: 16px;
  color: white;
  background-color: #007bff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.submit-btn:hover {
  background-color: #0056b3;
}

.delete-btn {
  padding: 5px 10px;
  background-color: #ff4d4d;
  border: none;
  border-radius: 4px;
  color: white;
  cursor: pointer;
}

.delete-btn:hover {
  background-color: #e60000;
}

.edit-btn {
  padding: 5px 10px;
  background-color: #ffa500;
  border: none;
  border-radius: 4px;
  color: white;
  cursor: pointer;
}

.edit-btn:hover {
  background-color: #cc8400;
}
</style>
