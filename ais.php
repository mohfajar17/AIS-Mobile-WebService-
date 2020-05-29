<?php
	function login($conn, $user_name, $password, $last_ip){ //get login data
		date_default_timezone_set('Asia/Jakarta');
		$user_lastlogin = date("Y-m-d H:i:s");

		$sql = "SELECT * FROM ki_user WHERE user_name='".$user_name."' AND user_pwd=MD5('".$password."') AND user_enabled='1' AND is_mobile='1'";
		$qur = mysqli_query($conn, $sql);

		$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
		$count = mysqli_num_rows($qur);
		
		if($count>0){
			$sql = "UPDATE `ki_user` SET `user_lastlogin` = '".$user_lastlogin."',`last_ip` ='".$last_ip."' WHERE user_name='".$user_name."' AND user_pwd=MD5('".$password."')";
			$qur = mysqli_query($conn, $sql);

			$sql_value = "SELECT ki_user.employee_id, ki_user.user_name, ki_user.user_displayname, ki_user.usergroup_id, ki_usergroup.usergroup_name, ki_user.user_pwd, ki_user.token FROM ki_user LEFT JOIN ki_usergroup ON ki_user.usergroup_id=ki_usergroup.usergroup_id WHERE user_name='".$user_name."' AND user_pwd=MD5('".$password."')";
			$qur_value = mysqli_query($conn, $sql_value);

			$row = mysqli_fetch_array($qur_value, MYSQLI_ASSOC);
		} else {
		 	$row=array();
		}

		mysqli_close($conn);
		return $row;
	}

	function employeeData($conn, $employee_id){ //get employee data
		$sql = "SELECT employee_number, place_birthday, birthday, address, mobile_phone, email1 FROM ki_employee WHERE employee_id='".$employee_id."'";
		$qur = mysqli_query($conn, $sql);

		$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
		$count = mysqli_num_rows($qur);
		
		if($count==0)
			$row=array();

		mysqli_close($conn);
		return $row;
	}

	function employeeLeave($conn, $employee_id){ //get employee leave
		$sql = "SELECT COUNT(employee_id) AS 'leave' FROM ki_employee_leave WHERE employee_id='".$employee_id."' AND proposed_date IS NULL AND status='Aktif'";
		$qur = mysqli_query($conn, $sql);

		$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
		$count = mysqli_num_rows($qur);
		
		if($count==0)
			$row=array();

		mysqli_close($conn);
		return $row;
	}

	function employeeMoneybox($conn, $employee_number){ //get employee leave
		$sql = "SELECT * FROM ki_employee_report WHERE employee_number='".$employee_number."'";
		$qur = mysqli_query($conn, $sql);
		
		if(mysqli_num_rows($qur)>0){
			$sql = "SELECT SUM(moneybox) AS 'money_box' FROM ki_employee_report WHERE employee_number='".$employee_number."'";
			$qur = mysqli_query($conn, $sql);
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
		} else $row=array();

		mysqli_close($conn);
		return $row;
	}

	function editAccount($conn, $user_name, $new_password, $old_password){ //update account data
		$sql = "SELECT * FROM ki_user WHERE user_name = '".$user_name."' AND user_pwd = MD5('".$old_password."')";
		$qur = mysqli_query($conn, $sql);

		$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
		$count = mysqli_num_rows($qur);
		
		if($count>0){
			$sql = "UPDATE `ki_user` SET `user_pwd` = MD5('".$new_password."') WHERE user_name = '".$user_name."'";
			$qur = mysqli_query($conn, $sql);

			$sql_value = "SELECT employee_id, user_name, user_displayname, user_pwd, token FROM ki_user WHERE user_name='".$user_name."'";
			$qur_value = mysqli_query($conn, $sql_value);

			$row = mysqli_fetch_array($qur_value, MYSQLI_ASSOC);
		} else {
		 	$row=array();
		}

		mysqli_close($conn);
		return $row;
	}

	function editPersonalData($conn, $employee_id, $address, $mobile_phone, $email){ //update account data
		$sql = "SELECT * FROM ki_employee WHERE employee_id = '".$employee_id."'";
		$qur = mysqli_query($conn, $sql);

		$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
		$count = mysqli_num_rows($qur);
		
		if($count>0){
			$sql = "UPDATE `ki_employee` SET `address` = '".$address."', `mobile_phone` = '".$mobile_phone."', `email1` = '".$email."' WHERE employee_id = '".$employee_id."'";
			$qur = mysqli_query($conn, $sql);

			$sql_value = "SELECT employee_id, employee_number, place_birthday, birthday, address, mobile_phone, email1 FROM ki_employee WHERE employee_id='".$employee_id."'";
			$qur_value = mysqli_query($conn, $sql_value);

			$row = mysqli_fetch_array($qur_value, MYSQLI_ASSOC);
		} else {
		 	$row=array();
		}

		mysqli_close($conn);
		return $row;
	}

	function photoProfile($conn, $employee_id, $photo_profile){
		$input = base64_decode($photo_profile);
    	file_put_contents('photo/'.$employee_id.'.jpg', $input);

		// Insert data into data base
		// $sql = "SELECT * FROM atlet WHERE id_atlet = '".$userName."'";
		// $qur = mysqli_query($conn, $sql);

		// $row = mysqli_fetch_array($qur,MYSQLI_ASSOC);
		// $count = mysqli_num_rows($qur);

		// if($count>0){
		// 	$sql = "UPDATE `atlet` SET `photo_profile` = '".$userName.".png' WHERE user_name = '".$userName."'";
		// 	$qur = mysqli_query($conn, $sql);
		// 	$row['user_name'] = $userName;
		// 	$row['photo_profile'] = $photoProfile;
		// }else{
		//  	$row = array();
		// }
    	$row['employee_id'] = $employee_id;
		mysqli_close($conn);
		return $row;
	}

	function listSalesQuotation($conn){ //get
        $sql = "SELECT sales_quotation_id, sales_quotation_number, description FROM ki_sales_quotation WHERE status='New' ORDER BY sales_quotation_id DESC";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        $row = array();

        if($count>0){
            while($r = mysqli_fetch_assoc($qur)) {
                $row[] = $r;
            }
        }else{
            $row = array();
        }
		mysqli_close($conn);
	    return $row;
	}

	function listEmployee($conn){ //get
        $sql = "SELECT fullname FROM ki_employee ORDER BY fullname ASC";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        $row = array();

        if($count>0){
            while($r = mysqli_fetch_assoc($qur)) {
                $row[] = $r;
            }
        }else{
            $row = array();
        }
		mysqli_close($conn);
	    return $row;
	}

	function listWorkbase($conn){ //get
        $sql = "SELECT company_workbase_name FROM ki_company_workbase ORDER BY company_workbase_name ASC";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        $row = array();

        if($count>0){
            while($r = mysqli_fetch_assoc($qur)) {
                $row[] = $r;
            }
        }else{
            $row = array();
        }
		mysqli_close($conn);
	    return $row;
	}

	function listDepartmen($conn){ //get
        $sql = "SELECT department_name FROM ki_department ORDER BY department_name ASC";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        $row = array();

        if($count>0){
            while($r = mysqli_fetch_assoc($qur)) {
                $row[] = $r;
            }
        }else{
            $row = array();
        }
		mysqli_close($conn);
	    return $row;
	}

	function listSalesOrder($conn){ //get
        $sql = "SELECT sales_order_number, short_description FROM ki_sales_order ORDER BY sales_order_number DESC";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        $row = array();

        if($count>0){
            while($r = mysqli_fetch_assoc($qur)) {
                $row[] = $r;
            }
        }else{
            $row = array();
        }
		mysqli_close($conn);
	    return $row;
	}
?>