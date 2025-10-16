<template>
  <div>
    <h1>Stock Report</h1>

    <!-- Filters for the report -->
    <div>
        <label for="location">Location:</label>
        <input type="text" id="location" v-model="filters.location">
        <button @click="fetchStockReport">Refresh</button>
    </div>

    <!-- Table to display stock levels -->
    <table>
      <thead>
        <tr>
          <th>Material Code</th>
          <th>Material Name</th>
          <th>Location</th>
          <th>Quantity</th>
          <th>UOM</th>
        </tr>
      </thead>
      <tbody>
        <!-- Loop through stock data -->
        <tr v-for="stock in stockData" :key="stock.id">
            <td>{{ stock.material_code }}</td>
            <td>{{ stock.material_name }}</td>
            <td>{{ stock.location_id }}</td>
            <td>{{ stock.quantity }}</td>
            <td>{{ stock.uom }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'StockReport',
  data() {
    return {
      filters: {
        location: ''
      },
      stockData: [],
    };
  },
  methods: {
    fetchStockReport() {
      // API call to GET /mm/stock with filters
    },
  },
  mounted() {
    this.fetchStockReport();
  },
};
</script>

<style scoped>
/* Component-specific styles */
</style>