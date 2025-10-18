<template>
  <div class="cart-container">
    <h2>Cart</h2>
    <table class="cart-table">
      <thead>
        <tr>
          <th>Product</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Total</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="cart.length === 0">
          <td colspan="5">Cart is empty</td>
        </tr>
        <tr v-for="item in cart" :key="item.product.id">
          <td>{{ item.product.name }}</td>
          <td>${{ item.product.price.toFixed(2) }}</td>
          <td>
            <input
              type="number"
              min="1"
              :value="item.quantity"
              @change="updateQuantity(item.product, $event.target.value)"
              class="quantity-input"
            />
          </td>
          <td>${{ (item.product.price * item.quantity).toFixed(2) }}</td>
          <td>
            <button @click="removeItem(item.product)">Remove</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-if="cart.length > 0" class="cart-summary">
      <h3>Total: ${{ total.toFixed(2) }}</h3>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CartTable',
  props: {
    cart: {
      type: Array,
      required: true,
    },
  },
  computed: {
    total() {
      return this.cart.reduce(
        (acc, item) => acc + item.product.price * item.quantity,
        0
      );
    },
  },
  methods: {
    updateQuantity(product, quantity) {
      this.$emit('update-quantity', { product, quantity: parseInt(quantity) });
    },
    removeItem(product) {
      this.$emit('remove-item', product);
    },
  },
};
</script>

<style scoped>
.cart-container {
  margin-top: 20px;
}
.cart-table {
  width: 100%;
  border-collapse: collapse;
}
.cart-table th,
.cart-table td {
  border: 1px solid #ccc;
  padding: 10px;
  text-align: left;
}
.quantity-input {
  width: 60px;
}
.cart-summary {
  margin-top: 20px;
  text-align: right;
}
</style>