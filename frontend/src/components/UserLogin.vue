<template>
  <div class="login-container">
    <h2>Login</h2>
    <form @submit.prevent="handleLogin" class="login-form">
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
      <button type="submit" class="login-button">Login</button>
    </form>
    <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'UserLogin',
  data() {
    return {
      form: {
        email: '',
        password: '',
      },
      errorMessage: '',
    };
  },
  methods: {
    async handleLogin() {
      try {
        const response = await axios.post('/api/login', this.form);
        const token = response.data.token;

        // Armazena o token no localStorage
        localStorage.setItem('authToken', token);

        // Redireciona o usuário após o login
        this.$router.push('/dashboard');
      } catch (error) {
        this.errorMessage = 'Credenciais inválidas';
      }
    },
  },
};
</script>

<style scoped>
/* Container geral da página de login */
.login-container {
  max-width: 400px;
  margin: 50px auto;
  padding: 20px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  background-color: #ffffff;
}

/* Estilo do formulário */
.login-form {
  display: flex;
  flex-direction: column;
  gap: 15px; /* Espaçamento entre os campos */
}

/* Estilo dos campos de entrada */
.form-group {
  position: relative;
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

.form-group input:focus {
  outline: none;
  border-color: #3498db;
}

/* Estilo do botão de login */
.login-button {
  padding: 12px;
  font-size: 16px;
  color: #ffffff;
  background-color: #3498db;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.login-button:hover {
  background-color: #2980b9;
}

/* Estilo da mensagem de erro */
.error {
  color: red;
  margin-top: 10px;
  font-size: 14px;
  text-align: center;
}
</style>
