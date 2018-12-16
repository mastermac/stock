<?php
require 'db_config.php';
session_start();
function test_input($data)
{
   $data = trim($data);
   $data = stripslashes($data);
   return $data;
}
$response['error'] = true;
if ($_POST['fromLogin'] == "key1234")
{
   if (empty($_POST["email"]) || empty($_POST["password"]))
   {
      $response['error'] = true;
   }
   else
   {
      $username = test_input($_POST["email"]);
      $pwd = test_input($_POST["password"]);
      $sql = "SELECT * FROM vendor WHERE email='$username' and pwd='$pwd'";
      $mysqli=getConn();
      $result = $mysqli->query($sql);
      if ($result->num_rows > 0)
      {
         while ($row = $result->fetch_assoc())
         {
            $_SESSION['userid'] = $row["vid"];
            $_SESSION['usertype'] = $row["type"];
            $_SESSION['username'] = $row["name"];
            $_SESSION['series'] = $row["series"];
            $_SESSION['canExport']=$row['canExport'];
            $_SESSION['vendorid']=$row['code'];
         }
         $_SESSION['msg'] = 1;
         $response['error'] = false;
	 $sql1 = "UPDATE product set userid=".$_SESSION['userid']." where vendor=".$_SESSION['vendorid']." and userid!=".$_SESSION['userid'].";";
      	 $mysqli1 =getConn();
      	 $result1 = $mysqli1->query($sql1);
         writelog(1,"1");
      }
      else
         writelog(1,"0");
   }
   echo json_encode($response);
}
else if ($_POST['fromLogout'] == "key5678")
{
   writelog(2,"1");
   session_destroy();
}
?>