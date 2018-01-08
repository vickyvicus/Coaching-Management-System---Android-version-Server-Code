<?php
	error_reporting(E_ALL ^ E_DEPRECATED);
	class DB_Connect
	{
		public $con;
		//Constructor
		function __construct()
		{}
		//Destructor
		function __destruct()
		{}
		//Connecting to database
		public function connect()
		{
			require_once 'db_config.php';
			
			//Connecting to mysql
			$this->con=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD,DB_DATABASE)or die(mysqli_error($this->con));
			if(mysqli_connect_errno())
			{
				die("Database Connection Failed");
			}
			//Return Database Handler
			return $this->con;
		}
		//Closing database connection
		public function close()
		{
			mysqli_close($this->con);
		}
	}
?>