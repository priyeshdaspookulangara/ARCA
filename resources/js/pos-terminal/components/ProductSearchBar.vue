<template>
  <div class="product-search">
    <input
      type="text"
      v-model="searchTerm"
      @input="search"
      placeholder="Scan or search for a product..."
      class="search-input"
    />
    <ul v-if="results.length" class="search-results">
      <li
        v-for="product in results"
        :key="product.id"
        @click="selectProduct(product)"
      >
        {{ product.name }} - ${{ product.price.toFixed(2) }}
      </li>
    </ul>
  </div>
</template>

<script>
import ApiService from '../services/ApiService';

export default {
  name: 'ProductSearchBar',
  data() {
    return {
      searchTerm: '',
      results: [],
      debounce: null,
    };
  },
  methods: {
    search() {
      clearTimeout(this.debounce);
      this.debounce = setTimeout(async () => {
        if (this.searchTerm.length > 1) {
          this.results = await ApiService.searchProducts(this.searchTerm);
        } else {
          this.results = [];
        }
      }, 300);
    },
    selectProduct(product) {
      this.$emit('product-selected', product);
      this.searchTerm = '';
      this.results = [];
    },
  },
};
</script>

<style scoped>
.product-search {
  position: relative;
  margin-bottom: 20px;
}
.search-input {
  width: 100%;
  padding: 10px;
  font-size: 16px;
}
.search-results {
  position: absolute;
  width: 100%;
  background: white;
  border: 1px solid #ccc;
  list-style: none;
  margin: 0;
  padding: 0;
  z-index: 10;
}
.search-results li {
  padding: 10px;
  cursor: pointer;
}
.search-results li:hover {
  background: #f0f0f0;
}
</style>