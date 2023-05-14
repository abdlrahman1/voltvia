<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Handle form submission
  if (isset($_POST["quantity_sale"]) && isset($_POST["id"])) {
    // Retrieve and sanitize form inputs
    $quantity_sale = $_POST["quantity_sale"];
    $id = $_POST["id"];
    $cust_name = $_POST["cust_name"];

    // Update product quantity
    $sale = $database->prepare("UPDATE products SET product_quantity = product_quantity - :quantity_sale, cust_name = :cust_name WHERE id = :id");
    $sale->bindParam(':quantity_sale', $quantity_sale);
    $sale->bindParam(':cust_name', $cust_name);
    $sale->bindParam(':id', $id);
    if ($sale->execute()) {
      // Insert sale record
      $insertSale = $database->prepare("INSERT INTO product_sale (product_id, product_name, quantity_sold, cust_name) VALUES (:product_id, :product_name, :quantity_sale, :cust_name)");
      $insertSale->bindParam(':product_id', $id);
      $insertSale->bindParam(':product_name', $_POST["product_name"]);
      $insertSale->bindParam(':quantity_sale', $quantity_sale);
      $insertSale->bindParam(':cust_name', $cust_name);
      if ($insertSale->execute()) {
        echo "The data has been sent.";
      } else {
        // Handle error
        echo "Error inserting sale record.";
      }
    } else {
      // Handle error
      echo "Error updating product quantity.";
    }
  }
}

$query = $database->query("SELECT * FROM products");
$products = $query->fetchAll(PDO::FETCH_ASSOC);
$_SESSION["user"] = $products;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Selection</title>

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f1f1f1;
    }

    .container {
      max-width: 600px;
      margin: 50px auto;
      padding: 20px;
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      border-radius: 4px;
    }

    .form-control {
      display: block;
      width: 100%;
      padding: 8px;
      font-size: 16px;
      line-height: 1.5;
      border: 1px solid #ccc;
      border-radius: 4px;
      transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
      border-color: #80bdff;
      outline: 0;
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      margin-bottom: 4px;
      font-weight: bold;
    }

    .form-inputs {
      display: flex;
      gap: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Product Selection</h1>
    <form method="post">
      <div class="form-group">
        <label for="productSelect" class="form-label">Select a product:</label>
        <select id="productSelect" class="form-control" onchange="selectProduct()">
          <option value="">Select a product</option>
          <?php foreach ($products as $product): ?>
            <option value="<?php echo $product["product_name"]; ?>" product_quantity="<?php echo $product["product_quantity"]; ?>" id="<?php echo $product["id"]; ?>"><?php echo $product["product_name"]; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="selectedProduct" class="form-label">Selected Product:</label>
        <input type="text" id="selectedProduct" readonly class="form-control" name="product_name">
        <input type="hidden" id="id" readonly name="id">
      </div>
      <div class="form-group">
        <label for="quantityInput" class="form-label">Product Quantity:</label>
        <input type="text" id="quantityInput" readonly class="form-control" name="product_quantity">
        <label for="quantity_sale" class="form-label">Quantity Sale:</label>
        <input type="text" id="quantity_sale" class="form-control" name="quantity_sale">
        <label for="cust_name" class="form-label">Customer Name:</label>
        <input type="text" id="cust_name" class="form-control" name="cust_name">
        <button type="submit" name="btn">Sale</button>
      </div>
    </form>
  </div>

  <script>
    function selectProduct() {
      var selectElement = document.getElementById("productSelect");
      var selectedValue = selectElement.value;
      var selectedText = selectElement.options[selectElement.selectedIndex].text;
      var selectedOption = selectElement.options[selectElement.selectedIndex];
      var product_quantity = selectedOption.getAttribute('product_quantity');
      var id = selectedOption.getAttribute('id');
      document.getElementById("selectedProduct").value = selectedText;
      document.getElementById("quantityInput").value = product_quantity;
      document.getElementById("id").value = id;
      // Check if the quantity input already has a value
      var quantityInput = document.getElementById("quantityInput");
      if (quantityInput.value !== "") {
        return; // If the value is already set, no need to fetch again
      }

      if (selectedValue) {
        // Make an AJAX request to fetch the quantity of the selected product
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "product_sale.php?product_name=" + encodeURIComponent(selectedText), true);
        xhr.onload = function() {
          if (xhr.status === 200) {
            var data = JSON.parse(xhr.responseText);
            quantityInput.value = data.quantity;
          } else {
            console.log("Error fetching product quantity. Status: " + xhr.status);
          }
        };
        xhr.onerror = function() {
          console.log("Error fetching product quantity. Network error.");
        };
        xhr.send();
      }
    }
  </script>
</body>
</html>

