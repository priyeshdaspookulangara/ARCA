<template>
  <div class="receipt-preview">
    <h3>Receipt</h3>
    <div class="receipt-header">
      <p>Invoice #: {{ invoiceNumber }}</p>
      <p>Date: {{ new Date().toLocaleDateString() }}</p>
    </div>
    <table class="receipt-table">
      <thead>
        <tr>
          <th>Item</th>
          <th>Qty</th>
          <th>Price</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in cart" :key="item.product.id">
          <td>{{ item.product.name }}</td>
          <td>{{ item.quantity }}</td>
          <td>${{ (item.product.price * item.quantity).toFixed(2) }}</td>
        </tr>
      </tbody>
    </table>
    <div class="receipt-footer">
      <p>Subtotal: ${{ subtotal.toFixed(2) }}</p>
      <p>Tax: ${{ tax.toFixed(2) }}</p>
      <p>Total: ${{ total.toFixed(2) }}</p>
    </div>
    <div class="receipt-actions">
      <button @click="printReceipt">Print</button>
      <button @click="$emit('close')">New Sale</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ReceiptPreview',
  props: {
    cart: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      invoiceNumber: `POS-${Date.now()}`,
    };
  },
  computed: {
    subtotal() {
      return this.cart.reduce(
        (acc, item) => acc + item.product.price * item.quantity,
        0
      );
    },
    tax() {
      return this.subtotal * 0.08; // 8% tax
    },
    total() {
      return this.subtotal + this.tax;
    },
  },
  methods: {
    printReceipt() {
      // In a real app, this would trigger the browser print dialog
      // or send data to a receipt printer.
      alert('Printing receipt...');
      window.print();
    },
  },
};
</script>

<style scoped>
.receipt-preview {
  padding: 20px;
  border: 1px solid #ccc;
  width: 300px;
  margin: 20px auto;
}
.receipt-table {
  width: 100%;
  border-collapse: collapse;
  margin: 10px 0;
}
.receipt-table th,
.receipt-table td {
  border-bottom: 1px solid #eee;
  padding: 5px;
  text-align: left;
}
.receipt-footer {
  text-align: right;
}
.receipt-actions {
  margin-top: 20px;
  text-align: center;
}
</style>