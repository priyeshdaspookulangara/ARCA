<template>
  <div>
    <h1>Material Master</h1>
    <!-- Material creation form -->
    <form @submit.prevent="createMaterial">
      <!-- Form fields for material properties -->
    </form>

    <!-- Table to display list of materials -->
    <table>
      <thead>
        <tr>
          <th>Code</th>
          <th>Name</th>
          <th>Category</th>
          <th>UOM</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Loop through materials -->
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'MaterialMaster',
  data() {
    return {
      materials: [],
      newMaterial: {
        code: '',
        name: '',
        description: '',
        category: '',
        uom: '',
      },
    };
  },
  methods: {
    fetchMaterials() {
      // API call to GET /mm/items
    },
    createMaterial() {
      // API call to POST /mm/items
    },
  },
  mounted() {
    this.fetchMaterials();
  },
};
</script>

<style scoped>
/* Component-specific styles */
</style>