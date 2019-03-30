<?php
// die('kek112e');
require 'vendor/autoload.php';

use Doctrine\DBAL\Schema\Schema;
use App\DatabaseConnection as DB;

$instance = DB::getInstance();
$conn = $instance->getConnection();


$schema =new Schema();
$product = $schema->createTable("product");
$product->addColumn("id", "integer", array("unsigned" => true,"autoincrement" => true));
$product->addColumn("sku", "string", array("length" => 60));
$product->addColumn("name", "string", array("length" => 255));
$product->addColumn("description", "string", array("length" => 255));
$product->addColumn("price", "string", array("length" => 60));
$product->addColumn("parentId", "integer", array("unsigned" => true));
$product->setPrimaryKey(array("id"));


$product_meta = $schema->createTable("product_meta");
$product_meta->addColumn("prod_id", "integer", array("unsigned" => true));
$product_meta->addColumn("meta_name", "string", array("length" => 255));
$product_meta->addColumn("meta_value", "string", array("length" => 255));
$product_meta->addForeignKeyConstraint($product, array('prod_id'), array('id'),array("onUpdate" => "CASCADE"));


/* Set the Schema output platform, as we are using MySQL
   a Mysql schema will be generated. */
   $platform = $conn->getDatabasePlatform();
 
   /* The 'queries' variable will now hold the 
      an array of sql statements.
   */
   $queries = $schema->toSql($platform);
//    dd($queries[0]);
foreach($queries as $query) {
    // dd($query);
    $stmt = $conn->prepare($query);
    $stmt->execute();
}