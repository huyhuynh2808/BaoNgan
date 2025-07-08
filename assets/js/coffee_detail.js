function getQueryParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}

document.addEventListener("DOMContentLoaded", function () {
  const productId = getQueryParam("id");
  console.log("Product ID:", productId);
  if (!productId) return;

  fetch("assets/forms/get_product_detail.php?id=" + productId)
    .then((res) => res.json())
    .then((prod) => {
      console.log("Product data:", prod);
      if (!prod) return;
      document.getElementById("product-name").textContent = prod.name;
      document.getElementById("product-image").src =
        "assets/img/products/" + prod.image;
      document.getElementById("product-price").textContent = prod.price;
      document.getElementById("product-description").textContent =
        prod.description;
    });
});
