<?php
	include 'ais.php';
	include 'config.php';

	function isTheseParametersAvailable($params){
		$available = true;
		$missingparams = "";
		foreach($params as $param){
			if(!isset($_POST[$param]) || strlen($_POST[$param])<=0){
				$available = false;
				$missingparams = $missingparams . ", " . $param;
			}
		}
		if(!$available){
			$response = array();
			$response['error'] = true;
			$response['message'] = 'Parameters' . substr($missingparams, 1,
			strlen($missingparams)) . ' missing';
			echo json_encode($response);
			die();
		}
	}

	$response = array();
	if(isset($_GET['apicall'])){
		switch($_GET['apicall']){
			case 'login': //get
				isTheseParametersAvailable(array('user_name', 'password', 'last_ip'));
				$result=login($conn, $_POST['user_name'], $_POST['password'], $_POST['last_ip']);
				if($result){
					$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!", "data" => array());
				}
				break;
			case 'employeeData': //get
				isTheseParametersAvailable(array('employee_id'));
				$result=employeeData($conn, $_POST['employee_id']);
				if($result){
					$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!", "data" => array());
				}
				break;
			case 'employeeLeave': //get
				isTheseParametersAvailable(array('employee_id'));
				$result=employeeLeave($conn, $_POST['employee_id']);
				if($result){
					$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				} else {
					$response = array("status" => 0, "massage" => "Some error!", "data" => array());
				}
				break;
			case 'employeeMoneybox':
				isTheseParametersAvailable(array('employee_number'));
				$result=employeeMoneybox($conn, $_POST['employee_number']);
				if ($result) {
					$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				} else {
					$response = array("status" => 0, "massage" => "Some error!", "data" => array());
				}
				break;
			case 'editAccount': //get
				isTheseParametersAvailable(array('user_name', 'new_password', 'old_password'));
				$result=editAccount($conn, $_POST['user_name'], $_POST['new_password'], $_POST['old_password']);
				if($result){
					$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!", "data" => array());
				}
				break;
			case 'editPersonalData': //get
				isTheseParametersAvailable(array('employee_id', 'address', 'mobile_phone', 'email'));
				$result=editPersonalData($conn, $_POST['employee_id'], $_POST['address'], $_POST['mobile_phone'], $_POST['email']);
				if($result){
					$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!", "data" => array());
				}
				break;
			case 'photoProfile': //update
				isTheseParametersAvailable(array('employee_id', 'photo_profile'));
				$result=photoProfile($conn, $_POST['employee_id'], $_POST['photo_profile']);
				if($result){
					$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!", "data" => array());
				}
				break;
			case 'listSalesQuotation': //get
				$response = array("status" => 1, "massage" => "Success!", "data" => listSalesQuotation($conn));
				break;
			case 'listEmployee': //get
				$response = array("status" => 1, "massage" => "Success!", "data" => listEmployee($conn));
				break;
			case 'listWorkbase': //get
				$response = array("status" => 1, "massage" => "Success!", "data" => listWorkbase($conn));
				break;
			case 'listDepartmen': //get
				$response = array("status" => 1, "massage" => "Success!", "data" => listDepartmen($conn));
				break;
			case 'listSalesOrder': //get
				$response = array("status" => 1, "massage" => "Success!", "data" => listSalesOrder($conn));
				break;
			
		}
	}else{
		$response['error'] = true;
		$response['message'] = 'Invalid API Call';
	}
	echo json_encode($response);
?>