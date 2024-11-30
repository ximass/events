<template>
  <div id="app">
    <!-- Navbar -->
    <nav v-if="isAuthenticated" class="navbar">
      <div class="navbar-container">
        <h1>Gerenciador de eventos</h1>
        <ul class="navbar-menu">
          <li>
            <router-link to="/dashboard">Listagem</router-link>
          </li>
          <li>
            <router-link to="/checkin" v-if="isAdmin">Checkin</router-link>
          </li>
          <li>
            <router-link to="/profile">Perfil</router-link>
          </li>
          <li>
            <button @click="logout">Logout</button>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Conteúdo principal -->
    <main>
      <router-view></router-view>
    </main>
  </div>
</template>

<script>
import { EventBus } from './eventBus';

export default {
  name: 'App',
  data() {
    return {
      isAuthenticated: !!localStorage.getItem('authToken'),
      isAdmin: false
    };
  },
  created() {
    EventBus.on('authChanged', (status, isAdmin) => {
      this.isAuthenticated = status;
      this.isAdmin = isAdmin;
    });

    if (this.isAuthenticated) {
      const user = JSON.parse(localStorage.getItem('user'));
      this.isAdmin = user && user.is_admin;
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('authToken');
      EventBus.emit('authChanged', false)
      this.isAuthenticated = false;
      this.$router.push('/login');
    },
  },
};
</script>

<style scoped>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  text-align: center;
  color: #2c3e50;
  margin-top: 0;
}

.navbar {
  background-color: #35495e;
  color: #ffffff;
  padding: 15px 0;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}

.navbar-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 800px;
  margin: 0 auto;
  padding: 0 20px;
}

.navbar-menu {
  list-style: none;
  display: flex;
  gap: 20px;
  padding: 0;
  margin: 0;
}

.navbar-menu li {
  display: inline;
}

.navbar-menu a,
.navbar-menu button {
  color: #ffffff;
  text-decoration: none;
  font-size: 16px;
  border: none;
  background: none;
  cursor: pointer;
}

.navbar-menu button:hover,
.navbar-menu a:hover {
  text-decoration: underline;
}

main {
  padding: 20px;
  max-width: 800px;
  margin: 0 auto;
}
</style>
