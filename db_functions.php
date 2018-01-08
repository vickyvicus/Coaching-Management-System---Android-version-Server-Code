<?php
error_reporting(E_ALL ^ E_DEPRECATED);
class DB_Functions
{
	private $db;
	//Constructor
	function __construct()
	{
		require_once 'db_connect.php';
		//Connecting to Database
		$this->db=new DB_Connect();
		$this->db->connect();
	}
	//Destructor
	function __destruct()
	{}
	//Store User Details
	public function storeUser($name, $email, $password)
	{
		$hash=$this->hashSSHA($password);
		$encrypted_password=$hash["encrypted"]; //Encrypted Password
		$salt=$hash["salt"];
		$result=mysqli_query($this->db->con,"INSERT INTO users(user_name, user_email, user_password, salt)VALUES('$name','$email','$encrypted_password', '$salt')") or die(mysqli_error($this->db));
		//Check for Result
		if($result)
		{
			//Getting the details
			$uid=mysqli_insert_id($this->db->con); //Last Inserted ID
			$result=mysqli_query($this->db->con, "SELECT * FROM users WHERE user_id=$uid");
			//Return Details
			return mysqli_fetch_array($result);
		}
		else
		{
			return false;
		}
	}
	//Get User by email and password
	public function getUserByEmailAndPassword($email, $password)
	{
		$result=mysqli_query($this->db->con, "SELECT * FROM users WHERE user_email='$email'") or die(mysqli_connect_errno());
		
		//Check for result
		$no_of_rows=mysqli_num_rows($result);
		if($no_of_rows>0)
		{
			$result=mysqli_fetch_array($result);
			$salt=$result['salt'];
			$encrypted_password=$result['user_password'];
			$hash=$this->checkhashSSHA($salt, $password);
			
			//Check for Password
			if($encrypted_password == $hash)
			{
				return $result;
			}
		}
		else
		{
			return false;
		}
	}
	//Check user is existed or not
	public function isUserExisted($email)
	{
		$result=mysqli_query($this->db->con, "SELECT user_email FROM users WHERE user_email='$email'");
		$no_of_rows=mysqli_num_rows($result);
		if($no_of_rows > 0)
		{
			//User Exist
			return true;
		}
		else
		{
			//User Not Exist
			return false;
		}
	}
	//Encrypting password @param password returns salt and encrypted password
	public function hashSSHA($password)
	{
		$salt=sha1(rand());
		$salt=substr($salt, 0, 10);
		$encrypted=base64_encode(sha1($password.$salt, true).$salt);
		$hash=array("salt"=>$salt, "encrypted"=>$encrypted);
		return $hash;
	}
	//Decrypting password @param salt, password return hash string
	public function checkhashSSHA($salt, $password)
	{
		$hash=base64_encode(sha1($password.$salt, true).$salt);
		return $hash;
	}
	
	public function addCourse($name,$months,$days,$fee)
	{
		$result=mysqli_query($this->db->con,"INSERT INTO course(course_name, months, days, fee) VALUES('$name',$months,$days,$fee)");
		
		if($result)
		{
			//Getting the details
			$uid=mysqli_insert_id($this->db->con); //Last Inserted ID
			//$result=mysqli_query($this->db->con, "SELECT * FROM users WHERE user_id=$uid");
			//Return Details
			return $uid;
		}
		else
		{
			return false;
		}
	}
	
	public function fillCourse()
	{
		$result=mysqli_query($this->db->con, "SELECT * FROM course");
		$response=array();
		//$result = mysqli_fetch_array($result);
		if($result) 
		{
			while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			{
				array_push($response,array("course_id"=>$row['course_id'],"course_name"=>$row['course_name']));
			}
			
			return $response;
		}
		else
		{
			return false;
		}
	}

	public function addBatch($cid,$date,$time)
	{
		$result=mysqli_query($this->db->con,"INSERT INTO batch(course_id,start_date,timing) VALUES($cid,'$date','$time')");

		if($result)
		{
			//Getting the details
			$uid=mysqli_insert_id($this->db->con); //Last Inserted ID
			//$result=mysqli_query($this->db->con, "SELECT * FROM users WHERE user_id=$uid");
			//Return Details
			return $uid;
		}
		else
		{
			return false;
		}
	}

	public function fillBatch($cource_id)
	{
		$result=mysqli_query($this->db->con, "SELECT * FROM batch where course_id='$cource_id'");
		$response=array();
		//$result = mysqli_fetch_array($result);
		if($result)
		{
			while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			{
				array_push($response,array("batch_id"=>$row['batch_id'],"start_date"=>$row['start_date'],"timing"=>$row['timing']));
			}

			return $response;
		}
		else
		{
			return false;
		}
	}

	public function addStudentEntry($name,$email,$mobile,$bid,$comments,$path)
	{
		$result=mysqli_query($this->db->con,"INSERT INTO studentrecord(name,mobile,email,batch_id,comments,photo) VALUES('$name','$mobile','$email',$bid,'$comments','$path')");

		if($result)
		{
			//Getting the details
			$uid=mysqli_insert_id($this->db->con); //Last Inserted ID
			//$result=mysqli_query($this->db->con, "SELECT * FROM users WHERE user_id=$uid");
			//Return Details
			return $uid;
		}
		else
		{
			return false;
		}
	}

	public function addStudentQuery($name,$email,$mobile,$cid,$comments,$path)
	{
		$result=mysqli_query($this->db->con,"INSERT INTO studentquery(name,mobile,email,course_id,comments,photo) VALUES('$name','$mobile','$email',$cid,'$comments','$path')");

		if($result)
		{
			//Getting the details
			$uid=mysqli_insert_id($this->db->con); //Last Inserted ID
			//$result=mysqli_query($this->db->con, "SELECT * FROM users WHERE user_id=$uid");
			//Return Details
			return $uid;
		}
		else
		{
			return false;
		}
	}

	public function addFeeRecord($student_id,$batch_id,$fee,$due)
	{
		$result=mysqli_query($this->db->con,"INSERT INTO fees(student_id,batch_id,fee,due) VALUES($student_id,$batch_id,$fee,$due)");

		if($result)
		{
			//Getting the details
			$receipt_no=mysqli_insert_id($this->db->con); //Last Inserted ID
			return $receipt_no;
		}
		else
		{
			return false;
		}
	}

	public function getStudentRecord($batch_id)
	{
		$result=mysqli_query($this->db->con, "SELECT * FROM studentrecord WHERE batch_id=$batch_id");
		//echo $batch_id;
		$response=array();
		//$result = mysqli_fetch_array($result);
		if($result)
		{
			while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			{
				array_push($response,array("name"=>$row['name'],"mobile"=>$row['mobile'],"email"=>$row['email'],"photo"=>$row['photo'],"id"=>$row['student_id'],"comments"=>$row['comments']));
			}

			return $response;
		}
		else
		{
			return false;
		}
	}

	public function getStudentQuery($course_id)
	{
		$result=mysqli_query($this->db->con, "SELECT * FROM studentquery WHERE course_id=$course_id");
		$response=array();
		//$result = mysqli_fetch_array($result);
		if($result)
		{
			while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			{
				array_push($response,array("name"=>$row['name'],"mobile"=>$row['mobile'],"email"=>$row['email'],"photo"=>$row['photo'],"id"=>$row["query_id"],"comments"=>$row["comments"]));
			}

			return $response;
		}
		else
		{
			return false;
		}
	}

	public function getReceiptNumber()
	{
		$result=mysqli_query($this->db->con, "select receipt_no from fees order by receipt_no desc");
		//$response=array();
		//$result = mysqli_fetch_array($result);
		if($result)
		{
			$row=mysqli_fetch_array($result);
			
			return $row['receipt_no'];
		}
		else
		{
			return false;
		}
	}

	public function fillStudent($batch_id)
	{
		$result=mysqli_query($this->db->con, "SELECT * FROM studentrecord where batch_id=$batch_id");
		$response=array();
		//$result = mysqli_fetch_array($result);
		if($result)
		{
			while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			{
				array_push($response,array("student_id"=>$row['student_id'],"name"=>$row['name']));
			}

			return $response;
		}
		else
		{
			return false;
		}
	}

	public function fillStudentInfo($student_id)
	{
		$result=mysqli_query($this->db->con, "SELECT * FROM studentrecord where student_id=$student_id");
		$response=array();
		//$result = mysqli_fetch_array($result);
		if($result)
		{
			while($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			{
				array_push($response,array("mobile"=>$row['mobile'],"email"=>$row['email']));
			}

			return $response;
		}
		else
		{
			return false;
		}
	}

	public function fetchFeeRecord($student_id,$batch_id)
	{
		$result=mysqli_query($this->db->con, "SELECT * FROM fees where student_id=$student_id and batch_id=$batch_id order by receipt_no desc");
		//$result = mysqli_fetch_array($result);
		if($result)
		{
			if($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			{
				$response=(array("fee"=>$row['fee'],"due"=>$row['due']));
				return $response;
			}


		}
		else
		{
			return false;
		}
	}

	public function getFee($cource_id)
	{
		$result=mysqli_query($this->db->con, "SELECT fee FROM course where course_id='$cource_id'");

		if($result)
		{
			if($row = mysqli_fetch_array($result,MYSQL_ASSOC))
			{
				$response=$row['fee'];
			}

			return $response;
		}
		else
		{
			return false;
		}
	}

	public function updateStudentEntry($sid,$name,$email,$mobile,$bid,$comments,$path)
	{
		$result=mysqli_query($this->db->con,"UPDATE `studentrecord` SET name='$name',mobile='$mobile',email='$email',batch_id=$bid,comments='$comments',photo='$path' WHERE student_id=$sid");

		if($result)
		{
			
			return $sid;
		}
		else
		{
			return false;
		}
	}

	public function updateStudentQuery($qid,$name,$email,$mobile,$cid,$comments,$path)
	{
		$result=mysqli_query($this->db->con,"UPDATE `studentquery` SET name='$name',email='$email',mobile='$mobile',course_id=$cid,comments='$comments',`photo`='$path' WHERE query_id=$qid");

		if($result)
		{

			return $qid;
		}
		else
		{
			return false;
		}
	}

	public function deleteStudentRecord($sid)
	{
		$result=mysqli_query($this->db->con,"DELETE FROM studentrecord WHERE student_id=$sid");

		if($result)
		{

			return true;
		}
		else
		{
			return false;
		}
	}

	public function deleteStudentQuery($qid)
	{
		$result=mysqli_query($this->db->con,"DELETE FROM studentquery WHERE query_id=$qid");

		if($result)
		{

			return true;
		}
		else
		{
			return false;
		}
	}
}
?>