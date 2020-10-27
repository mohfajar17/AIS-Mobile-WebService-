<?php
	include 'aisApproval.php';
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
			case 'getApprovalAssign':
				isTheseParametersAvailable(array('empId'));
				$result=getApprovalAssign($conn, $_POST['empId']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break;
			case 'getTotalSum':
				$response = array(
					"MaterialRequisition" => count(getMaterialRequisition($conn)), 
					"WorkOrder" => count(getWorkOrder($conn)), 
					"Spkl" => count(getSpkl($conn)), 
					"ProposedBudget" => count(getProposedBudget($conn)), 
					"CashProjectReport" => count(getCashProjectReport($conn)), 
					"TunjanganKaryawan" => count(getTunjanganKaryawan($conn)), 
					"TunjanganTemporary" => count(getTunjanganTemporary($conn)), 
					"PurchaseOrder" => count(getPurchaseOrder($conn)), 
					"PurchaseService" => count(getPurchaseService($conn)), 
					"CashOnDelivery" => count(getCashOnDelivery($conn)), 
					"MaterialReturn" => count(getMaterialReturn($conn)), 
					"StockAdjustment" => count(getStockAdjustment($conn)), 
					"Budgeting" => count(getBudgeting($conn)), 
					"PaymentSupplier" => count(getPaymentSupplier($conn)), 
					"BankTransaction" => count(getBankTransaction($conn)), 
					"Expense" => count(getExpense($conn)), 
					"CashAdvance" => count(getCashAdvance($conn))
				);
				break;
			case 'getMaterialRequisition':
				$response = array("status" => 1, "massage" => "Success!", "data" => getMaterialRequisition($conn));
				break;
			case 'updateMaterialRequisition':
				isTheseParametersAvailable(array('id', 'approve1', 'approve2', 'approve3'));
				$result=updateMaterialRequisition($conn, $_POST['id'], $_POST['approve1'], $_POST['approve2'], $_POST['approve3']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'updateMaterialRequisitionId':
				isTheseParametersAvailable(array('id', 'user', 'command', 'code'));
				$result=updateMaterialRequisitionId($conn, $_POST['id'], $_POST['user'], $_POST['command'], $_POST['code']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getWorkOrder':
				$response = array("status" => 1, "massage" => "Success!", "data" => getWorkOrder($conn));
				break;
			case 'updateWorkOrder':
				isTheseParametersAvailable(array('id', 'approve1', 'approve2', 'approve3'));
				$result=updateWorkOrder($conn, $_POST['id'], $_POST['approve1'], $_POST['approve2'], $_POST['approve3']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'updateWorkOrderId':
				isTheseParametersAvailable(array('id', 'user', 'command', 'code'));
				$result=updateWorkOrderId($conn, $_POST['id'], $_POST['user'], $_POST['command'], $_POST['code']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getSpkl':
				$response = array("status" => 1, "massage" => "Success!", "data" => getSpkl($conn));
				break;
			case 'updateSpkl':
				isTheseParametersAvailable(array('id', 'approve1', 'approve2'));
				$result=updateSpkl($conn, $_POST['id'], $_POST['approve1'], $_POST['approve2']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'updateSpklId':
				isTheseParametersAvailable(array('id', 'user', 'code'));
				$result=updateSpklId($conn, $_POST['id'], $_POST['user'], $_POST['code']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getProposedBudget':
				$response = array("status" => 1, "massage" => "Success!", "data" => getProposedBudget($conn));
				break;
			case 'UpdateProposedBudget':
				isTheseParametersAvailable(array('id', 'approve1', 'approve2', 'approve3'));
				$result=UpdateProposedBudget($conn, $_POST['id'], $_POST['approve1'], $_POST['approve2'], $_POST['approve3']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'UpdateProposedBudgetId':
				isTheseParametersAvailable(array('id', 'user', 'command', 'code'));
				$result=UpdateProposedBudgetId($conn, $_POST['id'], $_POST['user'], $_POST['command'], $_POST['code']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getCashProjectReport':
				$response = array("status" => 1, "massage" => "Success!", "data" => getCashProjectReport($conn));
				break;
			case 'updateCashProjectReport':
				isTheseParametersAvailable(array('id', 'approve1', 'approve2', 'approve3'));
				$result=updateCashProjectReport($conn, $_POST['id'], $_POST['approve1'], $_POST['approve2'], $_POST['approve3']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'updateCashProjectReportId':
				isTheseParametersAvailable(array('id', 'user', 'command', 'code'));
				$result=updateCashProjectReportId($conn, $_POST['id'], $_POST['user'], $_POST['command'], $_POST['code']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getTunjanganKaryawan':
				$response = array("status" => 1, "massage" => "Success!", "data" => getTunjanganKaryawan($conn));
				break;
			case 'updateTunjanganKaryawan':
				isTheseParametersAvailable(array('id', 'approve1', 'approve2'));
				$result=updateTunjanganKaryawan($conn, $_POST['id'], $_POST['approve1'], $_POST['approve2']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'updateTunjanganKaryawanId':
				isTheseParametersAvailable(array('id', 'user', 'command', 'code'));
				$result=updateTunjanganKaryawanId($conn, $_POST['id'], $_POST['user'], $_POST['command'], $_POST['code']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getTunjanganTemporary':
				$response = array("status" => 1, "massage" => "Success!", "data" => getTunjanganTemporary($conn));
				break;
			case 'updateTunjanganTemporary':
				isTheseParametersAvailable(array('id', 'approve1', 'approve2'));
				$result=updateTunjanganTemporary($conn, $_POST['id'], $_POST['approve1'], $_POST['approve2']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'updateTunjanganTemporaryId':
				isTheseParametersAvailable(array('id', 'user', 'command', 'code'));
				$result=updateTunjanganTemporaryId($conn, $_POST['id'], $_POST['user'], $_POST['command'], $_POST['code']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getPurchaseOrder':
				$response = array("status" => 1, "massage" => "Success!", "data" => getPurchaseOrder($conn));
				break;
			case 'updatePurchaseOrder':
				isTheseParametersAvailable(array('id', 'approve1'));
				$result=updatePurchaseOrder($conn, $_POST['id'], $_POST['approve1']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'updatePurchaseOrderId':
				isTheseParametersAvailable(array('id', 'user', 'command', 'code'));
				$result=updatePurchaseOrderId($conn, $_POST['id'], $_POST['user'], $_POST['command'], $_POST['code']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getPurchaseService':
				$response = array("status" => 1, "massage" => "Success!", "data" => getPurchaseService($conn));
				break;
			case 'updatePurchaseService':
				isTheseParametersAvailable(array('id', 'approve1'));
				$result=updatePurchaseService($conn, $_POST['id'], $_POST['approve1']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'updatePurchaseServiceId':
				isTheseParametersAvailable(array('id', 'user', 'command', 'code'));
				$result=updatePurchaseServiceId($conn, $_POST['id'], $_POST['user'], $_POST['command'], $_POST['code']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getCashOnDelivery':
				$response = array("status" => 1, "massage" => "Success!", "data" => getCashOnDelivery($conn));
				break;
			case 'updateCashOnDelivery':
				isTheseParametersAvailable(array('id', 'approve1'));
				$result=updateCashOnDelivery($conn, $_POST['id'], $_POST['approve1']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'updateCashOnDeliveryId':
				isTheseParametersAvailable(array('id', 'user', 'command', 'code'));
				$result=updateCashOnDeliveryId($conn, $_POST['id'], $_POST['user'], $_POST['command'], $_POST['code']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getMaterialReturn':
				$response = array("status" => 1, "massage" => "Success!", "data" => getMaterialReturn($conn));
				break;
			case 'updateMaterialReturnId':
				isTheseParametersAvailable(array('id'));
				$result=updateMaterialReturnId($conn, $_POST['id']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getStockAdjustment': 
				$response = array("status" => 1, "massage" => "Success!", "data" => getStockAdjustment($conn));
				break;
			case 'updateStockAdjustment':
				isTheseParametersAvailable(array('id', 'command'));
				$result=updateStockAdjustment($conn, $_POST['id'], $_POST['command']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'updateStockAdjustmentId':
				isTheseParametersAvailable(array('id', 'user', 'command'));
				$result=updateStockAdjustmentId($conn, $_POST['id'], $_POST['user'], $_POST['command']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getBudgeting':
				$response = array("status" => 1, "massage" => "Success!", "data" => getBudgeting($conn));
				break;
			case 'updateBudgeting':
				isTheseParametersAvailable(array('id', 'approve1', 'approve2', 'approve3'));
				$result=updateBudgeting($conn, $_POST['id'], $_POST['approve1'], $_POST['approve2'], $_POST['approve3']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'updateBudgetingId':
				isTheseParametersAvailable(array('id', 'user', 'command', 'code'));
				$result=updateBudgetingId($conn, $_POST['id'], $_POST['user'], $_POST['command'], $_POST['code']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getPaymentSupplier':
				$response = array("status" => 1, "massage" => "Success!", "data" => getPaymentSupplier($conn));
				break;
			case 'updatePaymentSupplier':
				isTheseParametersAvailable(array('id', 'approve1', 'approve2', 'approve3'));
				$result=updatePaymentSupplier($conn, $_POST['id'], $_POST['approve1'], $_POST['approve2'], $_POST['approve3']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'updatePaymentSupplierId':
				isTheseParametersAvailable(array('id', 'user', 'command', 'code'));
				$result=updatePaymentSupplierId($conn, $_POST['id'], $_POST['user'], $_POST['command'], $_POST['code']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getBankTransaction':
				$response = array("status" => 1, "massage" => "Success!", "data" => getBankTransaction($conn));
				break;
			case 'updateBankTransaction':
				isTheseParametersAvailable(array('id'));
				$result=updateBankTransaction($conn, $_POST['id']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'updateBankTransactionId':
				isTheseParametersAvailable(array('id', 'user', 'command', 'code'));
				$result=updateBankTransactionId($conn, $_POST['id'], $_POST['user'], $_POST['command'], $_POST['code']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getExpense':
				$response = array("status" => 1, "massage" => "Success!", "data" => getExpense($conn));
				break;
			case 'updateExpense':
				isTheseParametersAvailable(array('id', 'approve1', 'approve2', 'approve3'));
				$result=updateExpense($conn, $_POST['id'], $_POST['approve1'], $_POST['approve2'], $_POST['approve3']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'updateExpenseId':
				isTheseParametersAvailable(array('id', 'user', 'command', 'code'));
				$result=updateExpenseId($conn, $_POST['id'], $_POST['user'], $_POST['command'], $_POST['code']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getCashAdvance':
				$response = array("status" => 1, "massage" => "Success!", "data" => getCashAdvance($conn));
				break;
			case 'updateCashAdvance':
				isTheseParametersAvailable(array('id', 'user', 'command', 'code'));
				$result=updateCashAdvance($conn, $_POST['id'], $_POST['user'], $_POST['command'], $_POST['code']);
				if($result){
					$response = array("status" => 1, "massage" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!");
				}
				break; 
			case 'getApprovalAccess':
				isTheseParametersAvailable(array('user', 'id', 'code'));
				$code = $_POST['code'];
				if ($code == 1) { 			// access approval MR
					$response = array("access1" => cekMaterialRequestApproval1($conn, $_POST['user'], $_POST['id']), 
						"access2" => cekMaterialRequestApproval2($conn, $_POST['user'], $_POST['id']),
						"access3" => cekMaterialRequestApproval3($conn, $_POST['user'], $_POST['id']));
				} else if ($code == 2) {	// access approval WR
					$response = array("access1" => cekWorkRequestApproval1($conn, $_POST['user'], $_POST['id']), 
						"access2" => cekWorkRequestApproval2($conn, $_POST['user'], $_POST['id']),
						"access3" => cekWorkRequestApproval3($conn, $_POST['user'], $_POST['id']));
				} else if ($code == 3) {	// access approval SPKL
					$response = array("access1" => cekSPKLApproval1($conn, $_POST['user'], $_POST['id']), 
						"access2" => cekSPKLApproval2($conn, $_POST['user'], $_POST['id']));
				} else if ($code == 4) {	// access approval PB
					$response = array("access1" => cekProposedBudgetApproval1($conn, $_POST['user'], $_POST['id']), 
						"access2" => cekProposedBudgetApproval2($conn, $_POST['user'], $_POST['id']),
						"access3" => cekProposedBudgetApproval3($conn, $_POST['user'], $_POST['id']));
				} else if ($code == 5) {	// access approval PB
					$response = array("access1" => cekCPRApproval1($conn, $_POST['user'], $_POST['id']), 
						"access2" => cekCPRApproval2($conn, $_POST['user'], $_POST['id']),
						"access3" => cekCPRApproval3($conn, $_POST['user'], $_POST['id']));
				} else if ($code == 6) {	// access approval TunKar
					$response = array("access1" => cekTunjanganKaryawanApproval1($conn, $_POST['user'], $_POST['id']), 
						"access2" => cekTunjanganKaryawanApproval2($conn, $_POST['user'], $_POST['id']));
				} else if ($code == 7) {	// access approval TunTemp
					$response = array("access1" => cekTunjanganTemporaryApproval1($conn, $_POST['user'], $_POST['id']), 
						"access2" => cekTunjanganTemporaryApproval2($conn, $_POST['user'], $_POST['id']));
				}
				break; 
		}
	}else{
		$response['error'] = true;
		$response['message'] = 'Invalid API Call';
	}
	echo json_encode($response);
?>