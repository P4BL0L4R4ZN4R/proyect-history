<template>
  <div class="progress-container">
    <div class="progress-track">
      <div class="progress-fill" :style="{ width: progress + '%' }"></div>
      <div
        v-for="(step, index) in steps" :key="index" class="progress-step"
        :style="{ left: `${(index / (steps.length - 1)) * 100}%` }"
      >
        <div
          class="step-circle"
          :class="{ completed: index < currentStep || (index === currentStep && progress === 100) }"
        >
          <span v-if="index < currentStep || (index === currentStep && progress === 100)"> ✔ </span>
          <span v-else> {{ index + 1 }} </span>
        </div>
      </div>
    </div>
    <div class="progress-labels">
      <div
        v-for="(step, index) in steps" :key="index" class="progress-label"
        :style="{ left: `${(index / (steps.length - 1)) * 100}%` }"
      >
        {{ step }}
      </div>
    </div>
  </div>
</template>
<script setup>
  defineProps({
    steps: {
      type: Array,
      default: () => [
        'Seleccionar tarifa', 
        'Seleccionar asiento', 
        'Click a botón continuar',
        'Ingresar nombre', 
        'Ingresar apellido'
      ],
    },
    currentStep: {
      type: Number,
      default: 0
    },
    progress: {
      type: Number,
      required: true
    }
  });
</script>
<style scoped>
  .progress-container {
    position: relative;
    width: 90%;
    padding: 20px 0;
    justify-self: center;
  }
  .progress-track {
    position: relative;
    width: 100%;
    height: 12px;
    background-color: #e0e0e0;
    border-radius: 6px;
  }
  .progress-fill {
    height: 100%;
    background-color: #f39c12;
    border-radius: 6px;
    transition: width 0.4s ease;
  }
  .progress-step {
    position: absolute;
    top: 50%;
    transform: translate(-50%, -50%);
  }
  .step-circle {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background-color: white;
    border: 3px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    transition: all 0.3s ease;
  }
  .step-circle.completed {
    background-color: #f39c12;
    color: white;
    border-color: #f39c12;
  }
  .progress-labels {
    position: relative;
    margin-top: 8px;
  }
  .progress-label {
    position: absolute;
    transform: translateX(-50%);
    font-size: 0.85rem;
    color: #555;
    white-space: nowrap;
  }
</style>