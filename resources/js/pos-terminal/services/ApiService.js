const mockProducts = [
  { id: 1, sku: 'SKU123', name: 'Laptop', price: 1200.00 },
  { id: 2, sku: 'SKU456', name: 'Mouse', price: 25.00 },
  { id: 3, sku: 'SKU789', name: 'Keyboard', price: 75.00 },
  { id: 4, sku: 'SKU101', name: 'Monitor', price: 300.00 },
  { id: 5, sku: 'SKU112', name: 'Webcam', price: 50.00 },
];

export default {
  searchProducts(term) {
    return new Promise((resolve) => {
      setTimeout(() => {
        if (!term) {
          resolve([]);
          return;
        }
        const lowerCaseTerm = term.toLowerCase();
        const results = mockProducts.filter(
          (product) =>
            product.name.toLowerCase().includes(lowerCaseTerm) ||
            product.sku.toLowerCase().includes(lowerCaseTerm)
        );
        resolve(results);
      }, 300); // Simulate network latency
    });
  },
};