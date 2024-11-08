<template>
  <div class="dashboard-container">
    <h2>Dashboard de eventos</h2>
    
    <!-- Filtro de Eventos -->
    <div class="filter-container">
      <input
        v-model="searchTerm"
        type="text"
        placeholder="Filtrar eventos por nome"
        @input="filterEvents"
      />
    </div>
    
    <!-- Lista de Eventos -->
    <div class="events-list">
      <div v-if="filteredEvents.length === 0" class="no-events">
        Nenhum evento encontrado.
      </div>
      <div v-for="event in filteredEvents" :key="event.id" class="event-card">
        <h3>{{ event.title }}</h3>
        <p>{{ event.description }}</p>
        <p><strong>Data:</strong> {{ formatDate(event.start_date) }} - {{ formatDate(event.end_date) }}</p>
        <p><strong>Local:</strong> {{ event.local }}</p>
        
        <div v-if="event.isRegistered">
          <button @click="unregisterFromEvent(event.id)" class="unregister-button">
            Cancelar inscrição
          </button>
        </div>
        <div v-else>
          <button @click="registerForEvent(event.id)" class="register-button">
            Inscrever-se
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Dashboard',
  data() {
    return {
      events: [],
      filteredEvents: [],
      searchTerm: '',
    };
  },
  methods: {
    async fetchEvents() {
      try {
        // Obter a lista de eventos
        const eventsResponse = await axios.get('/api/events');
        this.events = eventsResponse.data;

        // Obter as inscrições do usuário
        const registrationsResponse = await axios.get('/api/registrations', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('authToken')}`,
          },
        });
        const userRegistrations = registrationsResponse.data;

        // Marcar eventos inscritos
        this.events = this.events.map((event) => ({
          ...event,
          isRegistered: userRegistrations.some(
            (registration) => registration.event_id === event.id
          ),
        }));

        // Inicializar os eventos filtrados
        this.filteredEvents = this.events;
      } catch (error) {
        console.error('Erro ao buscar eventos ou inscrições:', error);
      }
    },
    filterEvents() {
      this.filteredEvents = this.events.filter((event) =>
        event.title.toLowerCase().includes(this.searchTerm.toLowerCase())
      );
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('pt-BR', options);
    },
    async registerForEvent(eventId) {
      try {
        await axios.post(
          `/api/events/${eventId}/register`,
          {},
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('authToken')}`,
            },
          }
        );
        this.events = this.events.map((event) =>
          event.id === eventId ? { ...event, isRegistered: true } : event
        );
        this.filterEvents();
      } catch (error) {
        console.error('Erro ao se inscrever no evento:', error);
      }
    },
    async unregisterFromEvent(eventId) {
      try {
        await axios.post(
          `/api/events/${eventId}/unregister`,
          {},
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('authToken')}`,
            },
          }
        );
        this.events = this.events.map((event) =>
          event.id === eventId ? { ...event, isRegistered: false } : event
        );
        this.filterEvents();
      } catch (error) {
        console.error('Erro ao cancelar inscrição no evento:', error);
      }
    },
  },
  created() {
    this.fetchEvents();
  },
};
</script>
<style scoped>

.dashboard-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
  }
  
  .filter-container {
    margin-bottom: 20px;
  }
  
  .filter-container input {
    width: 100%;
    padding: 10px;
    font-size: 16px;
  }
  
  .events-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }
  
  .event-card {
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }
  
  .no-events {
    text-align: center;
    font-size: 18px;
    color: #777;
  }

.register-button {
  margin-top: 10px;
  padding: 8px 12px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.register-button:hover {
  background-color: #45a049;
}

.registered-label {
  color: green;
  font-weight: bold;
}

.unregister-button {
  margin-top: 10px;
  padding: 8px 12px;
  background-color: #f44336;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>
