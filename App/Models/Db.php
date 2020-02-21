<?php 


namespace App\Models;
use PDO;
class Db 
{
	
//para conectar cualquier bd
public function conectarBd($host,$dbmane,$username,$password){

try {

    $conn = new PDO("mysql:host=".$host.";dbname=".$dbmane, $username, $password);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->query("SET NAMES 'utf8';");
    return $conn;
   /* echo "Connected successfully";*/
  
   }catch(PDOException $e)
    {
   /* echo "Connection failed: " . $e->getMessage();*/
    }


}


	
}
 ?>