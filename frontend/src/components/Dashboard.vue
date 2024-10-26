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
          <h3>{{ event.nome }}</h3>
          <p>{{ event.descricao }}</p>
          <p><strong>Data:</strong> {{ formatDate(event.data) }}</p>
          <p><strong>Local:</strong> {{ event.local }}</p>
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
          const response = await axios.get('/api/events');
          this.events = response.data;
          this.filteredEvents = this.events;
        } catch (error) {
          console.error('Erro ao carregar eventos:', error);
        }
      },
  
      filterEvents() {
        if (this.searchTerm === '') {
          this.filteredEvents = this.events;
        } else {
          const lowerSearchTerm = this.searchTerm.toLowerCase();
          this.filteredEvents = this.events.filter(event =>
            event.nome.toLowerCase().includes(lowerSearchTerm)
          );
        }
      },
  
      formatDate(date) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(date).toLocaleDateString('pt-BR', options);
      },
    },
    mounted() {
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
  </style>
  