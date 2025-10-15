<template>
  <div>
    <h1>Goods Issue</h1>
    <form @submit.prevent="postGoodsIssue">
      <div>
        <label for="issue-type">Issue Type:</label>
        <select id="issue-type" v-model="issue.issue_type">
            <option value="INTERNAL_CONSUMPTION">Internal Consumption</option>
            <option value="SCRAPPING">Scrapping</option>
            <option value="POS_SALE">POS Sale</option>
        </select>
      </div>
      <div>
        <label for="issue-date">Issue Date:</label>
        <input type="date" id="issue-date" v-model="issue.issue_date" />
      </div>

      <hr />

      <h3>Items</h3>
      <!-- Dynamic form for adding issue items -->
      <div v-for="(item, index) in issue.items" :key="index">
        <label>Material:</label>
        <input type="text" v-model="item.material_code" />
        <label>Quantity:</label>
        <input type="number" v-model="item.quantity" />
      </div>
      <button type="button" @click="addItem">Add Item</button>

      <hr />

      <button type="submit">Post Goods Issue</button>
    </form>
  </div>
</template>

<script>
export default {
  name: 'GoodsIssue',
  data() {
    return {
      issue: {
        issue_type: 'INTERNAL_CONSUMPTION',
        issue_date: new Date().toISOString().substr(0, 10),
        items: [{ material_code: '', quantity: 0 }],
      },
    };
  },
  methods: {
    addItem() {
      this.issue.items.push({ material_code: '', quantity: 0 });
    },
    postGoodsIssue() {
      // API call to POST /mm/goods-issue
    },
  },
};
</script>

<style scoped>
/* Component-specific styles */
</style>