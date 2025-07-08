function showSection(section) {
  document.getElementById("account-section").style.display =
    section === "account" ? "block" : "none";
  document.getElementById("product-section").style.display =
    section === "product" ? "block" : "none";
  document
    .getElementById("account-tab")
    .classList.toggle("active", section === "account");
  document
    .getElementById("product-tab")
    .classList.toggle("active", section === "product");
}

// Modal tài khoản
function openAccountModal(
  type,
  id = "",
  username = "",
  email = "",
  password = "",
  role = ""
) {
  const modalLabel = document.getElementById("accountModalLabel");
  const accountId = document.getElementById("accountId");
  const accountName = document.getElementById("accountName");
  const accountPassword = document.getElementById("accountPassword");
  const accountEmail = document.getElementById("accountEmail");
  const accountRole = document.getElementById("accountRole");
  if (type === "add") {
    modalLabel.textContent = "Thêm tài khoản";
    accountId.value = "";
    accountName.value = "";
    accountPassword.value = "";
    accountEmail.value = "";
    accountRole.value = "user";
  } else {
    modalLabel.textContent = "Sửa tài khoản";
    accountId.value = id;
    accountName.value = username;
    accountPassword.value = password;
    accountEmail.value = email;
    accountRole.value = role;
  }
  var modal = new bootstrap.Modal(document.getElementById("accountModal"));
  modal.show();
}
// Modal sản phẩm
function openProductModal(
  type,
  id = "",
  name = "",
  price = "",
  description = "",
  image = ""
) {
  const modalLabel = document.getElementById("productModalLabel");
  const productId = document.getElementById("productId");
  const productName = document.getElementById("productName");
  const productPrice = document.getElementById("productPrice");
  const productDescription = document.getElementById("productDescription");
  const productImage = document.getElementById("productImage");
  const productImagePreview = document.getElementById("productImagePreview");
  const productImageName = document.getElementById("productImageName");

  if (type === "add") {
    modalLabel.textContent = "Thêm sản phẩm";
    productId.value = "";
    productName.value = "";
    productPrice.value = "";
    productDescription.value = "";
    productImage.value = "";
    if (productImagePreview) productImagePreview.src = "";
    if (productImageName) productImageName.textContent = "";
  } else {
    modalLabel.textContent = "Sửa sản phẩm";
    productId.value = id;
    productName.value = decodeURIComponent(name);
    productPrice.value = decodeURIComponent(price);
    productDescription.value = decodeURIComponent(description);
    if (productImagePreview) {
      if (image) {
        productImagePreview.src =
          "../assets/img/products/" + decodeURIComponent(image);
        productImagePreview.style.display = "block";
      } else {
        productImagePreview.src = "";
        productImagePreview.style.display = "none";
      }
    }
    if (productImageName) {
      if (image) {
        productImageName.textContent =
          "Tên file ảnh cũ: " + decodeURIComponent(image);
      } else {
        productImageName.textContent = "";
      }
    }
  }
  var modal = new bootstrap.Modal(document.getElementById("productModal"));
  modal.show();
}
// Modal xác nhận xóa
function openDeleteModal(type, id) {
  document.getElementById("deleteType").value = type;
  document.getElementById("deleteId").value = id;
  var modal = new bootstrap.Modal(document.getElementById("deleteModal"));
  modal.show();
}

// Lấy danh sách tài khoản
function loadAccounts() {
  fetch("php/get_accounts.php")
    .then((res) => res.json())
    .then((data) => {
      const tbody = document.querySelector("#account-section tbody");
      tbody.innerHTML = data
        .map(
          (acc) => `
        <tr>
          <td>${acc.id}</td>
          <td>${acc.username}</td>
          <td>${acc.password}</td>
          <td>${acc.email}</td>
          <td>${acc.role}</td>
          <td>
            <button class="btn btn-sm btn-primary me-1"
              onclick="openAccountModal('edit', '${acc.id}', '${acc.username}', '${acc.email}', '${acc.password}', '${acc.role}')">
              <i class="bi bi-pencil-square"></i> Sửa
            </button>
            <button class="btn btn-sm btn-danger" onclick="deleteAccount('${acc.id}')"><i class="bi bi-trash"></i> Xóa</button>
          </td>
        </tr>
      `
        )
        .join("");
    });
}

// Lấy danh sách sản phẩm
function loadProducts() {
  fetch("php/get_products.php")
    .then((res) => res.json())
    .then((data) => {
      const tbody = document.querySelector("#product-section tbody");
      tbody.innerHTML = data
        .map(
          (prod) => `
        <tr>
          <td>${prod.id}</td>
          <td>${prod.name}</td>
          <td>${prod.price}</td>
          <td>${prod.description}</td>
          <td>${prod.image}</td>
          <td>
            <button class="btn btn-sm btn-primary me-1"
              onclick="openProductModal('edit', '${prod.id}', 
                '${encodeURIComponent(prod.name)}', 
                '${encodeURIComponent(prod.price)}', 
                '${encodeURIComponent(prod.description)}', 
                '${encodeURIComponent(prod.image)}')">
              <i class="bi bi-pencil-square"></i> Sửa
            </button>
            <button class="btn btn-sm btn-danger"
              onclick="deleteProduct('${prod.id}')">
              <i class="bi bi-trash"></i> Xóa
            </button>
          </td>
        </tr>
      `
        )
        .join("");
    });
}

// Gọi khi trang tải xong
window.addEventListener("DOMContentLoaded", () => {
  loadAccounts();
  loadProducts();
});

// Thêm/sửa tài khoản
document.getElementById("accountForm").onsubmit = function (e) {
  e.preventDefault();
  const id = document.getElementById("accountId").value;
  const username = document.getElementById("accountName").value;
  const password = document.getElementById("accountPassword").value;
  const email = document.getElementById("accountEmail").value;
  const role = document.getElementById("accountRole").value;

  const url = id ? "php/update_account.php" : "php/add_account.php";
  fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id, username, password, email, role }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        loadAccounts();
        bootstrap.Modal.getInstance(
          document.getElementById("accountModal")
        ).hide();
      } else {
        alert("Lỗi: " + data.error);
      }
    });
};

// Thêm/sửa sản phẩm
// Gắn sự kiện submit cho form sản phẩm
// Đặt sau hàm thêm/sửa tài khoản

document.getElementById("productForm").onsubmit = function (e) {
  e.preventDefault();
  const id = document.getElementById("productId").value;
  const name = document.getElementById("productName").value;
  const price = document.getElementById("productPrice").value;
  const description = document.getElementById("productDescription").value;
  const imageInput = document.getElementById("productImage");
  const imageFile = imageInput.files[0];

  const formData = new FormData();
  if (id) formData.append("id", id);
  formData.append("name", name);
  formData.append("price", price);
  formData.append("description", description);
  if (imageFile) formData.append("image", imageFile);

  const url = id ? "php/update_product.php" : "php/add_product.php";
  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        loadProducts();
        bootstrap.Modal.getInstance(
          document.getElementById("productModal")
        ).hide();
      } else {
        alert("Lỗi: " + data.error);
      }
    });
};

// Xóa sản phẩm
function deleteProduct(id) {
  if (!confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) return;
  fetch("php/delete_product.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        loadProducts();
      } else {
        alert("Lỗi: " + data.error);
      }
    });
}

// Xóa tài khoản
function deleteAccount(id) {
  if (!confirm("Bạn có chắc chắn muốn xóa tài khoản này?")) return;
  fetch("php/delete_account.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        loadAccounts();
      } else {
        alert("Lỗi: " + data.error);
      }
    });
}
