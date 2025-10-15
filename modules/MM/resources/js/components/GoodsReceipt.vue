<template>
  <div>
    <h1>Goods Receipt</h1>
    <form @submit.prevent="postGoodsReceipt">
      <div>
        <label for="po-number">Purchase Order Number:</label>
        <input type="text" id="po-number" v-model="receipt.po_number" />
      </div>
      <div>
        <label for="receipt-date">Receipt Date:</label>
        <input type="date" id="receipt-date" v-model="receipt.receipt_date" />
      </div>

      <hr />

      <h3>Items</h3>
      <!-- Dynamic form for adding receipt items -->
      <div v-for="(item, index) in receipt.items" :key="index">
        <label>Material:</label>
        <input type="text" v-model="item.material_code" />
        <label>Quantity:</label>
        <input type="number" v-model="item.quantity" />
      </div>
      <button type="button" @click="addItem">Add Item</button>

      <hr />

      <button type="submit">Post Goods Receipt</button>
    </form>
  </div>
</template>

<script>
export default {
  name: 'GoodsReceipt',
  data() {
    return {
      receipt: {
        po_number: '',
        receipt_date: new Date().toISOString().substr(0, 10),
        items: [{ material_code: '', quantity: 0 }],
      },
    };
  },
  methods: {
    addItem() {
      this.receipt.items.push({ material_code: '', quantity: 0 });
    },
    postGoodsReceipt() {
      // API call to POST /mm/goods-receipt
    },
  },
};
</script>

<style scoped>
/* Component-specific styles */
</style>