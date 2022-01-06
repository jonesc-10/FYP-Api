<?php 

class dbOps {

   private $con;

   function __construct() {

      require_once dirname(__FILE__) . '/dbConnect.php';

      $db = new dbConnect;

      $this -> con = $db -> connect();

   }

   public function userLogin($username, $password){

      if($this -> doesUserExist($username)){
         $hashed_password = $this -> getUsersPasswordByUsername($username); 
         if(password_verify($password, $hashed_password)){
            return USER_AUTHENTICATED;
         }else{
            return USER_NOT_AUTHENTICATED;
         }
      }else{
         return USER_NOT_FOUND; 
      }

   }


   public function getUserByUsername($username){
      $stmt = $this -> con -> prepare("SELECT id, username, full_name, age FROM add_patient WHERE username = ?");
      $stmt->bind_param("s", $username);
      $stmt->execute(); 
      $stmt->bind_result($id, $username, $full_name, $age);
      $stmt->fetch(); 
      $user = array(); 
      $user['id'] = $id; 
      $user['username']=$username; 
      $user['full_name'] = $full_name; 
      $user['age'] = $age; 
      return $user; 
   }

   private function doesUserExist($username){

      $stmt = $this -> con -> prepare("SELECT id FROM add_patient WHERE username = ?");
      $stmt->bind_param("s", $username);
      $stmt->execute(); 
      $stmt->store_result(); 
      return $stmt->num_rows > 0;  

   }

   private function getUsersPasswordByUsername($username){

      $stmt = $this -> con-> prepare("SELECT password FROM add_patient WHERE username = ?");
      $stmt->bind_param("s", $username);
      $stmt->execute(); 
      $stmt->bind_result($password);
      $stmt->fetch(); 
      return $password; 

   }


}