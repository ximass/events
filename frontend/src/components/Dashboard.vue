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

      <select v-model="registrationFilter" @change="filterEvents">
        <option value="all">Todos</option>
        <option value="registered">Inscritos</option>
        <option value="not_registered">Não Inscritos</option>
      </select>
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

        <div v-if="event.isRegistered">
          <div v-if="event.isCheckedIn">
            <button @click="generateCertificate(event.id)" class="certificate-button">
              Gerar Certificado
            </button>
          </div>
          <div v-else>
            <p>Você precisa realizar o check-in para gerar o certificado.</p>
          </div>
          <button @click="unregisterFromEvent(event.id)" class="unregister-button">
            Cancelar Inscrição
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
      registrationFilter: 'all'
    };
  },
  methods: {
    async fetchEvents() {
      try {
        const eventsResponse = await axios.get('/api/events');
        const events = eventsResponse.data;

        const registrationsResponse = await axios.get('/api/registrations', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('authToken')}`,
          },
        });
        const userRegistrations = registrationsResponse.data;

        const checkinsResponse = await axios.get('/api/checkins', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('authToken')}`,
          },
        });
        const userCheckins = checkinsResponse.data;

        this.events = events.map((event) => {
          const isRegistered = userRegistrations.some(
            (registration) => registration.event_id === event.id
          );
          const isCheckedIn = userCheckins.some(
            (checkin) => checkin.event_id === event.id
          );
          return {
            ...event,
            isRegistered,
            isCheckedIn,
          };
        });

        this.filterEvents();
      } catch (error) {
        console.error('Erro ao buscar eventos ou inscrições:', error);
      }
    },
    filterEvents() {
      this.filteredEvents = this.events.filter((event) => {
        const matchesSearchTerm = event.title
          .toLowerCase()
          .includes(this.searchTerm.toLowerCase());

        let matchesRegistrationFilter = true;

        if (this.registrationFilter === 'registered') {
          matchesRegistrationFilter = event.isRegistered === true;
        } else if (this.registrationFilter === 'not_registered') {
          matchesRegistrationFilter = event.isRegistered === false;
        }

        return matchesSearchTerm && matchesRegistrationFilter;
      });
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
    async generateCertificate(eventId) {
      try {
        const response = await axios.post(
          `/api/events/${eventId}/certificate`,
          {},
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('authToken')}`,
            },
            responseType: 'blob',
          }
        );

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');

        link.href = url;
        link.setAttribute('download', `certificado_evento_${eventId}.pdf`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      } catch (error) {
        console.error('Erro ao gerar o certificado:', error);
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
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
  }

  .filter-container input {
    flex: 1;
    padding: 10px;
    font-size: 16px;
  }

  .filter-container select {
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

  .certificate-button {
    margin-top: 10px;
    padding: 8px 12px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .certificate-button:hover {
    background-color: #45A049;
  }
</style>
