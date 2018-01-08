<?php
error_reporting(E_ALL ^ E_DEPRECATED);

if (isset($_POST['tag']) && $_POST['tag'] != '') 
{
    // get tag
    $tag = $_POST['tag'];
 
    // include DB_function
    require_once 'DB_Functions.php';
    $db = new DB_Functions();
 
    // response Array
    $response = array("tag" => $tag, "error" => false);
 
    // checking tag
    if ($tag == 'login') 
	{
        // Request type is check Login
        $email = $_POST['email'];
        $password = $_POST['password'];
 
        // check for user
        $user = $db->getUserByEmailAndPassword($email, $password);
        if ($user != false) 
		{
            // user found
            $response["error"] = false;
            $response["user_id"] = $user["user_id"];
            $response["user_name"] = $user["user_name"];
            $response["user_email"] = $user["user_email"];
            echo json_encode($response);
        } 
		else 
		{
            // user not found
            // echo json with error = 1
            $response["error"] = true;
            $response["error_msg"] = "Incorrect email or password!";
            echo json_encode($response);
        }
    } 
	else if ($tag == 'register') 
	{
        // Request type is Register new user
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
 
        // check if user is already existed
        if ($db->isUserExisted($email)) 
		{
            // user is already existed - error response
            $response["error"] = TRUE;
            $response["error_msg"] = "User already existed";
            echo json_encode($response);
        } 
		else 
		{
            // store user
            $user = $db->storeUser($name, $email, $password);
            if ($user) 
			{
                // user stored successfully
                $response["error"] = FALSE;
                $response["uid"] = $user["user_id"];
                $response["user"]["name"] = $user["user_name"];
                $response["user"]["email"] = $user["user_email"];
                echo json_encode($response);
            } 
			else 
			{
                // user failed to store
                $response["error"] = TRUE;
                $response["error_msg"] = "Error occured in Registartion";
                echo json_encode($response);
            }
        }
    } 
	
	else if($tag=='addcourse')
	{
		$name=$_POST['name'];
		$months=$_POST['months'];
		$days=$_POST['days'];
		$fee=$_POST['fee'];
		
		$insert_id=$db->addCourse($name,$months,$days,$fee);
		
		if($insert_id)
		{
				$response['error']=false;
				$response['cid']=$insert_id;
				echo json_encode($response);
		}
		
		else
		{
			$response['error']=true;
			$response["error_msg"] = "Error occured in Adding Course";
			echo json_encode($response);
		}
	}
	
	else if($tag=='fillcourse')
	{
		$courses=$db->fillCourse();
		
		if($courses)
		{
			return json_encode(array("response"=>$courses));
		}
	}
	else if($tag=='addbatch')
	{
		$cid=$_POST['cource_id'];
		$date=$_POST['date'];
		$time=$_POST['time'];

		$insert_id=$db->addBatch($cid,$date,$time);

		if($insert_id)
		{
			$response['error']=false;
			$response['bid']=$insert_id;
			echo json_encode($response);
		}

		else
		{
			$response['error']=true;
			$response["error_msg"] = "Error occured in Adding Batch";
			echo json_encode($response);
		}
	}
	else if($tag=='addstudententry')
	{
		$name=$_POST['name'];
		$email=$_POST['email'];
		$mobile=$_POST['mobile'];
		$bid=$_POST['batch_id'];
		$comments=$_POST['comments'];
		$photo=$_POST['photo'];
		$binary=base64_decode($photo);
		/*header('Content-Type: bitmap; charset=utf-8');
		$path='uploadedimages/'.$name.$name.rand();
		$file = fopen($path, 'wb');
		// Create File
		fwrite($file, $binary);
		fclose($file);
		*/

		$path="uploadedimages/"."entry".$name.$bid.rand().".JPG";
		file_put_contents($path, $binary);
		$insert_id=$db->addStudentEntry($name,$email,$mobile,$bid,$comments,$path);

		if($insert_id)
		{
			$response['error']=false;
			$response['sid']=$insert_id;
			echo json_encode($response);
		}

		else
		{
			$response['error']=true;
			$response["error_msg"] = "Error occured in Adding Course";
			echo json_encode($response);
		}
	}

	else if($tag=='addstudentquery')
	{
		$name=$_POST['name'];
		$email=$_POST['email'];
		$mobile=$_POST['mobile'];
		$cid=$_POST['course_id'];
		$comments=$_POST['comments'];
		$photo=$_POST['photo'];
		$binary=base64_decode($photo);
		/*header('Content-Type: bitmap; charset=utf-8');
		$path='uploadedimages/'.$name.$name.rand();
		$file = fopen($path, 'wb');
		// Create File
		fwrite($file, $binary);
		fclose($file);
		*/

		$path="uploadedimages/"."query".$name.$cid.rand().".JPG";
		file_put_contents($path, $binary);
		$insert_id=$db->addStudentQuery($name,$email,$mobile,$cid,$comments,$path);

		if($insert_id)
		{
			$response['error']=false;
			$response['qid']=$insert_id;
			echo json_encode($response);
		}

		else
		{
			$response['error']=true;
			$response["error_msg"] = "Error occured in Adding Course";
			echo json_encode($response);
		}
	}

	else if($tag=='addfeerecord')
	{
		$student_id=$_POST['student_id'];
		$batch_id=$_POST['batch_id'];
		$fee=$_POST['fee'];
		$due=$_POST['due'];

		$receipt_no=$db->addFeeRecord($student_id,$batch_id,$fee,$due);

		if($receipt_no)
		{
			$response['error']=false;
			$response['receipt_no']=$receipt_no;
			echo json_encode($response);
		}

		else
		{
			$response['error']=true;
			$response["error_msg"] = "Error occured in Adding Fee Record";
			echo json_encode($response);
		}
	}

	else if($tag=='updatestudententry')
	{
		$sid=$_POST['sid'];
		$path=$_POST['path'];
		$name=$_POST['name'];
		$email=$_POST['email'];
		$mobile=$_POST['mobile'];
		$bid=$_POST['batch_id'];
		$comments=$_POST['comments'];
		$photo=$_POST['photo'];
		$binary=base64_decode($photo);

		
		file_put_contents($path, $binary);
		$insert_id=$db->updateStudentEntry($sid,$name,$email,$mobile,$bid,$comments,$path);

		if($insert_id)
		{
			$response['error']=false;
			$response['sid']=$insert_id;
			echo json_encode($response);
		}

		else
		{
			$response['error']=true;
			$response["error_msg"] = "Error occured in updating student";
			echo json_encode($response);
		}
	}

	else if($tag=='updatestudentquery')
	{
		$qid=$_POST['qid'];
		$path=$_POST['path'];
		$name=$_POST['name'];
		$email=$_POST['email'];
		$mobile=$_POST['mobile'];
		$cid=$_POST['course_id'];
		$comments=$_POST['comments'];
		$photo=$_POST['photo'];
		$binary=base64_decode($photo);
		
		
		file_put_contents($path, $binary);
		$insert_id=$db->updateStudentQuery($qid,$name,$email,$mobile,$cid,$comments,$path);

		if($insert_id)
		{
			$response['error']=false;
			$response['sid']=$insert_id;
			echo json_encode($response);
		}

		else
		{
			$response['error']=true;
			$response["error_msg"] = "Error occured in updating query";
			echo json_encode($response);
		}
	}

	else if($tag=='deletestudententry')
	{
		$sid=$_POST['sid'];
		$flag=$db->deleteStudentRecord($sid);

		if($flag)
		{
			$response['error']=false;
			echo json_encode($response);
		}

		else
		{
			$response['error']=true;
			$response["error_msg"] = "Error occured in deleting record";
			echo json_encode($response);
		}
	}

	else if($tag=='deletestudentquery')
	{
		$qid=$_POST['qid'];
		$flag=$db->deleteStudentQuery($qid);

		if($flag)
		{
			$response['error']=false;
			echo json_encode($response);
		}

		else
		{
			$response['error']=true;
			$response["error_msg"] = "Error occured in deleting record";
			echo json_encode($response);
		}
	}

	else 
	{
        // user failed to store
        $response["error"] = TRUE;
        $response["error_msg"] = "Unknown 'tag' value. It should be either 'login' or 'register'";
        echo json_encode($response);
    }
} 

if (isset($_GET['tag']) && $_GET['tag'] != '') 
{
    // get tag
    $tag = $_GET['tag'];
 
    // include DB_function
    require_once 'DB_Functions.php';
    $db = new DB_Functions();
 
    // response Array
    $response = array("tag" => $tag, "error" => false);
	
	if($tag=='fillcourse')
	{
		$courses=$db->fillCourse();
		
		if($courses)
		{
			echo json_encode($courses);
		}
	}
	else if($tag=='fillbatch')
	{
		$course_id=$_GET['course_id'];
		$batches=$db->fillBatch($course_id);

		if($batches)
		{
			echo json_encode($batches);
		}
		else
		{
			echo "error";
		}
	}
	else if($tag=='getstudentrecord')
	{
		$batch_id=$_GET['batch_id'];
		$record=$db->getStudentRecord($batch_id);

		if($record)
		{
			echo json_encode($record);
		}
		else
		{
			$response['error']=true;
			$response["error_msg"] = "No Record Found";
			echo json_encode(array($response));
		}
	}
	else if($tag=='getstudentquery')
	{
		$course_id=$_GET['course_id'];
		$record=$db->getStudentQuery($course_id);

		if($record)
		{
			echo json_encode($record);
		}
		else
		{
			$response['error']=true;
			$response["error_msg"] = "No Record Found";
			echo json_encode(array($response));
		}
	}
	else if($tag=='receipt_no')
	{
		$number=$db->getReceiptNumber();

		if($number) {
			$response['error'] = false;
			$response['receipt_no'] = $number;
			echo json_encode($response);
		}
		else
		{
			$response['error']=true;
			$response["error_msg"] = "Error occured in Adding Course";
			echo json_encode($response);
		}
	}
	else if($tag=='fillstudent')
	{
		$batch_id=$_GET['batch_id'];
		$students=$db->fillStudent($batch_id);

		if($students)
		{
			echo json_encode($students);
		}
		else
		{
			echo "error";
		}
	}
	else if($tag=='fillstudentinfo')
	{
		$student_id=$_GET['student_id'];
		$students=$db->fillStudentInfo($student_id);

		if($students)
		{
			echo json_encode($students);
		}
		else
		{
			echo "error";
		}
	}
	else if($tag=='fetchfeerecord')
	{
		$student_id=$_GET['student_id'];
		$batch_id=$_GET['batch_id'];
		$record=$db->fetchFeeRecord($student_id,$batch_id);

		if($record)
		{
			$response['error'] = false;
			$response['fee'] = $record['fee'];
			$response['due']=$record['due'];
			echo json_encode($response);
		}
		else
		{
			$response['error']=true;
			$response["error_msg"] = "Cann't find fee record";
			echo json_encode($response);
		}
	}
	else if($tag=='getfee')
	{
		$course_id=$_GET['course_id'];
		$fee=$db->getFee($course_id);

		if($fee)
		{
			$response['error'] = false;
			$response['fee'] = $fee;
			echo json_encode($response);
		}
		else
		{
			echo "error";
		}
	}

}
	
	
else 
{
    ?>
	<html>
		<head>
			<title>Android and PHP</title>
		</head>
		<body>
			<div>
				Android PHP Introduction
			</div>
		</body>
	</html>
	<?php
}
?>