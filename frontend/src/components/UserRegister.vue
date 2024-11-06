<template>
    <div class="register-container">
      <h2>Registrar-se</h2>
      <form @submit.prevent="handleRegister" class="register-form">
        <div class="form-group">
          <input
            v-model="form.name"
            type="text"
            placeholder="Nome"
            required
          />
        </div>
        <div class="form-group">
          <input
            v-model="form.email"
            type="email"
            placeholder="Email"
            required
          />
        </div>
        <div class="form-group">
          <input
            v-model="form.password"
            type="password"
            placeholder="Senha"
            required
          />
        </div>
        <button type="submit" class="register-button">Registrar</button>
      </form>
      <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
      <p v-if="successMessage" class="success">{{ successMessage }}</p>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name: 'UserRegister',
    data() {
      return {
        form: {
          name: '',
          email: '',
          password: '',
        },
        errorMessage: '',
        successMessage: '',
      };
    },
    methods: {
      async handleRegister() {
        try {
          await axios.post('/api/register', this.form);

          this.successMessage = 'Registro bem-sucedido! Agora você pode fazer login.';
          this.errorMessage = '';
          
          setTimeout(() => {
            this.$router.push('/login');
          }, 2000);
        } catch (error) {
          this.errorMessage = 'Erro ao registrar. Por favor, tente novamente.';
          this.successMessage = '';
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .register-container {
    max-width: 400px;
    margin: 50px auto;
    padding: 20px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    background-color: #ffffff;
  }
  
  .register-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
  }

  .form-group input {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-sizing: border-box;
    background-color: #f9f9f9;
    }
  
  .register-button {
    padding: 12px;
    font-size: 16px;
    color: #ffffff;
    background-color: #3498db;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  .register-button:hover {
    background-color: #2980b9;
  }
  
  .error {
    color: red;
    margin-top: 10px;
    font-size: 14px;
    text-align: center;
  }
  
  .success {
    color: green;
    margin-top: 10px;
    font-size: 14px;
    text-align: center;
  }
  </style>
  