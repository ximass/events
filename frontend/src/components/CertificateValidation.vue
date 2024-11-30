<template>
    <div class="certificate-validation">
      <h2>Validação de Certificados</h2>
      <form @submit.prevent="validateCertificate">
        <label for="certificateCode">Código do Certificado:</label>
        <input
          type="text"
          id="certificateCode"
          v-model="certificateCode"
          required
        />
        <button type="submit">Validar</button>
      </form>
  
      <div v-if="validationResult" class="result">
        <p>{{ validationResult }}</p>
      </div>
    </div>
</template>
  
<script>
  import axios from 'axios';
  
  export default {
    name: 'CertificateValidation',
    data() {
      return {
        certificateCode: '',
        validationResult: '',
      };
    },
    methods: {
      async validateCertificate() {
        this.validationResult = '';
        try {
          const response = await axios.post('/api/validate-certificate', {
            certificate_code: this.certificateCode,
          });
  
          if (response.status === 200) {
            this.validationResult = `Certificado válido!`;
          }
        } catch (error) {
          if (error.response && error.response.status === 404) {
            this.validationResult = 'Certificado inválido ou não encontrado.';
          } else {
            this.validationResult =
              'Ocorreu um erro ao validar o certificado. Tente novamente.';
            console.error(error);
          }
        }
      },
    },
  };
</script>
  
<style scoped>
  .certificate-validation {
    max-width: 500px;
    margin: 0 auto;
  }
  
  .certificate-validation form {
    display: flex;
    flex-direction: column;
  }
  
  .certificate-validation label {
    margin-bottom: 5px;
  }
  
  .certificate-validation input {
    margin-bottom: 15px;
    padding: 8px;
    font-size: 16px;
  }
  
  .certificate-validation button {
    padding: 10px 15px;
    font-size: 16px;
    cursor: pointer;
  }
  
  .result {
    margin-top: 20px;
    font-weight: bold;
  }
</style>