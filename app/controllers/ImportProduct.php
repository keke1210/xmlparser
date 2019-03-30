<?php

namespace App\Controllers;

use App\Controllers\XmlParser as Parser;
use App\DatabaseConnection as DB;

class ImportProduct{
    public $productData;

    public function __construct($path)
    {
        $this->instance = DB::getInstance();
        $this->conn = $this->instance->getConnection();
        $parser = Parser::getInstance();
        
        $this->productData= $parser->parse($path);

        $this->methods_container($this->productData);
        // $this->insert_frontData_product($this->productData);
        // $this->query();

        // $this->insert_front_product_meta($this->productData);
        // $this->insert_locale($this->productData);
        // $this->insert_details_data($this->productData);
        // $this->insert_header_data($this->productData);
    }


    public function methods_container($productData) {
        $this->insert_frontData_product($productData);
        $this->query();

        $this->insert_front_product_meta($productData);
        $this->insert_locale($productData);
        $this->insert_details_data($productData);
        $this->insert_header_data($productData);
    }



    public function query(){
        $this->sql = "SELECT id FROM `product` ORDER BY id DESC LIMIT 1";
        $this->stmt = $this->conn->fetchAll($this->sql);
        $this->id= $this->stmt[0]['id'];
    }

    // Kontrollon nqs egziston ne db ky produkt
    public function handle_insert_if_duplicate($productData){
        $sku = $productData['productID'];

        $sql = "SELECT sku FROM `product` WHERE sku=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $sku);
        $stmt->execute();
        $array = $stmt->fetchAll();

        if($array){
            return false;
        }else {
            return true;
        }
    }


    // Inserting the front data for product
    public function insert_frontData_product($productData)
    {
        if($this->handle_insert_if_duplicate($productData)){

            $this->conn->insert('product', array(
            'sku'  => $productData['productID'],
            'name' => $productData['definitions']['headerData']['EShopDisplayName'],
            'description' => $productData['definitions']['headerData']['EShopLongDescription'],
            'price' => $productData['definitions']['detailsData'][0]['sapPrice'],
            'parentId' => 0,
        ));
    } else {
        die('This product already exists');
    }

    }

    // Inserting front meta data
    public function insert_front_product_meta($productData) 
    {

        foreach($productData as $key=>$arr) {
            if ($key == 'definitions' ) {
                continue;
            }

            $this->conn->insert('product_meta', array(
                'prod_id' => $this->id,
                'meta_name'  => $key,  // get keys of array
                'meta_value' => $arr,
            ));
        }
    }


    //insert locale data
    public function insert_locale($productData) {
        $locale = $productData['definitions'];
        //locale
        foreach($locale as $key=>$loc){
            if(!is_string($loc)){continue;}
            // dump($key);
            $this->conn->insert('product_meta', array(
                'prod_id' => $this->id,
                'meta_name'  => $key,  // get keys of array
                'meta_value' => $loc,
            ));
            
        }
    }


    // Details Data insert
    public function insert_details_data($productData) {
        $variations = $productData['definitions']['detailsData'];

        // details Data   0 1 2 3 
        foreach($variations as $key=>$variation) {     

            $this->conn->insert('product', array(
            'sku'  => $variation['skuID'],
            'name' => $variation['skuName'],
            'description' => $productData['definitions']['headerData']['EShopLongDescription'],
            'price' => $variation['sapPrice'],
            'parentId' => $this->id,
        )); 

        $variationId = $this->conn->fetchAll($this->sql);

            foreach($variation as $fieldName=>$var) {
                if(!is_string($var)){continue;}
                    $this->conn->insert('product_meta', array(
                    'prod_id' => $variationId[0]['id'],
                    'meta_name'  => $fieldName,  // get keys of array
                    'meta_value' => $var,
                    ));
                }
            }
        }




        public function insert_header_data($productData)
        {
            $headerData = $productData['definitions']['headerData'];

            // Header Data
            foreach ($headerData as $key=>$header) {
                if(is_array($header) ){
                    if(count($header)==1){
                        $header = "";
                    } else {
                        $header = json_encode($header);
                    }
                }
                // dump($id);
                $this->conn->insert('product_meta', array(
                    'prod_id' => $this->id,
                    'meta_name'  => $key,  // get keys of array
                    'meta_value' => $header,
                ));
            }

        }


}