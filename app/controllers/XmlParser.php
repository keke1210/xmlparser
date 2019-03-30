<?php

namespace App\Controllers;

use App\Singleton;


class XmlParser extends Singleton {

    // Turn Objects into arrays
    public function object_to_array($obj) {
        if(is_object($obj)) $obj = (array) $obj;
        if(is_array($obj)) {
            $new = array();
            foreach($obj as $key => $val) {
                $new[$key] = $this->object_to_array($val);
            }
        }
        else $new = $obj;
        return $new;       
    }

    // Parse Xml Data
    public function parse($path) {
            $dir = 'app/resources/'.$path.'.xml';
            if(file_exists($dir)){
                $product = simplexml_load_file('app/resources/'.$path.'.xml');
                return $this->object_to_array($product);
            } else {
                die("This file doesn't exists");
            } 
        }
}