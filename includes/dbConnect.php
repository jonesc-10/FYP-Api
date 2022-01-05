<?php 

   class dbConnect {

      private $con; 

      function connect() {

         include_once dirname(__FILE__) . '/Constants.php';

         $this -> con = new mysqli(DB_HOST, DB_USER, DB_PW, DB_NAME);

         if(mysqli_connect_errno()){
            echo "Failed to connect to database " . mysqli_connect_error();
            return null;
         }

         return $this -> con;

      }

   }