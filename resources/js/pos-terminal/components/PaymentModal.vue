<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      <h2>Payment</h2>
      <div class="payment-summary">
        <p>Total Due: ${{ total.toFixed(2) }}</p>
      </div>
      <div class="payment-options">
        <button @click="processPayment('cash')">Cash</button>
        <button @click="processPayment('card')">Card</button>
      </div>
      <div v-if="paymentProcessed" class="payment-confirmation">
        <p>Payment successful!</p>
        <button @click="$emit('close')">Close</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PaymentModal',
  props: {
    total: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      paymentProcessed: false,
    };
  },
  methods: {
    processPayment(method) {
      console.log(`Processing payment via ${method}`);
      // In a real app, this would call the payment gateway
      this.paymentProcessed = true;
      this.$emit('payment-successful');
    },
  },
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}
.modal-content {
  background: white;
  padding: 20px;
  border-radius: 5px;
  width: 400px;
}
.payment-options {
  margin-top: 20px;
  display: flex;
  justify-content: space-around;
}
.payment-confirmation {
  margin-top: 20px;
  text-align: center;
}
</style>