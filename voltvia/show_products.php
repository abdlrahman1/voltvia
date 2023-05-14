<?php
session_start();
include("connection.php");

class Product {
  private $id;
  private $name;
  private $quantity;
  private $purchasePrice;
  private $sellingPrice;
  private $code;
  private $reviews;

  public function __construct($id, $name, $quantity, $purchasePrice, $sellingPrice, $code, $reviews) {
    $this->id = $id;
    $this->name = $name;
    $this->quantity = $quantity;
    $this->purchasePrice = $purchasePrice;
    $this->sellingPrice = $sellingPrice;
    $this->code = $code;
    $this->reviews = $reviews;
  }

  public function getId() {
    return $this->id;
  }

  public function getName() {
    return $this->name;
  }

  public function getQuantity() {
    return $this->quantity;
  }

  public function getPurchasePrice() {
    return $this->purchasePrice;
  }

  public function getSellingPrice() {
    return $this->sellingPrice;
  }

  public function getCode() {
    return $this->code;
  }

  public function getReviews() {
    return $this->reviews;
  }

  public function update($database) {
    $update_product = $database->prepare("UPDATE products SET product_name = :product_name, product_quantity = :product_quantity, product_Purchase_price = :product_purchase_price, Product_selling_price = :product_selling_price, product_code = :product_code, reviews = :reviews WHERE id = :product_id");
    $update_product->bindParam(':product_id', $this->id);
    $update_product->bindParam(':product_name', $this->name);
    $update_product->bindParam(':product_quantity', $this->quantity);
    $update_product->bindParam(':product_purchase_price', $this->purchasePrice);
    $update_product->bindParam(':product_selling_price', $this->sellingPrice);
    $update_product->bindParam(':product_code', $this->code);
    $update_product->bindParam(':reviews', $this->reviews);

    if($update_product->execute()) {
      $_SESSION['success_message'] = "Product information updated successfully.";
    } else {
      $_SESSION['error_message'] = "Error updating product information.";
    }
  }

  public function delete($database) {
    $del = $database->prepare("DELETE FROM products where id = '$this->id'");
    $del->execute();
  }
}

class ProductManager {
  private $database;

  public function __construct($database) {
    $this->database = $database;
  }

  public function getProducts() {
    $show_pro = $this->database->prepare("SELECT * FROM products");
    $show_pro->execute();
    $products = $show_pro->fetchAll(PDO::FETCH_ASSOC);
    return $products;
  }

  public function updateProduct($product_id, $product_name, $product_quantity, $product_purchase_price, $product_selling_price, $product_code, $reviews) {
    $product = new Product($product_id, $product_name, $product_quantity, $product_purchase_price, $product_selling_price, $product_code, $reviews);
    $product->update($this->database);
  }

  public function deleteProduct($product_id) {
    $product = new Product($product_id, null, null, null, null, null, null);
    $product->delete($this->database);
  }
}

$productManager = new ProductManager($database);

// If the form has been submitted, update the product information
if (isset($_POST['update_product'])) {
  $product_id = $_POST['product_id'];
  $product_name = $_POST['product_name'];
  $product_quantity = $_POST['product_quantity'];
  $product_purchase_price = $_POST['product_Purchase_price'];
  $product_selling_price = $_POST['Product_selling_price'];
  $product_code = $_POST['product_code'];
  $reviews = $_POST['reviews'];

  $productManager->updateProduct($product_id, $product_name, $product_quantity, $product_purchase_price, $product_selling_price, $product_code, $reviews);
}

if (isset($_POST["del"])) {
  $product_id = $_POST["product_id"];
  $productManager->deleteProduct($product_id);
}

// Retrieve the list of products from the database
$products = $productManager->getProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Management</title>
  <style>
    /* CSS styles omitted for brevity */
  </style>
</head>
<body>
  <!-- HTML markup omitted for brevity -->

  <table>
    <thead>
      <tr>
        <th>Product Name</th>
        <th>Quantity</th>
        <th>Purchase Price</th>
        <th>Selling Price</th>
        <th>Product Code</th>
        <th>Reviews</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($products as $product): ?>
        <tr>
          <form method="POST" class="edit-form">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <td><input type="text" name="product_name" value="<?php echo $product['product_name']?>"></td>
            <td><input type="text" name="product_quantity" value="<?php echo $product['product_quantity']?>"></td>
            <td><input type="text" name="product_Purchase_price" value="<?php echo $product['product_Purchase_price']?>"></td>
            <td><input type="text" name="Product_selling_price" value="<?php echo $product['Product_selling_price']?>"></td>
            <td><input type="text" name="product_code" value="<?php echo $product['product_code']?>"></td>
            <td><input type="text" name="reviews" value="<?php echo $product['reviews']?>"></td>
            <td>
              <button class="edit-submit" type="submit" name="update_product">Update</button>
              <button type="submit" name="del">Delete</button>
            </td>
          </form>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>
</html>
