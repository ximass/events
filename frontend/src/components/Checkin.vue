<template>
    <div class="checkin-container">
      <h2>Check-in de eventos</h2>
  
      <div v-if="events.length === 0">
        <p>Nenhum evento encontrado.</p>
      </div>
  
      <div v-for="event in events" :key="event.id" class="event-card">
        <h3>{{ event.title }}</h3>
        <p>{{ event.description }}</p>
        <p>
          <strong>Data:</strong>
          {{ formatDate(event.start_date) }} - {{ formatDate(event.end_date) }}
        </p>
  
        <!-- Sublistagem de usuários inscritos -->
        <div class="registrations">
          <h4>Usuários inscritos:</h4>
          <div v-if="event.registrations && event.registrations.length > 0">
            <ul>
              <li
                v-for="registration in event.registrations"
                :key="registration.id"
              >
                {{ registration.user.name }} ({{ registration.user.email }})
                <!-- Verificar se o usuário já realizou check-in -->
                <span v-if="registration.isCheckedIn" class="checked-in-label">
                  ✔️ Check-in realizado
                </span>
                <button
                  v-else
                  @click="performCheckin(event.id, registration.id)"
                  class="checkin-button"
                >
                  Realizar check-in
                </button>
              </li>
            </ul>
          </div>
          <div v-else>
            <p>Nenhum usuário inscrito neste evento.</p>
          </div>
        </div>
      </div>
    </div>
  </template>
  
<script>
import axios from 'axios';

export default {
    name: 'Checkin',
    data() {
        return {
        events: [],
        };
    },
    methods: {
        async fetchEvents() {
            try {
                const response = await axios.get('/api/events-with-registrations-and-checkins', {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('authToken')}`,
                },
                });
                this.events = response.data;
            } catch (error) {
                console.error('Erro ao buscar eventos:', error);
            }

        },
        formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('pt-BR', options);
        },
        async performCheckin(eventId, registrationId) {
            try {
                await axios.post(
                `/api/events/${eventId}/registrations/${registrationId}/checkin`,
                {},
                {
                    headers: {
                    Authorization: `Bearer ${localStorage.getItem('authToken')}`,
                    },
                }
                );

                this.updateRegistrationCheckinStatus(eventId, registrationId, true);
            } catch (error) {
                console.error('Erro ao realizar check-in:', error);
            }
        },
        updateRegistrationCheckinStatus(eventId, registrationId, isCheckedIn) {
            const event = this.events.find((e) => e.id === eventId);

            if (event) {
                const registration = event.registrations.find(
                    (r) => r.id === registrationId
                );

                if (registration) {
                    registration.isCheckedIn = isCheckedIn;
                }
            }
        },
    },
    created() {
        this.fetchEvents();
    },
};
</script>

<style scoped>
    .checkin-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    .event-card {
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .registrations {
        margin-top: 20px;
    }

    .registrations h4 {
        margin-bottom: 10px;
    }

    .registrations ul {
        list-style-type: none;
        padding: 0;
    }

    .registrations li {
        padding: 5px 0;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
    }

    .checkin-button {
        margin-left: auto;
        padding: 6px 12px;
        background-color: #2196F3;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .checkin-button:hover {
        background-color: #1976D2;
    }

    .checked-in-label {
        color: green;
        margin-left: auto;
    }
</style>