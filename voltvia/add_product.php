<?php
session_start();
include("connection.php");

class Product
{
    private $pro_name;
    private $pro_quantity;
    private $pro_purchase;
    private $pro_selling;
    private $pro_code;
    private $reviews;
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function setProductDetails($pro_name, $pro_quantity, $pro_purchase, $pro_selling, $pro_code, $reviews)
    {
        $this->pro_name = $pro_name;
        $this->pro_quantity = $pro_quantity;
        $this->pro_purchase = $pro_purchase;
        $this->pro_selling = $pro_selling;
        $this->pro_code = $pro_code;
        $this->reviews = $reviews;
    }

    public function addProduct()
    {
        if (isset($this->pro_name) && isset($this->pro_quantity) && isset($this->pro_purchase) && isset($this->pro_selling) && isset($this->pro_code) && isset($this->reviews)) {
            $insert = $this->database->prepare("INSERT INTO products(product_name, product_quantity, product_Purchase_price, Product_selling_price, product_code, reviews)
                VALUES(:pro_name, :pro_quantity, :pro_purchase, :pro_selling, :pro_code, :reviews)");
            $insert->bindParam(":pro_name", $this->pro_name);
            $insert->bindParam(":pro_quantity", $this->pro_quantity);
            $insert->bindParam(":pro_purchase", $this->pro_purchase);
            $insert->bindParam(":pro_selling", $this->pro_selling);
            $insert->bindParam(":pro_code", $this->pro_code);
            $insert->bindParam(":reviews", $this->reviews);
            if ($insert->execute()) {
                echo "The product was added";
                header("Location: add_product.php");
            } else {
                echo "There was an error while adding the product";
            }
        } else {
            echo "Please fill in the product inputs";
        }
    }
}

if (isset($_POST["btn"])) {
    $product = new Product($database);

    $pro_name = $_POST["product_name"];
    $pro_quantity = $_POST["product_quantity"];
    $pro_purchase = $_POST["product_Purchase_price"];
    $pro_selling = $_POST["Product_selling_price"];
    $pro_code = $_POST["product_code"];
    $reviews = $_POST["reviews"];

    $product->setProductDetails($pro_name, $pro_quantity, $pro_purchase, $pro_selling, $pro_code, $reviews);
    $product->addProduct();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
</head>
<body>
    <form method="post">
        <input type="text" name="product_name" placeholder="اسم المنتج">
        <input type="text" name="product_quantity" placeholder="كمية المنتج">
        <input type="text" name="product_Purchase_price" placeholder="سعر الشراء">
        <input type="text" name="Product_selling_price" placeholder="سعر البيع">
        <input type="text" name="product_code" placeholder="كود منتج ">
<textarea type="text" name="reviews"> </textarea>
<button type="submit" name="btn">ارسال</button>
</form>

</body>
</html>
