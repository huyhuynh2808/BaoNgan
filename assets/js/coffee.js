document.addEventListener("DOMContentLoaded", function () {
  fetch("assets/forms/get_all_products.php")
    .then((res) => res.json())
    .then((products) => {
      const container = document.getElementById("coffee-products-list");
      container.innerHTML = products
        .map(
          (prod) => `
      <div class="col mb-4">
        <div class="product-card position-relative">
          <div class="card-img product-img-hover position-relative">
            <img
              src="assets/img/products/${prod.image}"
              alt="${prod.name}"
              class="product-image img-fluid"
            />
            <a
              href="coffee_detail.html?id=${prod.id}&name=${encodeURIComponent(
            prod.name
          )}"
              class="btn btn-light quick-view-btn"
              aria-label="Xem nhanh"
            >
              <svg class="quick-view" width="24" height="24">
                <use xlink:href="#quick-view"></use>
              </svg>
            </a>
          </div>
          <div class="card-detail d-flex flex-column align-items-start mt-3">
            <h3 class="card-title fs-6 fw-bold m-0">${prod.name}</h3>
            <span class="card-price fw-bold">${prod.price}</span>
          </div>
        </div>
      </div>
    `
        )
        .join("");
    });
});
