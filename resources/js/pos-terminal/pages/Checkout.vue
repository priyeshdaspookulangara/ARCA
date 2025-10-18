<template>
  <div class="checkout-page">
    <div v-if="!transactionComplete">
      <h1>Checkout</h1>
      <product-search-bar @product-selected="addProductToCart" />
      <cart-table
        :cart="cart"
        @update-quantity="updateItemQuantity"
        @remove-item="removeItemFromCart"
      />
      <div class="actions">
        <button @click="showPaymentModal = true" :disabled="cart.length === 0">
          Pay
        </button>
      </div>
    </div>

    <receipt-preview
      v-if="transactionComplete"
      :cart="completedCart"
      @close="resetSale"
    />

    <payment-modal
      v-if="showPaymentModal"
      :total="total"
      @close="showPaymentModal = false"
      @payment-successful="completeTransaction"
    />
  </div>
</template>

<script>
import ProductSearchBar from '../components/ProductSearchBar.vue';
import CartTable from '../components/CartTable.vue';
import PaymentModal from '../components/PaymentModal.vue';
import ReceiptPreview from '../components/ReceiptPreview.vue';

export default {
  name: 'Checkout',
  components: {
    ProductSearchBar,
    CartTable,
    PaymentModal,
    ReceiptPreview,
  },
  data() {
    return {
      cart: [],
      completedCart: [],
      showPaymentModal: false,
      transactionComplete: false,
    };
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
    addProductToCart(product) {
      const existingItem = this.cart.find(
        (item) => item.product.id === product.id
      );
      if (existingItem) {
        existingItem.quantity++;
      } else {
        this.cart.push({ product, quantity: 1 });
      }
    },
    updateItemQuantity({ product, quantity }) {
      const item = this.cart.find((item) => item.product.id === product.id);
      if (item) {
        if (quantity > 0) {
          item.quantity = quantity;
        } else {
          this.removeItemFromCart(product);
        }
      }
    },
    removeItemFromCart(product) {
      this.cart = this.cart.filter((item) => item.product.id !== product.id);
    },
    completeTransaction() {
      this.completedCart = [...this.cart];
      this.transactionComplete = true;
      this.showPaymentModal = false;
    },
    resetSale() {
      this.cart = [];
      this.completedCart = [];
      this.transactionComplete = false;
    },
  },
};
</script>

<style scoped>
.checkout-page {
  padding: 20px;
  font-family: sans-serif;
}
.actions {
  margin-top: 20px;
  text-align: right;
}
</style>