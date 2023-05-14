<?php
session_start();
include("connection.php");

class SalesInvoice {
  private $database;

  public function __construct($db) {
    $this->database = $db;
  }

  public function getSalesInvoices() {
    $salesInvoices = $this->database->prepare("SELECT * FROM product_sale");
    $salesInvoices->execute();
    return $salesInvoices->fetchAll();
  }
}

$salesInvoice = new SalesInvoice($database);
$result = $salesInvoice->getSalesInvoices();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #dddddd;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <th>product_name</th>
            <th>quantity sold</th>
            <th>cust_name</th>
        </thead>
        <tbody>
            <?php 
            foreach ($result as $data) {
                echo "<tr>";
                echo "<td>".$data["product_name"]."</td>";
                echo "<td>".$data["quantity_sold"]."</td>";
                echo "<td>".$data["cust_name"]."</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
