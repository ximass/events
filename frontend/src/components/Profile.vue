<template>
    <div class="profile-container">
      <h2>Editar Perfil</h2>
      <form @submit.prevent="updateProfile" class="profile-form">
        <div class="form-group">
          <label for="name">Nome:</label>
          <input
            type="text"
            id="name"
            v-model="form.name"
            required
          />
        </div>
        <div class="form-group">
          <label for="email">Email:</label>
          <input
            type="email"
            id="email"
            v-model="form.email"
            required
          />
        </div>
        <div class="form-group">
            <label for="address">Descrição</label>
            <input
              type="text"
              id="address"
              v-model="form.address"
              required
            />
        </div>
        <div class="form-group">
          <label for="password">Nova Senha:</label>
          <input
            type="password"
            id="password"
            v-model="form.password"
          />
        </div>
        <div class="form-group">
          <label for="password_confirmation">Confirmar Nova Senha:</label>
          <input
            type="password"
            id="password_confirmation"
            v-model="form.password_confirmation"
          />
        </div>
        <button type="submit" class="update-button">Atualizar</button>
      </form>
      <p v-if="successMessage" class="success">{{ successMessage }}</p>
      <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
    </div>
</template>
  
<script>
  import axios from 'axios';
  
  export default {
    name: 'Profile',
    data() {
      return {
        form: {
          name: '',
          email: '',
          address: '',
          password: '',
          password_confirmation: '',
        },
        successMessage: '',
        errorMessage: '',
      };
    },
    methods: {
      async fetchUserData() {
        try {
          const response = await axios.get('/api/user', {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('authToken')}`,
            },
          });

          this.form.name = response.data.name;
          this.form.email = response.data.email;
          this.form.address = response.data.address;
        } catch (error) {
          console.error('Erro ao obter dados do usuário:', error);
        }
      },
      async updateProfile() {
        try {
          const updateData = {
            name: this.form.name,
            email: this.form.email,
            address: this.form.address,
          };
  
          if (this.form.password) {
            updateData.password = this.form.password;
            updateData.password_confirmation = this.form.password_confirmation;
          }
  
          await axios.put('/api/user', updateData, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('authToken')}`,
            },
          });

          this.successMessage = 'Perfil atualizado com sucesso.';
          this.errorMessage = '';
          this.form.password = '';
          this.form.password_confirmation = '';
        } catch (error) {
          if (error.response && error.response.data && error.response.data.errors) {
            this.errorMessage = Object.values(error.response.data.errors).join(' ');
          } else {
            this.errorMessage = 'Erro ao atualizar perfil.';
          }
          this.successMessage = '';
        }
      },
    },
    created() {
      this.fetchUserData();
    },
  };
</script>
  
<style scoped>
  .profile-container {
    max-width: 500px;
    margin: 50px auto;
    padding: 20px;
    border-radius: 8px;
    background-color: #ffffff;
  }
  
  .profile-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
  }
  
  .form-group {
    display: flex;
    flex-direction: column;
  }
  
  .form-group label {
    margin-bottom: 5px;
  }
  
  .form-group input {
    padding: 10px;
    font-size: 16px;
  }
  
  .update-button {
    padding: 12px;
    font-size: 16px;
    color: #ffffff;
    background-color: #3498db;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .update-button:hover {
    background-color: #2980b9;
  }
  
  .success {
    color: green;
    margin-top: 10px;
    text-align: center;
  }
  
  .error {
    color: red;
    margin-top: 10px;
    text-align: center;
  }
</style>