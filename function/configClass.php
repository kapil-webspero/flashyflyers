<?php

	ob_start();

	class CONNECTION {



	const HOST = 'localhost';


	const USERNAME = 'root1';

		const PASS = '123456';

		const DATABASE = 'flashyflyers_2';




		function __construct() {
// print_r(phpinfo()); die();
			$CONNECT = mysql_connect(self::HOST,self::USERNAME,self::PASS) or die(mysql_error());

			$DB = mysql_select_db(self::DATABASE,$CONNECT) or die(mysql_error());

		}

	}

	$OBJ = new CONNECTION;



	// Mysql PDO

	/*try{

		$conn = new PDO("mysql:host=localhost;dbname=FlashyFlyers","ff_developer","_8OgL[[OEezP");

	}

	catch(PDOException $e){

		echo "Error: ".$e->getMessage();

	}*/

?>
