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
			case 'userActiveMobile': //get
				isTheseParametersAvailable(array('user_id'));
				$result=userActiveMobile($conn, $_POST['user_id']);
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
			case 'checkViewAccess':
				isTheseParametersAvailable(array('user_id', 'feature', 'access', 'id'));
				$result=checkViewAccess($conn, $_POST['user_id'], $_POST['feature'], $_POST['access'], $_POST['id']);
				if ($result) {
					$response = array("status" => $result, "massage" => "Success!");
				} else {
					$response = array("status" => 0, "massage" => "Error!");
				}
				break;
			case 'getHoliday':
				$response = array("status" => 1, "massage" => "Success!", "data" => getHoliday($conn));
				break;
			case 'getInv':
				$response = array("status" => 1, "massage" => "Success!", "data" => getInv($conn));
				break;
			case 'getSinv':
				$response = array("status" => 1, "massage" => "Success!", "data" => getSinv($conn));
				break;
			case 'getBank':
				$response = array("status" => 1, "massage" => "Success!", "data" => getBank($conn));
				break;
			case 'getSalesQuot':
				$response = array("status" => 1, "massage" => "Success!", "data" => getSalesQuot($conn));
				break;
			case 'getInventory':
				$response = array("status" => 1, "massage" => "Success!", "data" => getInventory($conn));
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
			case 'getDataJobOrder': //get
				$response = array("status" => 1, "massage" => "Success!", "data" => getDataJobOrder($conn, $_POST['jobOrder']));
				break;
			case 'listSalesQuotation': //get
				$response = array("status" => 1, "massage" => "Success!", "data" => listSalesQuotation($conn));
				break;
			case 'listSalesQuotationUpdate': //get
				$response = array("status" => 1, "massage" => "Success!", "data" => listSalesQuotationUpdate($conn));
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
			case 'listJobOrder': //get
				$response = array("status" => 1, "massage" => "Success!", "data" => listJobOrder($conn));
				break;
			case 'getApprovalAllow':
				isTheseParametersAvailable(array('user', 'code'));
				$code = $_POST['code'];
				if ($code == 1) { 			// access approval MR
					$response = array("access1" => accessAllow($conn, $_POST['user'], "approval-material-request-I"), 
						"access2" => accessAllow($conn, $_POST['user'], "approval-material-request-II"),
						"access3" => accessAllow($conn, $_POST['user'], "approval-material-request-III"));
				} else if ($code == 2) {	// access approval WR
					$response = array("access1" => accessAllow($conn, $_POST['user'], "approval1-work-order"), 
						"access2" => accessAllow($conn, $_POST['user'], "approval2-work-order"),
						"access3" => accessAllow($conn, $_POST['user'], "approval3-work-order"));
				} else if ($code == 3) {	// access approval SPKL
					$response = array("access1" => accessAllow($conn, $_POST['user'], "approval-overtime-workorder-I"),
						"access2" => accessAllow($conn, $_POST['user'], "approval-overtime-workorder-II"));
				} else if ($code == 4) {	// access approval PB
					$response = array("access1" => accessAllow($conn, $_POST['user'], "approval-cash-advance-I"), 
						"access2" => accessAllow($conn, $_POST['user'], "approval-cash-advance-II"),
						"access3" => accessAllow($conn, $_POST['user'], "approval-cash-advance-III"));
				} else if ($code == 5) {	// access approval CPR
					$response = array("access1" => accessAllow($conn, $_POST['user'], "responsbility-advance-I"), 
						"access2" => accessAllow($conn, $_POST['user'], "responsbility-advance-II"),
						"access3" => accessAllow($conn, $_POST['user'], "responsbility-advance-III"));
				} else if ($code == 6) {	// access approval TUNJANGAN Karyawan
					$response = array("access1" => accessAllow($conn, $_POST['user'], "employee-allowance:approval1"),
						"access2" => accessAllow($conn, $_POST['user'], "employee-allowance:approval2"));
				} else if ($code == 7) {	// access approval TUNJANGAN Temporary
					$response = array("access1" => accessAllow($conn, $_POST['user'], "employee-allowance:approval1"),
						"access2" => accessAllow($conn, $_POST['user'], "employee-allowance:approval2"));
				} else if ($code == 8) {	// access approval PO
					$response = array("access1" => accessAllow($conn, $_POST['user'], "approval-purchase-order-I"));
				} else if ($code == 9) {	// access approval WO
					$response = array("access1" => accessAllow($conn, $_POST['user'], "approval1-purchase-service"));
				} else if ($code == 10) {	// access approval COD
					$response = array("access1" => accessAllow($conn, $_POST['user'], "approval-cash-on-delivery-I"));
				} else if ($code == 11) {	// access approval Material Return
					$response = array("access1" => accessAllow($conn, $_POST['user'], "material-return:action"));
				} else if ($code == 12) {	// access approval Stock Adjusment
					$response = array("access1" => accessAllow($conn, $_POST['user'], "approval-stock-adjustment"));
				} else if ($code == 13) {	// access approval Budgeting
					$response = array("access1" => accessAllow($conn, $_POST['user'], "approval-budgeting-I"), 
						"access2" => accessAllow($conn, $_POST['user'], "approval-budgeting-II"),
						"access3" => accessAllow($conn, $_POST['user'], "approval-budgeting-III"));
				} else if ($code == 14) {	// access approval PS
					$response = array("access1" => accessAllow($conn, $_POST['user'], "approval-budgeting-supplier-I"),
						"access2" => accessAllow($conn, $_POST['user'], "approval-budgeting-supplier-II"),
						"access3" => accessAllow($conn, $_POST['user'], "approval-budgeting-supplier-III"));
				} else if ($code == 15) {	// access approval BT
					$response = array("access1" => accessAllow($conn, $_POST['user'], "approval1-bank_transaction"), 
						"access2" => accessAllow($conn, $_POST['user'], "approval2-bank_transaction"));
				} else if ($code == 16) {	// access approval Expenses
					$response = array("access1" => accessAllow($conn, $_POST['user'], "approval1-expenses"), 
						"access2" => accessAllow($conn, $_POST['user'], "approval2-expenses"));
				} else if ($code == 17) {	// access approval CA
					$response = array("access1" => accessAllow($conn, $_POST['user'], "approval-advanced"));
				} else {
					$response = array("access1" => 0, "access2" => 0, "access3" => 0);
				}
				break;
			case 'accessAllowModul': //get
				isTheseParametersAvailable(array('user_id'));
				$result['modul_contact']=accessAllow($conn, $_POST['user_id'], "module:contact");
				$result['modul_crm']=accessAllow($conn, $_POST['user_id'], "module:crm");
				$result['modul_fa']=accessAllow($conn, $_POST['user_id'], "module:fa");
				$result['modul_hr']=accessAllow($conn, $_POST['user_id'], "module:hr");
				$result['modul_hse']=accessAllow($conn, $_POST['user_id'], "module:hse");
				$result['modul_inventory']=accessAllow($conn, $_POST['user_id'], "module:inventory");
				$result['modul_marketing']=accessAllow($conn, $_POST['user_id'], "module:marketing");
				$result['modul_project']=accessAllow($conn, $_POST['user_id'], "module:project");
				$result['modul_purchasing']=accessAllow($conn, $_POST['user_id'], "module:purchasing");
				$result['modul_dashboard']=accessAllow($conn, $_POST['user_id'], "module:dashboard");
				if($result){
					$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				}else{
					$response = array("status" => 0, "massage" => "Some error!", "data" => array());
				}
				break;
			case 'accessAllowFinance': //get
				isTheseParametersAvailable(array('user_id'));
				$result['supplier_invoice']=accessAllow($conn, $_POST['user_id'], "supplier-invoice:index");
				$result['sales_invoice']=accessAllow($conn, $_POST['user_id'], "sales-order-invoice:index");
				$result['bank_transaction']=accessAllow($conn, $_POST['user_id'], "bank-transaction:index");
				$result['expenses']=accessAllow($conn, $_POST['user_id'], "expenses:index");
				$result['advanced']=accessAllow($conn, $_POST['user_id'], "advanced:index");
				$result['budgeting']=accessAllow($conn, $_POST['user_id'], "budgeting:index");
				$result['payment_supplier']=accessAllow($conn, $_POST['user_id'], "budgeting-supplier:index");
				$result['installment']=accessAllow($conn, $_POST['user_id'], "installment:index");
				$result['bank_account']=accessAllow($conn, $_POST['user_id'], "bank-account:index");
				$result['tax_report']=accessAllow($conn, $_POST['user_id'], "tax-report:index");
				$result['chart_of_account']=accessAllow($conn, $_POST['user_id'], "chart-of-account:index");
				$result['employee_salary']=accessAllow($conn, $_POST['user_id'], "employee-salary:index");
				
				$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				break;
			case 'accessAllowMarketing': //get
				isTheseParametersAvailable(array('user_id'));
				$result['sales_quotation']=accessAllow($conn, $_POST['user_id'], "sales-quotation:index");
				$result['sales_order']=accessAllow($conn, $_POST['user_id'], "sales-order:index");
				
				$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				break;
			case 'accessAllowHR': //get
				isTheseParametersAvailable(array('user_id'));
				$result['attendance']=accessAllow($conn, $_POST['user_id'], "attendance:index");
				$result['payroll']=accessAllow($conn, $_POST['user_id'], "payroll:index");
				$result['calendar']=accessAllow($conn, $_POST['user_id'], "employee:index");
				$result['employee']=accessAllow($conn, $_POST['user_id'], "employee:index");
				$result['department']=accessAllow($conn, $_POST['user_id'], "department:index");
				$result['employee_grade']=accessAllow($conn, $_POST['user_id'], "employee-grade:index");
				$result['job_title']=accessAllow($conn, $_POST['user_id'], "job-title:index");
				$result['job_grade']=accessAllow($conn, $_POST['user_id'], "job-grade:index");
				$result['news']=accessAllow($conn, $_POST['user_id'], "news:index");
				$result['employee_report']=accessAllow($conn, $_POST['user_id'], "employee-report:index");
				
				$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				break;
			case 'accessAllowPurchasing': //get
				isTheseParametersAvailable(array('user_id'));
				$result['purchase_order']=accessAllow($conn, $_POST['user_id'], "purchase-order:index");
				$result['purchase_service']=accessAllow($conn, $_POST['user_id'], "purchase-service:index");
				$result['cash_on_delivery']=accessAllow($conn, $_POST['user_id'], "cash-on-delivery:index");
				$result['contract_agreement']=accessAllow($conn, $_POST['user_id'], "contract-agreement:index");
				$result['good_received_note']=accessAllow($conn, $_POST['user_id'], "good-received-note:index");
				$result['work_handover']=accessAllow($conn, $_POST['user_id'], "work-handover:index");
				$result['services_receipt']=accessAllow($conn, $_POST['user_id'], "services-receipt:index");
				
				$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				break;
			case 'accessAllowInventory': //get
				isTheseParametersAvailable(array('user_id'));
				$result['item']=accessAllow($conn, $_POST['user_id'], "item-and-service:index");
				$result['asset']=accessAllow($conn, $_POST['user_id'], "asset:index");
				$result['asset_rental']=accessAllow($conn, $_POST['user_id'], "asset-rental:index");
				$result['stock_adjustment']=accessAllow($conn, $_POST['user_id'], "stock-adjustment:index");
				$result['master_item_price']=accessAllow($conn, $_POST['user_id'], "item-and-service:index");
				$result['material_return']=accessAllow($conn, $_POST['user_id'], "material-return:index");
				
				$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				break;
			case 'accessAllowHSE': //get
				isTheseParametersAvailable(array('user_id'));
				$result['work_accident']=accessAllow($conn, $_POST['user_id'], "work-accident:index");
				$result['genba_safety']=accessAllow($conn, $_POST['user_id'], "genba-safety:index");
				$result['safety_file_report']=accessAllow($conn, $_POST['user_id'], "job-order-safety:index");
				
				$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				break;
			case 'accessAllowProject': //get
				isTheseParametersAvailable(array('user_id'));
				$result['job_order']=accessAllow($conn, $_POST['user_id'], "job-order:index");
				$result['job_progress_report']=accessAllow($conn, $_POST['user_id'], "job-progress-report:index");
				$result['material_request']=accessAllow($conn, $_POST['user_id'], "material-request:index");
				$result['resources_request']=accessAllow($conn, $_POST['user_id'], "resources-request:index");
				$result['work_order']=accessAllow($conn, $_POST['user_id'], "work-order:index");
				$result['pickup']=accessAllow($conn, $_POST['user_id'], "pickup:index");
				$result['spkl']=accessAllow($conn, $_POST['user_id'], "overtime-workorder:index");
				$result['cash_advance']=accessAllow($conn, $_POST['user_id'], "cash-advance:index");
				$result['respons_advance']=accessAllow($conn, $_POST['user_id'], "respons-advance:index");
				$result['employee_allowance']=accessAllow($conn, $_POST['user_id'], "employee-allowance:index");
				$result['employee_allowance_temp']=accessAllow($conn, $_POST['user_id'], "employee-allowance-temp:index");
				
				$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				break;
			case 'accessAllowCRM': //get
				isTheseParametersAvailable(array('user_id'));
				$result['customer_feedback']=accessAllow($conn, $_POST['user_id'], "customer-feedback:index");
				$result['question']=accessAllow($conn, $_POST['user_id'], "question:index");
				$result['kuesioner']=accessAllow($conn, $_POST['user_id'], "survey:index");
				$result['grafik_kuesioner']=accessAllow($conn, $_POST['user_id'], "survey:index");
				$result['sales_quotation']=accessAllow($conn, $_POST['user_id'], "sales-quotation:index");
				$result['lead']=accessAllow($conn, $_POST['user_id'], "lead:index");
				$result['followup']=accessAllow($conn, $_POST['user_id'], "lead-followup:index");
				$result['event']=accessAllow($conn, $_POST['user_id'], "event:index");
				$result['schedule_visits']=accessAllow($conn, $_POST['user_id'], "schedule-visits:index");
				
				$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				break;
			case 'accessAllowContact': //get
				isTheseParametersAvailable(array('user_id'));
				$result['contact']=accessAllow($conn, $_POST['user_id'], "contact:index");
				$result['supplier']=accessAllow($conn, $_POST['user_id'], "supplier:index");
				$result['company']=accessAllow($conn, $_POST['user_id'], "company:index");
				
				$response = array("status" => 1, "massage" => "Success!", "data" => $result);
				break;
			case 'getJobOrder':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getJobOrder($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getWorkCompletion':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getWorkCompletion($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getWorkCompletionDetail':
				isTheseParametersAvailable(array('jobId'));
				$response = array("status" => 1, "massage" => getWorkCompletionData($conn, $_POST['jobId']), "data" => getWorkCompletionDetail($conn, $_POST['jobId']));
				break;
			case 'getMaterialRequisition':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getMaterialRequisition($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getMaterialReqDetail':
				isTheseParametersAvailable(array('materialId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getMaterialReqDetail($conn, $_POST['materialId']));
				break;
			case 'getMaterialReqPickup':
				isTheseParametersAvailable(array('materialId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getMaterialReqPickup($conn, $_POST['materialId']));
				break;
			case 'getResourcesRequest':
				isTheseParametersAvailable(array('sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getResourcesRequest($conn, $_POST['sortBy']));
				break;
			case 'getWorkOrder':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getWorkOrder($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getWorkOrderDetail':
				isTheseParametersAvailable(array('workOrderId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getWorkOrderDetail($conn, $_POST['workOrderId']));
				break;
			case 'getPickup':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getPickup($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getPickupDetail':
				isTheseParametersAvailable(array('id'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getPickupDetail($conn, $_POST['id']));
				break;
			case 'getSpkl':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getSpkl($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getProposedBudget':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getProposedBudget($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getProposedBudgetDetail':
				isTheseParametersAvailable(array('id'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getProposedBudgetDetail($conn, $_POST['id']));
				break;
			case 'getCashProjectReport':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getCashProjectReport($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getCashProjectReportDetail':
				isTheseParametersAvailable(array('id'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getCashProjectReportDetail($conn, $_POST['id']));
				break;
			case 'getCashProjectReportPbReceived':
				isTheseParametersAvailable(array('id'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getCashProjectReportPbReceived($conn, $_POST['id']));
				break;
			case 'getTunjanganKaryawan':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getTunjanganKaryawan($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getTunjanganTemporary':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getTunjanganTemporary($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getPurchaseOrder':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getPurchaseOrder($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getPurchaseOrderDetail':
				isTheseParametersAvailable(array('po_id'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getPurchaseOrderDetail($conn, $_POST['po_id']));
				break;
			case 'getPurchaseService':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getPurchaseService($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getPurchaseServiceDetail':
				isTheseParametersAvailable(array('ps_id'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getPurchaseServiceDetail($conn, $_POST['ps_id']));
				break;
			case 'getCashOnDelivery':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getCashOnDelivery($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getCashOnDeliveryDetail':
				isTheseParametersAvailable(array('cod_id'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getCashOnDeliveryDetail($conn, $_POST['cod_id']));
				break;
			case 'getContractAgreement':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getContractAgreement($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getGoodReceivedNote':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getGoodReceivedNote($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getGoodReceivedNoteDetail':
				isTheseParametersAvailable(array('grn_id'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getGoodReceivedNoteDetail($conn, $_POST['grn_id']));
				break;
			case 'getPurchasingDetailSi':
				isTheseParametersAvailable(array('tabel', 'id'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getPurchasingDetailSi($conn, $_POST['tabel'], $_POST['id']));
				break;
			case 'getWorkHandover':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getWorkHandover($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getWorkHandoverDetail':
				isTheseParametersAvailable(array('wh_id'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getWorkHandoverDetail($conn, $_POST['wh_id']));
				break;
			case 'getServicesReceipt':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getServicesReceipt($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getServicesReceiptDetail':
				isTheseParametersAvailable(array('svr_id'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getServicesReceiptDetail($conn, $_POST['svr_id']));
				break;
			case 'getContact':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getContact($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getSupplier':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getSupplier($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getSupplierDetail':
				isTheseParametersAvailable(array('siId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getSupplierDetail($conn, $_POST['siId']));
				break;
			case 'getCompany':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getCompany($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getCompanyDetContact':
				isTheseParametersAvailable(array('cpnId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getCompanyDetContact($conn, $_POST['cpnId']));
				break;
			case 'getCompanyDetInvoice':
				isTheseParametersAvailable(array('cpnId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getCompanyDetInvoice($conn, $_POST['cpnId']));
				break;
			case 'getAccessRequest':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getAccessRequest($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getAccessRequestDetail':
				isTheseParametersAvailable(array('arId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getAccessRequestDetail($conn, $_POST['arId']));
				break;
			case 'getNews':
				$response = array("status" => 1, "massage" => "Success!", "data" => getNews($conn));
				break;
			case 'getSupplierInvoice':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getSupplierInvoice($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getSupplierInvoiceDetail':
				isTheseParametersAvailable(array('siId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getSupplierInvoiceDetail($conn, $_POST['siId']));
				break;
			case 'getCustomerInvoices':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getCustomerInvoices($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getCiDetWorkCompletion':
				isTheseParametersAvailable(array('ciId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getCiDetWorkCompletion($conn, $_POST['ciId']));
				break;
			case 'getBankTransaction':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getBankTransaction($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getBankTransactionDetail':
				isTheseParametersAvailable(array('btId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getBankTransactionDetail($conn, $_POST['btId']));
				break;
			case 'getExpense':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getExpense($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getExpenseDetail':
				isTheseParametersAvailable(array('expensesId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getExpenseDetail($conn, $_POST['expensesId']));
				break;
			case 'getCashAdvance':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getCashAdvance($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getBudgeting':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getBudgeting($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getBudgetingDetail':
				isTheseParametersAvailable(array('budgetId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getBudgetingDetail($conn, $_POST['budgetId']));
				break;
			case 'getPaymentSupplier':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getPaymentSupplier($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getPaymentSupplierDetail':
				isTheseParametersAvailable(array('psId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getPaymentSupplierDetail($conn, $_POST['psId']));
				break;
			case 'getBankAccount':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getBankAccount($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getDaftarAkun':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDaftarAkun($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getEkspedisi':
				$response = array("status" => 1, "massage" => "Success!", "data" => getEkspedisi($conn));
				break;
			case 'getCustomerReceives':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getCustomerReceives($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getCustomerReceivesDetail':
				isTheseParametersAvailable(array('crId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getCustomerReceivesDetail($conn, $_POST['crId']));
				break;
			case 'getSalesQuotation':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getSalesQuotation($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getSalesOrder':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getSalesOrder($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getSalesOrderDetail':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getSalesOrderDetail($conn, $_POST['jobOrder']));
				break;
			case 'getWorkAccident':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getWorkAccident($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getWorkAccident':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getWorkAccident($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getGenbaSafety':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getGenbaSafety($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getGenbaSafetyDetail':
				isTheseParametersAvailable(array('gs_id'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getGenbaSafetyDetail($conn, $_POST['gs_id']));
				break;
			case 'getJobOrderSafety':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getJobOrderSafety($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getCustomerFeedback':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getCustomerFeedback($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getQuestions':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getQuestions($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getKuesioner':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getKuesioner($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getKuesionerDetail':
				isTheseParametersAvailable(array('svId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getKuesionerDetail($conn, $_POST['svId']));
				break;
			case 'getLead':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getLead($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getFollowup':
				isTheseParametersAvailable(array('counter', 'category', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getFollowup($conn, $_POST['counter'], $_POST['category'], $_POST['sortBy']));
				break;
			case 'getEvent':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEvent($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getScheduleVisits':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getScheduleVisits($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getItem':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getItem($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getItemGroup':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getItemGroup($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getItemCategory':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getItemCategory($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getItemType':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getItemType($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getAsset':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getAsset($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getAssetRental':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getAssetRental($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getStockAdjustment':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getStockAdjustment($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getStockAdjustmentDetail':
				isTheseParametersAvailable(array('saId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getStockAdjustmentDetail($conn, $_POST['saId']));
				break;
			case 'getMaterialReturn':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getMaterialReturn($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getMaterialReturnDetail':
				isTheseParametersAvailable(array('mrId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getMaterialReturnDetail($conn, $_POST['mrId']));
				break;
			case 'getEmployeeCheckClock':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeCheckClock($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getEmployeeCheckClockDetail':
				isTheseParametersAvailable(array('counter', 'empId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeCheckClockDetail($conn, $_POST['counter'], $_POST['empId']));
				break;
			case 'getEmployeeAchievement':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeAchievement($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getEmployeeLeave':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeLeave($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getEmployeeLeaveDetail':
				isTheseParametersAvailable(array('empId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeLeaveDetail($conn, $_POST['empId']));
				break;
			case 'getEmployeeEducation':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeEducation($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getEmploymentHistory':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmploymentHistory($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getEmploymentHistoryDetail':
				isTheseParametersAvailable(array('empId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmploymentHistoryDetail($conn, $_POST['empId']));
				break;
			case 'getEmployeeFamily':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeFamily($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getTrainingList':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getTrainingList($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getEmployeeNotice':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeNotice($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getWorkExperience':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getWorkExperience($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getHistoryContract':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getHistoryContract($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getHariLibur':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getHariLibur($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getEmployeeDeduction':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeDeduction($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getEmployeeGradeAllowance':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeGradeAllowance($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getDeduction':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDeduction($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getAllowance':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getAllowance($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getSalaryGrade':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getSalaryGrade($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getMaritalStatus':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getMaritalStatus($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getSalaryCorrection':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getSalaryCorrection($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getLateDeduction':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getLateDeduction($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getRemainLeave':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getRemainLeave($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getKabupaten':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getKabupaten($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getProvinsi':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getProvinsi($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getEmployee':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployee($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getEmployeeDetailFamily':
				isTheseParametersAvailable(array('empId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeDetailFamily($conn, $_POST['empId']));
				break;
			case 'getEmployeeDetailAchievement':
				isTheseParametersAvailable(array('empId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeDetailAchievement($conn, $_POST['empId']));
				break;
			case 'getEmployeeDetailTraining':
				isTheseParametersAvailable(array('empId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeDetailTraining($conn, $_POST['empId']));
				break;
			case 'getEmployeeDetailExperience':
				isTheseParametersAvailable(array('empId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeDetailExperience($conn, $_POST['empId']));
				break;
			case 'getEmployeeDetailEducation':
				isTheseParametersAvailable(array('empId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeDetailEducation($conn, $_POST['empId']));
				break;
			case 'getEmployeeDetailHistory':
				isTheseParametersAvailable(array('empId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeDetailHistory($conn, $_POST['empId']));
				break;
			case 'getEmployeeDetailPotongan':
				isTheseParametersAvailable(array('empId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeDetailPotongan($conn, $_POST['empId']));
				break;
			case 'getEmployeeDetailTunjangan':
				isTheseParametersAvailable(array('empId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeDetailTunjangan($conn, $_POST['empId']));
				break;
			case 'getEmployeeDetailFile':
				isTheseParametersAvailable(array('empId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeDetailFile($conn, $_POST['empId']));
				break;
			case 'getEmployeeDetailLeave':
				isTheseParametersAvailable(array('empId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeDetailLeave($conn, $_POST['empId']));
				break;
			case 'getEmployeeDetailKerja':
				isTheseParametersAvailable(array('empId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeDetailKerja($conn, $_POST['empId']));
				break;
			case 'getDepartment':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDepartment($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getDepartmentDetail':
				isTheseParametersAvailable(array('departemenId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDepartmentDetail($conn, $_POST['departemenId']));
				break;
			case 'getEmployeeGrade':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeGrade($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getEmployeeGradeDetail':
				isTheseParametersAvailable(array('jenjangId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeGradeDetail($conn, $_POST['jenjangId']));
				break;
			case 'getJobTitle':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getJobTitle($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getJobGrade':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getJobGrade($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getPersonaliaNews':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getPersonaliaNews($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getEmployeeReport':
				isTheseParametersAvailable(array('counter', 'sortBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeReport($conn, $_POST['counter'], $_POST['sortBy']));
				break;
			case 'getEmployeeReportDetail':
				isTheseParametersAvailable(array('empId'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getEmployeeReportDetail($conn, $_POST['empId']));
				break;
			case 'getDetailJOMR':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOMR($conn, $_POST['jobOrder']));
				break;
			case 'getDetailJOPR':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOPR($conn, $_POST['jobOrder']));
				break;
			case 'getDetailJOTR':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOTR($conn, $_POST['jobOrder']));
				break;
			case 'getDetailJOManPowerTemp':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOManPowerTemp($conn, $_POST['jobOrder']));
				break;
			case 'getDetailJOManPowerPermanenKontrak':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOManPowerPermanenKontrak($conn, $_POST['jobOrder']));
				break;
			case 'getDetailJOCOD':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOCOD($conn, $_POST['jobOrder']));
				break;
			case 'getDetailJOWO':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOWO($conn, $_POST['jobOrder']));
				break;
			case 'getDetailJOPBHalf':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOPBHalf($conn, $_POST['jobOrder']));
				break;
			case 'getDetailJOPB':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOPB($conn, $_POST['jobOrder']));
				break;
			case 'getDetailJOPBRestFrom':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOPBRestFrom($conn, $_POST['jobOrder']));
				break;
			case 'getDetailJOCPR':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOCPR($conn, $_POST['jobOrder']));
				break;
			case 'getDetailJOCPRRestFrom':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOCPRRestFrom($conn, $_POST['jobOrder']));
				break;
			case 'getDetailJOExpenses':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOExpenses($conn, $_POST['jobOrder']));
				break;
			case 'getDetailJOInvoice':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOInvoice($conn, $_POST['jobOrder']));
				break;
			case 'getDetailJOMaterialReturn':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOMaterialReturn($conn, $_POST['jobOrder']));
				break;
			case 'getDetailJOMaterialReturnDetail':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailJOMaterialReturnDetail($conn, $_POST['jobOrder']));
				break;
			case 'getTotalDetailJO':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getTotalDetailJO($conn, $_POST['jobOrder']));
				break;
			case 'getDetailSpkl':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getDetailSpkl($conn, $_POST['jobOrder']));
				break;
			case 'addSpkl':
				isTheseParametersAvailable(array('number', 'proposedDate', 'workDescription', 'workLocation', 'jobOrder', 'departmentId', 'requestedId', 'createdBy'));
				$response = array("status" => 1, "massage" => "Success!", "data" => addSpkl($conn, $_POST['number'], $_POST['proposedDate'], $_POST['workDescription'], $_POST['workLocation'], $_POST['jobOrder'], $_POST['departmentId'], $_POST['requestedId'], $_POST['createdBy'], $_POST['employeeId1'], $_POST['overtimeDate1'], $_POST['description1'], $_POST['startTime1'], $_POST['finishTime1'], $_POST['employeeId2'], $_POST['overtimeDate2'], $_POST['description2'], $_POST['startTime2'], $_POST['finishTime2'], $_POST['employeeId3'], $_POST['overtimeDate3'], $_POST['description3'], $_POST['startTime3'], $_POST['finishTime3'], $_POST['employeeId4'], $_POST['overtimeDate4'], $_POST['description4'], $_POST['startTime4'], $_POST['finishTime4'], $_POST['employeeId5'], $_POST['overtimeDate5'], $_POST['description5'], $_POST['startTime5'], $_POST['finishTime5']));
				break;
			case 'getListDetailSpkl':
				isTheseParametersAvailable(array('jobOrder'));
				$response = array("status" => 1, "massage" => "Success!", "data" => getListDetailSpkl($conn, $_POST['jobOrder']));
				break;
			case 'updateSpkl':
				$response = array("status" => 1, "massage" => "Success!", "data" => updateSpkl($conn, $_POST['work_description'], $_POST['proposed_date'], $_POST['modified_by'], $_POST['overtime_workorder_id'], $_POST['employeeId1'], $_POST['overtime_date1'], $_POST['description1'], $_POST['start_time1'], $_POST['finish_time1'], $_POST['otwo_detail_id1'], $_POST['employeeId2'], $_POST['overtime_date2'], $_POST['description2'], $_POST['start_time2'], $_POST['finish_time2'], $_POST['otwo_detail_id2'], $_POST['employeeId3'], $_POST['overtime_date3'], $_POST['description3'], $_POST['start_time3'], $_POST['finish_time3'], $_POST['otwo_detail_id3'], $_POST['employeeId4'], $_POST['overtime_date4'], $_POST['description4'], $_POST['start_time4'], $_POST['finish_time4'], $_POST['otwo_detail_id4'], $_POST['employeeId5'], $_POST['overtime_date5'], $_POST['description5'], $_POST['start_time5'], $_POST['finish_time5'], $_POST['otwo_detail_id5']));
				break;
			case 'getSpklNumber':
				$response = array("status" => 1, "massage" => "Success!", "data" => getSpklNumber($conn));
				break;
			case 'addJobOrder':
				isTheseParametersAvailable(array('job_order_id', 'job_order_status', 'job_order_type', 'job_order_category_id', 'job_order_description', 'sales_quotation_id', 'department_id', 'supervisor', 'job_order_location', 'begin_date', 'end_date', 'created_by', 'amount', 'budgeting_amount', 'max_pb_amount', 'material_amount', 'tools_amount', 'man_power_amount', 'cod_amount', 'wo_amount', 'material_return_amount', 'pb_amount', 'cpr_amount', 'expenses_amount', 'tax_type_id', 'sales_order_id'));
				$response = array("status" => 1, "massage" => "Success!", "data" => addJobOrder($conn, $_POST['job_order_id'], $_POST['job_order_status'], $_POST['job_order_type'], $_POST['job_order_category_id'], $_POST['job_order_description'], $_POST['sales_quotation_id'], $_POST['department_id'], $_POST['supervisor'], $_POST['job_order_location'], $_POST['begin_date'], $_POST['end_date'], $_POST['notes'], $_POST['created_by'], $_POST['amount'], $_POST['budgeting_amount'], $_POST['max_pb_amount'], $_POST['material_amount'], $_POST['tools_amount'], $_POST['man_power_amount'], $_POST['cod_amount'], $_POST['wo_amount'], $_POST['material_return_amount'], $_POST['pb_amount'], $_POST['cpr_amount'], $_POST['expenses_amount'], $_POST['client_po_number'], $_POST['tax_type_id'], $_POST['sales_order_id']));
				break;
			case 'updateJobOrder':
				$response = array("status" => 1, "massage" => "Success!", "data" => updateJobOrder($conn, $_POST['job_order_id'], $_POST['job_order_number'], $_POST['job_order_status'], $_POST['job_order_type'], $_POST['job_order_category_id'], $_POST['job_order_description'], $_POST['sales_quotation_id'], $_POST['department_id'], $_POST['supervisor'], $_POST['job_order_location'], $_POST['begin_date'], $_POST['end_date'], $_POST['notes'], $_POST['modified_by'], $_POST['amount'], $_POST['budgeting_amount'], $_POST['material_amount'], $_POST['tools_amount'], $_POST['man_power_amount'], $_POST['cod_amount'], $_POST['wo_amount'], $_POST['material_return_amount'], $_POST['pb_amount'], $_POST['cpr_amount'], $_POST['expenses_amount'], $_POST['client_po_number'], $_POST['tax_type_id'], $_POST['sales_order_id']));
				break;
			case 'getJobOrderNumber':
				$response = array("status" => 1, "massage" => "Success!", "data" => getJobOrderNumber($conn));
				break;
		}
	}else{
		$response['error'] = true;
		$response['message'] = 'Invalid API Call';
	}
	echo json_encode($response);
?>
