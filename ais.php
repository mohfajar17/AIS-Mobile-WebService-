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

			$sql_value = "SELECT ki_user.user_id, ki_user.employee_id, ki_user.user_name, ki_user.user_displayname, ki_user.usergroup_id, ki_usergroup.usergroup_name, ki_user.user_pwd, ki_user.token FROM ki_user LEFT JOIN ki_usergroup ON ki_user.usergroup_id=ki_usergroup.usergroup_id WHERE user_name='".$user_name."' AND user_pwd=MD5('".$password."')";
			$qur_value = mysqli_query($conn, $sql_value);

			$row = mysqli_fetch_array($qur_value, MYSQLI_ASSOC);
		} else {
		 	$row=array();
		}

		mysqli_close($conn);
		return $row;
	}

	function userActiveMobile($conn, $user_id){ //get employee data
		$sql = "SELECT * FROM ki_user WHERE user_id='".$user_id."'";
		$qur = mysqli_query($conn, $sql);

		$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
		$count = mysqli_num_rows($qur);
		
		if($count==0)
			$row=array();

		mysqli_close($conn);
		return $row;
	}

	function employeeData($conn, $employee_id){ //get employee data
		$sql = "SELECT a.employee_number, a.gender, a.place_birthday, a.birthday, a.address, a.mobile_phone, a.email1, a.employee_file_name, c.department_name AS department_id
		FROM ki_employee a
		LEFT JOIN ki_department c ON a.department_id = c.department_id 
		WHERE employee_id='".$employee_id."'";
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
		$sql = "SELECT * FROM ki_employee_report WHERE employee_id='".$employee_number."'";
		$qur = mysqli_query($conn, $sql);
		
		if(mysqli_num_rows($qur)>0){
			$sql = "SELECT SUM(moneybox) AS 'money_box' FROM ki_employee_report WHERE employee_id='".$employee_number."'";
			$qur = mysqli_query($conn, $sql);
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
		} else $row=array();

		mysqli_close($conn);
		return $row;
	}

	function getHoliday($conn){ //get holiday
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y");

		$sql = "SELECT * FROM ki_holiday WHERE YEAR(holiday_date)='".$date."'";
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

	function getInv($conn){
		$sql = "SELECT SUM(ki_job_progress_report_detail.amount) AS result_amount FROM ki_sales_order_invoice RIGHT JOIN ki_job_progress_report_detail ON ki_sales_order_invoice.job_progress_report_id=ki_job_progress_report_detail.job_progress_report_id WHERE ki_sales_order_invoice.sales_order_invoice_status LIKE '%New%' OR ki_sales_order_invoice.sales_order_invoice_status LIKE '%Delivered%'";
		$qur = mysqli_query($conn, $sql);
		$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);

		$sql2 = "SELECT SUM(ki_sales_order_invoice.discount) AS discount, SUM(ki_sales_order_invoice.service_discount) AS service_discount FROM ki_sales_order_invoice WHERE sales_order_invoice_status LIKE '%New%' OR sales_order_invoice_status LIKE '%Delivered%'";
		$qur2 = mysqli_query($conn, $sql2);
		$row2 = mysqli_fetch_array($qur2, MYSQLI_ASSOC);
		$sub_total = $row["result_amount"] - ($row2["discount"] + $row2["service_discount"]);

		$sql3 = "SELECT SUM(a.amount) AS SOI_amount, SUM(a.service_amount) AS SOI_service_amount, SUM(a.ppn) AS SOI_ppn, SUM(a.service_ppn) AS SOI_service_ppn, SUM(a.pph) AS SOI_pph FROM ki_sales_order_invoice a WHERE a.sales_order_invoice_status LIKE '%New%' OR a.sales_order_invoice_status LIKE '%Delivered%'";
		$qur3 = mysqli_query($conn, $sql3);
		$row3 = mysqli_fetch_array($qur3, MYSQLI_ASSOC);
		$vat = (10/100)*$sub_total;
		$total_final = $sub_total + $vat;

		$grandTotal['dpp'] = $sub_total;
        $grandTotal['ppn'] = $vat;
        $grandTotal['grand_total'] = round($total_final);

		mysqli_close($conn);
		return $grandTotal;
	}

	function getSinv($conn){
		$sql = "SELECT SUM(a.amount) AS amount, SUM(a.discount) AS discount, SUM(a.ppn) AS ppn, SUM(a.stamp) AS stamp, SUM(a.adjustment_value) AS adjustment_value, SUM(a.pph) AS pph FROM ki_supplier_invoice a WHERE a.supplier_invoice_status LIKE '%Received%' ORDER BY datediff(a.due_date, curdate()) ASC";
		$qur = mysqli_query($conn, $sql);
		$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);

		mysqli_close($conn);
		return $row;
	}

	function getBank($conn){
		$sql = "SELECT SUM(a.ending_reconcile_balance) AS ending_reconcile_balance FROM ki_bank_account a WHERE a.is_active = 1";
		$qur = mysqli_query($conn, $sql);
		$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);

		mysqli_close($conn);
		return $row;
	}

	function getSalesQuot($conn){
		$sql = "SELECT SUM(SQ.amount) AS amount, SUM(SQ.wo_amount) AS wo_amount FROM ki_sales_quotation SQ WHERE SQ.status LIKE '%Finish%'";
		$qur = mysqli_query($conn, $sql);
		$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);

		mysqli_close($conn);
		return $row;
	}

	function getInventory($conn){
		$sql = "SELECT SUM(i.price_buy*i.current_stock) AS item FROM ki_item_and_service i WHERE i.is_active = 1";
		$qur = mysqli_query($conn, $sql);
		$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);

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

	// get data satu job order
	function getDataJobOrder($conn, $jobOrder){
        $sql = "SELECT * FROM ki_job_order WHERE job_order_id = '".$jobOrder."'";
        $qur = mysqli_query($conn, $sql);

        $row[] = mysqli_fetch_array($qur, MYSQLI_ASSOC);

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

	function listSalesQuotationUpdate($conn){ //get
        $sql = "SELECT sales_quotation_id, sales_quotation_number, description FROM ki_sales_quotation WHERE status='New' OR status='Progress' ORDER BY sales_quotation_id DESC";
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
        $sql = "SELECT emp.employee_id, emp.fullname, jg.job_grade_name
		FROM ki_employee emp 
		LEFT JOIN ki_employee_grade eg ON emp.employee_grade_id = eg.employee_grade_id
		LEFT JOIN ki_job_grade jg ON eg.job_grade_id = jg.job_grade_id
		WHERE emp.is_active = 1 ORDER BY emp.fullname ASC";
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
        $sql = "SELECT company_workbase_id, company_workbase_name FROM ki_company_workbase ORDER BY company_workbase_name ASC";
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
        $sql = "SELECT department_id, department_name, department_code FROM ki_department ORDER BY department_name ASC";
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
        $sql = "SELECT sales_order_id, sales_order_number, short_description FROM ki_sales_order ORDER BY sales_order_number DESC";
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

	function listJobOrder($conn){ //get
        $sql = "SELECT jo.job_order_id, jo.job_order_number, jo.job_order_description FROM ki_job_order jo ORDER BY jo.job_order_id DESC";
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

	function accessAllow($conn, $user_id, $access_name) { //mencari usergroup_id
    	//mencari usergroup_id
		$sql_ug = "SELECT * FROM ki_user WHERE user_id = '".$user_id."'";
		$qur_ug = mysqli_query($conn, $sql_ug);
		$row_ug = mysqli_fetch_array($qur_ug, MYSQLI_ASSOC);
		$ug_id = $row_ug["usergroup_id"];
		
		//mencari hak access sesuai usergroup
		$sql_ac = "SELECT * FROM ki_access WHERE access_name = '".$access_name."'";
		$qur_ac = mysqli_query($conn, $sql_ac);
		$row_ac = mysqli_fetch_array($qur_ac, MYSQLI_ASSOC);
		$count = mysqli_num_rows($qur_ac);		
		$temp_group = explode(",",$row_ac['access_usergroup_ids']);
		
		$hasil = 0;
		if($row_ac['access_ispublic']==1) {
			$hasil = 1;
		} else if($row_ac['access_isalluser']==1) {
			if($ug_id) {
				$hasil = 1;
			}	
		} else if(in_array($ug_id, $temp_group)) {
			$hasil = 1;
		} else {
			$hasil = 0;
		}
		
		return $hasil;
	}

	function getJobOrder($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.job_order_id, a.job_order_number, a.job_order_status, a.job_order_type, b.job_order_category_name AS job_order_category_id, a.job_order_description, f.sales_quotation_number AS sales_quotation_id, c.department_name AS department_id, d.fullname AS supervisor, a.job_order_location, a.begin_date, a.end_date, a.notes, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, a.amount, a.budgeting_amount, a.max_pb_amount, a.material_amount, a.tools_amount, a.man_power_amount, a.cod_amount, a.wo_amount, a.material_return_amount, a.pb_amount, a.cpr_amount, a.expenses_amount, a.sales_archive, f.sales_file_name, f.sales_file_type, a.client_po_number, a.client_po_archive, f.client_po_file_name, f.client_po_file_type, e.tax_type_name AS tax_type_id, so.sales_order_number AS sales_order_id, a.account_job_order
			FROM ki_job_order a
			LEFT JOIN ki_job_order_category b ON a.job_order_category_id = b.job_order_category_id 
			LEFT JOIN ki_department c ON a.department_id = c.department_id 
			LEFT JOIN ki_employee d ON a.supervisor = d.employee_id
			LEFT JOIN ki_tax_type e ON a.tax_type_id = e.tax_type_id
			LEFT JOIN ki_sales_quotation f ON a.sales_quotation_id = f.sales_quotation_id
            LEFT JOIN ki_sales_order so ON a.sales_order_id = so.sales_order_id
            LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
            LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
            LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
            LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", job_order_id DESC";
		} else {
			$sql = "SELECT a.job_order_id, a.job_order_number, a.job_order_status, a.job_order_type, b.job_order_category_name AS job_order_category_id, a.job_order_description, f.sales_quotation_number AS sales_quotation_id, c.department_name AS department_id, d.fullname AS supervisor, a.job_order_location, a.begin_date, a.end_date, a.notes, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, a.amount, a.budgeting_amount, a.max_pb_amount, a.material_amount, a.tools_amount, a.man_power_amount, a.cod_amount, a.wo_amount, a.material_return_amount, a.pb_amount, a.cpr_amount, a.expenses_amount, a.sales_archive, f.sales_file_name, f.sales_file_type, a.client_po_number, a.client_po_archive, f.client_po_file_name, f.client_po_file_type, e.tax_type_name AS tax_type_id, so.sales_order_number AS sales_order_id, a.account_job_order
			FROM ki_job_order a
			LEFT JOIN ki_job_order_category b ON a.job_order_category_id = b.job_order_category_id 
			LEFT JOIN ki_department c ON a.department_id = c.department_id 
			LEFT JOIN ki_employee d ON a.supervisor = d.employee_id
			LEFT JOIN ki_tax_type e ON a.tax_type_id = e.tax_type_id
			LEFT JOIN ki_sales_quotation f ON a.sales_quotation_id = f.sales_quotation_id
            LEFT JOIN ki_sales_order so ON a.sales_order_id = so.sales_order_id
            LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
            LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
            LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
            LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", job_order_id DESC
			LIMIT $counter, 15";
		}
		
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

	function getWorkCompletion($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.job_progress_report_id, a.job_progress_report_number, b.job_order_number AS job_order_id, IFNULL(b.job_order_description,'') AS job_order_description, c.sales_quotation_number AS sales_quotation_id, a.company_id, a.progress_percentage, DATE_FORMAT(a.start_work,'%d-%m-%Y') AS start_work, DATE_FORMAT(a.end_work,'%d-%m-%Y') AS end_work, e.term AS payment_term_id, a.notes, IFNULL(e1.fullname,'') AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, e3.fullname AS prepared_by, a.prepared_date, a.accepted_by, a.accepted_by2, a.accepted_by3, a.client_po_number, a.is_delete
			FROM ki_job_progress_report a 
			LEFT JOIN ki_job_order b ON a.job_order_id = b.job_order_id 
			LEFT JOIN ki_sales_quotation c ON a.sales_quotation_id = c.sales_quotation_id
			LEFT JOIN ki_payment_term e ON a.payment_term_id = e.payment_term_id
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
			LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
			LEFT JOIN ki_user u3 ON a.prepared_by = u3.user_id
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
			LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", job_progress_report_id DESC";
		} else {
			$sql = "SELECT a.job_progress_report_id, a.job_progress_report_number, b.job_order_number AS job_order_id, IFNULL(b.job_order_description,'') AS job_order_description, c.sales_quotation_number AS sales_quotation_id, a.company_id, a.progress_percentage, DATE_FORMAT(a.start_work,'%d-%m-%Y') AS start_work, DATE_FORMAT(a.end_work,'%d-%m-%Y') AS end_work, e.term AS payment_term_id, a.notes, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, e3.fullname AS prepared_by, a.prepared_date, a.accepted_by, a.accepted_by2, a.accepted_by3, a.client_po_number, a.is_delete
			FROM ki_job_progress_report a 
			LEFT JOIN ki_job_order b ON a.job_order_id = b.job_order_id 
			LEFT JOIN ki_sales_quotation c ON a.sales_quotation_id = c.sales_quotation_id
			LEFT JOIN ki_payment_term e ON a.payment_term_id = e.payment_term_id
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
			LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
			LEFT JOIN ki_user u3 ON a.prepared_by = u3.user_id
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
			LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", job_progress_report_id DESC
			LIMIT $counter, 15";
		}
		
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

	function getWorkCompletionData($conn, $jobId){
		$sql = "SELECT a.job_progress_report_id, a.job_progress_report_number, b.job_order_number AS job_order_id, IFNULL(b.job_order_description,'') AS job_order_description, c.sales_quotation_number AS sales_quotation_id, a.company_id, a.progress_percentage, DATE_FORMAT(a.start_work,'%d-%m-%Y') AS start_work, DATE_FORMAT(a.end_work,'%d-%m-%Y') AS end_work, e.term AS payment_term_id, a.notes, IFNULL(e1.fullname,'') AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, e3.fullname AS prepared_by, a.prepared_date, a.accepted_by, a.accepted_by2, a.accepted_by3, a.client_po_number, a.is_delete
			FROM ki_job_progress_report a
			LEFT JOIN ki_job_order b ON a.job_order_id = b.job_order_id 
			LEFT JOIN ki_sales_quotation c ON a.sales_quotation_id = c.sales_quotation_id
			LEFT JOIN ki_payment_term e ON a.payment_term_id = e.payment_term_id
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
			LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
			LEFT JOIN ki_user u3 ON a.prepared_by = u3.user_id
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
			LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id
			WHERE a.job_progress_report_id = ".$jobId."";
		$qur = mysqli_query($conn, $sql);

		$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
	    return $row;
	}

	function getWorkCompletionDetail($conn, $jobId){
		
		$sql = "SELECT * FROM ki_job_progress_report_detail WHERE job_progress_report_id = ".$jobId."";
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

	function getMaterialRequisition($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.material_request_id, a.material_request_number, a.material_request_status, b.job_order_number AS job_order_id, b.job_order_description, c.sales_quotation_number AS sales_quotation_id, DATE(a.requisition_date) AS requisition_date, DATE_FORMAT(a.requisition_date,'%d-%m-%Y') AS created_date, a.usage_date, a.notes, a.version, a.priority, e1.fullname AS created_by, e2.fullname AS modified_by, a.modified_date, e3.fullname AS checked_by, a.checked_date, a.checked_comment, IF(a.approval1!='',e4.fullname,'-') AS approval1, a.approval_date1, a.comment1, IF(a.approval2!='',e5.fullname,'-') AS approval2, a.approval_date2, a.comment2, IF(a.approval3!='',e6.fullname,'-') AS approval3, a.approval_date3, a.comment3, a.material_request_discount_type 
			FROM ki_material_request a 
			LEFT JOIN ki_job_order b ON a.job_order_id = b.job_order_id 
			LEFT JOIN ki_sales_quotation c ON a.sales_quotation_id = c.sales_quotation_id 
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
			LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
			LEFT JOIN ki_user u3 ON a.checked_by = u3.user_id
			LEFT JOIN ki_user u4 ON a.approval1 = u4.user_id
			LEFT JOIN ki_user u5 ON a.approval2 = u5.user_id
			LEFT JOIN ki_user u6 ON a.approval3 = u6.user_id
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
			LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id
			LEFT JOIN ki_employee e4 ON u4.employee_id = e4.employee_id
			LEFT JOIN ki_employee e5 ON u5.employee_id = e5.employee_id
			LEFT JOIN ki_employee e6 ON u6.employee_id = e6.employee_id
			WHERE YEAR(a.requisition_date) > 2016
			ORDER BY ".$sortBy.", material_request_id DESC";
		} else {
			$sql = "SELECT a.material_request_id, a.material_request_number, a.material_request_status, b.job_order_number AS job_order_id, b.job_order_description, c.sales_quotation_number AS sales_quotation_id, DATE(a.requisition_date) AS requisition_date, DATE_FORMAT(a.requisition_date,'%d-%m-%Y') AS created_date, a.usage_date, a.notes, a.version, a.priority, e1.fullname AS created_by, e2.fullname AS modified_by, a.modified_date, e3.fullname AS checked_by, a.checked_date, a.checked_comment, IF(a.approval1!='',e4.fullname,'-') AS approval1, a.approval_date1, a.comment1, IF(a.approval2!='',e5.fullname,'-') AS approval2, a.approval_date2, a.comment2, IF(a.approval3!='',e6.fullname,'-') AS approval3, a.approval_date3, a.comment3, a.material_request_discount_type 
			FROM ki_material_request a 
			LEFT JOIN ki_job_order b ON a.job_order_id = b.job_order_id 
			LEFT JOIN ki_sales_quotation c ON a.sales_quotation_id = c.sales_quotation_id 
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
			LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
			LEFT JOIN ki_user u3 ON a.checked_by = u3.user_id
			LEFT JOIN ki_user u4 ON a.approval1 = u4.user_id
			LEFT JOIN ki_user u5 ON a.approval2 = u5.user_id
			LEFT JOIN ki_user u6 ON a.approval3 = u6.user_id
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
			LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id
			LEFT JOIN ki_employee e4 ON u4.employee_id = e4.employee_id
			LEFT JOIN ki_employee e5 ON u5.employee_id = e5.employee_id
			LEFT JOIN ki_employee e6 ON u6.employee_id = e6.employee_id
			WHERE YEAR(a.requisition_date) > 2016
			ORDER BY ".$sortBy.", material_request_id DESC 
			LIMIT $counter, 15";
		}
		
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

	function getMaterialReqDetail($conn, $materialId){
    	$sql = "SELECT mrd.material_request_detail_id, kis.item_name, kis.item_specification, mrd.notes, po.purchase_order_number, mrd.quantity, mrd.unit_abbr, mrd.is_stock_request, mrd.quantity_purchase_request AS quantity_stock_request, IF(mrd.approval_status1!='',mrd.approval_status1,'-') AS po_app1, IF(mrd.approval_status2!='',mrd.approval_status2,'-') AS po_app2, IF(mrd.approval_status3!='',mrd.approval_status3,'-') AS po_app3, mrd.status, kis.current_stock AS stock_charging
    	FROM ki_material_request_detail mrd 
	    LEFT JOIN ki_purchase_order po ON po.purchase_order_id = mrd.purchase_order_id
	    LEFT JOIN ki_item_and_service kis ON kis.item_id = mrd.item_id
	    WHERE mrd.material_request_id = ".$materialId."";
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

	function getMaterialReqPickup($conn, $materialId){
		$sql = "SELECT p.pickup_number, emp.fullname AS pickup_by, p.taken_date, IF(p.recognized='1','Ya','Tidak') AS recognized
		FROM ki_pickup p
		LEFT JOIN ki_employee emp ON emp.employee_id = p.pickup_by
		WHERE p.material_request_id = ".$materialId."
		ORDER BY taken_date DESC";
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

	function getResourcesRequest($conn, $sortBy){
		$sql = "SELECT r.resources_request_id, r.resources_request_number, jo.job_order_number, c.contact_name AS contact_id, e.fullname AS created_by, r.begin_date, r.end_date, r.version
			FROM ki_resources_request r
            LEFT JOIN ki_job_order jo ON r.job_order_id = jo.job_order_id
			LEFT JOIN ki_contact c ON r.contact_id = c.contact_id
			LEFT JOIN ki_user u ON r.created_by = u.user_id
			LEFT JOIN ki_employee e ON u.employee_id = e.employee_id
			ORDER BY ".$sortBy.", resources_request_id DESC";
		
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

	function getWorkOrder($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.work_order_id, a.work_order_number, a.work_order_description, b.job_order_number AS job_order_id, b.job_order_description, b.job_order_type AS job_order_type, DATE_FORMAT(a.begin_date,'%d-%m-%Y') AS begin_date, DATE_FORMAT(a.end_date,'%d-%m-%Y') AS end_date, a.notes, IF(a.created_by!='',e1.fullname,'') AS created_by, a.created_date, IF(a.modified_by!='',e2.fullname,'') AS modified_by, a.modified_date, IF(a.checked_by!='',e3.fullname,'') AS checked_by, IF(a.approval1!='',e4.fullname,'') AS approval1, IF(a.approval2!='',e5.fullname,'') AS approval2, IF(a.approval3!='',e6.fullname,'') AS approval3, a.checked_date, a.checked_comment, a.approval_date1, a.approval_comment1, a.approval_date2, a.approval_comment2, a.approval_date3, a.approval_comment3, a.work_order_discount_type 
			FROM ki_work_order a
			LEFT JOIN ki_job_order b ON a.job_order_id = b.job_order_id 
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id 
			LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id 
			LEFT JOIN ki_user u3 ON a.checked_by = u3.user_id 
			LEFT JOIN ki_user u4 ON a.approval1 = u4.user_id 
			LEFT JOIN ki_user u5 ON a.approval2 = u5.user_id 
			LEFT JOIN ki_user u6 ON a.approval3 = u6.user_id 
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
			LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id
			LEFT JOIN ki_employee e4 ON u4.employee_id = e4.employee_id
			LEFT JOIN ki_employee e5 ON u5.employee_id = e5.employee_id
			LEFT JOIN ki_employee e6 ON u6.employee_id = e6.employee_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", work_order_id DESC";
		} else {
			$sql = "SELECT a.work_order_id, a.work_order_number, a.work_order_description, b.job_order_number AS job_order_id, b.job_order_description, b.job_order_type AS job_order_type, DATE_FORMAT(a.begin_date,'%d-%m-%Y') AS begin_date, DATE_FORMAT(a.end_date,'%d-%m-%Y') AS end_date, a.notes, IF(a.created_by!='',e1.fullname,'') AS created_by, a.created_date, IF(a.modified_by!='',e2.fullname,'') AS modified_by, a.modified_date, IF(a.checked_by!='',e3.fullname,'') AS checked_by, IF(a.approval1!='',e4.fullname,'') AS approval1, IF(a.approval2!='',e5.fullname,'') AS approval2, IF(a.approval3!='',e6.fullname,'') AS approval3, a.checked_date, a.checked_comment, a.approval_date1, a.approval_comment1, a.approval_date2, a.approval_comment2, a.approval_date3, a.approval_comment3, a.work_order_discount_type 
			FROM ki_work_order a
			LEFT JOIN ki_job_order b ON a.job_order_id = b.job_order_id 
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id 
			LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id 
			LEFT JOIN ki_user u3 ON a.checked_by = u3.user_id 
			LEFT JOIN ki_user u4 ON a.approval1 = u4.user_id 
			LEFT JOIN ki_user u5 ON a.approval2 = u5.user_id
			LEFT JOIN ki_user u6 ON a.approval3 = u6.user_id 
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
			LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id
			LEFT JOIN ki_employee e4 ON u4.employee_id = e4.employee_id
			LEFT JOIN ki_employee e5 ON u5.employee_id = e5.employee_id
			LEFT JOIN ki_employee e6 ON u6.employee_id = e6.employee_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", work_order_id DESC
			LIMIT $counter, 15";
		}
		
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

	function getWorkOrderDetail($conn, $workOrderId){
		$sql = "SELECT wod.work_order_detail_id, wod.item_name, wod.wo_notes, wod.quantity, wod.unit_abbr, wod.unit_price, wod.discount, IF(wod.wo_app1!='', wod.wo_app1, '-') AS wo_app1, IF(wod.wo_app2!='', wod.wo_app2, '-') AS wo_app2, IF(wod.wo_app3!='', wod.wo_app3, '-') AS wo_app3
		FROM ki_work_order_detail wod
		WHERE wod.work_order_id = ".$workOrderId."";
		
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

	function getPickup($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.pickup_id, a.pickup_number, b.material_request_number AS material_request_id, e1.fullname AS pickup_by, DATE_FORMAT(a.taken_date,'%d-%m-%Y') AS taken_date, a.notes, a.recognized, IFNULL(e2.fullname,'') AS recognized_by, a.recognized_date, e3.fullname AS created_by, a.created_date, e4.fullname AS modified_by, a.modified_date
			FROM ki_pickup a
			LEFT JOIN ki_material_request b ON a.material_request_id = b.material_request_id
			LEFT JOIN ki_user u2 ON a.recognized_by = u2.user_id
			LEFT JOIN ki_user u3 ON a.created_by = u3.user_id
			LEFT JOIN ki_user u4 ON a.modified_by = u4.user_id
			LEFT JOIN ki_employee e1 ON a.pickup_by = e1.employee_id
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
			LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id
			LEFT JOIN ki_employee e4 ON u4.employee_id = e4.employee_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", pickup_id DESC";
		} else {
			$sql = "SELECT a.pickup_id, a.pickup_number, b.material_request_number AS material_request_id, e1.fullname AS pickup_by, DATE_FORMAT(a.taken_date,'%d-%m-%Y') AS taken_date, a.notes, a.recognized, e2.fullname AS recognized_by, a.recognized_date, e3.fullname AS created_by, a.created_date, e4.fullname AS modified_by, a.modified_date
			FROM ki_pickup a
			LEFT JOIN ki_material_request b ON a.material_request_id = b.material_request_id
			LEFT JOIN ki_user u2 ON a.recognized_by = u2.user_id
			LEFT JOIN ki_user u3 ON a.created_by = u3.user_id
			LEFT JOIN ki_user u4 ON a.modified_by = u4.user_id
			LEFT JOIN ki_employee e1 ON a.pickup_by = e1.employee_id
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
			LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id
			LEFT JOIN ki_employee e4 ON u4.employee_id = e4.employee_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", pickup_id DESC
			LIMIT $counter, 15";
		}
		
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

	function getPickupDetail($conn, $id){
    	$sql = "SELECT kis.item_name, kis.item_specification, w.warehouse_name AS location, 
             pd.quantity_taked, '' AS picked_up, pd.quantity_taked AS taken, pd.unit_abbr, pd.notes,
             mr.material_request_detail_id
	    FROM ki_pickup_detail pd
	    LEFT JOIN ki_material_request_detail mr ON mr.material_request_detail_id = pd.material_request_detail_id
	    LEFT JOIN ki_item_and_service kis ON kis.item_id = mr.item_id
	    LEFT JOIN ki_warehouse w ON w.warehouse_id = kis.warehouse_id
	    WHERE pd.pickup_id = ".$id."";
	    $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        $row = array();

        if($count>0){
            while($r = mysqli_fetch_assoc($qur)) {
		        //query telah diambil
		        $sql_diambil ="SELECT SUM(quantity_taked) AS jumlah, PD.material_request_detail_id
		            FROM ki_pickup_detail AS PD
		            LEFT JOIN ki_material_request_detail AS MRN ON PD.material_request_detail_id = MRN.material_request_detail_id
		            LEFT JOIN ki_pickup AS P ON P.pickup_id = PD.pickup_id
		            WHERE PD.material_request_detail_id = '".$r['material_request_detail_id']."' AND P.recognized=1" ;
		        $qur_diambil = mysqli_query($conn, $sql_diambil);
		        $jumlah_diambil = 0;
		        
		        while($r_diambil = mysqli_fetch_assoc($qur_diambil)) {
		        	$tampung = $r_diambil['jumlah'];
		        	if($tampung > 0) {
		        		$jumlah_diambil = $tampung;
		        	} else {
		        		$jumlah_diambil = 0;
		        	}        
		        }      
		        $r['picked_up'] = $jumlah_diambil;
		        $row[] = $r;
            }
        }else{
            $row = array();
        }
    	mysqli_close($conn);
      	return $row;
  	}

	function getSpkl($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.overtime_workorder_id, a.overtime_workorder_number, DATE_FORMAT(a.proposed_date,'%d-%m-%Y') AS proposed_date, a.work_description, cw.company_workbase_name AS work_location, b.job_order_number AS job_order_id, c.department_name AS department_id, i.fullname AS requested_id, a.request_date, a.ordered_by, a.ordered_date, IF(a.approval1_by!='',d.fullname,'') AS approval1_by, a.approval1_date, IF(a.approval2_by!='',e.fullname,'') AS approval2_by, a.approval2_date, IF(a.verified_by!='',f.fullname,'') AS verified_by, a.verified_date, IF(a.created_by!='',g.fullname,'') AS created_by, a.created_date, IF(a.modified_by!='',h.fullname,'') AS modified_by, a.modified_date, IFNULL(a.overtime_archive,'') AS overtime_archive, IFNULL(a.overtime_file_name,'') AS overtime_file_name, IFNULL(a.overtime_file_type,'') AS overtime_file_type
			FROM ki_overtime_workorder a
			LEFT JOIN ki_job_order b ON(a.job_order_id = b.job_order_id)
			LEFT JOIN ki_department c ON(a.department_id = c.department_id)
			LEFT JOIN ki_company_workbase cw ON(cw.company_workbase_id = a.work_location)
			LEFT JOIN ki_user u1 ON(a.approval1_by = u1.user_id)
			LEFT JOIN ki_user u2 ON(u2.user_id = a.approval2_by)
			LEFT JOIN ki_user u3 ON(u3.user_id = a.verified_by)
			LEFT JOIN ki_user u4 ON(u4.user_id = a.created_by)
			LEFT JOIN ki_user u5 ON(u5.user_id = a.modified_by)
            LEFT JOIN ki_user u6 ON(u6.user_id = a.requested_id)
			LEFT JOIN ki_employee d ON(u1.employee_id = d.employee_id)
			LEFT JOIN ki_employee e ON(u2.employee_id = e.employee_id)
			LEFT JOIN ki_employee f ON(u3.employee_id = f.employee_id)
			LEFT JOIN ki_employee g ON(u4.employee_id = g.employee_id)
			LEFT JOIN ki_employee h ON(u5.employee_id = h.employee_id)
			LEFT JOIN ki_employee i ON(u6.employee_id = i.employee_id)
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", overtime_workorder_id DESC";
		} else {
			$sql = "SELECT a.overtime_workorder_id, a.overtime_workorder_number, DATE_FORMAT(a.proposed_date,'%d-%m-%Y') AS proposed_date, a.work_description, cw.company_workbase_name AS work_location, b.job_order_number AS job_order_id, c.department_name AS department_id, i.fullname AS requested_id, a.request_date, a.ordered_by, a.ordered_date, IF(a.approval1_by!='',d.fullname,'') AS approval1_by, a.approval1_date, IF(a.approval2_by!='',e.fullname,'') AS approval2_by, a.approval2_date, IF(a.verified_by!='',f.fullname,'') AS verified_by, a.verified_date, IF(a.created_by!='',g.fullname,'') AS created_by, a.created_date, IF(a.modified_by!='',h.fullname,'') AS modified_by, a.modified_date, IFNULL(a.overtime_archive,'') AS overtime_archive, IFNULL(a.overtime_file_name,'') AS overtime_file_name, IFNULL(a.overtime_file_type,'') AS overtime_file_type
			FROM ki_overtime_workorder a
			LEFT JOIN ki_job_order b ON(a.job_order_id = b.job_order_id)
			LEFT JOIN ki_department c ON(a.department_id = c.department_id)
			LEFT JOIN ki_company_workbase cw ON(cw.company_workbase_id = a.work_location)
			LEFT JOIN ki_user u1 ON(a.approval1_by = u1.user_id)
			LEFT JOIN ki_user u2 ON(u2.user_id = a.approval2_by)
			LEFT JOIN ki_user u3 ON(u3.user_id = a.verified_by)
			LEFT JOIN ki_user u4 ON(u4.user_id = a.created_by)
			LEFT JOIN ki_user u5 ON(u5.user_id = a.modified_by)
            LEFT JOIN ki_user u6 ON(u6.user_id = a.requested_id)
			LEFT JOIN ki_employee d ON(u1.employee_id = d.employee_id)
			LEFT JOIN ki_employee e ON(u2.employee_id = e.employee_id)
			LEFT JOIN ki_employee f ON(u3.employee_id = f.employee_id)
			LEFT JOIN ki_employee g ON(u4.employee_id = g.employee_id)
			LEFT JOIN ki_employee h ON(u5.employee_id = h.employee_id)
			LEFT JOIN ki_employee i ON(u6.employee_id = i.employee_id)
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", overtime_workorder_id DESC 
			LIMIT $counter, 15";
		}
		
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

	function getProposedBudget($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.cash_advance_id, a.cash_advance_number, b.job_order_number AS job_order_id, b.job_order_description, c.fullname AS person_in_charge, a.requisition_date, IFNULL(a.due_date,'') AS due_date, a.payment_date, a.rest_value, a.rest_from, a.notes, e1.fullname AS created_by, a.created_date, IFNULL(e2.fullname, '') AS modified_by, a.modified_date, IFNULL(e3.fullname, '') AS approval1, a.approval_date1, a.approval_comment1, a.approval1_status, IFNULL(e4.fullname, '') AS approval2, a.approval_date2, a.approval_comment2, a.approval2_status, IFNULL(e5.fullname, '') AS approval3, a.approval_date3, a.approval_comment3, a.approval3_status, e6.fullname AS checked_by, a.checked_date, e7.fullname AS recipient_by, a.done, bt.bank_transaction_type_name AS bank_transaction_type_id, bt.category
			FROM ki_cash_advance a
			LEFT JOIN ki_job_order b ON a.job_order_id = b.job_order_id
			LEFT JOIN ki_employee c ON a.person_in_charge = c.employee_id
            LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
            LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
            LEFT JOIN ki_user u3 ON a.approval1 = u3.user_id
            LEFT JOIN ki_user u4 ON a.approval2 = u4.user_id
            LEFT JOIN ki_user u5 ON a.approval3 = u5.user_id
            LEFT JOIN ki_user u6 ON a.checked_by = u6.user_id
            LEFT JOIN ki_user u7 ON a.recipient_by = u7.user_id
            LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
            LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
            LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id
            LEFT JOIN ki_employee e4 ON u4.employee_id = e4.employee_id
            LEFT JOIN ki_employee e5 ON u5.employee_id = e5.employee_id
            LEFT JOIN ki_employee e6 ON u6.employee_id = e6.employee_id
            LEFT JOIN ki_employee e7 ON u7.employee_id = e7.employee_id
            LEFT JOIN ki_bank_transaction_type bt ON bt.bank_transaction_type_id = a.bank_transaction_type_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", cash_advance_id DESC";
		} else {
			$sql = "SELECT a.cash_advance_id, a.cash_advance_number, b.job_order_number AS job_order_id, b.job_order_description, c.fullname AS person_in_charge, a.requisition_date, IFNULL(a.due_date,'') AS due_date, a.payment_date, a.rest_value, a.rest_from, a.notes, e1.fullname AS created_by, a.created_date, IFNULL(e2.fullname, '') AS modified_by, a.modified_date, IFNULL(e3.fullname, '') AS approval1, a.approval_date1, a.approval_comment1, a.approval1_status, IFNULL(e4.fullname, '') AS approval2, a.approval_date2, a.approval_comment2, a.approval2_status, IFNULL(e5.fullname, '') AS approval3, a.approval_date3, a.approval_comment3, a.approval3_status, e6.fullname AS checked_by, a.checked_date, e7.fullname AS recipient_by, a.done, bt.bank_transaction_type_name AS bank_transaction_type_id, bt.category
			FROM ki_cash_advance a
			LEFT JOIN ki_job_order b ON a.job_order_id = b.job_order_id
			LEFT JOIN ki_employee c ON a.person_in_charge = c.employee_id
            LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
            LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
            LEFT JOIN ki_user u3 ON a.approval1 = u3.user_id
            LEFT JOIN ki_user u4 ON a.approval2 = u4.user_id
            LEFT JOIN ki_user u5 ON a.approval3 = u5.user_id
            LEFT JOIN ki_user u6 ON a.checked_by = u6.user_id
            LEFT JOIN ki_user u7 ON a.recipient_by = u7.user_id
            LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
            LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
            LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id
            LEFT JOIN ki_employee e4 ON u4.employee_id = e4.employee_id
            LEFT JOIN ki_employee e5 ON u5.employee_id = e5.employee_id
            LEFT JOIN ki_employee e6 ON u6.employee_id = e6.employee_id
            LEFT JOIN ki_employee e7 ON u7.employee_id = e7.employee_id
            LEFT JOIN ki_bank_transaction_type bt ON bt.bank_transaction_type_id = a.bank_transaction_type_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", cash_advance_id DESC
			LIMIT $counter, 15";
		}
		
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

	function getProposedBudgetDetail($conn, $id){
		$sql = "SELECT cad.cash_advance_detail_id, cad.item_name, cad.quantity, cad.unit_abbr, cad.unit_price, IF(cad.advance_app1!='',cad.advance_app1,'-') AS advance_app1, IF(cad.advance_app2!='',cad.advance_app2,'-') AS advance_app2, IF(cad.advance_app3!='',cad.advance_app3,'-') AS advance_app3
		FROM ki_cash_advance_detail cad 
		WHERE cad.cash_advance_id = ".$id."";
		
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

	function getCashProjectReport($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.responsbility_advance_id, a.responsbility_advance_number, a.cash_advance_id, jo.job_order_description AS job_order_id, jo.job_order_number, a.begin_date, a.end_date, a.notes, IF(a.created_by!='',e1.fullname,'') AS created_by, a.created_date, IF(a.modified_by!='',e5.fullname,'') AS modified_by, a.modified_date, IF(a.approval1!='',e2.fullname,'') AS approval1, a.approval_date1, a.approval_comment1, IF(a.approval2!='',e3.fullname,'') AS approval2, a.approval_date2, a.approval_comment2, IF(a.approval3!='',e4.fullname,'') AS approval3, a.approval_date3, a.approval_comment3, IF(a.checked_by!='',e6.fullname,'') AS checked_by, a.checked_date, a.done, a.ra_archive, a.ra_file_name, a.ra_file_type, bt.bank_transaction_type_name AS bank_transaction_type_id, bt.category, a.ra_discount_type, b.cash_advance_number
			FROM ki_responsbility_advance a
			LEFT JOIN ki_cash_advance b ON a.cash_advance_id=b.cash_advance_id
			LEFT JOIN ki_job_order jo ON b.job_order_id=jo.job_order_id
			LEFT JOIN ki_user u1 ON(u1.user_id = a.created_by)
			LEFT JOIN ki_user u2 ON(u2.user_id = a.approval1)
			LEFT JOIN ki_user u3 ON(u3.user_id = a.approval2)
			LEFT JOIN ki_user u4 ON(u4.user_id = a.approval3)
			LEFT JOIN ki_user u5 ON(u5.user_id = a.modified_by)
			LEFT JOIN ki_user u6 ON(u6.user_id = a.checked_by)
			LEFT JOIN ki_employee e1 ON(e1.employee_id = u1.employee_id)
			LEFT JOIN ki_employee e2 ON(e2.employee_id = u2.employee_id)
			LEFT JOIN ki_employee e3 ON(e3.employee_id = u3.employee_id)
			LEFT JOIN ki_employee e4 ON(e4.employee_id = u4.employee_id)
			LEFT JOIN ki_employee e5 ON(e5.employee_id = u5.employee_id)
			LEFT JOIN ki_employee e6 ON(e6.employee_id = u6.employee_id)
            LEFT JOIN ki_bank_transaction_type bt ON bt.bank_transaction_type_id = a.bank_transaction_type_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", responsbility_advance_id DESC";
		} else {
			$sql = "SELECT a.responsbility_advance_id, a.responsbility_advance_number, a.cash_advance_id, jo.job_order_description AS job_order_id, jo.job_order_number, a.begin_date, a.end_date, a.notes, IF(a.created_by!='',e1.fullname,'') AS created_by, a.created_date, IF(a.modified_by!='',e5.fullname,'') AS modified_by, a.modified_date, IF(a.approval1!='',e2.fullname,'') AS approval1, a.approval_date1, a.approval_comment1, IF(a.approval2!='',e3.fullname,'') AS approval2, a.approval_date2, a.approval_comment2, IF(a.approval3!='',e4.fullname,'') AS approval3, a.approval_date3, a.approval_comment3, IF(a.checked_by!='',e6.fullname,'') AS checked_by, a.checked_date, a.done, a.ra_archive, a.ra_file_name, a.ra_file_type, bt.bank_transaction_type_name AS bank_transaction_type_id, bt.category, a.ra_discount_type, b.cash_advance_number
			FROM ki_responsbility_advance a
			LEFT JOIN ki_cash_advance b ON a.cash_advance_id=b.cash_advance_id
			LEFT JOIN ki_job_order jo ON b.job_order_id=jo.job_order_id
			LEFT JOIN ki_user u1 ON(u1.user_id = a.created_by)
			LEFT JOIN ki_user u2 ON(u2.user_id = a.approval1)
			LEFT JOIN ki_user u3 ON(u3.user_id = a.approval2)
			LEFT JOIN ki_user u4 ON(u4.user_id = a.approval3)
			LEFT JOIN ki_user u5 ON(u5.user_id = a.modified_by)
			LEFT JOIN ki_user u6 ON(u6.user_id = a.checked_by)
			LEFT JOIN ki_employee e1 ON(e1.employee_id = u1.employee_id)
			LEFT JOIN ki_employee e2 ON(e2.employee_id = u2.employee_id)
			LEFT JOIN ki_employee e3 ON(e3.employee_id = u3.employee_id)
			LEFT JOIN ki_employee e4 ON(e4.employee_id = u4.employee_id)
			LEFT JOIN ki_employee e5 ON(e5.employee_id = u5.employee_id)
			LEFT JOIN ki_employee e6 ON(e6.employee_id = u6.employee_id)
            LEFT JOIN ki_bank_transaction_type bt ON bt.bank_transaction_type_id = a.bank_transaction_type_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", responsbility_advance_id DESC
			LIMIT $counter, 15";
		}
		
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

	function getCashProjectReportDetail($conn, $id){
		$sql = "SELECT rad.responsbility_advance_detail, rad.usage_date, rad.item_name, rad.quantity, rad.unit_abbr, rad.unit_price, rad.discount, IF(rad.respons_advance_app1!='',rad.respons_advance_app1,'-') AS respons_advance_app1, IF(rad.respons_advance_app2!='',rad.respons_advance_app2,'-') AS respons_advance_app2, IF(rad.respons_advance_app3!='',rad.respons_advance_app3,'-') AS respons_advance_app3
		FROM ki_responsbility_advance_detail rad 
		WHERE rad.responsbility_advance_id = ".$id."";
		
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

	function getCashProjectReportPbReceived($conn, $id){
		$sql = "SELECT SUM(cad.quantity*cad.unit_price)+ca.rest_value AS total
		FROM ki_cash_advance_detail cad
        LEFT JOIN ki_cash_advance ca ON ca.cash_advance_id = cad.cash_advance_id
		WHERE cad.cash_advance_id = ".$id."";
		
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

	function getTunjanganKaryawan($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.employee_allowance_id, a.employee_allowance_number, b.fullname AS employee_id, eg.employee_grade_name, IF(a.additional_allowance_type='9','Tunjangan Lokasi','Tunjangan Perjalanan Dinas') AS additional_allowance_type, d.kab_name AS kab_id, e.job_order_number AS job_order_id, e.job_order_description, e.job_order_location,  DATE_FORMAT(a.begin_date,'%d-%m-%Y') AS begin_date, DATE_FORMAT(a.end_date,'%d-%m-%Y') AS end_date, a.days, a.notes, a.amount_perday, IF(a.approval1_status!='',a.approval1_status,'') AS approval1_status, IF(a.approval2_status!='',a.approval2_status,'') AS approval2_status, IF(a.requested_by!='',b.fullname,'') AS requested_by, IF(a.approval1_by!='',e2.fullname,'-') AS approval1_by, a.approval1_date, a.approval1_comment, IF(a.approval2_by!='',e3.fullname,'-') AS approval2_by, a.approval2_date, a.approval2_comment, IF(a.verified_by!='',e1.fullname,'-') AS verified_by, a.verified_date, IF(a.checked_by!='',e5.fullname,'') AS checked_by, a.checked_date, a.checked_comment, IF(a.paid='1','Ya','Tidak') AS paid, IF(a.created_by!='',e6.fullname,'') AS created_by, a.created_date, IF(a.modified_by!='',e7.fullname,'') AS modified_by, a.modified_date
			FROM ki_employee_allowance a
			LEFT JOIN ki_employee b ON a.employee_id=b.employee_id
			LEFT JOIN ki_kabupaten d ON a.kab_id=d.kab_id
			LEFT JOIN ki_job_order e ON a.job_order_id=e.job_order_id
			LEFT JOIN ki_user u1 ON(u1.user_id = a.verified_by)
			LEFT JOIN ki_user u2 ON(u2.user_id = a.approval1_by)
			LEFT JOIN ki_user u3 ON(u3.user_id = a.approval2_by)
			LEFT JOIN ki_user u4 ON(u4.user_id = a.requested_by)
			LEFT JOIN ki_user u5 ON(u5.user_id = a.checked_by)
			LEFT JOIN ki_user u6 ON(u6.user_id = a.created_by)
			LEFT JOIN ki_user u7 ON(u7.user_id = a.modified_by)
			LEFT JOIN ki_employee e1 ON(e1.employee_id = u1.employee_id)
			LEFT JOIN ki_employee e2 ON(e2.employee_id = u2.employee_id)
			LEFT JOIN ki_employee e3 ON(e3.employee_id = u3.employee_id)
			LEFT JOIN ki_employee e4 ON(e4.employee_id = u4.employee_id)
			LEFT JOIN ki_employee e5 ON(e5.employee_id = u5.employee_id)
			LEFT JOIN ki_employee e6 ON(e6.employee_id = u6.employee_id)
			LEFT JOIN ki_employee e7 ON(e7.employee_id = u7.employee_id)
            LEFT JOIN ki_employee_grade eg ON(eg.employee_grade_id = b.employee_grade_id)
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", employee_allowance_id DESC";
		} else {
			$sql = "SELECT a.employee_allowance_id, a.employee_allowance_number, b.fullname AS employee_id, eg.employee_grade_name, IF(a.additional_allowance_type='9','Tunjangan Lokasi','Tunjangan Perjalanan Dinas') AS additional_allowance_type, d.kab_name AS kab_id, e.job_order_number AS job_order_id, e.job_order_description, e.job_order_location,  DATE_FORMAT(a.begin_date,'%d-%m-%Y') AS begin_date, DATE_FORMAT(a.end_date,'%d-%m-%Y') AS end_date, a.days, a.notes, a.amount_perday, a.approval1_status, a.approval2_status, IF(a.requested_by!='',b.fullname,'') AS requested_by, IF(a.approval1_by!='',e2.fullname,'') AS approval1_by, a.approval1_date, a.approval1_comment, IF(a.approval2_by!='',e3.fullname,'') AS approval2_by, a.approval2_date, a.approval2_comment, IF(a.verified_by!='',e1.fullname,'') AS verified_by, a.verified_date, IF(a.checked_by!='',e5.fullname,'') AS checked_by, a.checked_date, a.checked_comment, IF(a.paid='1','Ya','Tidak') AS paid, IF(a.created_by!='',e6.fullname,'') AS created_by, a.created_date, IF(a.modified_by!='',e7.fullname,'') AS modified_by, a.modified_date
			FROM ki_employee_allowance a
			LEFT JOIN ki_employee b ON a.employee_id=b.employee_id
			LEFT JOIN ki_kabupaten d ON a.kab_id=d.kab_id
			LEFT JOIN ki_job_order e ON a.job_order_id=e.job_order_id
			LEFT JOIN ki_user u1 ON(u1.user_id = a.verified_by)
			LEFT JOIN ki_user u2 ON(u2.user_id = a.approval1_by)
			LEFT JOIN ki_user u3 ON(u3.user_id = a.approval2_by)
			LEFT JOIN ki_user u4 ON(u4.user_id = a.requested_by)
			LEFT JOIN ki_user u5 ON(u5.user_id = a.checked_by)
			LEFT JOIN ki_user u6 ON(u6.user_id = a.created_by)
			LEFT JOIN ki_user u7 ON(u7.user_id = a.modified_by)
			LEFT JOIN ki_employee e1 ON(e1.employee_id = u1.employee_id)
			LEFT JOIN ki_employee e2 ON(e2.employee_id = u2.employee_id)
			LEFT JOIN ki_employee e3 ON(e3.employee_id = u3.employee_id)
			LEFT JOIN ki_employee e4 ON(e4.employee_id = u4.employee_id)
			LEFT JOIN ki_employee e5 ON(e5.employee_id = u5.employee_id)
			LEFT JOIN ki_employee e6 ON(e6.employee_id = u6.employee_id)
			LEFT JOIN ki_employee e7 ON(e7.employee_id = u7.employee_id)
            LEFT JOIN ki_employee_grade eg ON(eg.employee_grade_id = b.employee_grade_id)
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", employee_allowance_id DESC
			LIMIT $counter, 15";
		}
		
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

	function getTunjanganTemporary($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.employee_allowance_id, a.employee_allowance_number, a.employee_name, IF(a.additional_allowance_type='9','Tunjangan Lokasi','Tunjangan Perjalanan Dinas') AS additional_allowance_type, c.kab_name AS kab_id, d.job_order_number AS job_order_id, d.job_order_location, d.job_order_description, DATE_FORMAT(a.begin_date,'%d-%m-%Y') AS begin_date, DATE_FORMAT(a.end_date,'%d-%m-%Y') AS end_date, a.days, a.notes, a.amount_perday, jg.job_grade_name AS employee_grade_id, IF(a.approval1_status!='',a.approval1_status,'') AS approval1_status, IF(a.approval2_status!='',a.approval2_status,'') AS approval2_status, e.fullname AS requested_by, IF(a.approval1_by!='',e2.fullname,'-') AS approval1_by, a.approval1_date, a.approval1_comment, IF(a.approval2_by!='',e3.fullname,'-') AS approval2_by, a.approval2_date, a.approval2_comment, IF(a.verified_by!='',e1.fullname,'-') AS verified_by, a.verified_date, IF(a.checked_by!='',e5.fullname,'') AS checked_by, a.checked_date, a.checked_comment, IF(a.paid='1','Ya','Tidak') AS paid, IF(a.created_by!='',e6.fullname,'') AS created_by, a.created_date, IF(a.modified_by!='',e7.fullname,'') AS modified_by, a.modified_date
			FROM ki_employee_allowance_temporary a
			LEFT JOIN ki_allowance_type b ON a.additional_allowance_type=b.allowance_type_id
			LEFT JOIN ki_kabupaten c ON a.kab_id=c.kab_id
			LEFT JOIN ki_job_order d ON a.job_order_id=d.job_order_id
			LEFT JOIN ki_employee e ON a.requested_by=e.employee_id
			LEFT JOIN ki_user u1 ON(u1.user_id = a.verified_by)
			LEFT JOIN ki_user u2 ON(u2.user_id = a.approval1_by)
			LEFT JOIN ki_user u3 ON(u3.user_id = a.approval2_by)
			LEFT JOIN ki_user u5 ON(u5.user_id = a.checked_by)
			LEFT JOIN ki_user u6 ON(u6.user_id = a.created_by)
			LEFT JOIN ki_user u7 ON(u7.user_id = a.modified_by)
			LEFT JOIN ki_employee e1 ON(e1.employee_id = u1.employee_id)
			LEFT JOIN ki_employee e2 ON(e2.employee_id = u2.employee_id)
			LEFT JOIN ki_employee e3 ON(e3.employee_id = u3.employee_id)
			LEFT JOIN ki_employee e5 ON(e5.employee_id = u5.employee_id)
			LEFT JOIN ki_employee e6 ON(e6.employee_id = u6.employee_id)
			LEFT JOIN ki_employee e7 ON(e7.employee_id = u7.employee_id)
            LEFT JOIN ki_employee_grade eg ON(eg.employee_grade_id = a.employee_grade_id)
            LEFT JOIN ki_job_grade jg ON(jg.job_grade_id = eg.job_grade_id)
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", employee_allowance_id DESC";
		} else {
			$sql = "SELECT a.employee_allowance_id, a.employee_allowance_number, a.employee_name, IF(a.additional_allowance_type='9','Tunjangan Lokasi','Tunjangan Perjalanan Dinas') AS additional_allowance_type, c.kab_name AS kab_id, d.job_order_number AS job_order_id, d.job_order_location, d.job_order_description, DATE_FORMAT(a.begin_date,'%d-%m-%Y') AS begin_date, DATE_FORMAT(a.end_date,'%d-%m-%Y') AS end_date, a.days, a.notes, a.amount_perday, jg.job_grade_name AS employee_grade_id, IF(a.approval1_status!='',a.approval1_status,'') AS approval1_status, IF(a.approval2_status!='',a.approval2_status,'') AS approval2_status, e.fullname AS requested_by, IF(a.approval1_by!='',e2.fullname,'') AS approval1_by, a.approval1_date, a.approval1_comment, IF(a.approval2_by!='',e3.fullname,'') AS approval2_by, a.approval2_date, a.approval2_comment, IF(a.verified_by!='',e1.fullname,'') AS verified_by, a.verified_date, IF(a.checked_by!='',e5.fullname,'') AS checked_by, a.checked_date, a.checked_comment, IF(a.paid='1','Ya','Tidak') AS paid, IF(a.created_by!='',e6.fullname,'') AS created_by, a.created_date, IF(a.modified_by!='',e7.fullname,'') AS modified_by, a.modified_date
			FROM ki_employee_allowance_temporary a
			LEFT JOIN ki_allowance_type b ON a.additional_allowance_type=b.allowance_type_id
			LEFT JOIN ki_kabupaten c ON a.kab_id=c.kab_id
			LEFT JOIN ki_job_order d ON a.job_order_id=d.job_order_id
			LEFT JOIN ki_employee e ON a.requested_by=e.employee_id
			LEFT JOIN ki_user u1 ON(u1.user_id = a.verified_by)
			LEFT JOIN ki_user u2 ON(u2.user_id = a.approval1_by)
			LEFT JOIN ki_user u3 ON(u3.user_id = a.approval2_by)
			LEFT JOIN ki_user u5 ON(u5.user_id = a.checked_by)
			LEFT JOIN ki_user u6 ON(u6.user_id = a.created_by)
			LEFT JOIN ki_user u7 ON(u7.user_id = a.modified_by)
			LEFT JOIN ki_employee e1 ON(e1.employee_id = u1.employee_id)
			LEFT JOIN ki_employee e2 ON(e2.employee_id = u2.employee_id)
			LEFT JOIN ki_employee e3 ON(e3.employee_id = u3.employee_id)
			LEFT JOIN ki_employee e5 ON(e5.employee_id = u5.employee_id)
			LEFT JOIN ki_employee e6 ON(e6.employee_id = u6.employee_id)
			LEFT JOIN ki_employee e7 ON(e7.employee_id = u7.employee_id)
            LEFT JOIN ki_employee_grade eg ON(eg.employee_grade_id = a.employee_grade_id)
            LEFT JOIN ki_job_grade jg ON(jg.job_grade_id = eg.job_grade_id)
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", employee_allowance_id DESC
			LIMIT $counter, 15";
		}
		
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

	function getPurchaseOrder($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.purchase_order_id, a.purchase_order_number, pot.purchase_order_type_name AS purchase_order_type_id, tt.tax_type_name AS tax_type_id, a.purchase_order_status_id, a.contract_agreement_id, ca.agreement_number AS contract_agreement_id, s.supplier_name AS supplier_id, a.purchase_quotation_number, a.purchase_quotation_date, a.begin_date, a.end_date, pt.term AS payment_term_id, a.payment_desc, a.delivery_address, a.delivery_address2, IFNULL(e0.fullname, '') AS approval_assign_id, a.notes, a.version, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, IFNULL(e4.fullname, '') AS po_approval1, a.po_approval_date1, a.po_comment1, e5.fullname AS po_approval2, a.po_approval_date2, a.po_comment2, e6.fullname AS po_approval3, a.po_approval_date3, a.po_comment3, IFNULL(e3.fullname, '') AS checked_by, IFNULL(a.checked_date, '') AS checked_date, a.purchase_archive, a.purchase_file_name, a.purchase_file_type, a.purchase_order_discount_type, jo.job_order_number, IFNULL(tt.tax_type_rate, '0') AS tax_type_rate
			FROM ki_purchase_order a
			LEFT JOIN ki_material_request_detail mrd ON a.purchase_order_id = mrd.purchase_order_id 
			LEFT JOIN ki_material_request mr ON mrd.material_request_id = mr.material_request_id
			LEFT JOIN ki_job_order jo ON mr.job_order_id = jo.job_order_id
			LEFT JOIN ki_supplier s ON a.supplier_id = s.supplier_id
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
			LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
			LEFT JOIN ki_user u3 ON a.checked_by = u3.user_id
			LEFT JOIN ki_user u4 ON a.po_approval1 = u4.user_id
			LEFT JOIN ki_user u5 ON a.po_approval2 = u5.user_id
			LEFT JOIN ki_user u6 ON a.po_approval3 = u6.user_id
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id 
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id 
			LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id 
			LEFT JOIN ki_employee e4 ON u4.employee_id = e4.employee_id 
			LEFT JOIN ki_employee e5 ON u5.employee_id = e5.employee_id 
			LEFT JOIN ki_employee e6 ON u6.employee_id = e6.employee_id 
			LEFT JOIN ki_approval_assign e0 ON a.approval_assign_id = e0.approval_assign_id 
            LEFT JOIN ki_purchase_order_type pot ON a.purchase_order_type_id = pot.purchase_order_type_id
            LEFT JOIN ki_contract_agreement ca ON a.contract_agreement_id = ca.contract_agreement_id
            LEFT JOIN ki_payment_term pt ON a.payment_term_id = pt.payment_term_id
            LEFT JOIN ki_tax_type tt ON a.tax_type_id = tt.tax_type_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", purchase_order_id DESC";
		} else {
			$sql = "SELECT a.purchase_order_id, a.purchase_order_number, pot.purchase_order_type_name AS purchase_order_type_id, tt.tax_type_name AS tax_type_id, a.purchase_order_status_id, a.contract_agreement_id, ca.agreement_number AS contract_agreement_id, s.supplier_name AS supplier_id, a.purchase_quotation_number, a.purchase_quotation_date, a.begin_date, a.end_date, pt.term AS payment_term_id, a.payment_desc, a.delivery_address, a.delivery_address2, IFNULL(e0.fullname, '') AS approval_assign_id, a.notes, a.version, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, IFNULL(e4.fullname, '') AS po_approval1, a.po_approval_date1, a.po_comment1, e5.fullname AS po_approval2, a.po_approval_date2, a.po_comment2, e6.fullname AS po_approval3, a.po_approval_date3, a.po_comment3, IFNULL(e3.fullname, '') AS checked_by, IFNULL(a.checked_date, '') AS checked_date, a.purchase_archive, a.purchase_file_name, a.purchase_file_type, a.purchase_order_discount_type, jo.job_order_number, IFNULL(tt.tax_type_rate, '0') AS tax_type_rate
			FROM ki_purchase_order a
			LEFT JOIN ki_material_request_detail mrd ON a.purchase_order_id = mrd.purchase_order_id 
			LEFT JOIN ki_material_request mr ON mrd.material_request_id = mr.material_request_id
			LEFT JOIN ki_job_order jo ON mr.job_order_id = jo.job_order_id
			LEFT JOIN ki_supplier s ON a.supplier_id = s.supplier_id
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
			LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
			LEFT JOIN ki_user u3 ON a.checked_by = u3.user_id
			LEFT JOIN ki_user u4 ON a.po_approval1 = u4.user_id
			LEFT JOIN ki_user u5 ON a.po_approval2 = u5.user_id
			LEFT JOIN ki_user u6 ON a.po_approval3 = u6.user_id
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id 
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id 
			LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id 
			LEFT JOIN ki_employee e4 ON u4.employee_id = e4.employee_id 
			LEFT JOIN ki_employee e5 ON u5.employee_id = e5.employee_id 
			LEFT JOIN ki_employee e6 ON u6.employee_id = e6.employee_id 
			LEFT JOIN ki_approval_assign e0 ON a.approval_assign_id = e0.approval_assign_id 
            LEFT JOIN ki_purchase_order_type pot ON a.purchase_order_type_id = pot.purchase_order_type_id
            LEFT JOIN ki_contract_agreement ca ON a.contract_agreement_id = ca.contract_agreement_id
            LEFT JOIN ki_payment_term pt ON a.payment_term_id = pt.payment_term_id
            LEFT JOIN ki_tax_type tt ON a.tax_type_id = tt.tax_type_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", purchase_order_id DESC
			LIMIT $counter, 15";
		}
		
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

	//detail purchase order
	function getPurchaseOrderDetail($conn, $po_id){
		$sql = "SELECT mrd.material_request_detail_id, ias.item_name, ias.item_specification, mrd.quantity, mrd.unit_abbr, IF(mrd.unit_price_buy!='',mrd.unit_price_buy,'0') AS unit_price_buy, IF(mrd.max_budget!='',mrd.max_budget,'0') AS max_budget, IF(mrd.discount!='',mrd.discount,'0') AS discount, IF(mrd.po_app1!='',mrd.po_app1,'-') AS po_app1
        FROM ki_material_request_detail mrd
        INNER JOIN ki_item_and_service ias ON(ias.item_id = mrd.item_id)
        INNER JOIN ki_purchase_order po
        ON(po.purchase_order_id = mrd.purchase_order_id)
        WHERE po.purchase_order_id = ".$po_id."";
    
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

	function getPurchaseService($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.purchase_service_id, a.purchase_service_number, pot.purchase_order_type_name AS purchase_order_type_id, tt.tax_type_name AS tax_type_id, a.purchase_order_status_id, ca.agreement_number AS contract_agreement_id, s.supplier_name AS supplier_id, a.purchase_quotation_number, a.purchase_quotation_date, a.begin_date, a.end_date, pt.term AS payment_term_id, a.payment_desc, IFNULL(e0.fullname, '') AS approval_assign_id, a.notes, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, IFNULL(e4.fullname, '') AS po_approval1, a.po_approval_date1, a.po_comment1, IFNULL(e3.fullname, '') AS checked_by, a.checked_date, a.purchase_service_archive, a.purchase_service_file_name, a.purchase_service_file_type, a.purchase_service_discount_type, IFNULL(tt.tax_type_rate, '0') AS tax_type_rate
			FROM ki_purchase_service a
			LEFT JOIN ki_supplier s ON a.supplier_id = s.supplier_id
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
			LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
			LEFT JOIN ki_user u3 ON a.checked_by = u3.user_id
			LEFT JOIN ki_user u4 ON a.po_approval1 = u4.user_id
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id 
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id 
			LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id 
			LEFT JOIN ki_employee e4 ON u4.employee_id = e4.employee_id 
			LEFT JOIN ki_approval_assign e0 ON a.approval_assign_id = e0.approval_assign_id 
            LEFT JOIN ki_purchase_order_type pot ON a.purchase_order_type_id = pot.purchase_order_type_id
            LEFT JOIN ki_contract_agreement ca ON a.contract_agreement_id = ca.contract_agreement_id
            LEFT JOIN ki_payment_term pt ON a.payment_term_id = pt.payment_term_id
            LEFT JOIN ki_tax_type tt ON a.tax_type_id = tt.tax_type_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", purchase_service_id DESC";
		} else {
			$sql = "SELECT a.purchase_service_id, a.purchase_service_number, pot.purchase_order_type_name AS purchase_order_type_id, tt.tax_type_name AS tax_type_id, a.purchase_order_status_id, ca.agreement_number AS contract_agreement_id, s.supplier_name AS supplier_id, a.purchase_quotation_number, a.purchase_quotation_date, a.begin_date, a.end_date, pt.term AS payment_term_id, a.payment_desc, IFNULL(e0.fullname, '') AS approval_assign_id, a.notes, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, IFNULL(e4.fullname, '') AS po_approval1, a.po_approval_date1, a.po_comment1, IFNULL(e3.fullname, '') AS checked_by, a.checked_date, a.purchase_service_archive, a.purchase_service_file_name, a.purchase_service_file_type, a.purchase_service_discount_type, IFNULL(tt.tax_type_rate, '0') AS tax_type_rate
			FROM ki_purchase_service a
			LEFT JOIN ki_supplier s ON a.supplier_id = s.supplier_id
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
			LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
			LEFT JOIN ki_user u3 ON a.checked_by = u3.user_id
			LEFT JOIN ki_user u4 ON a.po_approval1 = u4.user_id
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id 
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id 
			LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id 
			LEFT JOIN ki_employee e4 ON u4.employee_id = e4.employee_id 
			LEFT JOIN ki_approval_assign e0 ON a.approval_assign_id = e0.approval_assign_id 
            LEFT JOIN ki_purchase_order_type pot ON a.purchase_order_type_id = pot.purchase_order_type_id
            LEFT JOIN ki_contract_agreement ca ON a.contract_agreement_id = ca.contract_agreement_id
            LEFT JOIN ki_payment_term pt ON a.payment_term_id = pt.payment_term_id
            LEFT JOIN ki_tax_type tt ON a.tax_type_id = tt.tax_type_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", purchase_service_id DESC
			LIMIT $counter, 15";
		}
		
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

	//detail work order
	function getPurchaseServiceDetail($conn, $ps_id){
		$sql = "SELECT wod.work_order_detail_id, wod.item_name, wod.quantity, wod.unit_abbr, IF(wod.unit_price!='',wod.unit_price,'0') AS unit_price, IF(wod.max_budget!='',wod.max_budget,'0') AS max_budget, IF(wod.discount!='',wod.discount,'0') AS discount, IF(wod.ps_app1!='',wod.ps_app1,'-') AS ps_app1
        FROM ki_work_order_detail wod
        INNER JOIN ki_purchase_service ps ON(ps.purchase_service_id = wod.purchase_service_id)
        WHERE ps.purchase_service_id = ".$ps_id."";
    
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

	function getCashOnDelivery($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.cash_on_delivery_id, a.cash_on_delivery_number, jo.job_order_number AS job_order_id, jo.job_order_description, ub.fullname AS used_by, pot.purchase_order_type_name AS purchase_order_type_id, tt.tax_type_name AS tax_type_id, a.purchase_order_status_id, s.supplier_name AS supplier_id, a.begin_date, a.end_date, pt.term AS payment_term_id, a.payment_desc, a.delivery_address, a.delivery_address2, a.notes, a.version, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, IFNULL(e0.fullname, '') AS approval_assign_id, IFNULL(e4.fullname, '') AS approval1, a.approval_date1, a.approval_comment1, IFNULL(e3.fullname, '') AS checked_by, a.checked_date, a.cod_archive, a.cod_file_name, a.cod_file_type, a.cod_discount_type, IFNULL(tt.tax_type_rate, '0') AS tax_type_rate
			FROM ki_cash_on_delivery a
			LEFT JOIN ki_supplier s ON a.supplier_id = s.supplier_id
			LEFT JOIN ki_job_order jo ON a.job_order_id = jo.job_order_id
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
			LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
			LEFT JOIN ki_user u3 ON a.checked_by = u3.user_id
			LEFT JOIN ki_user u4 ON a.approval1 = u4.user_id
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id 
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id 
			LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id 
			LEFT JOIN ki_employee e4 ON u4.employee_id = e4.employee_id 
			LEFT JOIN ki_approval_assign e0 ON a.approval_assign_id = e0.approval_assign_id 
            LEFT JOIN ki_purchase_order_type pot ON a.purchase_order_type_id = pot.purchase_order_type_id
            LEFT JOIN ki_employee ub ON a.used_by = ub.employee_id
            LEFT JOIN ki_tax_type tt ON a.tax_type_id = tt.tax_type_id
            LEFT JOIN ki_payment_term pt ON a.payment_term_id = pt.payment_term_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", cash_on_delivery_id DESC";
		} else {
			$sql = "SELECT a.cash_on_delivery_id, a.cash_on_delivery_number, jo.job_order_number AS job_order_id, jo.job_order_description, ub.fullname AS used_by, pot.purchase_order_type_name AS purchase_order_type_id, tt.tax_type_name AS tax_type_id, a.purchase_order_status_id, s.supplier_name AS supplier_id, a.begin_date, a.end_date, pt.term AS payment_term_id, a.payment_desc, a.delivery_address, a.delivery_address2, a.notes, a.version, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, IFNULL(e0.fullname, '') AS approval_assign_id, IFNULL(e4.fullname, '') AS approval1, a.approval_date1, a.approval_comment1, IFNULL(e3.fullname, '') AS checked_by, a.checked_date, a.cod_archive, a.cod_file_name, a.cod_file_type, a.cod_discount_type, IFNULL(tt.tax_type_rate, '0') AS tax_type_rate
			FROM ki_cash_on_delivery a
			LEFT JOIN ki_supplier s ON a.supplier_id = s.supplier_id
			LEFT JOIN ki_job_order jo ON a.job_order_id = jo.job_order_id
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
			LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
			LEFT JOIN ki_user u3 ON a.checked_by = u3.user_id
			LEFT JOIN ki_user u4 ON a.approval1 = u4.user_id
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id 
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id 
			LEFT JOIN ki_employee e3 ON u3.employee_id = e3.employee_id 
			LEFT JOIN ki_employee e4 ON u4.employee_id = e4.employee_id 
			LEFT JOIN ki_approval_assign e0 ON a.approval_assign_id = e0.approval_assign_id 
            LEFT JOIN ki_purchase_order_type pot ON a.purchase_order_type_id = pot.purchase_order_type_id
            LEFT JOIN ki_employee ub ON a.used_by = ub.employee_id
            LEFT JOIN ki_tax_type tt ON a.tax_type_id = tt.tax_type_id
            LEFT JOIN ki_payment_term pt ON a.payment_term_id = pt.payment_term_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", cash_on_delivery_id DESC
			LIMIT $counter, 15";
		}
		
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

	function getCashOnDeliveryDetail($conn, $cod_id){
		$sql = "SELECT codd.cash_on_delivery_detail_id, codd.item_name, codd.quantity, codd.unit_abbr, IF(codd.unit_price!='', codd.unit_price, '0') AS unit_price,  IF(codd.max_budget!='', codd.max_budget, '0') AS max_budget, IF(codd.discount!='', codd.discount, '0') AS discount, IF(codd.cod_app1!='', codd.cod_app1, '-') AS cod_app1
		FROM ki_cash_on_delivery_detail codd
		WHERE codd.cash_on_delivery_id = ".$cod_id."";
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

	function getContractAgreement($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.contract_agreement_id, a.agreement_number, IFNULL(a.related_agreement, '') AS related_agreement, IFNULL(s.supplier_name, '') AS supplier_id, IFNULL(ct.contact_name, '') AS contact_id, a.agreement_date, a.begin_date, a.end_date, a.agreement_archive, a.agreement_status, a.notes, e1.fullname AS created_by, a.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date, a.agreement_file_name
			FROM ki_contract_agreement a
			LEFT JOIN ki_supplier s ON a.supplier_id = s.supplier_id
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
			LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
            LEFT JOIN ki_contact ct ON ct.contact_id = a.contact_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", contract_agreement_id DESC";
		} else {
			$sql = "SELECT a.contract_agreement_id, a.agreement_number, IFNULL(a.related_agreement, '') AS related_agreement, IFNULL(s.supplier_name, '') AS supplier_id, IFNULL(ct.contact_name, '') AS contact_id, a.agreement_date, a.begin_date, a.end_date, a.agreement_archive, a.agreement_status, a.notes, e1.fullname AS created_by, a.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date, a.agreement_file_name
			FROM ki_contract_agreement a
			LEFT JOIN ki_supplier s ON a.supplier_id = s.supplier_id
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
			LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
            LEFT JOIN ki_contact ct ON ct.contact_id = a.contact_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", contract_agreement_id DESC
			LIMIT $counter, 15";
		}
		
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

	function getGoodReceivedNote($conn, $counter, $sortBy){
        if ($counter<0) {
            $sql = "SELECT a.grn_id, a.grn_number, a.receipt_date, po.purchase_order_number AS purchase_order_id, a.notes, a.recognized, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, s.supplier_name
            FROM ki_good_received_note a
            LEFT JOIN ki_purchase_order po ON a.purchase_order_id = po.purchase_order_id
            LEFT JOIN ki_supplier s ON po.supplier_id = s.supplier_id
            LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
            LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
            LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
            LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
			WHERE YEAR(a.created_date) > 2016
            ORDER BY ".$sortBy.", grn_id DESC";
        } else {
            $sql = "SELECT a.grn_id, a.grn_number, a.receipt_date, po.purchase_order_number AS purchase_order_id, a.notes, a.recognized, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, s.supplier_name
            FROM ki_good_received_note a
            LEFT JOIN ki_purchase_order po ON a.purchase_order_id = po.purchase_order_id
            LEFT JOIN ki_supplier s ON po.supplier_id = s.supplier_id
            LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
            LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
            LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
            LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
			WHERE YEAR(a.created_date) > 2016
            ORDER BY ".$sortBy.", grn_id DESC
            LIMIT $counter, 15";
        }
        
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

    function getGoodReceivedNoteDetail($conn, $grn_id){
    	$sql = "SELECT ias.item_name, grnd.quantity_received, grnd.unit_abbr, grnd.notes, ias.item_specification, wh.warehouse_name, jo.job_order_number
	    	FROM ki_good_received_note_detail grnd
	    	INNER JOIN ki_material_request_detail mrd ON(mrd.material_request_detail_id = grnd.material_request_detail_id)
	    	INNER JOIN ki_item_and_service ias ON(ias.item_id = mrd.item_id)
            LEFT JOIN ki_warehouse wh ON wh.warehouse_id = ias.warehouse_id
            LEFT JOIN ki_material_request mr ON mr.material_request_id = mrd.material_request_id
            LEFT JOIN ki_job_order jo ON jo.job_order_id = mr.job_order_id
            WHERE grnd.grn_id = ".$grn_id."";
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

    function getPurchasingDetailSi($conn, $tabel, $id){
    	$sql = "SELECT si.supplier_invoice_number, s.supplier_name, sid.amount, sid.discount, sid.ppn, IF(sid.status=1, 'Paid', 'Received') AS supplier_invoice_status, si.supplier_invoice_date
		FROM ki_supplier_invoice_detail sid
		LEFT JOIN ki_supplier_invoice si ON si.supplier_invoice_id = sid.supplier_invoice_id
		LEFT JOIN ki_supplier s ON s.supplier_id = si.supplier_id
		WHERE ".$tabel." =".$id."";
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
	
	function getWorkHandover($conn, $counter, $sortBy){
        if ($counter<0) {
            $sql = "SELECT a.work_handover_id, a.work_handover_number, a.receipt_date, ps.purchase_service_number AS purchase_service_id, a.recognized, a.notes, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, s.supplier_name
            FROM ki_work_handover a
            LEFT JOIN ki_purchase_service ps ON a.purchase_service_id = ps.purchase_service_id
            LEFT JOIN ki_supplier s ON ps.supplier_id = s.supplier_id
            LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
            LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
            LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
            LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
			WHERE YEAR(a.created_date) > 2016
            ORDER BY ".$sortBy.", work_handover_id DESC";
        } else {
            $sql = "SELECT a.work_handover_id, a.work_handover_number, a.receipt_date, ps.purchase_service_number AS purchase_service_id, a.recognized, a.notes, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, s.supplier_name
            FROM ki_work_handover a
            LEFT JOIN ki_purchase_service ps ON a.purchase_service_id = ps.purchase_service_id
            LEFT JOIN ki_supplier s ON ps.supplier_id = s.supplier_id
            LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
            LEFT JOIN ki_user u2 ON a.modified_by = u2.user_id
            LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
            LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
			WHERE YEAR(a.created_date) > 2016
            ORDER BY ".$sortBy.", work_handover_id DESC
            LIMIT $counter, 15";
        }
        
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

	function getWorkHandoverDetail($conn, $wh_id){
		$sql = "SELECT wod.item_name, whd.quantity, whd.unit_abbr, whd.notes, wo.work_order_description, wo.notes AS wo_notes
		    FROM ki_work_handover_detail whd
		    INNER JOIN ki_work_order_detail wod ON (whd.work_order_detail_id = wod.work_order_detail_id)
            LEFT JOIN ki_work_order wo ON wo.work_order_id = wod.work_order_id
		    WHERE whd.work_handover_id = ".$wh_id."";
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

	function getServicesReceipt($conn, $counter, $sortBy){  
		if ($counter<0) {
			$sql = "SELECT a.services_receipt_id, a.services_receipt_number, a.receipt_date, cod.cash_on_delivery_number AS cash_on_delivery_id, a.recognized, a.notes, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, jo.job_order_number
			FROM ki_services_receipt a
			LEFT JOIN ki_cash_on_delivery cod ON a.cash_on_delivery_id = cod.cash_on_delivery_id
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
			LEFT JOIN ki_user u2 ON a.created_by = u2.user_id
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
            LEFT JOIN ki_job_order jo ON cod.job_order_id = jo.job_order_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", services_receipt_id DESC";
		} else {
			$sql = "SELECT a.services_receipt_id, a.services_receipt_number, a.receipt_date, cod.cash_on_delivery_number AS cash_on_delivery_id, a.recognized, a.notes, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, jo.job_order_number
			FROM ki_services_receipt a
			LEFT JOIN ki_cash_on_delivery cod ON a.cash_on_delivery_id = cod.cash_on_delivery_id
			LEFT JOIN ki_user u1 ON a.created_by = u1.user_id
			LEFT JOIN ki_user u2 ON a.created_by = u2.user_id
			LEFT JOIN ki_employee e1 ON u1.employee_id = e1.employee_id
			LEFT JOIN ki_employee e2 ON u2.employee_id = e2.employee_id
            LEFT JOIN ki_job_order jo ON cod.job_order_id = jo.job_order_id
			WHERE YEAR(a.created_date) > 2016
			ORDER BY ".$sortBy.", services_receipt_id DESC
			LIMIT $counter, 15";
		}
		
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

	function getServicesReceiptDetail($conn, $svr_id){
		$sql = "SELECT codd.item_name, srd.quantity, srd.unit_abbr, srd.notes
		    FROM ki_services_receipt_detail srd
		    INNER JOIN ki_cash_on_delivery_detail codd 
		    ON(codd.cash_on_delivery_detail_id = srd.cash_on_delivery_detail_id) 
		    WHERE srd.services_receipt_id = ".$svr_id."";
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

	function getNews($conn){
        $sql = "SELECT news_id, news_title, image_name, news_contents 
        FROM ki_news ORDER BY news_id DESC
        LIMIT 0,5";

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

  	function getContact($conn, $counter, $sortBy){
  		if ($counter<0) {
			$sql = "SELECT t.contact_id, t.contact_name, c.company_name, t.contact_jobtitle, t.contact_email, t.contact_phone1, t.contact_address, t.contact_city, t.contact_state, t.contact_country, t.contact_zipcode, pt.term AS payment_term_id, t.contact_catid, t.contact_phone1, t.contact_phone2, t.contact_fax, t.contact_email, t.contact_ym, t.contact_skype, t.contact_bill_address, t.contact_bill_city, t.contact_bill_state, t.contact_bill_country, t.contact_bill_zipcode, t.npwp, t.contact_notes, t.contact_salutation
			FROM ki_contact t
			LEFT JOIN ki_company c ON c.company_id = t.company_id
            LEFT JOIN ki_payment_term pt ON pt.payment_term_id = t.payment_term_id
			ORDER BY ".$sortBy.", t.contact_name ASC";
		} else {
			$sql = "SELECT t.contact_id, t.contact_name, c.company_name, t.contact_jobtitle, t.contact_email, t.contact_phone1, t.contact_address, t.contact_city, t.contact_state, t.contact_country, t.contact_zipcode, pt.term AS payment_term_id, t.contact_catid, t.contact_phone1, t.contact_phone2, t.contact_fax, t.contact_email, t.contact_ym, t.contact_skype, t.contact_bill_address, t.contact_bill_city, t.contact_bill_state, t.contact_bill_country, t.contact_bill_zipcode, t.npwp, t.contact_notes, t.contact_salutation
			FROM ki_contact t
			LEFT JOIN ki_company c ON c.company_id = t.company_id
            LEFT JOIN ki_payment_term pt ON pt.payment_term_id = t.payment_term_id
			ORDER BY ".$sortBy.", t.contact_name ASC
			LIMIT $counter, 15";
		}
		
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

  	function getSupplier($conn, $counter, $sortBy){
  		if ($counter<0) {
			$sql = "SELECT t.supplier_id, t.supplier_name, t.supplier_number, IFNULL(t.supplier_type, '') AS supplier_type, t.office_phone, t.supplier_contact, t.phone, t.email_address, t.bank_name, t.bank_account, IF(t.is_active=1,'Ya','Tidak') AS is_active, t.website, t.supplier_lastlogin, t.office_address1, t.office_address2, t.office_fax, t.npwp, t.sppkp_number, t.name_of_bank_account, t.description, t.supplier_file_name
			FROM ki_supplier t
			ORDER BY ".$sortBy."";
		} else {
			$sql = "SELECT t.supplier_id, t.supplier_name, t.supplier_number, IFNULL(t.supplier_type, '') AS supplier_type, t.office_phone, t.supplier_contact, t.phone, t.email_address, t.bank_name, t.bank_account, IF(t.is_active=1,'Ya','Tidak') AS is_active, t.website, t.supplier_lastlogin, t.office_address1, t.office_address2, t.office_fax, t.npwp, t.sppkp_number, t.name_of_bank_account, t.description, t.supplier_file_name
			FROM ki_supplier t
			ORDER BY ".$sortBy."
			LIMIT $counter, 15";
		}
		
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

  	function getSupplierDetail($conn, $siId){
  		$sql = "SELECT si.due_date AS order_date, si.cash_on_delivery_id AS order_number, si.amount, si.supplier_invoice_date, si.supplier_invoice_number, si.amount-si.discount-si.ppn-si.pph AS grand_total, si.supplier_invoice_status, si.payment_date, si.amount, si.discount, si.ppn, si.pph, si.stamp, si.adjustment_value
		FROM ki_supplier_invoice si
		WHERE si.supplier_id = ".$siId."
		ORDER BY order_date DESC";
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

  	function getCompany($conn, $counter, $sortBy){
  		if ($counter<0) {
			$sql = "SELECT t.company_id, t.company_name, t.company_code, t.company_address, t.company_city, t.company_email, t.npwp, t.company_address_sppkp, t.company_city, t.company_state, t.company_country, t.company_zipcode, t.company_phone1, t.company_phone2, t.company_fax, t.company_email, t.company_www, IFNULL(t.company_notes, '') AS company_notes
			FROM ki_company t
			ORDER BY ".$sortBy.", company_id ASC";
		} else {
			$sql = "SELECT t.company_id, t.company_name, t.company_code, t.company_address, t.company_city, t.company_email, t.npwp, t.company_address_sppkp, t.company_city, t.company_state, t.company_country, t.company_zipcode, t.company_phone1, t.company_phone2, t.company_fax, t.company_email, t.company_www, IFNULL(t.company_notes, '') AS company_notes
			FROM ki_company t
			ORDER BY ".$sortBy.", company_id ASC
			LIMIT $counter, 15";
		}
		
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

  	function getCompanyDetContact($conn, $cpnId){
  		$sql = "SELECT c.contact_name, c.contact_catid, c.contact_jobtitle, c.contact_email, c.contact_phone1
		FROM ki_contact c
		WHERE c.company_id = ".$cpnId."
		ORDER BY c.contact_name ASC";
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

  	function getCompanyDetInvoice($conn, $cpnId){
  		$sql = "SELECT soi.invoice_date, soi.sales_order_invoice_number, soi.due_date, soi.service_amount, soi.service_ppn, soi.pph, soi.adjustment_value, soi.sales_order_invoice_status
		FROM ki_sales_order_invoice soi WHERE soi.company_id = ".$cpnId." 
		ORDER BY soi.invoice_date ASC";
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

  	function getAccessRequest($conn, $counter, $sortBy){
  		if ($counter<0) {
			$sql = "SELECT t.access_request_id, t.access_request_number, t.notes, DATE_FORMAT(t.request_date,'%d-%m-%Y') AS request_date, IF(t.approval1!='',e1.fullname,'') AS approval1, e2.fullname AS employee_request, e3.fullname AS created_by, t.created_date, IFNULL(e4.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date, IFNULL(t.approval_date1, '') AS approval_date, t.approval_comment1
			FROM ki_access_request t
			LEFT JOIN ki_user u ON(u.user_id = t.approval1)
			LEFT JOIN ki_employee e1 ON(e1.employee_id = u.employee_id)
			LEFT JOIN ki_employee e2 ON(e2.employee_id = t.employee_id)
			LEFT JOIN ki_user u3 ON u3.user_id = t.created_by
			LEFT JOIN ki_user u4 ON u4.user_id = t.modified_by
			LEFT JOIN ki_employee e3 ON(e3.employee_id = u3.employee_id)
			LEFT JOIN ki_employee e4 ON(e4.employee_id = u4.employee_id)
			ORDER BY ".$sortBy.", t.access_request_id DESC";
		} else {
			$sql = "SELECT t.access_request_id, t.access_request_number, t.notes, DATE_FORMAT(t.request_date,'%d-%m-%Y') AS request_date, IF(t.approval1!='',e1.fullname,'') AS approval1, e2.fullname AS employee_request, e3.fullname AS created_by, t.created_date, IFNULL(e4.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date, IFNULL(t.approval_date1, '') AS approval_date, t.approval_comment1
			FROM ki_access_request t
			LEFT JOIN ki_user u ON(u.user_id = t.approval1)
			LEFT JOIN ki_employee e1 ON(e1.employee_id = u.employee_id)
			LEFT JOIN ki_employee e2 ON(e2.employee_id = t.employee_id)
			LEFT JOIN ki_user u3 ON u3.user_id = t.created_by
			LEFT JOIN ki_user u4 ON u4.user_id = t.modified_by
			LEFT JOIN ki_employee e3 ON(e3.employee_id = u3.employee_id)
			LEFT JOIN ki_employee e4 ON(e4.employee_id = u4.employee_id)
			ORDER BY ".$sortBy.", t.access_request_id DESC
			LIMIT $counter, 15";
		}
		
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

  	function getAccessRequestDetail($conn, $arId){
  		$sql = "SELECT ard.access_name, ard.notes, ard.approval1
		FROM ki_access_request_detail ard 
		WHERE ard.access_request_id = ".$arId."";
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

  	//list supplier invoice
	function getSupplierInvoice($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT si.supplier_invoice_id, si.supplier_invoice_number, s.supplier_name, DATE_FORMAT(si.supplier_invoice_date,'%d-%m-%Y') AS supplier_invoice_date, DATE_FORMAT(si.invoice_receipt_date,'%d-%m-%Y') AS invoice_receipt_date, DATE_FORMAT(si.due_date,'%d-%m-%Y') AS due_date, IFNULL(DATE_FORMAT(si.payment_date,'%d-%m-%Y'), '') AS payment_date, si.supplier_invoice_status, datediff(si.due_date, curdate()) as late_days, (si.amount - si.discount + si.ppn + si.stamp + si.adjustment_value - si.pph) AS TotalSI, IFNULL(cod.cash_on_delivery_number, '') AS transaction_number, btt.category, si.tax_number, si.number_pieces_of_evidence, IFNULL(si.date_pieces_of_evidence, '') AS date_pieces_of_evidence, IFNULL(DATE_FORMAT(si.schedule_date,'%d-%m-%Y'), '') AS schedule_date, btt.bank_transaction_type_name, si.amount, si.discount, si.ppn, si.pph, si.stamp, si.adjustment_value, bt.bank_transaction_number, si.supplier_invoice_file_name, si.supplier_invoice_description, si.notes, e1.fullname AS created_by, si.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(si.modified_date, '') AS modified_date
			FROM ki_supplier_invoice si
			LEFT JOIN ki_supplier s ON(si.supplier_id = s.supplier_id)
            LEFT JOIN ki_cash_on_delivery cod ON cod.cash_on_delivery_id = si.cash_on_delivery_id
            LEFT JOIN ki_bank_transaction_type btt ON btt.bank_transaction_type_id = si.bank_transaction_type_id
            LEFT JOIN ki_bank_transaction_detail btd ON btd.supplier_invoice_id = si.supplier_invoice_id
            LEFT JOIN ki_bank_transaction bt ON bt.bank_transaction_id = btd.bank_transaction_id
            LEFT JOIN ki_user u1 ON u1.user_id = si.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = si.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", si.invoice_receipt_date DESC";
		} else {
			$sql = "SELECT si.supplier_invoice_id, si.supplier_invoice_number, s.supplier_name, DATE_FORMAT(si.supplier_invoice_date,'%d-%m-%Y') AS supplier_invoice_date, DATE_FORMAT(si.invoice_receipt_date,'%d-%m-%Y') AS invoice_receipt_date, DATE_FORMAT(si.due_date,'%d-%m-%Y') AS due_date, IFNULL(DATE_FORMAT(si.payment_date,'%d-%m-%Y'), '') AS payment_date, si.supplier_invoice_status, datediff(si.due_date, curdate()) as late_days, (si.amount - si.discount + si.ppn + si.stamp + si.adjustment_value - si.pph) AS TotalSI, IFNULL(cod.cash_on_delivery_number, '') AS transaction_number, btt.category, si.tax_number, si.number_pieces_of_evidence, IFNULL(si.date_pieces_of_evidence, '') AS date_pieces_of_evidence, IFNULL(DATE_FORMAT(si.schedule_date,'%d-%m-%Y'), '') AS schedule_date, btt.bank_transaction_type_name, si.amount, si.discount, si.ppn, si.pph, si.stamp, si.adjustment_value, IFNULL(bt.bank_transaction_number, '') AS bank_transaction_number, si.supplier_invoice_file_name, si.supplier_invoice_description, si.notes, e1.fullname AS created_by, si.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(si.modified_date, '') AS modified_date
			FROM ki_supplier_invoice si
			LEFT JOIN ki_supplier s ON(si.supplier_id = s.supplier_id)
            LEFT JOIN ki_cash_on_delivery cod ON cod.cash_on_delivery_id = si.cash_on_delivery_id
            LEFT JOIN ki_bank_transaction_type btt ON btt.bank_transaction_type_id = si.bank_transaction_type_id
            LEFT JOIN ki_bank_transaction_detail btd ON btd.supplier_invoice_id = si.supplier_invoice_id
            LEFT JOIN ki_bank_transaction bt ON bt.bank_transaction_id = btd.bank_transaction_id
            LEFT JOIN ki_user u1 ON u1.user_id = si.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = si.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", si.invoice_receipt_date DESC
			LIMIT $counter, 15";
		}
    
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

	function getSupplierInvoiceDetail($conn, $siId){
		$sql = "SELECT IFNULL(grn.grn_number, '') AS grn, IFNULL(sr.services_receipt_number, '') AS services_receipt, IFNULL(wh.work_handover_number, '') AS work_handover, IFNULL(cod.cash_on_delivery_number, '') AS cash_on_delivery, sp.supplier_name, IFNULL(sid.notes, '') AS notes, sid.amount, sid.discount, sid.ppn
		FROM ki_supplier_invoice_detail sid
		LEFT JOIN ki_supplier_invoice si ON si.supplier_invoice_id = sid.supplier_invoice_id
		LEFT JOIN ki_cash_on_delivery cod ON cod.cash_on_delivery_id = si.cash_on_delivery_id
		LEFT JOIN ki_supplier sp ON sp.supplier_id = si.supplier_id
		LEFT JOIN ki_good_received_note grn ON grn.grn_id = sid.grn_id
		LEFT JOIN ki_work_handover wh ON wh.work_handover_id = sid.work_handover_id
        LEFT JOIN ki_services_receipt sr ON sr.services_receipt_id = sid.services_receipt_id
		WHERE sid.supplier_invoice_id = ".$siId."";
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

  	//list customer invoice
	function getCustomerInvoices($conn, $counter, $sortBy){
		if ($counter<0) 
		{
			$sql = "SELECT a.sales_order_invoice_id, a.sales_order_invoice_number, 
						   a.sales_order_invoice_description, 
						   jo.job_order_number AS job_order_id, 
						   sq.sales_quotation_number AS sales_quotation_id, 
						   a.job_progress_report_id AS wcc_id,	
						   jpr.job_progress_report_number AS job_progress_report_id, 
						   a.due_date, a.client_po_number, a.sales_order_invoice_status, 
						   pt.days_before_due AS payment_late, 
						   (a.service_amount + a.service_ppn) AS grand_total, 
						   cp.company_name, app.fullname AS approval_assign, 
						   btt.category, btt.bank_transaction_type_name, 
						   a.tax_number, a.number_pieces_of_evidence, 
						   IFNULL(a.date_pieces_of_evidence, '') AS date_pieces_of_evidence, 
						   IFNULL(a.payment_date, '') AS payment_date, 
						   a.invoice_date, a.customer_receive_date, 
						   IFNULL(a.material_description, 'No Material Description') AS material_description, 
						   a.amount, a.ppn, a.discount, a.service_description, a.service_amount, 
						   a.service_discount, a.service_ppn, tt.tax_type_name AS tax_type, 
						   tt.tax_type_rate, IFNULL(a.adjustment_desc, '') AS adjustment_desc, 
						   a.adjustment_value, IFNULL(bt.bank_transaction_number, '') AS bank_transaction_number, 
						   IFNULL(cr.customer_receive_number, '') AS customer_receive_number, a.notes, 
						   e1.fullname AS created_by, a.created_date, IFNULL(e2.fullname, '') AS modified_by, 
						   IFNULL(a.modified_date, '') AS modified_date, 
						   IFNULL(a.material_income_type_id, 'No Type Material Income') AS material_income_type, 
						   IFNULL(a.service_income_type_id, 'No Type Service Income') AS service_income_type,
						   tts.tax_type_rate AS tax_type_ppn, a.tax_type_id
            FROM ki_sales_order_invoice a
            LEFT JOIN ki_job_order jo ON a.job_order_id=jo.job_order_id
            LEFT JOIN ki_sales_quotation sq ON a.sales_quotation_id=sq.sales_quotation_id
            LEFT JOIN ki_job_progress_report jpr ON a.job_progress_report_id=jpr.job_progress_report_id
            LEFT JOIN ki_payment_term pt ON pt.payment_term_id = jpr.payment_term_id
            LEFT JOIN ki_company cp ON cp.company_id = a.company_id
            LEFT JOIN ki_approval_assign app ON app.approval_assign_id = a.approval_assign_id
            LEFT JOIN ki_tax_type tt ON tt.tax_type_id = a.tax_type_id
			LEFT JOIN ki_tax_type tts ON tts.tax_type_id = a.tax_type_ppn_id
            LEFT JOIN ki_bank_transaction_type btt ON btt.bank_transaction_type_id = a.bank_transaction_type_id
            LEFT JOIN ki_bank_transaction_detail btd ON btd.sales_order_invoice_id = a.sales_order_invoice_id
            LEFT JOIN ki_bank_transaction bt ON bt.bank_transaction_id = btd.bank_transaction_id
            LEFT JOIN ki_customer_receive_detail crd ON crd.sales_order_invoice_id = a.sales_order_invoice_id
            LEFT JOIN ki_customer_receive cr ON cr.customer_receive_id = crd.customer_receive_id
            LEFT JOIN ki_user u1 ON u1.user_id = a.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = a.modified_by
            LEFT JOIN ki_employee e1 On e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 On e2.employee_id = u2.employee_id
			WHERE YEAR(a.due_date) > 2016
            ORDER BY ".$sortBy.", sales_order_invoice_id DESC";
		} 
		else 
		{
			$sql = "SELECT a.sales_order_invoice_id, a.sales_order_invoice_number, 
						   a.sales_order_invoice_description, 
						   jo.job_order_number AS job_order_id, 
						   sq.sales_quotation_number AS sales_quotation_id,
						   a.job_progress_report_id AS wcc_id,	
						   jpr.job_progress_report_number AS job_progress_report_id, 
						   a.due_date, a.client_po_number, a.sales_order_invoice_status, 
						   pt.days_before_due AS payment_late, 
						   (a.service_amount + a.service_ppn) AS grand_total, 
						   cp.company_name, app.fullname AS approval_assign, 
						   btt.category, btt.bank_transaction_type_name, 
						   a.tax_number, a.number_pieces_of_evidence, 
						   IFNULL(a.date_pieces_of_evidence, '') AS date_pieces_of_evidence, 
						   IFNULL(a.payment_date, '') AS payment_date, 
						   a.invoice_date, a.customer_receive_date, 
						   IFNULL(a.material_description, 'No Material Description') AS material_description, 
						   a.amount, a.ppn, a.discount, a.service_description, a.service_amount, 
						   a.service_discount, a.service_ppn, tt.tax_type_name AS tax_type, 
						   tt.tax_type_rate, IFNULL(a.adjustment_desc, '') AS adjustment_desc, 
						   a.adjustment_value, IFNULL(bt.bank_transaction_number, '') AS bank_transaction_number, 
						   IFNULL(cr.customer_receive_number, '') AS customer_receive_number, a.notes, 
						   e1.fullname AS created_by, a.created_date, IFNULL(e2.fullname, '') AS modified_by, 
						   IFNULL(a.modified_date, '') AS modified_date, 
						   IFNULL(a.material_income_type_id, 'No Type Material Income') AS material_income_type, 
						   IFNULL(a.service_income_type_id, 'No Type Service Income') AS service_income_type,
						   tts.tax_type_rate AS tax_type_ppn, a.tax_type_id
            FROM ki_sales_order_invoice a
            LEFT JOIN ki_job_order jo ON a.job_order_id=jo.job_order_id
            LEFT JOIN ki_sales_quotation sq ON a.sales_quotation_id=sq.sales_quotation_id
            LEFT JOIN ki_job_progress_report jpr ON a.job_progress_report_id=jpr.job_progress_report_id
            LEFT JOIN ki_payment_term pt ON pt.payment_term_id = jpr.payment_term_id
            LEFT JOIN ki_company cp ON cp.company_id = a.company_id
            LEFT JOIN ki_approval_assign app ON app.approval_assign_id = a.approval_assign_id
            LEFT JOIN ki_tax_type tt ON tt.tax_type_id = a.tax_type_id
			LEFT JOIN ki_tax_type tts ON tts.tax_type_id = a.tax_type_ppn_id
            LEFT JOIN ki_bank_transaction_type btt ON btt.bank_transaction_type_id = a.bank_transaction_type_id
            LEFT JOIN ki_bank_transaction_detail btd ON btd.sales_order_invoice_id = a.sales_order_invoice_id
            LEFT JOIN ki_bank_transaction bt ON bt.bank_transaction_id = btd.bank_transaction_id
            LEFT JOIN ki_customer_receive_detail crd ON crd.sales_order_invoice_id = a.sales_order_invoice_id
            LEFT JOIN ki_customer_receive cr ON cr.customer_receive_id = crd.customer_receive_id
            LEFT JOIN ki_user u1 ON u1.user_id = a.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = a.modified_by
            LEFT JOIN ki_employee e1 On e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 On e2.employee_id = u2.employee_id
			WHERE YEAR(a.due_date) > 2016
			ORDER BY ".$sortBy.", sales_order_invoice_id DESC
			LIMIT $counter, 15";
		}
    
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        $row = array();

        if($count>0){
            while($r = mysqli_fetch_assoc($qur)) {
				$hasil = 0;
				$amount = 0;$sub_total=0;$discount=0;$service_discount=0;$pph=0;
				$grand_totals=0;$grand_total=0;$vat=0;
				$total_final = 0;
				//total wcc
				$sql_wcc = "SELECT amount 
							FROM ki_job_progress_report_detail 
							WHERE job_progress_report_id = '".$r['wcc_id']."'";
				$qur_wcc = mysqli_query($conn, $sql_wcc);
				
				$count_wcc = mysqli_num_rows($qur_wcc);
				if($count_wcc>0)
				{	
					while($r_wcc = mysqli_fetch_assoc($qur_wcc)) 
					{
						$grand_total += $r_wcc['amount'];
					}
					
					$grand_totals = $grand_total;
				}
				else
				{
					$grand_totals = $r['amount'] + $r['service_amount'];
				}	
				$discount = $r['discount'];
				$service_discount = $r['service_discount'];
				$sub_total = $grand_totals - ($discount + $service_discount);
				$vat = $sub_total * ($r['tax_type_ppn']/ 100);
			
				if($r['tax_type_id'] == 8){
					$pph = $r['pph'];
				}else{		
					$pph = $sub_total * ($r['tax_type_rate'] / 100);
				}			
				
				$total_final = $sub_total + $vat - ($pph);
				
				$r['grand_total'] = $total_final;
				
                $row[] = $r;
            }
        }else{
            $row = array();
        }
		mysqli_close($conn);
		return $row;
	}

  	//list customer invoice
	function getCiDetWorkCompletion($conn, $ciId){
		$sql = "SELECT prd.description, prd.quantity, prd.unit_abbr, prd.amount
		FROM ki_job_progress_report_detail prd
		LEFT JOIN ki_job_progress_report pr ON pr.job_progress_report_id = prd.job_progress_report_id
		LEFT JOIN ki_sales_order_invoice soi ON soi.job_progress_report_id = pr.job_progress_report_id
		WHERE soi.sales_order_invoice_id = ".$ciId."";
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

	  //list bank transaction
	function getBankTransaction($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT bt.bank_transaction_id, bt.bank_transaction_number, IF(bt.checked_by!='',e1.fullname,'') AS checked_by, IF(bt.approval1!='',e2.fullname,'') AS approval1, IF(bt.approval1!='',e3.fullname,'') AS approval2, DATE_FORMAT(bt.transaction_date,'%d-%m-%Y') AS transaction_date, bt.total_amount, IF(bt.status='1','Debet','Kredit') AS status, IF(bt.reconciled='1','Ya','Tidak') AS reconciled, bt.bank_transaction_description, ba.bank_name, ba.bank_account_number, bt.transaction_number, IFNULL(e4.fullname, '') AS reconciled_by, IFNULL(bt.reconciled_date, '') AS reconciled_date, IFNULL(bt.checked_date, '') AS checked_date, IFNULL(bt.checked_comment, '') AS checked_comment, IFNULL(bt.approval_date1, '') AS approval_date1, IFNULL(bt.approval_comment1, '') AS approval_comment1, IFNULL(bt.approval_date2, '') AS approval_date2, IFNULL(bt.approval_comment2, '') AS approval_comment2, e5.fullname AS created_by, bt.created_date, e6.fullname AS modified_by, bt.modified_date, bt.notes, bt.bank_transaction_file_name
			FROM ki_bank_transaction bt
			LEFT JOIN ki_bank_account ba ON(ba.bank_account_id = bt.bank_account_id)
			LEFT JOIN ki_user u1 ON(u1.user_id = bt.checked_by)
			LEFT JOIN ki_user u2 ON(u2.user_id = bt.approval1)
			LEFT JOIN ki_user u3 ON(u3.user_id = bt.approval2)
            LEFT JOIN ki_user u4 ON(u4.user_id = bt.reconciled_by)
            LEFT JOIN ki_user u5 ON(u5.user_id = bt.created_by)
            LEFT JOIN ki_user u6 ON(u6.user_id = bt.modified_by)
			LEFT JOIN ki_employee e1 ON(u1.employee_id = e1.employee_id)
			LEFT JOIN ki_employee e2 ON(u2.employee_id = e2.employee_id)
			LEFT JOIN ki_employee e3 ON(u3.employee_id = e3.employee_id)
			LEFT JOIN ki_employee e4 ON(u4.employee_id = e4.employee_id)
			LEFT JOIN ki_employee e5 ON(u5.employee_id = e5.employee_id)
			LEFT JOIN ki_employee e6 ON(u6.employee_id = e6.employee_id)
			WHERE YEAR(transaction_date) > 2016
			ORDER BY ".$sortBy.", bt.transaction_date DESC";
		} else {
			$sql = "SELECT bt.bank_transaction_id, bt.bank_transaction_number, IF(bt.checked_by!='',e1.fullname,'') AS checked_by, IF(bt.approval1!='',e2.fullname,'') AS approval1, IF(bt.approval1!='',e3.fullname,'') AS approval2, DATE_FORMAT(bt.transaction_date,'%d-%m-%Y') AS transaction_date, bt.total_amount, IF(bt.status='1','Debet','Kredit') AS status, IF(bt.reconciled='1','Ya','Tidak') AS reconciled, bt.bank_transaction_description, ba.bank_name, ba.bank_account_number, bt.transaction_number, IFNULL(e4.fullname, '') AS reconciled_by, IFNULL(bt.reconciled_date, '') AS reconciled_date, IFNULL(bt.checked_date, '') AS checked_date, IFNULL(bt.checked_comment, '') AS checked_comment, IFNULL(bt.approval_date1, '') AS approval_date1, IFNULL(bt.approval_comment1, '') AS approval_comment1, IFNULL(bt.approval_date2, '') AS approval_date2, IFNULL(bt.approval_comment2, '') AS approval_comment2, e5.fullname AS created_by, bt.created_date, e6.fullname AS modified_by, bt.modified_date, bt.notes, bt.bank_transaction_file_name
			FROM ki_bank_transaction bt
			LEFT JOIN ki_bank_account ba ON(ba.bank_account_id = bt.bank_account_id)
			LEFT JOIN ki_user u1 ON(u1.user_id = bt.checked_by)
			LEFT JOIN ki_user u2 ON(u2.user_id = bt.approval1)
			LEFT JOIN ki_user u3 ON(u3.user_id = bt.approval2)
            LEFT JOIN ki_user u4 ON(u4.user_id = bt.reconciled_by)
            LEFT JOIN ki_user u5 ON(u5.user_id = bt.created_by)
            LEFT JOIN ki_user u6 ON(u6.user_id = bt.modified_by)
			LEFT JOIN ki_employee e1 ON(u1.employee_id = e1.employee_id)
			LEFT JOIN ki_employee e2 ON(u2.employee_id = e2.employee_id)
			LEFT JOIN ki_employee e3 ON(u3.employee_id = e3.employee_id)
			LEFT JOIN ki_employee e4 ON(u4.employee_id = e4.employee_id)
			LEFT JOIN ki_employee e5 ON(u5.employee_id = e5.employee_id)
			LEFT JOIN ki_employee e6 ON(u6.employee_id = e6.employee_id)
			WHERE YEAR(transaction_date) > 2016
			ORDER BY ".$sortBy.", bt.transaction_date DESC
			LIMIT $counter, 15";
		}
    
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        $row = array();

        if($count>0){
            while($r = mysqli_fetch_assoc($qur)) {
				$sql_btd = "SELECT supplier_invoice_id, cash_advance_id, sales_order_invoice_id,
								   responsbility_advance_id, adjustment_value, amount
				            FROM ki_bank_transaction_detail
							WHERE bank_transaction_id = '".$r['bank_transaction_id']."'";
				$Grand_Total = 0; 
				$Tadjustment_value=0;
				$qur_btd = mysqli_query($conn, $sql_btd);
				while($r_btd = mysqli_fetch_assoc($qur_btd)) 
				{
					$supplier_invoice_id = $r_btd['supplier_invoice_id'];
					$cash_advance_id = $r_btd['cash_advance_id'];
					$sales_order_invoice_id = $r_btd['sales_order_invoice_id'];
					$responsbility_advance_id = $r_btd['responsbility_advance_id'];
					if ($supplier_invoice_id != null)
					{ 
						$sql_si = "SELECT supplier_invoice_id, adjustment_value
								   FROM ki_supplier_invoice
								   WHERE supplier_invoice_id = '".$supplier_invoice_id."'";
						$qur_si = mysqli_query($conn, $sql_si);
						$adjustment_value = 0;
						while($r_si = mysqli_fetch_assoc($qur_si))
						{
							$adjustment_value = $r_si['adjustment_value'];
						}	
						$amount = $r_btd['amount'];
						$sub_total = $amount + $adjustment_value;
					}
					else if ($sales_order_invoice_id != null)
					{ 
						$sql_soi = "SELECT a.sales_order_invoice_id, a.adjustment_value,
										   a.job_progress_report_id, a.amount, a.service_amount,
										   a.discount, a.service_amount, tt.tax_type_rate,
										   tts.tax_type_ppn, a.tax_type_id, a.pph
								   FROM ki_sales_order_invoice a
								   LEFT JOIN ki_tax_type tt ON tt.tax_type_id = a.tax_type_id
								   LEFT JOIN ki_tax_type tts ON tts.tax_type_id = a.tax_type_ppn_id
								   WHERE sales_order_invoice_id = '".$sales_order_invoice_id."'";
						$qur_soi = mysqli_query($conn, $sql_soi);
						$adjustment_value = 0;
						$wcc_id = 0;
						while($r_soi = mysqli_fetch_assoc($qur_soi))
						{
							$adjustment_value = $r_soi['adjustment_value'];
							$wcc_id = $r_soi['job_progress_report_id'];
						}
						
						$nilai_a = $r_btd['amount'];
						//total sales order invoice
						$hasil = 0;
						$amount = 0;$sub_total=0;$discount=0;$service_discount=0;$pph=0;
						$grand_totals=0;$grand_total=0;$vat=0;
						$total_final = 0;
						//total wcc
						$sql_wcc = "SELECT amount 
									FROM ki_job_progress_report_detail 
									WHERE job_progress_report_id = '".$wcc_id."'";
						$qur_wcc = mysqli_query($conn, $sql_wcc);
				
						$count_wcc = mysqli_num_rows($qur_wcc);
						if($count_wcc>0)
						{	
							while($r_wcc = mysqli_fetch_assoc($qur_wcc)) 
							{
								$grand_total += $r_wcc['amount'];
							}
					
							$grand_totals = $grand_total;
						}
						else
						{
							$grand_totals = $r_soi['amount'] + $r_soi['service_amount'];
						}	
						$discount = $r_soi['discount'];
						$service_discount = $r_soi['service_discount'];
						$sub_total = $grand_totals - ($discount + $service_discount);
						$vat = $sub_total * ($r_soi['tax_type_ppn']/ 100);
			
						if($r_soi['tax_type_id'] == 8){
							$pph = $r_soi['pph'];
						}else{		
							$pph = $sub_total * ($r['tax_type_rate'] / 100);
						}			
				
						$total_final = $sub_total + $vat - ($pph);
						
						$nilai_b = $total_final;
					
						if($nilai_a > $nilai_b){
							$amount = $nilai_b;
						}elseif($nilai_a == $nilai_b){
							$amount = $nilai_b;
						}else{
							$amount = $nilai_a;
						}
						$sub_total = $amount + $adjustment_value;
					}
					else
					{ 
						$adjustment_value = $r_btd['adjustment_value'];
						$amount = $r_btd['amount'];
						$sub_total = $amount + $adjustment_value;
					} 
					$Grand_Total += $sub_total;
					$Tadjustment_value += $adjustment_value;		
				}
				$r['total_amount'] = round($Grand_Total);
                $row[] = $r;
            }
        }else{
            $row = array();
        }
		mysqli_close($conn);
		return $row;
	}

	  //list bank transaction
	function getBankTransactionDetail($conn, $btId){
		$sql = "SELECT btd.bank_transaction_detail_id, IFNULL(si.supplier_invoice_number, '') AS supplier_invoice, IFNULL(ca.cash_advance_number, '') AS proposed_budget, IFNULL(soi.sales_order_invoice_number, '') AS customer_invoice, IFNULL(ra.responsbility_advance_number, '') AS cash_project_report, IFNULL(btd.transaction_detail_name, '') AS transaction_detail_name, btd.destination, btd.adjustment_value, btd.amount, btt.category, btt.bank_transaction_type_name
		FROM ki_bank_transaction_detail btd 
		LEFT JOIN ki_supplier_invoice si ON si.supplier_invoice_id = btd.supplier_invoice_id
		LEFT JOIN ki_cash_advance ca ON ca.cash_advance_id = btd.cash_advance_id
		LEFT JOIN ki_sales_order_invoice soi ON soi.sales_order_invoice_id = btd.sales_order_invoice_id
		LEFT JOIN ki_responsbility_advance ra ON ra.responsbility_advance_id = btd.responsbility_advance_id
		LEFT JOIN ki_bank_transaction_type btt ON btt.bank_transaction_type_id = btd.bank_transaction_type_id
		WHERE btd.bank_transaction_id = ".$btId."";
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

	//list expense
	function getExpense($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.expenses_id, t.expenses_number, t.expenses_desc, IF(t.checked_by!='',e1.fullname,'') AS checked_by, IF(t.approval1!='',e2.fullname,'') AS approval1, IF(t.approval2!='',e3.fullname,'') AS approval2, IF(t.advanced_id!='',ad.advanced_number,'') AS advanced_number, DATE_FORMAT(t.begin_date,'%d-%m-%Y') AS expenses_date, SUM(ED.amount) as total_amount, ba.bank_account_name, IF(t.done=1,'Ya','Tidak') AS done, t.notes, IFNULL(t.approval_date1, '') AS approval_date1, IFNULL(t.approval_comment1, '') AS approval_comment1, IFNULL(t.approval_date2, '') AS approval_date2, IFNULL(t.approval_comment2, '') AS approval_comment2, IFNULL(t.checked_date, '') AS checked_date, IFNULL(t.checked_comment, '') AS checked_comment, e4.fullname AS created_by, t.created_date, IFNULL(e5.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date, t.expenses_file_name
			FROM ki_expenses t
			INNER JOIN ki_expenses_detail ED ON(ED.expenses_id = t.expenses_id)
			LEFT JOIN ki_advanced ad ON(t.advanced_id = ad.advanced_id)
			LEFT JOIN ki_user u1 ON(u1.user_id = t.checked_by)
			LEFT JOIN ki_user u2 ON(u2.user_id = t.approval1)
			LEFT JOIN ki_user u3 ON(u3.user_id = t.approval2)
			LEFT JOIN ki_user u4 ON(u4.user_id = t.created_by)
			LEFT JOIN ki_user u5 ON(u5.user_id = t.modified_by)
			LEFT JOIN ki_employee e1 ON(e1.employee_id = u1.employee_id)
			LEFT JOIN ki_employee e2 ON(e2.employee_id = u2.employee_id)
			LEFT JOIN ki_employee e3 ON(e3.employee_id = u3.employee_id)
			LEFT JOIN ki_employee e4 ON(e4.employee_id = u4.employee_id)
			LEFT JOIN ki_employee e5 ON(e5.employee_id = u5.employee_id)
			LEFT JOIN ki_bank_account ba ON(ba.bank_account_id = t.bank_account_id)
			WHERE YEAR(t.created_date) > 2016
			GROUP BY t.expenses_id
			ORDER BY ".$sortBy.", begin_date DESC";
		} else {
			$sql = "SELECT t.expenses_id, t.expenses_number, t.expenses_desc, IF(t.checked_by!='',e1.fullname,'') AS checked_by, IF(t.approval1!='',e2.fullname,'') AS approval1, IF(t.approval2!='',e3.fullname,'') AS approval2, IF(t.advanced_id!='',ad.advanced_number,'') AS advanced_number, DATE_FORMAT(t.begin_date,'%d-%m-%Y') AS expenses_date, SUM(ED.amount) as total_amount, ba.bank_account_name, IF(t.done=1,'Ya','Tidak') AS done, t.notes, IFNULL(t.approval_date1, '') AS approval_date1, IFNULL(t.approval_comment1, '') AS approval_comment1, IFNULL(t.approval_date2, '') AS approval_date2, IFNULL(t.approval_comment2, '') AS approval_comment2, IFNULL(t.checked_date, '') AS checked_date, IFNULL(t.checked_comment, '') AS checked_comment, e4.fullname AS created_by, t.created_date, IFNULL(e5.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date, t.expenses_file_name
			FROM ki_expenses t
			INNER JOIN ki_expenses_detail ED ON(ED.expenses_id = t.expenses_id)
			LEFT JOIN ki_advanced ad ON(t.advanced_id = ad.advanced_id)
			LEFT JOIN ki_user u1 ON(u1.user_id = t.checked_by)
			LEFT JOIN ki_user u2 ON(u2.user_id = t.approval1)
			LEFT JOIN ki_user u3 ON(u3.user_id = t.approval2)
			LEFT JOIN ki_user u4 ON(u4.user_id = t.created_by)
			LEFT JOIN ki_user u5 ON(u5.user_id = t.modified_by)
			LEFT JOIN ki_employee e1 ON(e1.employee_id = u1.employee_id)
			LEFT JOIN ki_employee e2 ON(e2.employee_id = u2.employee_id)
			LEFT JOIN ki_employee e3 ON(e3.employee_id = u3.employee_id)
			LEFT JOIN ki_employee e4 ON(e4.employee_id = u4.employee_id)
			LEFT JOIN ki_employee e5 ON(e5.employee_id = u5.employee_id)
			LEFT JOIN ki_bank_account ba ON(ba.bank_account_id = t.bank_account_id)
			WHERE YEAR(t.created_date) > 2016
			GROUP BY t.expenses_id
			ORDER BY ".$sortBy.", begin_date DESC
			LIMIT $counter, 15";
		}
    
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

	//list expense Detail
	function getExpenseDetail($conn, $expensesId){
		$sql = "SELECT ed.expenses_detail_id, ed.item_name, jo.job_order_number, bty.status, bty.bank_transaction_type_name, bty.category, ed.amount, IF(ed.checked_app!='', ed.checked_app, '-') AS checked_app, IF(ed.expenses_app1!='', ed.expenses_app1, '-') AS expenses_app1, IF(ed.expenses_app2!='', ed.expenses_app2, '-') AS expenses_app2
		FROM ki_expenses_detail ed
		LEFT JOIN ki_job_order jo ON jo.job_order_id = ed.job_order_id
		LEFT JOIN ki_bank_transaction_type bty ON bty.bank_transaction_type_id = ed.bank_transaction_type_id
		WHERE ed.expenses_id = ".$expensesId."";
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
	
	//list cash advance
	function getCashAdvance($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.advanced_id, t.advanced_number, DATE_FORMAT(t.advanced_date,'%d-%m-%Y') AS advanced_date, t.advanced_for, t.received_by, t.amount, t.status, IFNULL(t.approved_status, '') AS approved_status, IFNULL(e1.fullname, '') AS approved_by, IFNULL(t.approved_date, '') AS approved_date, IFNULL(t.approved_comment, '') AS approved_comment, IFNULL(e2.fullname, '') AS reconciled_by, IFNULL(t.reconciled_date, '') AS reconciled_date, t.notes, IFNULL(e3.fullname, '') AS created_by, t.created_date, IFNULL(e4.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
			FROM ki_advanced t
            LEFT JOIN ki_user u1 ON u1.user_id=t.approved_by
            LEFT JOIN ki_user u2 ON u2.user_id=t.reconciled_by
            LEFT JOIN ki_user u3 ON u3.user_id=t.created_by
            LEFT JOIN ki_user u4 ON u4.user_id=t.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
            LEFT JOIN ki_employee e3 ON e3.employee_id = u3.employee_id
            LEFT JOIN ki_employee e4 ON e4.employee_id = u4.employee_id
			ORDER BY ".$sortBy.", advanced_id DESC";
		} else {
			$sql = "SELECT t.advanced_id, t.advanced_number, DATE_FORMAT(t.advanced_date,'%d-%m-%Y') AS advanced_date, t.advanced_for, t.received_by, t.amount, t.status, IFNULL(t.approved_status, '') AS approved_status, IFNULL(e1.fullname, '') AS approved_by, IFNULL(t.approved_date, '') AS approved_date, IFNULL(t.approved_comment, '') AS approved_comment, IFNULL(e2.fullname, '') AS reconciled_by, IFNULL(t.reconciled_date, '') AS reconciled_date, t.notes, IFNULL(e3.fullname, '') AS created_by, t.created_date, IFNULL(e4.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
			FROM ki_advanced t
            LEFT JOIN ki_user u1 ON u1.user_id=t.approved_by
            LEFT JOIN ki_user u2 ON u2.user_id=t.reconciled_by
            LEFT JOIN ki_user u3 ON u3.user_id=t.created_by
            LEFT JOIN ki_user u4 ON u4.user_id=t.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
            LEFT JOIN ki_employee e3 ON e3.employee_id = u3.employee_id
            LEFT JOIN ki_employee e4 ON e4.employee_id = u4.employee_id
			ORDER BY ".$sortBy.", advanced_id DESC
			LIMIT $counter, 15";
		}
    
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

	//list budgeting 
	function getBudgeting($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.budget_id, t.budget_number, IF(t.created_by!='',e1.fullname,'') AS created_by, DATE_FORMAT(t.start_date,'%d-%m-%Y') AS start_date, DATE_FORMAT(t.end_date,'%d-%m-%Y') AS end_date, IF(t.checked_by!='',e2.fullname,'') AS checked_by, IF(t.approval1!='',e3.fullname,'') AS approval1, IF(t.approval2!='',e4.fullname,'') AS approval2, IF(t.approval3!='',e5.fullname,'') AS approval3, IF(t.done=1,'Ya','Tidak') AS done, IFNULL(t.checked_date, '') AS checked_date, IFNULL(t.approval_date1, '') AS approval_date1, t.approval_comment1, IFNULL(t.approval_date2, '') AS approval_date2, t.approval_comment2, IFNULL(t.approval_date3, '') AS approval_date3, t.approval_comment3, t.notes, IFNULL(e6.fullname, '') AS created_by, t.created_date, IFNULL(e7.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
			FROM ki_budgeting t
			LEFT JOIN ki_user u1 ON(u1.user_id = t.created_by)
			LEFT JOIN ki_user u2 ON(u2.user_id = t.checked_by)
			LEFT JOIN ki_user u3 ON(u3.user_id = t.approval1)
			LEFT JOIN ki_user u4 ON(u4.user_id = t.approval2)
			LEFT JOIN ki_user u5 ON(u5.user_id = t.approval3)
			LEFT JOIN ki_user u6 ON(u6.user_id = t.created_by)
			LEFT JOIN ki_user u7 ON(u7.user_id = t.modified_by)
			LEFT JOIN ki_employee e1 ON(u1.employee_id = e1.employee_id)
			LEFT JOIN ki_employee e2 ON(u2.employee_id = e2.employee_id)
			LEFT JOIN ki_employee e3 ON(u3.employee_id = e3.employee_id)
			LEFT JOIN ki_employee e4 ON(u4.employee_id = e4.employee_id)
			LEFT JOIN ki_employee e5 ON(u5.employee_id = e5.employee_id)
			LEFT JOIN ki_employee e6 ON(u6.employee_id = e6.employee_id)
			LEFT JOIN ki_employee e7 ON(u7.employee_id = e7.employee_id)
			WHERE YEAR(t.created_date) > 2016
			ORDER BY ".$sortBy.", budget_id DESC";
		} else {
			$sql = "SELECT t.budget_id, t.budget_number, IF(t.created_by!='',e1.fullname,'') AS created_by, DATE_FORMAT(t.start_date,'%d-%m-%Y') AS start_date, DATE_FORMAT(t.end_date,'%d-%m-%Y') AS end_date, IF(t.checked_by!='',e2.fullname,'') AS checked_by, IF(t.approval1!='',e3.fullname,'') AS approval1, IF(t.approval2!='',e4.fullname,'') AS approval2, IF(t.approval3!='',e5.fullname,'') AS approval3, IF(t.done=1,'Ya','Tidak') AS done, IFNULL(t.checked_date, '') AS checked_date, IFNULL(t.approval_date1, '') AS approval_date1, t.approval_comment1, IFNULL(t.approval_date2, '') AS approval_date2, t.approval_comment2, IFNULL(t.approval_date3, '') AS approval_date3, t.approval_comment3, t.notes, IFNULL(e6.fullname, '') AS created_by, t.created_date, IFNULL(e7.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
			FROM ki_budgeting t
			LEFT JOIN ki_user u1 ON(u1.user_id = t.created_by)
			LEFT JOIN ki_user u2 ON(u2.user_id = t.checked_by)
			LEFT JOIN ki_user u3 ON(u3.user_id = t.approval1)
			LEFT JOIN ki_user u4 ON(u4.user_id = t.approval2)
			LEFT JOIN ki_user u5 ON(u5.user_id = t.approval3)
			LEFT JOIN ki_user u6 ON(u6.user_id = t.created_by)
			LEFT JOIN ki_user u7 ON(u7.user_id = t.modified_by)
			LEFT JOIN ki_employee e1 ON(u1.employee_id = e1.employee_id)
			LEFT JOIN ki_employee e2 ON(u2.employee_id = e2.employee_id)
			LEFT JOIN ki_employee e3 ON(u3.employee_id = e3.employee_id)
			LEFT JOIN ki_employee e4 ON(u4.employee_id = e4.employee_id)
			LEFT JOIN ki_employee e5 ON(u5.employee_id = e5.employee_id)
			LEFT JOIN ki_employee e6 ON(u6.employee_id = e6.employee_id)
			LEFT JOIN ki_employee e7 ON(u7.employee_id = e7.employee_id)
			WHERE YEAR(t.created_date) > 2016
			ORDER BY ".$sortBy.", budget_id DESC
			LIMIT $counter, 15";
		}
    
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

	//list budgeting detail
	function getBudgetingDetail($conn, $budgetId){
		$sql = "SELECT bd.budget_detail_id, bd.description, dp1.department_name AS department_id, cw.company_workbase_name AS company_workbase_id, dp2.department_name AS to_department, bd.amount, IF(bd.budget_app1!='', bd.budget_app1, '-') AS budget_app1, IF(bd.budget_app2!='', bd.budget_app2, '-') AS budget_app2, IF(bd.budget_app3!='', bd.budget_app3, '-') AS budget_app3
		FROM ki_budgeting_detail bd
		LEFT JOIN ki_department dp1 ON dp1.department_id = bd.department_id
		LEFT JOIN ki_department dp2 ON dp2.department_id = bd.to_department
		LEFT JOIN ki_company_workbase cw ON cw.company_workbase_id = bd.company_workbase_id
		WHERE bd.budget_id = ".$budgetId."";
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
  
	//list payment supplier
	function getPaymentSupplier($conn, $counter, $sortBy){
		if ($counter<0) 
		{
			$sql = "SELECT t.budget_supplier_id, t.budget_supplier_number, DATE_FORMAT(t.start_date,'%d-%m-%Y') AS start_date, DATE_FORMAT(t.end_date,'%d-%m-%Y') AS end_date, IF(t.checked_by!='',e1.fullname,'') AS checked_by, IF(t.approval1!='',e2.fullname,'') AS approval1, IF(t.approval2!='',e3.fullname,'') AS approval2, IF(t.approval3!='',e4.fullname,'') AS approval3, IF(t.done=1,'Ya','Tidak') AS done, DATE_FORMAT(t.checked_date,'%d-%m-%Y') AS checked_date, DATE_FORMAT(t.approval_date1,'%d-%m-%Y') AS approval_date1, IFNULL(t.approval_comment1, '') AS approval_comment1, DATE_FORMAT(t.approval_date2,'%d-%m-%Y') AS approval_date2, IFNULL(t.approval_comment2, '') AS approval_comment2, DATE_FORMAT(t.approval_date3,'%d-%m-%Y') AS approval_date3, IFNULL(t.approval_comment3, '') AS approval_comment3, t.notes, IFNULL(e5.fullname, '') AS created_by, IFNULL(t.created_date, '') AS created_date, IFNULL(e6.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
			FROM ki_budgeting_supplier t
			LEFT JOIN ki_user u1 ON(u1.user_id = t.checked_by)
			LEFT JOIN ki_user u2 ON(u2.user_id = t.approval1)
			LEFT JOIN ki_user u3 ON(u3.user_id = t.approval2)
			LEFT JOIN ki_user u4 ON(u4.user_id = t.approval3)
			LEFT JOIN ki_user u5 ON(u5.user_id = t.created_by)
			LEFT JOIN ki_user u6 ON(u6.user_id = t.modified_by)
			LEFT JOIN ki_employee e1 ON(e1.employee_id = u1.employee_id)
			LEFT JOIN ki_employee e2 ON(e2.employee_id = u2.employee_id)
			LEFT JOIN ki_employee e3 ON(e3.employee_id = u3.employee_id)
			LEFT JOIN ki_employee e4 ON(e4.employee_id = u4.employee_id)
			LEFT JOIN ki_employee e5 ON(e5.employee_id = u5.employee_id)
			LEFT JOIN ki_employee e6 ON(e6.employee_id = u6.employee_id)
			WHERE YEAR(t.created_date) > 2016
			ORDER BY ".$sortBy.", budget_supplier_id DESC";
		} 
		else 
		{
			$sql = "SELECT t.budget_supplier_id, t.budget_supplier_number, DATE_FORMAT(t.start_date,'%d-%m-%Y') AS start_date, DATE_FORMAT(t.end_date,'%d-%m-%Y') AS end_date, IF(t.checked_by!='',e1.fullname,'') AS checked_by, IF(t.approval1!='',e2.fullname,'') AS approval1, IF(t.approval2!='',e3.fullname,'') AS approval2, IF(t.approval3!='',e4.fullname,'') AS approval3, IF(t.done=1,'Ya','Tidak') AS done, DATE_FORMAT(t.checked_date,'%d-%m-%Y') AS checked_date, DATE_FORMAT(t.approval_date1,'%d-%m-%Y') AS approval_date1, IFNULL(t.approval_comment1, '') AS approval_comment1, DATE_FORMAT(t.approval_date2,'%d-%m-%Y') AS approval_date2, IFNULL(t.approval_comment2, '') AS approval_comment2, DATE_FORMAT(t.approval_date3,'%d-%m-%Y') AS approval_date3, IFNULL(t.approval_comment3, '') AS approval_comment3, t.notes, IFNULL(e5.fullname, '') AS created_by, IFNULL(t.created_date, '') AS created_date, IFNULL(e6.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
			FROM ki_budgeting_supplier t
			LEFT JOIN ki_user u1 ON(u1.user_id = t.checked_by)
			LEFT JOIN ki_user u2 ON(u2.user_id = t.approval1)
			LEFT JOIN ki_user u3 ON(u3.user_id = t.approval2)
			LEFT JOIN ki_user u4 ON(u4.user_id = t.approval3)
			LEFT JOIN ki_user u5 ON(u5.user_id = t.created_by)
			LEFT JOIN ki_user u6 ON(u6.user_id = t.modified_by)
			LEFT JOIN ki_employee e1 ON(e1.employee_id = u1.employee_id)
			LEFT JOIN ki_employee e2 ON(e2.employee_id = u2.employee_id)
			LEFT JOIN ki_employee e3 ON(e3.employee_id = u3.employee_id)
			LEFT JOIN ki_employee e4 ON(e4.employee_id = u4.employee_id)
			LEFT JOIN ki_employee e5 ON(e5.employee_id = u5.employee_id)
			LEFT JOIN ki_employee e6 ON(e6.employee_id = u6.employee_id)
			WHERE YEAR(t.created_date) > 2016
			ORDER BY ".$sortBy.", budget_supplier_id DESC
			LIMIT $counter, 15";
		}
    
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

	//list budgeting detail
	function getPaymentSupplierDetail($conn, $psId){
		$sql = "SELECT bsd.budget_supplier_detail_id, sp.supplier_name AS supplier_id, bsd.bank_account, bsd.bank_name, bsd.name_of_bank_account, bsd.amount, IF(bsd.budget_supplier_app1!='', bsd.budget_supplier_app1, '-') AS budget_supplier_app1, IF(bsd.budget_supplier_app2!='', bsd.budget_supplier_app2, '-') AS budget_supplier_app2, IF(bsd.budget_supplier_app3!='', bsd.budget_supplier_app3, '-') AS budget_supplier_app3
		FROM ki_budgeting_supplier_detail bsd
		LEFT JOIN ki_supplier sp ON sp.supplier_id = bsd.supplier_id
		WHERE bsd.budget_supplier_id = ".$psId."";
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
 
	//list bank account
	function getBankAccount($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.bank_account_id, t.bank_account_name, t.bank_account_number, t.bank_name, c.currency_code, t.ending_reconcile_balance, DATE_FORMAT(t.last_reconciled_date,'%d-%m-%Y') AS last_reconciled_date, IF(t.is_active=1,'Ya','Tidak') AS is_active
			FROM ki_bank_account t
			LEFT JOIN ki_currency c ON(c.currency_id = t.currency_id)
			ORDER BY ".$sortBy.", bank_account_id ASC";
		} else {
			$sql = "SELECT t.bank_account_id, t.bank_account_name, t.bank_account_number, t.bank_name, c.currency_code, t.ending_reconcile_balance, DATE_FORMAT(t.last_reconciled_date,'%d-%m-%Y') AS last_reconciled_date, IF(t.is_active=1,'Ya','Tidak') AS is_active
			FROM ki_bank_account t
			LEFT JOIN ki_currency c ON(c.currency_id = t.currency_id)
			ORDER BY ".$sortBy.", bank_account_id ASC
			LIMIT $counter, 15";
		}
    
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
  
	//list daftar akun
	function getDaftarAkun($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.account_id, coat.chart_of_account_type_name, t.chart_of_account_group_code, t.account, t.account_code, t.is_group, IF(t.is_active=1,'Ya','Tidak') AS is_active
			FROM ki_chart_of_account t
			LEFT JOIN ki_chart_of_account_type coat ON(t.chart_of_account_type_id=coat.chart_of_account_type_id)
			ORDER BY ".$sortBy.", account_code ASC";
		} else {
			$sql = "SELECT t.account_id, coat.chart_of_account_type_name, t.chart_of_account_group_code, t.account, t.account_code, t.is_group, IF(t.is_active=1,'Ya','Tidak') AS is_active
			FROM ki_chart_of_account t
			LEFT JOIN ki_chart_of_account_type coat ON(t.chart_of_account_type_id=coat.chart_of_account_type_id)
			ORDER BY ".$sortBy.", account_code ASC
			LIMIT $counter, 15";
		}
    
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
  
	//list 
	function getEkspedisi($conn){
		$sql = "SELECT ek.ekspedisi_code, ek.ekspedisi_name
			FROM ki_ekspedisi ek
			ORDER BY ek.ekspedisi_name ASC";
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
  
	//list Customer Receives
	function getCustomerReceives($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT cr.customer_receive_id, cr.customer_receive_number, cr.customer_receive_date, cr.receipt_number, ek.ekspedisi_name AS ekspedisi_id, cp.company_name, IFNULL(cr.delivery_date, '') AS delivery_date, e1.fullname AS created_by, cr.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(cr.modified_date, '') AS modified_date
			FROM ki_customer_receive cr
			LEFT JOIN ki_company cp ON cp.company_id = cr.company_id
            LEFT JOIN ki_ekspedisi ek ON ek.ekspedisi_id = cr.ekspedisi_id
            LEFT JOIN ki_user u1 ON u1.user_id = cr.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = cr.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", cr.customer_receive_id DESC";
		} else {
			$sql = "SELECT cr.customer_receive_id, cr.customer_receive_number, cr.customer_receive_date, cr.receipt_number, ek.ekspedisi_name AS ekspedisi_id, cp.company_name, IFNULL(cr.delivery_date, '') AS delivery_date, e1.fullname AS created_by, cr.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(cr.modified_date, '') AS modified_date
			FROM ki_customer_receive cr
			LEFT JOIN ki_company cp ON cp.company_id = cr.company_id
            LEFT JOIN ki_ekspedisi ek ON ek.ekspedisi_id = cr.ekspedisi_id
            LEFT JOIN ki_user u1 ON u1.user_id = cr.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = cr.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", cr.customer_receive_id DESC
			LIMIT $counter, 15";
		}
    
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
  
	//list Customer Receives
	function getCustomerReceivesDetail($conn, $crId){
		$sql = "SELECT soi.sales_order_invoice_number, soi.sales_order_invoice_description, soi.amount
		FROM ki_customer_receive_detail crd 
		LEFT JOIN ki_sales_order_invoice soi ON soi.sales_order_invoice_id = crd.sales_order_invoice_id
		WHERE crd.customer_receive_id = ".$crId."";
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
  
	//list Sales Quotation
	function getSalesQuotation($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT sq.sales_quotation_id, sq.sales_quotation_number, co.company_name, e1.fullname AS created_by, DATE_FORMAT(sq.sales_quotation_date,'%d-%m-%Y') AS sq_date, d.department_name, sq.description, sq.amount, sq.wo_amount, sq.status, IFNULL(sq.completion_date, '') AS completion_date, sq.client_po_number, sq.sales_quotation_category, sq.payment_desc, e3.fullname AS supervisor, IFNULL(app.fullname, '') AS approval_assign_id, c.contact_name AS contact_id, sq.notes, sq.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(sq.modified_date, '') AS modified_date, jo.job_order_number, jo.job_order_description, jo.begin_date, jo.end_date
          	FROM ki_sales_quotation sq
          	LEFT JOIN ki_contact c ON(sq.contact_id = c.contact_id)
	        LEFT JOIN ki_company co ON(co.company_id = c.company_id)
	        LEFT JOIN ki_user u1 ON(u1.user_id = sq.created_by)
	        LEFT JOIN ki_user u2 ON(u2.user_id = sq.modified_by)
	        LEFT JOIN ki_employee e1 ON(e1.employee_id = u1.employee_id) 
	        LEFT JOIN ki_employee e2 ON(e2.employee_id = u2.employee_id) 
	        LEFT JOIN ki_department d ON(d.department_id = sq.department_id)
            LEFT JOIN ki_employee e3 ON e3.employee_id = sq.supervisor
            LEFT JOIN ki_approval_assign app ON app.approval_assign_id = sq.approval_assign_id
            LEFT JOIN ki_job_order jo ON jo.job_order_id = sq.job_order_id
	        ORDER BY ".$sortBy.", sales_quotation_id DESC";
		} else {
			$sql = "SELECT sq.sales_quotation_id, sq.sales_quotation_number, co.company_name, e1.fullname AS created_by, DATE_FORMAT(sq.sales_quotation_date,'%d-%m-%Y') AS sq_date, d.department_name, sq.description, sq.amount, sq.wo_amount, sq.status, IFNULL(sq.completion_date, '') AS completion_date, sq.client_po_number, sq.sales_quotation_category, sq.payment_desc, e3.fullname AS supervisor, IFNULL(app.fullname, '') AS approval_assign_id, c.contact_name AS contact_id, sq.notes, sq.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(sq.modified_date, '') AS modified_date, jo.job_order_number, jo.job_order_description, jo.begin_date, jo.end_date
          	FROM ki_sales_quotation sq
          	LEFT JOIN ki_contact c ON(sq.contact_id = c.contact_id)
	        LEFT JOIN ki_company co ON(co.company_id = c.company_id)
	        LEFT JOIN ki_user u1 ON(u1.user_id = sq.created_by)
	        LEFT JOIN ki_user u2 ON(u2.user_id = sq.modified_by)
	        LEFT JOIN ki_employee e1 ON(e1.employee_id = u1.employee_id) 
	        LEFT JOIN ki_employee e2 ON(e2.employee_id = u2.employee_id) 
	        LEFT JOIN ki_department d ON(d.department_id = sq.department_id)
            LEFT JOIN ki_employee e3 ON e3.employee_id = sq.supervisor
            LEFT JOIN ki_approval_assign app ON app.approval_assign_id = sq.approval_assign_id
            LEFT JOIN ki_job_order jo ON jo.job_order_id = sq.job_order_id
	        ORDER BY ".$sortBy.", sales_quotation_id DESC
			LIMIT $counter, 15";
		}
    
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
  
	//list sales order
	function getSalesOrder($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT so.sales_order_id, so.sales_order_number, so.short_description, DATE_FORMAT(so.sales_order_date,'%d-%m-%Y') AS sales_order_date, DATE_FORMAT(so.due_date,'%d-%m-%Y') AS due_date, sos.status, IFNULL(cpn.company_name, '') AS company_id
		    FROM ki_sales_order so
		    LEFT JOIN ki_sales_order_status sos ON(so.sales_order_status_id = sos.sales_order_status_id)
            LEFT JOIN ki_company cpn ON cpn.company_id = so.company_id
		    ORDER BY ".$sortBy.", sales_order_id DESC";
		} else {
			$sql = "SELECT so.sales_order_id, so.sales_order_number, so.short_description, DATE_FORMAT(so.sales_order_date,'%d-%m-%Y') AS sales_order_date, DATE_FORMAT(so.due_date,'%d-%m-%Y') AS due_date, sos.status, IFNULL(cpn.company_name, '') AS company_id
		    FROM ki_sales_order so
		    LEFT JOIN ki_sales_order_status sos ON(so.sales_order_status_id = sos.sales_order_status_id)
            LEFT JOIN ki_company cpn ON cpn.company_id = so.company_id
		    ORDER BY ".$sortBy.", sales_order_id DESC
			LIMIT $counter, 15";
		}
    
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
  
	//list work accident
	function getWorkAccident($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.work_accident_id, IFNULL(emp.fullname, '') AS employee_id, a.employee_name, a.day_accident, a.date_accident, a.time_accident, jg.job_grade_name AS job_grade_id, cw.company_workbase_name AS company_workbase_id, a.accident_type, IF(a.is_work_location=1, 'Ya', 'Tidak') AS is_work_location, IFNULL(a.witness, '') AS witness, a.notes, a.action, a.counter_meassure, e1.fullname AS created_by, a.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date, a.accident_photo_1, a.accident_photo_2, a.accident_photo_3
			FROM ki_work_accident a
			LEFT JOIN ki_employee emp ON a.employee_id = emp.employee_id
			LEFT JOIN ki_job_grade jg ON a.job_grade_id = jg.job_grade_id
			LEFT JOIN ki_company_workbase cw ON a.company_workbase_id = cw.company_workbase_id
            LEFT JOIN ki_user u1 ON u1.user_id = a.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = a.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", work_accident_id DESC";
		} else {
			$sql = "SELECT a.work_accident_id, IFNULL(emp.fullname, '') AS employee_id, a.employee_name, a.day_accident, a.date_accident, a.time_accident, jg.job_grade_name AS job_grade_id, cw.company_workbase_name AS company_workbase_id, a.accident_type, IF(a.is_work_location=1, 'Ya', 'Tidak') AS is_work_location, IFNULL(a.witness, '') AS witness, a.notes, a.action, a.counter_meassure, e1.fullname AS created_by, a.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date, a.accident_photo_1, a.accident_photo_2, a.accident_photo_3
			FROM ki_work_accident a
			LEFT JOIN ki_employee emp ON a.employee_id = emp.employee_id
			LEFT JOIN ki_job_grade jg ON a.job_grade_id = jg.job_grade_id
			LEFT JOIN ki_company_workbase cw ON a.company_workbase_id = cw.company_workbase_id
            LEFT JOIN ki_user u1 ON u1.user_id = a.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = a.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", work_accident_id DESC
			LIMIT $counter, 15";
		}
    
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
  
	//list Genba Safety
	function getGenbaSafety($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.genba_safety_id, a.genba_safety_number, a.genba_date, cw.company_workbase_name AS company_workbase_id, e1.fullname AS created_by, a.created_date, IFNULL(e2.fullname,'') AS modified_by, IFNULL(a.modified_date,'') AS modified_date, a.genba_time, a.notes, a.genba_photo1, a.genba_photo2, a.genba_photo3
			FROM ki_genba_safety a
			LEFT JOIN ki_company_workbase cw ON a.company_workbase_id = cw.company_workbase_id
			LEFT JOIN ki_user u1 ON u1.user_id = a.created_by
			LEFT JOIN ki_user u2 ON u2.user_id = a.modified_by
			LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
			LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", genba_safety_id DESC";
		} else {
			$sql = "SELECT a.genba_safety_id, a.genba_safety_number, a.genba_date, cw.company_workbase_name AS company_workbase_id, e1.fullname AS created_by, a.created_date, IFNULL(e2.fullname,'') AS modified_by, IFNULL(a.modified_date,'') AS modified_date, a.genba_time, a.notes, a.genba_photo1, a.genba_photo2, a.genba_photo3
			FROM ki_genba_safety a
			LEFT JOIN ki_company_workbase cw ON a.company_workbase_id = cw.company_workbase_id
			LEFT JOIN ki_user u1 ON u1.user_id = a.created_by
			LEFT JOIN ki_user u2 ON u2.user_id = a.modified_by
			LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
			LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", genba_safety_id DESC
			LIMIT $counter, 15";
		}
    
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

	function getGenbaSafetyDetail($conn, $gs_id){
		$sql = "SELECT a.genba_safety_detail_id, IFNULL(emp.fullname, '') AS employee_id, a.employee_name1, IFNULL(jg.job_grade_name, '') AS departemen, a.notes
		FROM ki_genba_safety_detail a
		LEFT JOIN ki_employee emp ON emp.employee_id = a.employee_id
		LEFT JOIN ki_job_grade jg ON jg.job_grade_id = emp.job_grade_id
		WHERE a.genba_safety_id = ".$gs_id."";
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
  
	//list Job Order Safety
	function getJobOrderSafety($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.job_order_safety_id, CONCAT(jo.job_order_number,' - ', jo.job_order_description) AS job_order_id, a.description, IF(a.category_id='1','JSA Report','Safety Report') AS category_id, a.job_safety_file_name
			FROM ki_job_order_safety a
			LEFT JOIN ki_job_order jo ON a.job_order_id = jo.job_order_id
			ORDER BY ".$sortBy.", job_order_safety_id ASC";
		} else {
			$sql = "SELECT a.job_order_safety_id, CONCAT(jo.job_order_number,' - ', jo.job_order_description) AS job_order_id, a.description, IF(a.category_id='1','JSA Report','Safety Report') AS category_id, a.job_safety_file_name
			FROM ki_job_order_safety a
			LEFT JOIN ki_job_order jo ON a.job_order_id = jo.job_order_id
			ORDER BY ".$sortBy.", job_order_safety_id ASC
			LIMIT $counter, 15";
		}
    
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

	//list Customer Feedbacks
	function getCustomerFeedback($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.feedback_id, a.feedback_number, a.feedback_date, a.feedback_subject, IFNULL(cpn.company_name, '') AS company_name, IFNULL(ct.contact_name, '') AS contact_id, a.contact_personal, ma.aspect_name AS marketing_aspect_id, fc.category_name AS feedback_category_id, a.feedback_status, dpr.department_name, fm.media_name, IFNULL(u1.user_displayname, 'n/a') AS closed_user_id, IFNULL(a.closed_time, 'n/a') AS closed_time, a.priority, a.feedback_description, u2.user_displayname, a.feedback_entry_time, a.analyze_description, a.answer_description, a.preventive_description
			FROM ki_customer_feedback a
			LEFT JOIN ki_contact ct ON a.contact_id = ct.contact_id
			LEFT JOIN ki_company cpn ON ct.company_id = cpn.company_id
			LEFT JOIN ki_marketing_aspect ma ON a.marketing_aspect_id = ma.marketing_aspect_id
			LEFT JOIN ki_feedback_category fc ON a.feedback_category_id = fc.feedback_category_id
            LEFT JOIN ki_department dpr ON a.feedback_destination_id = dpr.department_id
            LEFT JOIN ki_feedback_media fm ON a.feedback_media_id = fm.feedback_media_id
            LEFT JOIN ki_user u1 ON a.closed_user_id = u1.user_id
            LEFT JOIN ki_user u2 ON a.feedback_user_id = u2.user_id
			ORDER BY ".$sortBy.", feedback_id DESC";
		} else {
			$sql = "SELECT a.feedback_id, a.feedback_number, a.feedback_date, a.feedback_subject, IFNULL(cpn.company_name, '') AS company_name, IFNULL(ct.contact_name, '') AS contact_id, a.contact_personal, ma.aspect_name AS marketing_aspect_id, fc.category_name AS feedback_category_id, a.feedback_status, dpr.department_name, fm.media_name, IFNULL(u1.user_displayname, 'n/a') AS closed_user_id, IFNULL(a.closed_time, 'n/a') AS closed_time, a.priority, a.feedback_description, u2.user_displayname, a.feedback_entry_time, a.analyze_description, a.answer_description, a.preventive_description
			FROM ki_customer_feedback a
			LEFT JOIN ki_contact ct ON a.contact_id = ct.contact_id
			LEFT JOIN ki_company cpn ON ct.company_id = cpn.company_id
			LEFT JOIN ki_marketing_aspect ma ON a.marketing_aspect_id = ma.marketing_aspect_id
			LEFT JOIN ki_feedback_category fc ON a.feedback_category_id = fc.feedback_category_id
            LEFT JOIN ki_department dpr ON a.feedback_destination_id = dpr.department_id
            LEFT JOIN ki_feedback_media fm ON a.feedback_media_id = fm.feedback_media_id
            LEFT JOIN ki_user u1 ON a.closed_user_id = u1.user_id
            LEFT JOIN ki_user u2 ON a.feedback_user_id = u2.user_id
			ORDER BY ".$sortBy.", feedback_id DESC
			LIMIT $counter, 15";
		}
    
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

	//list Questions
	function getQuestions($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.question_id, a.question, a.question_year, a.type, IFNULL(e1.fullname, 'admin') AS created_by, a.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date
			FROM ki_question a 
            LEFT JOIN ki_user u1 ON u1.user_id = a.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = a.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", question_id ASC";
		} else {
			$sql = "SELECT a.question_id, a.question, a.question_year, a.type, IFNULL(e1.fullname, 'admin') AS created_by, a.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date
			FROM ki_question a 
            LEFT JOIN ki_user u1 ON u1.user_id = a.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = a.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", question_id ASC
			LIMIT $counter, 15";
		}
    
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
  
	//list kuesioner
	function getKuesioner($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.survey_id, a.survey_number, cpn.company_name AS company_id, a.contact_name, a.survey_date, a.survey_attachment, a.notes, IFNULL(e1.fullname, 'admin') AS created_by, a.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date
			FROM ki_survey a
			LEFT JOIN ki_company cpn ON a.company_id = cpn.company_id
            LEFT JOIN ki_user u1 ON u1.user_id = a.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = a.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", survey_id ASC";
		} else {
			$sql = "SELECT a.survey_id, a.survey_number, cpn.company_name AS company_id, a.contact_name, a.survey_date, a.survey_attachment, a.notes, IFNULL(e1.fullname, 'admin') AS created_by, a.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date
			FROM ki_survey a
			LEFT JOIN ki_company cpn ON a.company_id = cpn.company_id
            LEFT JOIN ki_user u1 ON u1.user_id = a.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = a.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", survey_id ASC
			LIMIT $counter, 15";
		}
    
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
  
	//list kuesioner
	function getKuesionerDetail($conn, $svId){
		$sql = "SELECT q.question, sd.value, sd.comment
		FROM ki_survey_detail sd
		LEFT JOIN ki_question q ON q.question_id = sd.question_id
		WHERE sd.survey_id =".$svId."
		ORDER BY q.question_id ASC";
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
  
	//list Lead
	function getLead($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.lead_id, a.lead_name, a.lead_phone, a.lead_email, IFNULL(a.person, '') AS person, IFNULL(a.position, '') AS position, IFNULL(a.personal_phone, '') AS personal_phone, a.status, a.lead_address, a.notes, IFNULL(e1.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date
			FROM ki_lead a
            LEFT JOIN ki_user u1 ON u1.user_id = a.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
			ORDER BY ".$sortBy.", lead_name ASC";
		} else {
			$sql = "SELECT a.lead_id, a.lead_name, a.lead_phone, a.lead_email, IFNULL(a.person, '') AS person, IFNULL(a.position, '') AS position, IFNULL(a.personal_phone, '') AS personal_phone, a.status, a.lead_address, a.notes, IFNULL(e1.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date
			FROM ki_lead a
            LEFT JOIN ki_user u1 ON u1.user_id = a.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
			ORDER BY ".$sortBy.", lead_name ASC
			LIMIT $counter, 15";
		}
    
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
  
	//list Followup
	function getFollowup($conn, $counter, $category, $sortBy){
		if ($category==0) { // category lead
			if ($counter<0) {
				$sql = "SELECT a.lead_followup_id, IFNULL(ld.lead_name, '') AS lead_id, a.company_name, a.notes, IFNULL(a.followup_date, '') AS followup_date, IFNULL(CASE WHEN a.followup_by = 1 THEN 'By Phone' WHEN a.followup_by = 2 THEN 'By Email' WHEN a.followup_by = 3 THEN 'By Other' END, '') AS followup_by, emp.fullname AS created_by, a.created_date, IFNULL(e1.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date, a.followup_file_name
				FROM ki_lead_followup a
				LEFT JOIN ki_lead ld ON a.lead_id = ld.lead_id
				LEFT JOIN ki_user us ON a.created_by = us.user_id
				LEFT JOIN ki_employee emp ON us.employee_id = emp.employee_id
	            LEFT JOIN ki_user u1 ON u1.user_id = a.modified_by
	            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
				WHERE company_name IS NULL
				ORDER BY ".$sortBy.", lead_followup_id DESC";
			} else {
				$sql = "SELECT a.lead_followup_id, IFNULL(ld.lead_name, '') AS lead_id, a.company_name, a.notes, IFNULL(a.followup_date, '') AS followup_date, IFNULL(CASE WHEN a.followup_by = 1 THEN 'By Phone' WHEN a.followup_by = 2 THEN 'By Email' WHEN a.followup_by = 3 THEN 'By Other' END, '') AS followup_by, emp.fullname AS created_by, a.created_date, IFNULL(e1.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date, a.followup_file_name
				FROM ki_lead_followup a
				LEFT JOIN ki_lead ld ON a.lead_id = ld.lead_id
				LEFT JOIN ki_user us ON a.created_by = us.user_id
				LEFT JOIN ki_employee emp ON us.employee_id = emp.employee_id
	            LEFT JOIN ki_user u1 ON u1.user_id = a.modified_by
	            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
				WHERE company_name IS NULL
				ORDER BY ".$sortBy.", lead_followup_id DESC
				LIMIT $counter, 15";
			}
		} else {
			if ($counter<0) {
				$sql = "SELECT a.lead_followup_id, ld.lead_name AS lead_id, a.company_name, a.notes, a.followup_date, CASE WHEN a.followup_by = 1 THEN 'By Phone' WHEN a.followup_by = 2 THEN 'By Email' WHEN a.followup_by = 3 THEN 'By Other' END AS followup_by, emp.fullname AS created_by, a.created_date, IFNULL(e1.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date, a.followup_file_name
				FROM ki_lead_followup a
				LEFT JOIN ki_lead ld ON a.lead_id = ld.lead_id
				LEFT JOIN ki_user us ON a.created_by = us.user_id
				LEFT JOIN ki_employee emp ON us.employee_id = emp.employee_id
	            LEFT JOIN ki_user u1 ON u1.user_id = a.modified_by
	            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
				WHERE lead_name IS NULL
				ORDER BY ".$sortBy.", lead_followup_id DESC";
			} else {
				$sql = "SELECT a.lead_followup_id, ld.lead_name AS lead_id, a.company_name, a.notes, a.followup_date, CASE WHEN a.followup_by = 1 THEN 'By Phone' WHEN a.followup_by = 2 THEN 'By Email' WHEN a.followup_by = 3 THEN 'By Other' END AS followup_by, emp.fullname AS created_by, a.created_date, IFNULL(e1.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date, a.followup_file_name
				FROM ki_lead_followup a
				LEFT JOIN ki_lead ld ON a.lead_id = ld.lead_id
				LEFT JOIN ki_user us ON a.created_by = us.user_id
				LEFT JOIN ki_employee emp ON us.employee_id = emp.employee_id
	            LEFT JOIN ki_user u1 ON u1.user_id = a.modified_by
	            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
				WHERE lead_name IS NULL
				ORDER BY ".$sortBy.", lead_followup_id DESC
				LIMIT $counter, 15";
			}
		}
		
    
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
  
	//list Event
	function getEvent($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.event_id, a.event_name, a.event_location, a.start_date, a.end_date, a.event_photo_1, a.event_photo_2, a.event_photo_3, a.event_photo_4
			FROM ki_event a
			ORDER BY ".$sortBy.", event_id DESC";
		} else {
			$sql = "SELECT a.event_id, a.event_name, a.event_location, a.start_date, a.end_date, a.event_photo_1, a.event_photo_2, a.event_photo_3, a.event_photo_4
			FROM ki_event a
			ORDER BY ".$sortBy.", event_id DESC
			LIMIT $counter, 15";
		}
    
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
  
	//list Schedule Visits
	function getScheduleVisits($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.schedule_visits_id, a.visits_number, IFNULL(a.visits_date, '') AS visits_date, IFNULL(l.lead_name, '') AS lead_id, IFNULL(cpn.company_name, '') AS company_id, IF(a.done=1, 'Ya', 'Tidak') AS done, a.propose, a.notes, e1.fullname AS created_by, a.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date
			FROM ki_schedule_visits a
			LEFT JOIN ki_lead l ON a.lead_id = l.lead_id
			LEFT JOIN ki_company cpn ON a.company_id = cpn.company_id
            LEFT JOIN ki_user u1 ON u1.user_id = a.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = a.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", schedule_visits_id DESC";
		} else {
			$sql = "SELECT a.schedule_visits_id, a.visits_number, IFNULL(a.visits_date, '') AS visits_date, IFNULL(l.lead_name, '') AS lead_id, IFNULL(cpn.company_name, '') AS company_id, IF(a.done=1, 'Ya', 'Tidak') AS done, a.propose, a.notes, e1.fullname AS created_by, a.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(a.modified_date, '') AS modified_date
			FROM ki_schedule_visits a
			LEFT JOIN ki_lead l ON a.lead_id = l.lead_id
			LEFT JOIN ki_company cpn ON a.company_id = cpn.company_id
            LEFT JOIN ki_user u1 ON u1.user_id = a.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = a.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", schedule_visits_id DESC
			LIMIT $counter, 15";
		}
    
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
  
	//list item
	function getItem($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.item_id, t.item_code, t.item_name, t.item_specification, t.current_stock, t.low_level_stock, t.reorder_level_stock, r.rack_code,  IF(t.is_stock=1,'Ya','Tidak') AS is_warehouse, IFNULL(t.item_section, '') AS item_section, IF(t.is_asset=1,'Ya','Tidak') AS is_asset, IF(t.is_active=1,'Ya','Tidak') AS is_active, IFNULL(t.brand_name,'') AS brand_name, IFNULL(t.serial_number,'') AS serial_number, IFNULL(t.model_number,'') AS model_number, IFNULL(it.item_type_name,'') AS item_type_name, ic.item_category_name, ig.item_group_code, sp.supplier_name, sp.office_address1, sp.office_phone, sp.office_fax, t.unit_abbr, t.description, t.price_buy, IFNULL(e1.fullname, '') AS created_by, IFNULL(t.created_date, '') AS created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(t.modified_date,'') AS modified_date, IFNULL(t.item_file_name,'') AS item_file_name
		    FROM  ki_item_and_service t
		    LEFT JOIN  ki_rack r ON ( t.rack_id = r.rack_id ) 
            LEFT JOIN ki_item_type it ON it.item_type_id = t.item_id
            LEFT JOIN ki_item_category ic ON ic.item_category_id = t.item_category_id
            LEFT JOIN ki_item_group ig ON ig.item_group_id = t.item_group_id
            LEFT JOIN ki_supplier sp ON sp.supplier_id = t.supplier_id
            LEFT JOIN ki_user u1 ON u1.user_id = t.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = t.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
		    ORDER BY ".$sortBy.", t.item_id DESC";
		} else {
			$sql = "SELECT t.item_id, t.item_code, t.item_name, t.item_specification, t.current_stock, t.low_level_stock, t.reorder_level_stock, r.rack_code,  IF(t.is_stock=1,'Ya','Tidak') AS is_warehouse, IFNULL(t.item_section, '') AS item_section, IF(t.is_asset=1,'Ya','Tidak') AS is_asset, IF(t.is_active=1,'Ya','Tidak') AS is_active, IFNULL(t.brand_name,'') AS brand_name, IFNULL(t.serial_number,'') AS serial_number, IFNULL(t.model_number,'') AS model_number, IFNULL(it.item_type_name,'') AS item_type_name, ic.item_category_name, ig.item_group_code, sp.supplier_name, sp.office_address1, sp.office_phone, sp.office_fax, t.unit_abbr, t.description, t.price_buy, IFNULL(e1.fullname, '') AS created_by, IFNULL(t.created_date, '') AS created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(t.modified_date,'') AS modified_date, IFNULL(t.item_file_name,'') AS item_file_name
		    FROM  ki_item_and_service t
		    LEFT JOIN  ki_rack r ON ( t.rack_id = r.rack_id ) 
            LEFT JOIN ki_item_type it ON it.item_type_id = t.item_id
            LEFT JOIN ki_item_category ic ON ic.item_category_id = t.item_category_id
            LEFT JOIN ki_item_group ig ON ig.item_group_id = t.item_group_id
            LEFT JOIN ki_supplier sp ON sp.supplier_id = t.supplier_id
            LEFT JOIN ki_user u1 ON u1.user_id = t.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = t.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
		    ORDER BY ".$sortBy.", t.item_id DESC
			LIMIT $counter, 15";
		}
    
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
  
	//list item Group
	function getItemGroup($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.item_group_id, a.item_group_code, a.description, a.is_active
			FROM ki_item_group a
			ORDER BY ".$sortBy.", item_group_id ASC";
		} else {
			$sql = "SELECT a.item_group_id, a.item_group_code, a.description, a.is_active
			FROM ki_item_group a
			ORDER BY ".$sortBy.", item_group_id ASC
			LIMIT $counter, 15";
		}
    
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
  
	//list item Category
	function getItemCategory($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.item_category_id, a.item_category_name, a.item_category_description, a.code_for_item
			FROM ki_item_category a
			ORDER BY ".$sortBy.", item_category_id ASC";
		} else {
			$sql = "SELECT a.item_category_id, a.item_category_name, a.item_category_description, a.code_for_item
			FROM ki_item_category a
			ORDER BY ".$sortBy.", item_category_id ASC
			LIMIT $counter, 15";
		}
    
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

	//list item Type
	function getItemType($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.item_type_id, a.item_type_name, a.is_active
			FROM ki_item_type a
			ORDER BY ".$sortBy.", item_type_id ASC";
		} else {
			$sql = "SELECT a.item_type_id, a.item_type_name, a.is_active
			FROM ki_item_type a
			ORDER BY ".$sortBy.", item_type_id ASC
			LIMIT $counter, 15";
		}
    
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
  
	//list asset
  	function getAsset($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.asset_id, t.asset_code, t.asset_name, am.short_description, t.location, t.quantity, t.remark, t.status, ast.asset_type_name AS asset_type_id, t.brand_name, t.serial_number, t.acquisition_date, IFNULL(t.usage_date, '') AS usage_date, dm.method AS depreciation_method_id, t.depreciation_rate, t.age_type, t.activa_account, t.unit_abbr, crr.currency_symbol, t.price_buy, crr.is_base_currency, sp.supplier_name, sp.office_phone, sp.email_address, sp.phone, t.notes, t.description, IFNULL(t.created_by, '') AS created_by, IFNULL(t.created_date, '') AS created_date, IFNULL(t.modified_by, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
	        FROM ki_asset t
	        LEFT JOIN ki_asset_model am ON(am.asset_model_id = t.asset_model_id)
            LEFT JOIN ki_asset_type ast ON ast.asset_type_id = t.asset_type_id
            LEFT JOIN ki_depreciation_method dm ON dm.depreciation_method_id = t.depreciation_method_id
            LEFT JOIN ki_currency crr ON crr.currency_id = t.currency_id
            LEFT JOIN ki_supplier sp ON sp.supplier_id = t.supplier_id
	        ORDER BY ".$sortBy.", t.asset_id DESC";
		} else {
			$sql = "SELECT t.asset_id, t.asset_code, t.asset_name, am.short_description, t.location, t.quantity, t.remark, t.status, ast.asset_type_name AS asset_type_id, t.brand_name, t.serial_number, t.acquisition_date, IFNULL(t.usage_date, '') AS usage_date, dm.method AS depreciation_method_id, t.depreciation_rate, t.age_type, t.activa_account, t.unit_abbr, crr.currency_symbol, t.price_buy, crr.is_base_currency, sp.supplier_name, sp.office_phone, sp.email_address, sp.phone, t.notes, t.description, IFNULL(t.created_by, '') AS created_by, IFNULL(t.created_date, '') AS created_date, IFNULL(t.modified_by, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
	        FROM ki_asset t
	        LEFT JOIN ki_asset_model am ON(am.asset_model_id = t.asset_model_id)
            LEFT JOIN ki_asset_type ast ON ast.asset_type_id = t.asset_type_id
            LEFT JOIN ki_depreciation_method dm ON dm.depreciation_method_id = t.depreciation_method_id
            LEFT JOIN ki_currency crr ON crr.currency_id = t.currency_id
            LEFT JOIN ki_supplier sp ON sp.supplier_id = t.supplier_id
	        ORDER BY ".$sortBy.", t.asset_id DESC
			LIMIT $counter, 15";
		}
    
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
  
	//list asset rental
  	function getAssetRental($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.asset_rental_id, t.asset_rental_code, t.asset_rental_name, am.short_description, r.rack_code, IF( t.is_active =1, 'Ya', 'Tidak' ) AS is_active , t.remark, ast.asset_type_name AS asset_type_id, t.brand_name, t.serial_number, t.description, t.notes, wh.warehouse_name, crc.currency_symbol, t.price_buy, sp.supplier_name, t.quantity, t.unit_abbr, t.acquisition_date, t.usage_date, dm.method AS depreciation_method, t.depreciation_rate, t.age_type, t.activa_account, t.depreciation_account, t.exchange_depreciation_account, t.created_by, t.created_date, t.modified_by, t.modified_date
	      	FROM  ki_asset_rental t
	      	LEFT JOIN  ki_asset_model am ON t.asset_model_id = am.asset_model_id
	      	LEFT JOIN  ki_rack r ON r.rack_id = t.rack_id
            LEFT JOIN ki_asset_type ast ON ast.asset_type_id = t.asset_type_id
            LEFT JOIN ki_warehouse wh ON wh.warehouse_id = t.warehouse_id
            LEFT JOIN ki_currency crc ON crc.currency_id = t.currency_id
            LEFT JOIN ki_supplier sp ON sp.supplier_id = t.supplier_id
            LEFT JOIN ki_depreciation_method dm ON dm.depreciation_method_id = t.depreciation_method_id
	      	ORDER BY ".$sortBy.", t.asset_rental_id DESC";
		} else {
			$sql = "SELECT t.asset_rental_id, t.asset_rental_code, t.asset_rental_name, am.short_description, r.rack_code, IF( t.is_active =1, 'Ya', 'Tidak' ) AS is_active , t.remark, ast.asset_type_name AS asset_type_id, t.brand_name, t.serial_number, t.description, t.notes, wh.warehouse_name, crc.currency_symbol, t.price_buy, sp.supplier_name, t.quantity, t.unit_abbr, t.acquisition_date, t.usage_date, dm.method AS depreciation_method, t.depreciation_rate, t.age_type, t.activa_account, t.depreciation_account, t.exchange_depreciation_account, t.created_by, t.created_date, t.modified_by, t.modified_date
	      	FROM  ki_asset_rental t
	      	LEFT JOIN  ki_asset_model am ON t.asset_model_id = am.asset_model_id
	      	LEFT JOIN  ki_rack r ON r.rack_id = t.rack_id
            LEFT JOIN ki_asset_type ast ON ast.asset_type_id = t.asset_type_id
            LEFT JOIN ki_warehouse wh ON wh.warehouse_id = t.warehouse_id
            LEFT JOIN ki_currency crc ON crc.currency_id = t.currency_id
            LEFT JOIN ki_supplier sp ON sp.supplier_id = t.supplier_id
            LEFT JOIN ki_depreciation_method dm ON dm.depreciation_method_id = t.depreciation_method_id
	      	ORDER BY ".$sortBy.", t.asset_rental_id DESC
			LIMIT $counter, 15";
		}
    
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
  
	//list stock adjustment
  	function getStockAdjustment($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.stock_adjustment_id, t.adjustment_number, DATE_FORMAT(t.adjustment_date,'%d-%m-%Y') AS adjustment_date, t.short_description, t.notes, IF(t.approval_by!='',e.fullname,'') AS approval_by, IFNULL(t.approval_date, '') AS approval_date, t.approval_notes, IFNULL(e1.fullname, '') AS created_by, IFNULL(t.created_date, '') AS created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
	      	FROM ki_stock_adjustment t
	      	LEFT JOIN ki_user u ON(u.user_id = t.approval_by)
	      	LEFT JOIN ki_employee e ON(u.employee_id = e.employee_id)
            LEFT JOIN ki_user u1 ON u1.user_id = t.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = t.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
	      	ORDER BY ".$sortBy.", t.stock_adjustment_id DESC";
		} else {
			$sql = "SELECT t.stock_adjustment_id, t.adjustment_number, DATE_FORMAT(t.adjustment_date,'%d-%m-%Y') AS adjustment_date, t.short_description, t.notes, IF(t.approval_by!='',e.fullname,'') AS approval_by, IFNULL(t.approval_date, '') AS approval_date, t.approval_notes, IFNULL(e1.fullname, '') AS created_by, IFNULL(t.created_date, '') AS created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
	      	FROM ki_stock_adjustment t
	      	LEFT JOIN ki_user u ON(u.user_id = t.approval_by)
	      	LEFT JOIN ki_employee e ON(u.employee_id = e.employee_id)
            LEFT JOIN ki_user u1 ON u1.user_id = t.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = t.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
	      	ORDER BY ".$sortBy.", t.stock_adjustment_id DESC
			LIMIT $counter, 15";
		}
    
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
  
	//list stock adjustment
  	function getStockAdjustmentDetail($conn, $saId){
		$sql = "SELECT sad.stock_adjustment_detail_id, ias.item_name, ias.item_specification, sad.current_stock, sad.actual_stock, sad.adjustment_value, sad.unit_price, IFNULL(sad.notes, '') AS notes
		FROM ki_stock_adjustment_detail sad 
		LEFT JOIN ki_item_and_service ias ON ias.item_id = sad.item_id
		WHERE sad.stock_adjustment_id =".$saId."";
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
  
	//list material return
  	function getMaterialReturn($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.material_return_id, t.material_return_number, jo.job_order_number, jo.job_order_description, DATE_FORMAT(t.return_date,'%d-%m-%Y') AS return_date, IF(t.created_by!='',e.fullname,'') AS created_by, IFNULL(t.notes, '') AS notes, IF(t.recognized=1,'Ya','Tidak') AS recognized, IFNULL(t.created_date, '') AS created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
	      	FROM ki_material_return t
	      	LEFT JOIN ki_job_order jo ON(jo.job_order_id = t.job_order_id)
	     	LEFT JOIN ki_user u ON(u.user_id = t.created_by)
	      	LEFT JOIN ki_employee e ON(u.employee_id = e.employee_id)  
            LEFT JOIN ki_user u2 ON u2.user_id = t.modified_by
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
	      	ORDER BY ".$sortBy.", t.material_return_id DESC";
		} else {
			$sql = "SELECT t.material_return_id, t.material_return_number, jo.job_order_number, jo.job_order_description, DATE_FORMAT(t.return_date,'%d-%m-%Y') AS return_date, IF(t.created_by!='',e.fullname,'') AS created_by, IFNULL(t.notes, '') AS notes, IF(t.recognized=1,'Ya','Tidak') AS recognized, IFNULL(t.created_date, '') AS created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
	      	FROM ki_material_return t
	      	LEFT JOIN ki_job_order jo ON(jo.job_order_id = t.job_order_id)
	     	LEFT JOIN ki_user u ON(u.user_id = t.created_by)
	      	LEFT JOIN ki_employee e ON(u.employee_id = e.employee_id)  
            LEFT JOIN ki_user u2 ON u2.user_id = t.modified_by
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
	      	ORDER BY ".$sortBy.", t.material_return_id DESC
			LIMIT $counter, 15";
		}
    
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
  
	//list material return
  	function getMaterialReturnDetail($conn, $mrId){
		$sql = "SELECT it.item_name, it.item_specification, wh.warehouse_name, mrd.quantity, mrd.unit_abbr
		FROM ki_material_return_detail mrd
		LEFT JOIN ki_item_and_service it ON it.item_id = mrd.item_id
		LEFT JOIN ki_warehouse wh ON wh.warehouse_id = it.warehouse_id
		WHERE mrd.material_return_id = ".$mrId."
		ORDER BY item_name ASC";
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

	//list check clock
  	function getEmployeeCheckClock($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT IFNULL(t.employee_check_clock_id, '0') AS id, e.fullname, e.employee_id
			FROM  ki_employee e
			LEFT JOIN ki_employee_check_clock t ON t.employee_check_id = e.employee_check_id
			GROUP BY e.employee_check_id
			ORDER BY ".$sortBy.", e.fullname ASC";
		} else {
			$sql = "SELECT IFNULL(t.employee_check_clock_id, '0') AS id, e.fullname, e.employee_id
			FROM  ki_employee e
			LEFT JOIN ki_employee_check_clock t ON t.employee_check_id = e.employee_check_id
			GROUP BY e.employee_check_id
			ORDER BY ".$sortBy.", e.fullname ASC
			LIMIT $counter, 15";
		}
    
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

	//list check clock
  	function getEmployeeCheckClockDetail($conn, $counter, $empId){
  		if ($counter<0) {
			$sql = "SELECT IFNULL(jo.job_order_number, '') AS job_order, ec.daily_wages, ec.date_check_clock, DAYNAME(ec.date_check_clock) AS day, ec.check_in, ec.check_out, IFNULL(ow.start_time, '08:00:00') AS start_time, IFNULL(ow.finish_time, '17:00:00') AS finish_time, ec.emergency_call, ec.no_lunch, IFNULL(ec.note_for_shift, '') AS note_for_shift, ec.shift_category, ec.type_work_hour, IF(ec.permission_late=1,'Ya','Tidak') AS permission_late
			FROM ki_employee_check_clock ec
			LEFT JOIN ki_job_order jo ON jo.job_order_id = ec.job_order_id
			LEFT JOIN ki_employee emp ON emp.employee_check_id = ec.employee_check_id
			LEFT JOIN ki_overtime_workorder_detail ow ON ow.employee_id = emp.employee_id AND ow.overtime_date = ec.date_check_clock
			WHERE emp.employee_id = ".$empId."
			ORDER BY date_check_clock DESC";
		} else {
			$sql = "SELECT IFNULL(jo.job_order_number, '') AS job_order, ec.daily_wages, ec.date_check_clock, DAYNAME(ec.date_check_clock) AS day, ec.check_in, ec.check_out, IFNULL(ow.start_time, '08:00:00') AS start_time, IFNULL(ow.finish_time, '17:00:00') AS finish_time, ec.emergency_call, ec.no_lunch, IFNULL(ec.note_for_shift, '') AS note_for_shift, ec.shift_category, ec.type_work_hour, IF(ec.permission_late=1,'Ya','Tidak') AS permission_late
			FROM ki_employee_check_clock ec
			LEFT JOIN ki_job_order jo ON jo.job_order_id = ec.job_order_id
			LEFT JOIN ki_employee emp ON emp.employee_check_id = ec.employee_check_id
			LEFT JOIN ki_overtime_workorder_detail ow ON ow.employee_id = emp.employee_id AND ow.overtime_date = ec.date_check_clock
			WHERE emp.employee_id = ".$empId."
			ORDER BY date_check_clock DESC
			LIMIT $counter, 15";
		}
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

	//list prestasi
  	function getEmployeeAchievement($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.employee_achievement_id, e.fullname, t.event, t.achievement, t.achievement_date, t.notes
			FROM  ki_employee_achievement t
			LEFT JOIN ki_employee e ON t.employee_id = e.employee_id
			GROUP BY t.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC";
		} else {
			$sql = "SELECT t.employee_achievement_id, e.fullname, t.event, t.achievement, t.achievement_date, t.notes
			FROM  ki_employee_achievement t
			LEFT JOIN ki_employee e ON t.employee_id = e.employee_id
			GROUP BY t.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC
			LIMIT $counter, 15";
		}
    
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

	//list cuti
  	function getEmployeeLeave($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.employee_leave_id AS id, e.fullname, e.employee_id
			FROM ki_employee_leave t
			LEFT JOIN ki_employee e ON t.employee_id = e.employee_id
			GROUP BY t.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC";
		} else {
			$sql = "SELECT t.employee_leave_id AS id, e.fullname, e.employee_id
			FROM ki_employee_leave t
			LEFT JOIN ki_employee e ON t.employee_id = e.employee_id
			GROUP BY t.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC
			LIMIT $counter, 15";
		}
    
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

	//list karyawan
  	function getEmployeeLeaveDetail($conn, $empId){
		$sql = "SELECT IFNULL(el.date_leave, '') AS date_leave, IFNULL(el.proposed_date, '') AS proposed_date, el.date_extended, el.status, lc.leave_category_name, IFNULL(el.is_approved, 0) AS is_approved
		FROM ki_employee_leave el
		LEFT JOIN ki_leave_category lc ON lc.leave_category_id = el.leave_category_id
		WHERE el.employee_id = ".$empId."
		ORDER BY is_approved ASC, date_leave ASC";
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

	// list Pendidikan Karyawan
  	function getEmployeeEducation($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.employee_education_id AS id, e.fullname, e.employee_id
			FROM  ki_employee_education t
			LEFT JOIN ki_employee e ON t.employee_id = e.employee_id
			GROUP BY t.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC";
		} else {
			$sql = "SELECT t.employee_education_id AS id, e.fullname, e.employee_id
			FROM  ki_employee_education t
			LEFT JOIN ki_employee e ON t.employee_id = e.employee_id
			GROUP BY t.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC
			LIMIT $counter, 15";
		}
    
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
  
  	function getEmploymentHistory($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.employment_history_id AS id, e.fullname, e.employee_id
			FROM ki_employment_history t
			LEFT JOIN ki_employee e ON t.employee_id = e.employee_id
			GROUP BY t.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC";
		} else {
			$sql = "SELECT t.employment_history_id AS id, e.fullname, e.employee_id
			FROM ki_employment_history t
			LEFT JOIN ki_employee e ON t.employee_id = e.employee_id
			GROUP BY t.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC
			LIMIT $counter, 15";
		}
    
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
  
  	function getEmploymentHistoryDetail($conn, $empId){
		$sql = "SELECT eh.history_date, emp.employee_number, emp.fullname, eh.employee_grade_name, eh.marital_status_name, eh.company_workbase_name, eh.notes
		FROM ki_employment_history eh
		LEFT JOIN ki_employee emp ON emp.employee_id = eh.employee_id
		WHERE eh.employee_id = ".$empId."";
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

	//list Keluarga Karyawan
  	function getEmployeeFamily($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.employee_family_id AS id, e.fullname, e.employee_id
			FROM ki_employees_family t
			LEFT JOIN ki_employee e ON t.employee_id = e.employee_id
			GROUP BY t.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC";
		} else {
			$sql = "SELECT t.employee_family_id AS id, e.fullname, e.employee_id
			FROM ki_employees_family t
			LEFT JOIN ki_employee e ON t.employee_id = e.employee_id
			GROUP BY t.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC
			LIMIT $counter, 15";
		}
    
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
  
  	function getTrainingList($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.training_id AS id, e.fullname, e.employee_id
			FROM ki_training_list t
			LEFT JOIN ki_employee e ON t.employee_id = e.employee_id
			GROUP BY t.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC";
		} else {
			$sql = "SELECT t.training_id AS id, e.fullname, e.employee_id
			FROM ki_training_list t
			LEFT JOIN ki_employee e ON t.employee_id = e.employee_id
			GROUP BY t.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC
			LIMIT $counter, 15";
		}
    
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
  
  	function getEmployeeNotice($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.employee_notice_id, e.fullname, DATE_FORMAT(t.notice_date,'%d-%m-%Y') AS notice_date, t.subject, DATE_FORMAT(t.expired_date,'%d-%m-%Y') AS expired_date, eg.employee_grade_name, j.job_grade_name, t.basic_salary, t.meal_allowance, t.transport_allowance, t.profesional_allowance, t.overtime, t.welfare_allowance, t.location_project_allowance, t.other_allowance, t.notes, IFNULL(e1.fullname, '') AS prepared_by, IFNULL(e2.fullname, '') AS commented_by, IFNULL(e3.fullname, '') AS processed_by, e4.fullname AS created_by, t.created_date, IFNULL(e5.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
			FROM ki_employee_notice t
			LEFT JOIN ki_employee e ON ( t.employee_id = e.employee_id )
			LEFT JOIN ki_employee_grade eg ON(eg.employee_grade_id = e.employee_grade_id)
			LEFT JOIN ki_job_grade j ON(j.job_grade_id = e.job_grade_id)
            LEFT JOIN ki_user u1 ON u1.user_id = t.prepared_by
            LEFT JOIN ki_user u2 ON u2.user_id = t.commented_by
            LEFT JOIN ki_user u3 ON u3.user_id = t.processed_by
            LEFT JOIN ki_user u4 ON u4.user_id = t.created_by
            LEFT JOIN ki_user u5 ON u5.user_id = t.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
            LEFT JOIN ki_employee e3 ON e3.employee_id = u3.employee_id
            LEFT JOIN ki_employee e4 ON e4.employee_id = u4.employee_id
            LEFT JOIN ki_employee e5 ON e5.employee_id = u5.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC";
		} else {
			$sql = "SELECT t.employee_notice_id, e.fullname, DATE_FORMAT(t.notice_date,'%d-%m-%Y') AS notice_date, t.subject, DATE_FORMAT(t.expired_date,'%d-%m-%Y') AS expired_date, eg.employee_grade_name, j.job_grade_name, t.basic_salary, t.meal_allowance, t.transport_allowance, t.profesional_allowance, t.overtime, t.welfare_allowance, t.location_project_allowance, t.other_allowance, t.notes, IFNULL(e1.fullname, '') AS prepared_by, IFNULL(e2.fullname, '') AS commented_by, IFNULL(e3.fullname, '') AS processed_by, e4.fullname AS created_by, t.created_date, IFNULL(e5.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
			FROM ki_employee_notice t
			LEFT JOIN ki_employee e ON ( t.employee_id = e.employee_id )
			LEFT JOIN ki_employee_grade eg ON(eg.employee_grade_id = e.employee_grade_id)
			LEFT JOIN ki_job_grade j ON(j.job_grade_id = e.job_grade_id)
            LEFT JOIN ki_user u1 ON u1.user_id = t.prepared_by
            LEFT JOIN ki_user u2 ON u2.user_id = t.commented_by
            LEFT JOIN ki_user u3 ON u3.user_id = t.processed_by
            LEFT JOIN ki_user u4 ON u4.user_id = t.created_by
            LEFT JOIN ki_user u5 ON u5.user_id = t.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
            LEFT JOIN ki_employee e3 ON e3.employee_id = u3.employee_id
            LEFT JOIN ki_employee e4 ON e4.employee_id = u4.employee_id
            LEFT JOIN ki_employee e5 ON e5.employee_id = u5.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC
			LIMIT $counter, 15";
		}
    
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
  
  	function getWorkExperience($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.experience_id AS id, e.fullname, e.employee_id
			FROM ki_work_experience t
			LEFT JOIN ki_employee e ON t.employee_id = e.employee_id
			GROUP BY t.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC";
		} else {
			$sql = "SELECT t.experience_id AS id, e.fullname, e.employee_id
			FROM ki_work_experience t
			LEFT JOIN ki_employee e ON t.employee_id = e.employee_id
			GROUP BY t.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC
			LIMIT $counter, 15";
		}
    
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
  
  	function getHistoryContract($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.history_contract_id, e.fullname, eg.employee_grade_name, t.description, DATE_FORMAT(t.start_date,'%d-%m-%Y') AS start_date, DATE_FORMAT(t.end_date,'%d-%m-%Y') AS end_date, e1.fullname AS created_by, t.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
			FROM  ki_history_contract t
			LEFT JOIN  ki_employee e ON ( t.employee_id = e.employee_id ) 
			LEFT JOIN  ki_employee_grade eg ON ( t.employee_grade_id = eg.employee_grade_id )
            LEFT JOIN ki_user u1 ON u1.user_id = t.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = t.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", t.history_contract_id DESC";
		} else {
			$sql = "SELECT t.history_contract_id, e.fullname, eg.employee_grade_name, t.description, DATE_FORMAT(t.start_date,'%d-%m-%Y') AS start_date, DATE_FORMAT(t.end_date,'%d-%m-%Y') AS end_date, e1.fullname AS created_by, t.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
			FROM  ki_history_contract t
			LEFT JOIN  ki_employee e ON ( t.employee_id = e.employee_id ) 
			LEFT JOIN  ki_employee_grade eg ON ( t.employee_grade_id = eg.employee_grade_id )
            LEFT JOIN ki_user u1 ON u1.user_id = t.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = t.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", t.history_contract_id DESC
			LIMIT $counter, 15";
		}
    
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
  
  	function getHariLibur($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.holiday_id, t.holiday_name, ht.holiday_type_name, DATE_FORMAT(t.holiday_date,'%d-%m-%Y') AS holiday_date, t.description, IF(t.is_overtime=1,'Ya','Tidak') AS is_overtime
			FROM ki_holiday t
			LEFT JOIN ki_holiday_type ht ON t.holiday_type_id = ht.holiday_type_id
			ORDER BY ".$sortBy.", t.holiday_id DESC";
		} else {
			$sql = "SELECT t.holiday_id, t.holiday_name, ht.holiday_type_name, DATE_FORMAT(t.holiday_date,'%d-%m-%Y') AS holiday_date, t.description, IF(t.is_overtime=1,'Ya','Tidak') AS is_overtime
			FROM ki_holiday t
			LEFT JOIN ki_holiday_type ht ON t.holiday_type_id = ht.holiday_type_id
			ORDER BY ".$sortBy.", t.holiday_id DESC
			LIMIT $counter, 15";
		}
    
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

	//list potongan karyawan
  	function getEmployeeDeduction($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT t.employee_deduction_id, e.fullname, IFNULL(d.deduction_name, '') AS deduction_name, IFNULL(DATE_FORMAT(t.date_begin,'%d-%m-%Y'), '') AS date_begin, IFNULL(DATE_FORMAT(t.date_end,'%d-%m-%Y'), '') AS date_end, t.tax, IF(t.is_active=1,'Ya','Tidak') AS is_active
			FROM  ki_employee e
			LEFT JOIN ki_employee_deduction t ON( e.employee_id = t.employee_id )
			LEFT JOIN ki_deduction d ON d.deduction_id = t.deduction_id
			ORDER BY ".$sortBy.", e.fullname ASC";
		} else {
			$sql = "SELECT t.employee_deduction_id, e.fullname, IFNULL(d.deduction_name, '') AS deduction_name, IFNULL(DATE_FORMAT(t.date_begin,'%d-%m-%Y'), '') AS date_begin, IFNULL(DATE_FORMAT(t.date_end,'%d-%m-%Y'), '') AS date_end, t.tax, IF(t.is_active=1,'Ya','Tidak') AS is_active
			FROM  ki_employee e
			LEFT JOIN ki_employee_deduction t ON( e.employee_id = t.employee_id )
			LEFT JOIN ki_deduction d ON d.deduction_id = t.deduction_id
			ORDER BY ".$sortBy.", e.fullname ASC
			LIMIT $counter, 15";
		}
    
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

	//list Tunjangan Untuk Jenjang
  	function getEmployeeGradeAllowance($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT ega.employee_grade_allowance_id, ega.employee_grade_allowance_name, eg.employee_grade_name, a.allowance_name AS allowance_id, IF(ega.is_active=1,'Ya','Tidak') AS is_active
			FROM ki_employee_grade_allowance ega
			LEFT JOIN ki_employee_grade eg ON eg.employee_grade_id = ega.employee_grade_id
			LEFT JOIN ki_allowance a ON(a.allowance_id = ega.allowance_id)  
			ORDER BY ".$sortBy.", employee_grade_allowance_id ASC";
		} else {
			$sql = "SELECT ega.employee_grade_allowance_id, ega.employee_grade_allowance_name, eg.employee_grade_name, a.allowance_name AS allowance_id, IF(ega.is_active=1,'Ya','Tidak') AS is_active
			FROM ki_employee_grade_allowance ega
			LEFT JOIN ki_employee_grade eg ON eg.employee_grade_id = ega.employee_grade_id
			LEFT JOIN ki_allowance a ON(a.allowance_id = ega.allowance_id)  
			ORDER BY ".$sortBy.", employee_grade_allowance_id ASC
			LIMIT $counter, 15";
		}
    
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

	//list Potongan
  	function getDeduction($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT d.deduction_id, d.deduction_name, d.report_code, d.description, d.value, d.adjustment, IF(d.is_active=1,'Ya','Tidak') AS is_active
			FROM ki_deduction d
			ORDER BY ".$sortBy.", d.deduction_id DESC";
		} else {
			$sql = "SELECT d.deduction_id, d.deduction_name, d.report_code, d.description, d.value, d.adjustment, IF(d.is_active=1,'Ya','Tidak') AS is_active
			FROM ki_deduction d
			ORDER BY ".$sortBy.", d.deduction_id DESC
			LIMIT $counter, 15";
		}
    
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

	//list Tunjangan
  	function getAllowance($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT a.allowance_id, a.allowance_name, a.report_code, aty.allowance_type_name, ag.allowance_group_name, a.value, a.allowance_unit, a.description, cr.currency_code, a.adjustment, IF(a.count_as_religious_holiday_allowance=1,'Ya','Tidak') AS is_thr, IF(a.is_active=1,'Ya','Tidak') AS is_active
			FROM ki_allowance a
			LEFT JOIN ki_allowance_type aty ON(a.allowance_type_id = aty.allowance_type_id)
			LEFT JOIN ki_allowance_group ag ON(ag.allowance_group_id = a.allowance_group_id)
            LEFT JOIN ki_currency cr ON cr.currency_id = a.currency_id
			ORDER BY ".$sortBy.", a.allowance_id DESC";
		} else {
			$sql = "SELECT a.allowance_id, a.allowance_name, a.report_code, aty.allowance_type_name, ag.allowance_group_name, a.value, a.allowance_unit, a.description, cr.currency_code, a.adjustment, IF(a.count_as_religious_holiday_allowance=1,'Ya','Tidak') AS is_thr, IF(a.is_active=1,'Ya','Tidak') AS is_active
			FROM ki_allowance a
			LEFT JOIN ki_allowance_type aty ON(a.allowance_type_id = aty.allowance_type_id)
			LEFT JOIN ki_allowance_group ag ON(ag.allowance_group_id = a.allowance_group_id)
            LEFT JOIN ki_currency cr ON cr.currency_id = a.currency_id
			ORDER BY ".$sortBy.", a.allowance_id DESC
			LIMIT $counter, 15";
		}
    
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

	//list Daftar Golongan Gaji
  	function getSalaryGrade($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT sg.salary_grade_id, sg.salary_grade_name, sg.salary_grade_code, c.currency_code, sg.basic_salary, sg.basic_salary_per_day, sg.overtime_rule, sg.payment_cycle
			FROM ki_salary_grade sg
			LEFT JOIN ki_currency c ON(c.currency_id = sg.currency_id)
			ORDER BY ".$sortBy.", sg.salary_grade_id DESC";
		} else {
			$sql = "SELECT sg.salary_grade_id, sg.salary_grade_name, sg.salary_grade_code, c.currency_code, sg.basic_salary, sg.basic_salary_per_day, sg.overtime_rule, sg.payment_cycle
			FROM ki_salary_grade sg
			LEFT JOIN ki_currency c ON(c.currency_id = sg.currency_id)
			ORDER BY ".$sortBy.", sg.salary_grade_id DESC
			LIMIT $counter, 15";
		}
    
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

	//list Status Pernikahan
  	function getMaritalStatus($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT ms.marital_status_id, ms.marital_status_code, ms.marital_status_name, ms.minimum_amount,ms.minimum_amount_maried, ms.person_to_care, ms.tax_amount, ms.ptkp_tahunan, ms.jpk_bulanan, ms.umk_amount
			FROM ki_marital_status ms
			ORDER BY ".$sortBy.", ms.marital_status_id DESC";
		} else {
			$sql = "SELECT ms.marital_status_id, ms.marital_status_code, ms.marital_status_name, ms.minimum_amount,ms.minimum_amount_maried, ms.person_to_care, ms.tax_amount, ms.ptkp_tahunan, ms.jpk_bulanan, ms.umk_amount
			FROM ki_marital_status ms
			ORDER BY ".$sortBy.", ms.marital_status_id DESC
			LIMIT $counter, 15";
		}
    
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

	//list Salary Corrections
  	function getSalaryCorrection($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT sc.salary_correction_id, sc.amount, sc.notes, CONCAT(e.fullname,' - ',er.month_year) AS report_name, IF(sc.is_deduction=1,'Ya','Tidak') AS is_deduction
			FROM ki_salary_correction sc
			LEFT JOIN ki_employee_report er ON(er.employee_report_id = sc.employee_report_id)
			LEFT JOIN ki_employee e ON(e.employee_id = er.employee_id)
			ORDER BY ".$sortBy.", sc.salary_correction_id DESC";
		} else {
			$sql = "SELECT sc.salary_correction_id, sc.amount, sc.notes, CONCAT(e.fullname,' - ',er.month_year) AS report_name, IF(sc.is_deduction=1,'Ya','Tidak') AS is_deduction
			FROM ki_salary_correction sc
			LEFT JOIN ki_employee_report er ON(er.employee_report_id = sc.employee_report_id)
			LEFT JOIN ki_employee e ON(e.employee_id = er.employee_id)
			ORDER BY ".$sortBy.", sc.salary_correction_id DESC
			LIMIT $counter, 15";
		}
    
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

	//list Late Deduction
  	function getLateDeduction($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT ld.late_deduction_id, ld.amount, ld.notes, CONCAT(e.fullname,' - ',er.month_year) AS report_name
			FROM ki_late_deduction ld
			LEFT JOIN ki_employee_report er ON(er.employee_report_id = ld.employee_report_id)
			LEFT JOIN ki_employee e ON(e.employee_id = er.employee_id)
			ORDER BY ".$sortBy.", ld.late_deduction_id DESC";
		} else {
			$sql = "SELECT ld.late_deduction_id, ld.amount, ld.notes, CONCAT(e.fullname,' - ',er.month_year) AS report_name
			FROM ki_late_deduction ld
			LEFT JOIN ki_employee_report er ON(er.employee_report_id = ld.employee_report_id)
			LEFT JOIN ki_employee e ON(e.employee_id = er.employee_id)
			ORDER BY ".$sortBy.", ld.late_deduction_id DESC
			LIMIT $counter, 15";
		}
    
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

	//list Sisa Cuti
  	function getRemainLeave($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT rl.remain_leave_id, rl.amount, rl.notes, CONCAT(e.fullname,' - ',er.month_year) AS report_name
			FROM ki_remain_leave rl
			LEFT JOIN ki_employee_report er ON(er.employee_report_id = rl.employee_report_id)
			LEFT JOIN ki_employee e ON(e.employee_id = er.employee_id)
			ORDER BY ".$sortBy.", rl.remain_leave_id DESC";
		} else {
			$sql = "SELECT rl.remain_leave_id, rl.amount, rl.notes, CONCAT(e.fullname,' - ',er.month_year) AS report_name
			FROM ki_remain_leave rl
			LEFT JOIN ki_employee_report er ON(er.employee_report_id = rl.employee_report_id)
			LEFT JOIN ki_employee e ON(e.employee_id = er.employee_id)
			ORDER BY ".$sortBy.", rl.remain_leave_id DESC
			LIMIT $counter, 15";
		}
    
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

  	function getKabupaten($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT k.kab_id, p.prov_name, k.kab_name
		      FROM ki_kabupaten k
		      LEFT JOIN ki_provinsi p ON(k.prov_id = p.prov_id)
		      ORDER BY ".$sortBy.", k.kab_name ASC";
		} else {
			$sql = "SELECT k.kab_id, p.prov_name, k.kab_name
		      FROM ki_kabupaten k
		      LEFT JOIN ki_provinsi p ON(k.prov_id = p.prov_id)
		      ORDER BY ".$sortBy.", k.kab_name ASC
			LIMIT $counter, 15";
		}
    
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

  	function getProvinsi($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT p.prov_id, p.prov_name
	        FROM ki_provinsi p
	        ORDER BY ".$sortBy.", p.prov_name ASC";
		} else {
			$sql = "SELECT p.prov_id, p.prov_name
	        FROM ki_provinsi p
	        ORDER BY ".$sortBy.", p.prov_name ASC
			LIMIT $counter, 15";
		}
    
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

	//list karyawan
  	function getEmployee($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT e.employee_id, e.employee_number, e.fullname, eg.employee_grade_name, es.employee_status, d.department_name, cw.company_workbase_name, DATE_FORMAT(e.join_date,'%d-%m-%Y') AS join_date, IFNULL(DATE_FORMAT(e.termination_date,'%d-%m-%Y'), '') AS termination_date, IF(e.is_active=1,'Ya','Tidak') AS is_active, e.working_status, e.nickname, IF(e.gender=1, 'Laki-laki', 'Perempuan') AS gender, e.place_birthday, e.birthday, rl.religion_name, e.address, e.home_phone, e.mobile_phone, e.email1, e.no_rek, e.sin_num, e.sin_expiry_date, e.citizenship, e.city, e.state, e.country, e.origin_city_ktp, ms.marital_status_name, e.working_status, e.employee_check_id, jt.jobtitle_name, e.current_basic_salary, e.social_security_number, e.bpjs_health_number, e.npwp, e1.fullname AS created_by, e.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(e.modified_date, '') AS modified_date, e.employee_file_name, IFNULL(DATE_FORMAT(e.employment_date,'%d-%m-%Y'), '') AS employment_date, e.termination_reason, IFNULL(DATE_FORMAT(e.come_out_date,'%d-%m-%Y'), '') AS come_out_date, e.notes
			FROM ki_employee e
			LEFT JOIN ki_employee_grade eg ON(eg.employee_grade_id = e.employee_grade_id)
            LEFT JOIN ki_job_title jt ON jt.jobtitle_id = eg.jobtitle_id
			LEFT JOIN ki_employee_status es ON(es.employee_status_id = e.employee_status_id)
			LEFT JOIN ki_department d ON(d.department_id = e.department_id)
			LEFT JOIN ki_company_workbase cw ON(cw.company_workbase_id = e.company_workbase_id)
            LEFT JOIN ki_religion rl ON rl.religion_id = e.religion_id
            LEFT JOIN ki_marital_status ms ON ms.marital_status_id = e.marital_status_id
            LEFT JOIN ki_user u1 ON u1.user_id = e.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = e.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC";
		} else {
			$sql = "SELECT e.employee_id, e.employee_number, e.fullname, eg.employee_grade_name, es.employee_status, d.department_name, cw.company_workbase_name, DATE_FORMAT(e.join_date,'%d-%m-%Y') AS join_date, IFNULL(DATE_FORMAT(e.termination_date,'%d-%m-%Y'), '') AS termination_date, IF(e.is_active=1,'Ya','Tidak') AS is_active, e.working_status, e.nickname, IF(e.gender=1, 'Laki-laki', 'Perempuan') AS gender, e.place_birthday, e.birthday, rl.religion_name, e.address, e.home_phone, e.mobile_phone, e.email1, e.no_rek, e.sin_num, e.sin_expiry_date, e.citizenship, e.city, e.state, e.country, e.origin_city_ktp, ms.marital_status_name, e.working_status, e.employee_check_id, jt.jobtitle_name, e.current_basic_salary, e.social_security_number, e.bpjs_health_number, e.npwp, e1.fullname AS created_by, e.created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(e.modified_date, '') AS modified_date, e.employee_file_name, IFNULL(DATE_FORMAT(e.employment_date,'%d-%m-%Y'), '') AS employment_date, e.termination_reason, IFNULL(DATE_FORMAT(e.come_out_date,'%d-%m-%Y'), '') AS come_out_date, e.notes
			FROM ki_employee e
			LEFT JOIN ki_employee_grade eg ON(eg.employee_grade_id = e.employee_grade_id)
            LEFT JOIN ki_job_title jt ON jt.jobtitle_id = eg.jobtitle_id
			LEFT JOIN ki_employee_status es ON(es.employee_status_id = e.employee_status_id)
			LEFT JOIN ki_department d ON(d.department_id = e.department_id)
			LEFT JOIN ki_company_workbase cw ON(cw.company_workbase_id = e.company_workbase_id)
            LEFT JOIN ki_religion rl ON rl.religion_id = e.religion_id
            LEFT JOIN ki_marital_status ms ON ms.marital_status_id = e.marital_status_id
            LEFT JOIN ki_user u1 ON u1.user_id = e.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = e.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
			ORDER BY ".$sortBy.", e.fullname ASC
			LIMIT $counter, 15";
		}
    
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

	//list karyawan
  	function getEmployeeDetailAchievement($conn, $empId){
		$sql = "SELECT ea.event, ea.achievement, ea.achievement_date, ea.notes
		FROM ki_employee_achievement ea
		WHERE ea.employee_id = ".$empId."
		ORDER BY ea.employee_achievement_id DESC";
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

	//list karyawan
  	function getEmployeeDetailFamily($conn, $empId){
		$sql = "SELECT ef.family_name, ef.birthday, ft.family_type_name, ef.gender, ed.education_name AS last_education, ef.job
		FROM ki_employees_family ef
		LEFT JOIN ki_family_type ft ON ft.family_type_id = ef.family_type_id
        LEFT JOIN ki_education ed ON ed.education_id = ef.last_education
		WHERE ef.employee_id = ".$empId."";
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

	//list karyawan
  	function getEmployeeDetailTraining($conn, $empId){
		$sql = "SELECT tl.start_date, tl.end_date, tl.training_name, tl.description, tl.place, tl.provider, tl.duration_day, IFNULL(tl.evaluation_date, '') AS evaluation_date
		FROM ki_training_list tl
		WHERE tl.employee_id = ".$empId."";
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

	//list karyawan
  	function getEmployeeDetailExperience($conn, $empId){
		$sql = "SELECT we.start_date, we.end_date, we.experience_description, we.experience_position
		FROM ki_work_experience we
		WHERE we.employee_id = ".$empId."";
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

	//list karyawan
  	function getEmployeeDetailEducation($conn, $empId){
		$sql = "SELECT ed.education_name, ee.school_name, ee.major, ee.education_start, ee.education_end
		FROM ki_employee_education ee
		LEFT JOIN ki_education ed ON ed.education_id = ee.education_id
		WHERE ee.employee_id = ".$empId."
		ORDER BY ee.education_end DESC";
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

	//list karyawan
  	function getEmployeeDetailHistory($conn, $empId){
		$sql = "SELECT eh.history_date, eh.status_history, eh.employee_grade_name, eh.notes
		FROM ki_employment_history eh
		WHERE eh.employee_id = ".$empId."";
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

	//list karyawan
  	function getEmployeeDetailPotongan($conn, $empId){
		$sql = "SELECT de.deduction_name, de.value, IF(ed.is_active=1,'Ya','Tidak') AS is_active
		FROM ki_employee_deduction ed 
		LEFT JOIN ki_deduction de ON de.deduction_id = ed.deduction_id
		WHERE ed.employee_id = ".$empId."";
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

	//list karyawan
  	function getEmployeeDetailTunjangan($conn, $empId){
		$sql = "SELECT ega.employee_grade_allowance_name, al.value, IF(al.count_as_religious_holiday_allowance=1,'Ya','Tidak') AS count_as_religious_holiday_allowance, IF(al.is_active=1,'Ya','Tidak') AS is_active
		FROM ki_employee_grade_allowance ega
		LEFT JOIN ki_employee_grade eg ON eg.employee_grade_id = ega.employee_grade_id
		LEFT JOIN ki_employee emp ON emp.employee_grade_id = eg.employee_grade_id
		LEFT JOIN ki_allowance al ON al.allowance_id = ega.allowance_id
		WHERE emp.employee_id = ".$empId."";
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

	//list karyawan
  	function getEmployeeDetailFile($conn, $empId){
		$sql = "SELECT CASE WHEN ef.category = 1 THEN 'CV' WHEN ef.category = 2 THEN 'Ijazah' WHEN ef.category = 1 THEN 'Sertifikat' ELSE 'Sertifikat' END AS category, ef.file_description, ef.file_name, CASE WHEN ef.category = 1 THEN 'cv' WHEN ef.category = 2 THEN 'ijazah' WHEN ef.category = 1 THEN 'sertifikat' ELSE 'sertifikat' END AS file_location
		FROM ki_employee_file ef
		WHERE employee_id = ".$empId."
		ORDER BY ef.category ASC";
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

	//list karyawan
  	function getEmployeeDetailLeave($conn, $empId){
		$sql = "SELECT IFNULL(el.date_leave, '') AS date_leave, IFNULL(el.proposed_date, '') AS proposed_date, el.date_extended, el.status, lc.leave_category_name, IFNULL(el.is_approved, 0) AS is_approved
		FROM ki_employee_leave el
		LEFT JOIN ki_leave_category lc ON lc.leave_category_id = el.leave_category_id
		WHERE (YEAR(el.date_begin) = YEAR(CURDATE()) OR el.status LIKE 'Digunakan') AND el.employee_id = ".$empId."
		ORDER BY is_approved ASC, date_leave ASC";
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

	//list karyawan
  	function getEmployeeDetailKerja($conn, $empId){
		$sql = "SELECT hc.description, eg.employee_grade_name, hc.start_date, hc.end_date
		FROM ki_history_contract hc
		LEFT JOIN ki_employee_grade eg ON eg.employee_grade_id = hc.employee_grade_id
		WHERE employee_id = ".$empId."";
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

	//list Department
  	function getDepartment($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT d.department_id, d.department_code, IFNULL(d.code_for_item, '') AS code_for_item, d.department_name, e.fullname AS head_department, d.department_notes, d1.department_name AS atasan, c.company_name
			FROM ki_department d
			LEFT JOIN ki_employee e ON(e.employee_id = d.department_head_id)
			LEFT JOIN ki_department d1 ON(d1.department_id = d.parent_id)
			LEFT JOIN ki_company c ON(c.company_id = d.company_id)
			ORDER BY ".$sortBy.", d.department_name ASC";
		} else {
			$sql = "SELECT d.department_id, d.department_code, IFNULL(d.code_for_item, '') AS code_for_item, d.department_name, e.fullname AS head_department, d.department_notes, d1.department_name AS atasan, c.company_name
			FROM ki_department d
			LEFT JOIN ki_employee e ON(e.employee_id = d.department_head_id)
			LEFT JOIN ki_department d1 ON(d1.department_id = d.parent_id)
			LEFT JOIN ki_company c ON(c.company_id = d.company_id)
			ORDER BY ".$sortBy.", d.department_name ASC
			LIMIT $counter, 15";
		}
    
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

	//list Department
  	function getDepartmentDetail($conn, $departemenId){
  		$sql = "SELECT emp.employee_number, emp.fullname, cw.company_workbase_name, emp.address, rl.religion_name, IF(emp.is_active=1,'Ya','Tidak') AS is_active
		FROM ki_employee emp
		LEFT JOIN ki_company_workbase cw ON cw.company_workbase_id = emp.company_workbase_id
		LEFT JOIN ki_religion rl ON rl.religion_id = emp.religion_id
		WHERE emp.department_id = ".$departemenId."
		ORDER BY emp.employee_id ASC";
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

	//list jenjang karyawan
  	function getEmployeeGrade($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT eg.employee_grade_id, eg.employee_grade_name, eg.report_code,jt.jobtitle_name, jg.job_grade_name, sg.salary_grade_name, eg.overtime_limit, IF(eg.is_active=1,'Ya','Tidak') AS is_active
			FROM ki_employee_grade eg
			LEFT JOIN ki_job_title jt ON(jt.jobtitle_id = eg.jobtitle_id)
			LEFT JOIN ki_job_grade jg ON(jg.job_grade_id = eg.job_grade_id)
			LEFT JOIN ki_salary_grade sg ON(sg.salary_grade_id = eg.salary_grade_id)
			ORDER BY ".$sortBy.", eg.employee_grade_name ASC";
		} else {
			$sql = "SELECT eg.employee_grade_id, eg.employee_grade_name, eg.report_code,jt.jobtitle_name, jg.job_grade_name, sg.salary_grade_name, eg.overtime_limit, IF(eg.is_active=1,'Ya','Tidak') AS is_active
			FROM ki_employee_grade eg
			LEFT JOIN ki_job_title jt ON(jt.jobtitle_id = eg.jobtitle_id)
			LEFT JOIN ki_job_grade jg ON(jg.job_grade_id = eg.job_grade_id)
			LEFT JOIN ki_salary_grade sg ON(sg.salary_grade_id = eg.salary_grade_id)
			ORDER BY ".$sortBy.", eg.employee_grade_name ASC
			LIMIT $counter, 15";
		}
    
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

	//list Department
  	function getEmployeeGradeDetail($conn, $jenjangId){
  		$sql = "SELECT dp.department_name, emp.fullname, IFNULL(emp.employment_date, '') AS employment_date, IFNULL(emp.termination_date, '') AS termination_date, emp.address, rl.religion_name, IF(emp.is_active=1,'Ya','Tidak') AS is_active
		FROM ki_employee emp
        LEFT JOIN ki_department dp ON dp.department_id = emp.department_id
		LEFT JOIN ki_religion rl ON rl.religion_id = emp.religion_id
		WHERE emp.employee_grade_id = ".$jenjangId."
		ORDER BY emp.employee_id ASC";
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

	//list Pangkat
  	function getJobTitle($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT j.jobtitle_id, j.jobtitle_code, j.jobtitle_name, j.jobtitle_description, IF(j.is_active=1,'Ya','Tidak') AS is_active
			FROM ki_job_title j
			ORDER BY ".$sortBy.", j.jobtitle_name ASC";
		} else {
			$sql = "SELECT j.jobtitle_id, j.jobtitle_code, j.jobtitle_name, j.jobtitle_description, IF(j.is_active=1,'Ya','Tidak') AS is_active
			FROM ki_job_title j
			ORDER BY ".$sortBy.", j.jobtitle_name ASC
			LIMIT $counter, 15";
		}
    
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

	//list Jabatan
  	function getJobGrade($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT j.job_grade_id AS id, j.job_grade_name AS fullname
			FROM ki_job_grade j
			ORDER BY ".$sortBy.", j.job_grade_name ASC";
		} else {
			$sql = "SELECT j.job_grade_id AS id, j.job_grade_name AS fullname
			FROM ki_job_grade j
			ORDER BY ".$sortBy.", j.job_grade_name ASC
			LIMIT $counter, 15";
		}
    
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

	//list personalia news
  	function getPersonaliaNews($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT n.news_id, n.news_title, n.news_date, n.start_publish_date, n.finish_publish_date, e.fullname AS created_by
			FROM ki_news n
			LEFT JOIN ki_user u ON n.created_by = u.user_id
			LEFT JOIN ki_employee e ON u.employee_id = e.employee_id
			ORDER BY ".$sortBy.", news_id DESC";
		} else {
			$sql = "SELECT n.news_id, n.news_title, n.news_date, n.start_publish_date, n.finish_publish_date, e.fullname AS created_by
			FROM ki_news n
			LEFT JOIN ki_user u ON n.created_by = u.user_id
			LEFT JOIN ki_employee e ON u.employee_id = e.employee_id
			ORDER BY ".$sortBy.", news_id DESC
			LIMIT $counter, 15";
		}
    
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

	//list report karyawan
  	function getEmployeeReport($conn, $counter, $sortBy){
		if ($counter<0) {
			$sql = "SELECT er.employee_report_id, er.month_year, er.employee, IFNULL(jo.job_order_number, '') AS job_order_number, er.company_workbase_name, er.basic_salary, ((er.transport_allowance) + (er.meal_allowance) + (er.welfare_allowance) + (er.profesional_allowance) + (er.emergency_call) + (er.jamsostek_allowance) + (er.bpjs_allowance) + (er.jpk) + (er.overtime_meal)) AS total_tunjangan, er.overtime, er.absent, er.less_payment, ((er.basic_salary) + (er.transport_allowance) + (er.meal_allowance) + (er.welfare_allowance) + (er.overtime) + (er.overtime_meal) + (er.profesional_allowance) + (er.emergency_call) + (er.jamsostek_allowance) + (er.bpjs_allowance) + (er.jpk) - (er.absent)) AS total_pendapatan, er.location_project_allowance, er.official_travel_allowance, ((er.jamsostek_paid) + (er.bpjs_paid) + (er.jht) + (er.bpjs) + (er.pph1) + (er.moneybox) + (er.other_deduction) + (er.cooperative) + (er.loan_cooperative) + (er.absent) + (er.jpk_paid) + (er.deduction_k3_amount) + er.jaminan_pensiun) AS total_potongan, (((er.basic_salary) + (er.transport_allowance) + (er.meal_allowance) + (er.welfare_allowance) + (er.overtime) + (er.overtime_meal) + (er.profesional_allowance) + (er.emergency_call) + (er.jamsostek_allowance) + (er.bpjs_allowance) + (er.jpk) + er.less_payment) - ((er.absent) + (er.jpk_paid) + (er.jamsostek_paid) + (er.bpjs_paid) + (er.jht) + (er.bpjs) + (er.pph1) + (er.pph2/12) + (er.other_deduction) + (er.moneybox) + (er.cooperative) + (er.loan_cooperative) + (er.deduction_k3_amount) + er.jaminan_pensiun)) AS total_dibayar, ((er.basic_salary) + (er.transport_allowance) + (er.meal_allowance) + (er.welfare_allowance) + (er.overtime) + (er.overtime_meal) + (er.location_project_allowance) + (er.profesional_allowance) + (er.emergency_call) + (er.jamsostek_allowance) + (er.bpjs_allowance) + (er.jpk) + (er.less_payment) + (er.official_travel_allowance) + (er.pph2) + (er.overtime_meal) - (er.absent)) AS total_biaya, er.person, er.employee_status
			FROM ki_employee_report er
			LEFT JOIN ki_job_order jo ON(jo.job_order_id = er.job_order_id)
			ORDER BY ".$sortBy.", er.employee_report_id DESC";
		} else {
			$sql = "SELECT er.employee_report_id, er.month_year, er.employee, IFNULL(jo.job_order_number, '') AS job_order_number, er.company_workbase_name, er.basic_salary, ((er.transport_allowance) + (er.meal_allowance) + (er.welfare_allowance) + (er.profesional_allowance) + (er.emergency_call) + (er.jamsostek_allowance) + (er.bpjs_allowance) + (er.jpk) + (er.overtime_meal)) AS total_tunjangan, er.overtime, er.absent, er.less_payment, ((er.basic_salary) + (er.transport_allowance) + (er.meal_allowance) + (er.welfare_allowance) + (er.overtime) + (er.overtime_meal) + (er.profesional_allowance) + (er.emergency_call) + (er.jamsostek_allowance) + (er.bpjs_allowance) + (er.jpk) - (er.absent)) AS total_pendapatan, er.location_project_allowance, er.official_travel_allowance, ((er.jamsostek_paid) + (er.bpjs_paid) + (er.jht) + (er.bpjs) + (er.pph1) + (er.moneybox) + (er.other_deduction) + (er.cooperative) + (er.loan_cooperative) + (er.absent) + (er.jpk_paid) + (er.deduction_k3_amount) + er.jaminan_pensiun) AS total_potongan, (((er.basic_salary) + (er.transport_allowance) + (er.meal_allowance) + (er.welfare_allowance) + (er.overtime) + (er.overtime_meal) + (er.profesional_allowance) + (er.emergency_call) + (er.jamsostek_allowance) + (er.bpjs_allowance) + (er.jpk) + er.less_payment) - ((er.absent) + (er.jpk_paid) + (er.jamsostek_paid) + (er.bpjs_paid) + (er.jht) + (er.bpjs) + (er.pph1) + (er.pph2/12) + (er.other_deduction) + (er.moneybox) + (er.cooperative) + (er.loan_cooperative) + (er.deduction_k3_amount) + er.jaminan_pensiun)) AS total_dibayar, ((er.basic_salary) + (er.transport_allowance) + (er.meal_allowance) + (er.welfare_allowance) + (er.overtime) + (er.overtime_meal) + (er.location_project_allowance) + (er.profesional_allowance) + (er.emergency_call) + (er.jamsostek_allowance) + (er.bpjs_allowance) + (er.jpk) + (er.less_payment) + (er.official_travel_allowance) + (er.pph2) + (er.overtime_meal) - (er.absent)) AS total_biaya, er.person, er.employee_status
			FROM ki_employee_report er
			LEFT JOIN ki_job_order jo ON(jo.job_order_id = er.job_order_id)
			ORDER BY ".$sortBy.", er.employee_report_id DESC
			LIMIT $counter, 15";
		}
    
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

	//list report karyawan
  	function getEmployeeReportDetail($conn, $empId){
		$sql = "SELECT er.month_year, er.employee, er.employee_number, er.employee_grade, er.employee_status, IFNULL(er.npwp, '') AS npwp, IFNULL(er.no_rek, '') AS no_rek, IFNULL(jo.job_order_number, '') AS job_order_number, IFNULL(jo.job_order_description, '') AS job_order_description, er.company_workbase_name, er.person, u1.user_displayname, er.modified_date, er.basic_salary, er.transport_allowance, er.meal_allowance, er.welfare_allowance, er.overtime, er.overtime_meal, er.location_project_allowance, er.official_travel_allowance, er.profesional_allowance, er.emergency_call, er.jamsostek_allowance, er.bpjs_allowance, er.jpk, er.less_payment, er.absent, er.jpk_paid, er.jamsostek_paid, er.bpjs_paid, er.jht, er.bpjs, er.jaminan_pensiun, er.pph1, (er.pph2/12) AS pph2, er.other_deduction, er.moneybox, er.cooperative, er.loan_cooperative, er.deduction_k3_amount
		FROM ki_employee_report er
		LEFT JOIN ki_job_order jo ON(er.job_order_id = jo.job_order_id)
		LEFT JOIN ki_user u1 ON(u1.user_id = er.modified_by)
		WHERE er.employee_report_id = ".$empId."";
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

	//list Detail Job Order MR
  	function getDetailJOMR($conn, $jobOrder){
  		$sql = "SELECT ias.item_id, p.pickup_id, mr.material_request_id, ias.item_name, ias.item_specification, mr.material_request_number, pd.unit_abbr, IFNULL(p.pickup_number,'') AS pickup_number, pd.quantity_taked, IFNULL((pd.unit_price),0) AS unit_price, mrd.status, mrd.material_request_detail_id 
			FROM ki_material_request mr 
			LEFT JOIN ki_material_request_detail mrd ON(mrd.material_request_id = mr.material_request_id) 
			LEFT JOIN ki_pickup_detail pd ON(pd.material_request_detail_id = mrd.material_request_detail_id) 
			LEFT JOIN ki_pickup p ON(p.pickup_id = pd.pickup_id)
			LEFT JOIN ki_item_and_service ias ON(ias.item_id = mrd.item_id) 
			WHERE mr.job_order_id = '".$jobOrder."' AND mrd.is_purchase_request = 2
			ORDER BY mr.material_request_number ASC";
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

	//list Detail Job Order Purchase Request
  	function getDetailJOPR($conn, $jobOrder){
  		$sql = "SELECT ias.item_id, p.pickup_id, mr.material_request_id, ias.item_name, ias.item_specification, mr.material_request_number, pd.unit_abbr, 
					   IFNULL(p.pickup_number,'') AS pickup_number, pd.quantity_taked, 
					   IFNULL(mrd.unit_price_buy,0) AS unit_price, mrd.discount, po.purchase_order_number, mrd.quantity_purchase_request, po.purchase_order_id, mrd.unit_price_buy, mrd.status, mrd.material_request_detail_id
		FROM ki_material_request mr 
		LEFT JOIN ki_material_request_detail mrd ON(mrd.material_request_id = mr.material_request_id) 
		LEFT JOIN ki_pickup_detail pd ON(pd.material_request_detail_id = mrd.material_request_detail_id) 
		LEFT JOIN ki_pickup p ON(p.pickup_id = pd.pickup_id)
		LEFT JOIN ki_purchase_order po ON(po.purchase_order_id = mrd.purchase_order_id)
		LEFT JOIN ki_item_and_service ias ON(ias.item_id = mrd.item_id) 
		WHERE mr.job_order_id = '".$jobOrder."' AND mrd.is_purchase_request = 1 AND (mrd.approval_status1 = 'Approved' AND mrd.approval_status2 = 'Approved' AND mrd.approval_status3 = 'Approved')
		ORDER BY mr.material_request_number ASC";
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

	//list Detail Job Order tools request
  	function getDetailJOTR($conn, $jobOrder){
  		$sql = "SELECT a.asset_rental_id AS assetRental, a.quantity AS Qty, a.amount AS price, b.asset_rental_name AS assetName, b.description AS assetDesc, a.unit_abbr AS unit
		FROM ki_resources_request_detail a
		INNER JOIN ki_asset_rental b ON(a.asset_rental_id = b.asset_rental_id)
		INNER JOIN ki_resources_request rr ON(rr.resources_request_id = a.resources_request_id)
		WHERE rr.job_order_id = '".$jobOrder."' AND approval_status1='Approved' AND approval_status2='Approved' AND approval_status3='Approved'";
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

	//list Detail Job Order Man Power Temporary
  	function getDetailJOManPowerTemp($conn, $jobOrder){
  		$sql = "SELECT ER.employee_report_id, CONCAT(ER.employee,' / ',ER.month_year) AS year, SUM(ER.basic_salary) AS GP, SUM(ER.overtime + ER.emergency_call) AS GL, SUM(ER.transport_allowance + ER.meal_allowance + ER.welfare_allowance + ER.overtime_meal + ER.profesional_allowance + ER.jamsostek_allowance + ER.bpjs_allowance + ER.jpk + ER.less_payment) AS Tunjangan,SUM(ER.person) AS jumlah, SUM(ER.location_project_allowance + ER.official_travel_allowance) AS project_location, SUM(ER.jamsostek_paid + ER.bpjs_paid + ER.jht + ER.bpjs + ER.pph1 + ER.moneybox + ER.other_deduction + ER.cooperative + ER.loan_cooperative + ER.absent + ER.jpk_paid) AS jumlah_potongan
		FROM ki_employee_report as ER
		WHERE job_order_id = '".$jobOrder."' AND ER.employee_status ='Temporary'
		GROUP BY ER.employee_report_id
		ORDER BY ER.employee_report_id ASC";
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

	//list Detail Job Order Man Power Permanen Kontrak
  	function getDetailJOManPowerPermanenKontrak($conn, $jobOrder){
  		$sql = "SELECT ER.employee_report_id, ER.month_year AS year, SUM(ER.basic_salary) AS GP, SUM(ER.absent) AS AB, SUM(ER.overtime + ER.emergency_call) AS GL, SUM(ER.transport_allowance + ER.meal_allowance + ER.welfare_allowance + ER.overtime_meal + ER.profesional_allowance + ER.jamsostek_allowance + ER.bpjs_allowance + ER.jpk + ER.less_payment) AS Tunjangan, SUM(ER.person) AS jumlah, SUM(ER.location_project_allowance + ER.official_travel_allowance) AS project_location
		FROM ki_employee_report as ER
		WHERE job_order_id = '".$jobOrder."' AND ER.employee_status !='Temporary'
		GROUP BY ER.month_year
		ORDER BY ER.employee_report_id ASC";
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

	//list Detail Job Order COD
  	function getDetailJOCOD($conn, $jobOrder){
		$sql = "SELECT cod.cash_on_delivery_number, cod.approval_date1, codd.item_name AS nama_item, codd.quantity AS qunty, codd.unit_abbr AS abbr, codd.unit_price AS harga, codd.discount AS discount
		FROM ki_cash_on_delivery_detail codd
		INNER JOIN ki_cash_on_delivery cod ON(codd.cash_on_delivery_id = cod.cash_on_delivery_id)
		where cod.job_order_id = '".$jobOrder."' AND codd.cod_app1='Approved'";
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

	//list Detail Job Order WO
  	function getDetailJOWO($conn, $jobOrder){
		$sql = "SELECT PS.purchase_service_number, WOD.work_order_id, WOD.purchase_service_id, WOD.item_name, WOD.wo_notes, WOD.quantity, WOD.unit_abbr, WOD.unit_price, WO.work_order_id, WO.work_order_number, WOD.discount, PS.purchase_service_id, PS.purchase_quotation_date AS WO_Date
			FROM ki_work_order_detail as WOD
			LEFT JOIN ki_work_order as WO ON WOD.work_order_id = WO.work_order_id
			LEFT JOIN ki_purchase_service as PS ON PS.purchase_service_id = WOD.purchase_service_id
			WHERE WO.job_order_id = '".$jobOrder."' AND wo_app1='Approved' AND ps_app1='Approved'";
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

	//list Detail Job Order PB Half
  	function getDetailJOPBHalf($conn, $jobOrder){
		$sql = "SELECT CA.cash_advance_id, CA.cash_advance_number, CA.rest_from, IFNULL(ABS(CA.rest_value), 0) AS rest_value
		FROM ki_cash_advance CA
		WHERE CA.done = '1' AND CA.cash_advance_id NOT IN (select cash_advance_id from ki_responsbility_advance) AND CA.job_order_id = '".$jobOrder."'";
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

	//list Detail Job Order PB
  	function getDetailJOPB($conn, $jobOrder){
		$sql = "SELECT ca.cash_advance_id, ca.cash_advance_number, ca.rest_from, ca.rest_value
		FROM ki_cash_advance ca
		WHERE ca.done = '1' AND ca.job_order_id = '".$jobOrder."'";
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

	//list Detail Job Order PB RestFrom
  	function getDetailJOPBRestFrom($conn, $jobOrder){
		$sql = "SELECT cad.cash_advance_detail_id, cad.item_name, cad.quantity, cad.unit_abbr, cad.unit_price, cad.advance_app1, cad.advance_app2, cad.advance_app3
		FROM ki_cash_advance_detail cad
		WHERE cad.cash_advance_id = '".$jobOrder."'";
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

	//list Detail Job Order CPR
  	function getDetailJOCPR($conn, $jobOrder){
		$sql = "SELECT ra.responsbility_advance_id, ra.responsbility_advance_number, ca.cash_advance_number
		FROM ki_responsbility_advance ra
		LEFT JOIN ki_cash_advance ca ON ra.cash_advance_id = ca.cash_advance_id
		WHERE ca.job_order_id = '".$jobOrder."'";
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

	//list Detail Job Order CPR RestFrom
  	function getDetailJOCPRRestFrom($conn, $jobOrder){
		$sql = "SELECT rad.responsbility_advance_detail, rad.usage_date, rad.item_name, rad.quantity, rad.unit_abbr, rad.unit_price, rad.discount
		FROM ki_responsbility_advance_detail rad
		WHERE responsbility_advance_id = '".$jobOrder."'";
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

	//list Detail Job Order Expenses
  	function getDetailJOExpenses($conn, $jobOrder){
		$sql = "SELECT ED.expenses_detail_id, ED.expenses_id, ED.item_name, ED.amount, BTT.bank_transaction_type_name, E.expenses_id, E.expenses_number, E.expenses_desc, BA.bank_account_name, E.begin_date
		FROM ki_expenses_detail as ED
		LEFT JOIn ki_bank_transaction_type as BTT ON BTT.bank_transaction_type_id = ED.bank_transaction_type_id
		LEFT JOIN ki_expenses as E ON E.expenses_id = ED.expenses_id
		LEFT JOIN ki_bank_account as BA ON BA.bank_account_id = E.bank_account_id
		WHERE ED.job_order_id = '".$jobOrder."' AND E.done = 1 AND ED.expenses_app1 = 'Approved'";
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

	//list Detail Job Order Invoice
  	function getDetailJOInvoice($conn, $jobOrder){
		$sql = "SELECT soi.sales_order_invoice_id, soi.sales_order_invoice_number, soi.invoice_date, soi.sales_order_invoice_description, jor.job_progress_report_number, soi.service_amount, soi.sales_order_invoice_status AS status, soi.payment_date AS payment_later
		FROM ki_sales_order_invoice soi 
		LEFT JOIN ki_job_progress_report jor ON soi.job_progress_report_id = jor.job_progress_report_id
		WHERE soi.job_order_id = '".$jobOrder."'
		ORDER BY sales_order_invoice_id  ASC";
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

	//list Detail Job Order material return
  	function getDetailJOMaterialReturn($conn, $jobOrder){
		$sql = "SELECT mr.material_return_id, mr.material_return_number 
		FROM ki_material_return mr 
		WHERE mr.job_order_id = '".$jobOrder."' 
		ORDER BY mr.material_return_id DESC";
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

	//list Detail Job Order material return detail
  	function getDetailJOMaterialReturnDetail($conn, $jobOrder){
		$sql = "SELECT mrd.material_return_detail_id, ias.item_name, ias.item_specification, mrd.quantity, mrd.unit_abbr, mrd.unit_price_stock
		FROM ki_material_return_detail mrd
		LEFT JOIN ki_item_and_service ias ON ias.item_id = mrd.item_id
		WHERE mrd.material_return_id = '".$jobOrder."'";
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

	//list Detail Job Order material return detail
  	function getTotalDetailJO($conn, $jobOrder){
        $row = array();
        $rowResult = array();
        $rowResult["mr"] = 0;
        $rowResult["pr"] = 0;
        $rowResult["tr"] = 0;
        $rowResult["manPowerTemporary"] = 0;
        $rowResult["manPowerKontrak"] = 0;
        $rowResult["manPower"] = 0;
        $rowResult["cod"] = 0;
        $rowResult["wo"] = 0;
        $rowResult["pbHalf"] = 0;
        $rowResult["pb"] = 0;
        $rowResult["cpr"] = 0;
        $rowResult["expenses"] = 0;
        $rowResult["invoice"] = 0;
        $rowResult["payment"] = 0;
        $rowResult["matret"] = 0;

        //total MR
		$sql = "SELECT IFNULL(SUM(pd.quantity_taked*pd.unit_price), 0) AS result
		FROM ki_material_request mr 
		LEFT JOIN ki_material_request_detail mrd ON(mrd.material_request_id = mr.material_request_id) 
		LEFT JOIN ki_pickup_detail pd ON(pd.material_request_detail_id = mrd.material_request_detail_id)
		WHERE mr.job_order_id = '".$jobOrder."' AND mrd.is_purchase_request = 2";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        if($count>0){
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
			$rowResult["mr"] = $row["result"];
        }else{
            $rowResult["mr"] = 0;
        }

        //total PR
        $sql = "SELECT IFNULL(SUM((pd.quantity_taked*mrd.unit_price_buy)-mrd.discount), 0) AS result
		FROM ki_material_request mr 
		LEFT JOIN ki_material_request_detail mrd ON(mrd.material_request_id = mr.material_request_id) 
		LEFT JOIN ki_pickup_detail pd ON(pd.material_request_detail_id = mrd.material_request_detail_id) 
		LEFT JOIN ki_pickup p ON(p.pickup_id = pd.pickup_id)
		LEFT JOIN ki_purchase_order po ON(po.purchase_order_id = mrd.purchase_order_id)
		LEFT JOIN ki_item_and_service ias ON(ias.item_id = mrd.item_id) 
		WHERE mr.job_order_id = '".$jobOrder."' AND mrd.is_purchase_request = 1 AND (mrd.approval_status1 = 'Approved' AND mrd.approval_status2 = 'Approved' AND mrd.approval_status3 = 'Approved')";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        if($count>0){
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
            $rowResult["pr"] = $row["result"];
        }else{
            $rowResult["pr"] = 0;
        }

        //total TR
        $sql = "SELECT IFNULL(a.asset_rental_id, '0') AS assetRental, IFNULL(SUM(a.quantity*a.amount), 0) AS result
		FROM ki_resources_request_detail a
		INNER JOIN ki_asset_rental b ON(a.asset_rental_id = b.asset_rental_id)
		INNER JOIN ki_resources_request rr ON(rr.resources_request_id = a.resources_request_id)
		WHERE rr.job_order_id = '".$jobOrder."' AND approval_status1='Approved' AND approval_status2='Approved' AND approval_status3='Approved'";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        if($count>0){
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
            $rowResult["tr"] = $row["result"];
        }else{
            $rowResult["tr"] = 0;
        }

        //total Man power temp
        $sql = "SELECT IFNULL(SUM(ER.basic_salary), 0) AS GP, IFNULL(SUM(ER.overtime + ER.emergency_call), 0) AS GL, IFNULL(SUM(ER.transport_allowance + ER.meal_allowance + ER.welfare_allowance + ER.overtime_meal + ER.profesional_allowance + ER.jamsostek_allowance + ER.bpjs_allowance + ER.jpk + ER.less_payment), 0) AS Tunjangan, IFNULL(SUM(ER.person), 0) AS jumlah, IFNULL(SUM(ER.location_project_allowance + ER.official_travel_allowance), 0) AS project_location, IFNULL(SUM(ER.jamsostek_paid + ER.bpjs_paid + ER.jht + ER.bpjs + ER.pph1 + ER.moneybox + ER.other_deduction + ER.cooperative + ER.loan_cooperative + ER.absent + ER.jpk_paid), 0) AS jumlah_potongan
		FROM ki_employee_report as ER
		WHERE job_order_id = '".$jobOrder."' AND ER.employee_status ='Temporary'";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        if($count>0){
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
            $rowResult["manPowerTemporary"] = $row["GP"] + $row["GL"] + $row["Tunjangan"];
        }else{
            $rowResult["manPowerTemporary"] = 0;
        }

        //total Man power kontrak
        $sql = "SELECT IFNULL(SUM(ER.basic_salary), 0) AS GP, IFNULL(SUM(ER.absent), 0) AS AB, IFNULL(SUM(ER.overtime + ER.emergency_call), 0) AS GL, IFNULL(SUM(ER.transport_allowance + ER.meal_allowance + ER.welfare_allowance + ER.overtime_meal + ER.profesional_allowance + ER.jamsostek_allowance + ER.bpjs_allowance + ER.jpk + ER.less_payment), 0) AS Tunjangan, IFNULL(SUM(ER.person), 0) AS jumlah, IFNULL(SUM(ER.location_project_allowance + ER.official_travel_allowance), 0) AS project_location
		FROM ki_employee_report as ER
		WHERE job_order_id = '".$jobOrder."' AND ER.employee_status !='Temporary'";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        if($count>0){
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
			$rowResult["manPowerKontrak"] = $row["GP"] - $row["AB"] + $row["GL"] + $row["Tunjangan"] + $row["project_location"];
        }else{
            $rowResult["manPowerKontrak"] = 0;
        }

        //total man power
        $rowResult["manPower"] = $rowResult["manPowerKontrak"] + $rowResult["manPowerTemporary"];

        //total COD
        $sql = "SELECT IFNULL(SUM((codd.quantity*codd.unit_price)-codd.discount), 0) AS result
		FROM ki_cash_on_delivery_detail codd
		INNER JOIN ki_cash_on_delivery cod ON(codd.cash_on_delivery_id = cod.cash_on_delivery_id)
		where cod.job_order_id = '".$jobOrder."' AND codd.cod_app1='Approved'";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        if($count>0){
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
            $rowResult["cod"] = $row["result"];
        }else{
            $rowResult["cod"] = 0;
        }

        //total WO
        $sql = "SELECT IFNULL(SUM((WOD.quantity*WOD.unit_price)-WOD.discount), 0) AS result
			FROM ki_work_order_detail as WOD
			LEFT JOIN ki_work_order as WO ON WOD.work_order_id = WO.work_order_id
			LEFT JOIN ki_purchase_service as PS ON PS.purchase_service_id = WOD.purchase_service_id
			WHERE WO.job_order_id = '".$jobOrder."' AND wo_app1='Approved' AND ps_app1='Approved'";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        if($count>0){
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
            $rowResult["wo"] = $row["result"];
        }else{
            $rowResult["wo"] = 0;
        }

        //total PB Half
        $sql = "SELECT IFNULL(SUM(ABS(CA.rest_value)), 0) AS result
		FROM ki_cash_advance CA
		WHERE CA.done = '1' AND CA.cash_advance_id NOT IN (select cash_advance_id from ki_responsbility_advance) AND ca.job_order_id = '".$jobOrder."'";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        if($count>0){
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
            $rowResult["pbHalf"] = $row["result"];
        }else{
            $rowResult["pbHalf"] = 0;
        }

        //total PB
        $rowpb["pbRest"] = 0;
        $rowpb["pbRestDetail"] = 0;
        $sql = "SELECT SUM(ca.rest_value) AS result_rest
		FROM ki_cash_advance ca
		WHERE ca.done = '1' AND ca.job_order_id = '".$jobOrder."'";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        if($count>0){
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
            $rowpb["pbRest"] = $row["result_rest"];
        }else{
            $rowpb["pbRest"] = 0;
        }

        $sql = "SELECT IFNULL(SUM(cad.quantity * cad.unit_price), 0) AS result
		FROM ki_cash_advance_detail cad
        LEFT JOIN ki_cash_advance ca ON ca.cash_advance_id = cad.cash_advance_id
		WHERE ca.job_order_id = '".$jobOrder."' AND ca.done = '1'";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        if($count>0){
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
            $rowpb["pbRestDetail"] = $row["result"];
        }else{
            $rowpb["pbRestDetail"] = 0;
        }

        $rowResult["pb"] = $rowpb["pbRestDetail"]+$rowpb["pbRest"];

        //total CPR
        $sql = "SELECT IFNULL(SUM((rad.quantity*rad.unit_price)-rad.discount), 0) AS result
		FROM ki_responsbility_advance_detail rad
        LEFT JOIN ki_responsbility_advance ra ON ra.responsbility_advance_id = rad.responsbility_advance_id
        LEFT JOIN ki_cash_advance ca ON ra.cash_advance_id = ca.cash_advance_id
		WHERE ca.job_order_id = '".$jobOrder."'";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        if($count>0){
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
            $rowResult["cpr"] = $row["result"];
        }else{
            $rowResult["cpr"] = 0;
        }

        //total Expenses
        $sql = "SELECT IFNULL(SUM(ED.amount), 0) AS result
		FROM ki_expenses_detail as ED
		LEFT JOIn ki_bank_transaction_type as BTT ON BTT.bank_transaction_type_id = ED.bank_transaction_type_id
		LEFT JOIN ki_expenses as E ON E.expenses_id = ED.expenses_id
		LEFT JOIN ki_bank_account as BA ON BA.bank_account_id = E.bank_account_id
		WHERE ED.job_order_id = '".$jobOrder."' AND E.done = 1 AND ED.expenses_app1 = 'Approved'";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        if($count>0){
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
            $rowResult["expenses"] = $row["result"];
        }else{
            $rowResult["expenses"] = 0;
        }

        //total invoice
        $sql = "SELECT IFNULL(SUM(soi.service_amount), 0) AS result
		FROM ki_sales_order_invoice soi 
		LEFT JOIN ki_job_progress_report jor ON soi.job_progress_report_id = jor.job_progress_report_id
		WHERE soi.job_order_id = '".$jobOrder."'";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        if($count>0){
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
            $rowResult["invoice"] = $row["result"];
        }else{
            $rowResult["invoice"] = 0;
        }

        //total payment
        $sql = "SELECT IFNULL(SUM(soi.service_amount), 0) AS result
		FROM ki_sales_order_invoice soi 
		LEFT JOIN ki_job_progress_report jor ON soi.job_progress_report_id = jor.job_progress_report_id
		WHERE soi.job_order_id = '".$jobOrder."' AND soi.sales_order_invoice_status = 'Paid'";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        if($count>0){
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
            $rowResult["payment"] = $row["result"];
        }else{
            $rowResult["payment"] = 0;
        }

        //total Matret
        $sql = "SELECT IFNULL(SUM(mrd.quantity*mrd.unit_price_stock), 0) AS result
		FROM ki_material_return_detail mrd
		LEFT JOIN ki_item_and_service ias ON ias.item_id = mrd.item_id
        LEFT JOIN ki_material_return mr ON mr.material_return_id = mrd.material_return_id
		WHERE mr.job_order_id = '".$jobOrder."'";
        $qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        if($count>0){
			$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
            $rowResult["matret"] = $row["result"];
        }else{
            $rowResult["matret"] = 0;
        }

		// mysqli_close($conn);
		return $rowResult;
	}
  
	//list sales order detail
	function getSalesOrderDetail($conn, $jobOrder){
		$sql = "SELECT jo.job_order_id, jo.job_order_number, jo.job_order_description, jo.sales_order_id, jo.budgeting_amount, jo.amount
		FROM ki_job_order jo
		WHERE jo.sales_order_id = '".$jobOrder."'
		ORDER BY jo.job_order_id DESC";
		$qur = mysqli_query($conn, $sql);

        $count = mysqli_num_rows($qur);
        $row = array();

        if($count>0){
            while($r = mysqli_fetch_assoc($qur)) {
            	$newRow = array();
            	$newRow = getTotalDetailJO($conn, $r["job_order_id"]);
            	$newRow['job_order_number'] = $r["job_order_number"];
            	$newRow['job_order_description'] = $r["job_order_description"];
            	$newRow['budgeting_amount'] = $r["budgeting_amount"];
            	$newRow['amount'] = $r["amount"];
                $row[] = $newRow;
            }
        }else{
            $row = array();
        }
		mysqli_close($conn);
		return $row;
	}

	//list Detail SPKL
  	function getDetailSpkl($conn, $jobOrder){
		$sql = "SELECT owd.otwo_detail_id, emp.fullname, jg.job_grade_name, owd.overtime_date, owd.description, owd.start_time, owd.finish_time, owd.approval_status1, owd.approval_status2
		FROM ki_overtime_workorder_detail owd
		LEFT JOIN ki_employee emp ON owd.employee_id = emp.employee_id
		LEFT JOIN ki_employee_grade eg ON emp.employee_grade_id = eg.employee_grade_id 
		LEFT JOIN ki_job_grade jg ON eg.job_grade_id = jg.job_grade_id
		WHERE owd.overtime_workorder_id = '".$jobOrder."'
		ORDER BY otwo_detail_id ASC";
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

	//add SPKL detail
  	function addSpkldetail($conn, $overtimeWorkorderId, $employeeId, $overtimeDate, $description, $startTime, $finishTime){
		$sql = "INSERT INTO `ki_overtime_workorder_detail` (`otwo_detail_id`, `overtime_workorder_id`, `employee_id`, `overtime_date`, `description`, `start_time`, `finish_time`, `approval_status1`, `approval_status2`) VALUES (NULL, '".$overtimeWorkorderId."', '".$employeeId."', '".$overtimeDate."', '".$description."', '".$startTime."', '".$finishTime."', '-', '-')";
        $qur = mysqli_query($conn, $sql);
	}

	//add SPKL
  	function addSpkl($conn, $number, $proposedDate, $workDescription, $workLocation, $jobOrder, $departmentId,$requestedId,$createdBy, $employeeId1, $overtimeDate1, $description1, $startTime1, $finishTime1, $employeeId2, $overtimeDate2, $description2, $startTime2, $finishTime2, $employeeId3, $overtimeDate3, $description3, $startTime3, $finishTime3, $employeeId4, $overtimeDate4, $description4, $startTime4, $finishTime4, $employeeId5, $overtimeDate5, $description5, $startTime5, $finishTime5){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		$sql = "INSERT INTO `ki_overtime_workorder` (`overtime_workorder_id`, `overtime_workorder_number`, `proposed_date`, `work_description`, `work_location`, `job_order_id`, `department_id`, `requested_id`, `request_date`, `ordered_by`, `ordered_date`, `approval1_by`, `approval1_date`, `approval2_by`, `approval2_date`, `verified_by`, `verified_date`, `created_by`, `created_date`, `modified_by`, `modified_date`, `overtime_archive`, `overtime_file_name`, `overtime_file_type`) VALUES (NULL, '".$number."', '".$proposedDate."', '".$workDescription."', '".$workLocation."', '".$jobOrder."', '".$departmentId."', '".$requestedId."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '".$createdBy."', '".$date."', NULL, NULL, NULL, NULL, NULL)";
        $qur = mysqli_query($conn, $sql);

        $sql_value = "SELECT overtime_workorder_id, overtime_workorder_number FROM ki_overtime_workorder ORDER BY overtime_workorder_id DESC LIMIT 1";
		$qur_value = mysqli_query($conn, $sql_value);

		$row = mysqli_fetch_array($qur_value, MYSQLI_ASSOC);
		$count = mysqli_num_rows($qur_value);

		$overtimeWorkorderId = $row["overtime_workorder_id"];
		$overtimeWorkorderNumber = $row["overtime_workorder_number"];
		
		if($count>0){
		 	if (isset($employeeId1)) {
		 		addSpkldetail($conn, $overtimeWorkorderId, $employeeId1, $overtimeDate1, $description1, $startTime1, $finishTime1);
		 	} if (isset($employeeId2)) {
		 		addSpkldetail($conn, $overtimeWorkorderId, $employeeId2, $overtimeDate2, $description2, $startTime2, $finishTime2);
		 	} if (isset($employeeId3)) {
		 		addSpkldetail($conn, $overtimeWorkorderId, $employeeId3, $overtimeDate3, $description3, $startTime3, $finishTime3);
		 	} if (isset($employeeId4)) {
		 		addSpkldetail($conn, $overtimeWorkorderId, $employeeId4, $overtimeDate4, $description4, $startTime4, $finishTime4);
		 	} if (isset($employeeId5)) {
		 		addSpkldetail($conn, $overtimeWorkorderId, $employeeId5, $overtimeDate5, $description5, $startTime5, $finishTime5);
		 	}
		 	$row = "Success";
		}else{
		 	$row="Filed create data";
		}

		mysqli_close($conn);
		return $row;
	}

	//list list Detail SPKL
  	function getListDetailSpkl($conn, $jobOrder){
		$sql = "SELECT owd.otwo_detail_id, emp.employee_id, emp.fullname, jg.job_grade_name, owd.overtime_date, owd.description, owd.start_time, owd.finish_time
		FROM ki_overtime_workorder_detail owd
		LEFT JOIN ki_employee emp ON owd.employee_id = emp.employee_id
		LEFT JOIN ki_job_grade jg ON emp.job_grade_id = jg.job_grade_id
		WHERE owd.overtime_workorder_id = '".$jobOrder."'";
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

	//get SPKL number
  	function getSpklNumber($conn){
  		$sql_number = "SELECT * FROM ki_overtime_workorder ORDER BY overtime_workorder_id DESC LIMIT 1";
		$qur_number = mysqli_query($conn, $sql_number);

		$row = mysqli_fetch_array($qur_number, MYSQLI_ASSOC);
		$OWN = $row["overtime_workorder_number"];

		if($OWN!='') {
			$OWNO   = explode('-',$OWN);

			$no_temp = explode('.',$OWNO[2]);

			$OWNOtemp = '';

			$year = date("y");

			if($no_temp[0]==$year) {
				$OWNOtemp = $no_temp[1]+1;
			} else {
				$OWNOtemp = '1';
			}

			$jum = strlen($OWNOtemp); 

			if($jum==1) {
				$RealNO = '000'.$OWNOtemp;  
			} else if($jum==2) {
				$RealNO = '00'.$OWNOtemp;
			} else if($jum==3) {
				$RealNO = '0'.$OWNOtemp;  
			} else {
				$RealNO = $OWNOtemp; 
			}

			$dept   = 'XXX';
			$modul  = 'SPKL';
			$NO   = $year.".".$RealNO;
		} else {
			$dept   = 'XXX';
			$modul   = 'SPKL';
			$NO   = $year.'.0001';
		}

		mysqli_close($conn);
		return $NO;
  	}

	//update SPKL detail
  	function updateSpkldetail($conn, $overtime_date, $description, $start_time, $finish_time, $otwo_detail_id){
		$sql = "UPDATE ki_overtime_workorder_detail owd SET owd.overtime_date = '".$overtime_date."', owd.description = '".$description."', owd.start_time = '".$start_time."', owd.finish_time = '".$finish_time."' WHERE owd.otwo_detail_id = '".$otwo_detail_id."'";
        $qur = mysqli_query($conn, $sql);
	}

	//update SPKL
  	function updateSpkl($conn, $work_description, $proposed_date, $modified_by, $overtime_workorder_id, $employeeId1, $overtime_date1, $description1, $start_time1, $finish_time1, $otwo_detail_id1, $employeeId2, $overtime_date2, $description2, $start_time2, $finish_time2, $otwo_detail_id2, $employeeId3, $overtime_date3, $description3, $start_time3, $finish_time3, $otwo_detail_id3, $employeeId4, $overtime_date4, $description4, $start_time4, $finish_time4, $otwo_detail_id4, $employeeId5, $overtime_date5, $description5, $start_time5, $finish_time5, $otwo_detail_id5){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		$sql = "UPDATE ki_overtime_workorder ow SET ow.work_description = '".$work_description."', ow.proposed_date = '".$proposed_date."', ow.modified_by = '".$modified_by."', ow.modified_date = '".$date."' WHERE ow.overtime_workorder_id = '".$overtime_workorder_id."'";
        $qur = mysqli_query($conn, $sql);

        if (isset($employeeId1)) {
		 	updateSpkldetail($conn, $overtime_date1, $description1, $start_time1, $finish_time1, $otwo_detail_id1);
		} if (isset($employeeId2)) {
		 	updateSpkldetail($conn, $overtime_date2, $description2, $start_time2, $finish_time2, $otwo_detail_id2);
		} if (isset($employeeId3)) {
		 	updateSpkldetail($conn, $overtime_date3, $description3, $start_time3, $finish_time3, $otwo_detail_id3);
		} if (isset($employeeId4)) {
			updateSpkldetail($conn, $overtime_date4, $description4, $start_time4, $finish_time4, $otwo_detail_id4);
		} if (isset($employeeId5)) {
			updateSpkldetail($conn, $overtime_date5, $description5, $start_time5, $finish_time5, $otwo_detail_id5);
		}
		
		$row = "Success";

		mysqli_close($conn);
		return $row;
	}

	//add Job Order
  	function addJobOrder($conn, $job_order_id, $job_order_status, $job_order_type, $job_order_category_id, $job_order_description, $sales_quotation_id, $department_id, $supervisor, $job_order_location, $begin_date, $end_date, $notes, $created_by, $amount, $budgeting_amount, $max_pb_amount, $material_amount, $tools_amount, $man_power_amount, $cod_amount, $wo_amount, $material_return_amount, $pb_amount, $cpr_amount, $expenses_amount, $client_po_number, $tax_type_id, $sales_order_id){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		$sql = "INSERT INTO `ki_job_order` (`job_order_id`, `job_order_number`, `job_order_status`, `job_order_type`, `job_order_category_id`, `job_order_description`, `sales_quotation_id`, `department_id`, `supervisor`, `job_order_location`, `begin_date`, `end_date`, `notes`, `created_by`, `created_date`, `modified_by`, `modified_date`, `amount`, `budgeting_amount`, `max_pb_amount`, `material_amount`, `tools_amount`, `man_power_amount`, `cod_amount`, `wo_amount`, `material_return_amount`, `pb_amount`, `cpr_amount`, `expenses_amount`, `sales_archive`, `sales_file_name`, `sales_file_type`, `client_po_number`, `client_po_archive`, `client_po_file_name`, `client_po_file_type`, `tax_type_id`, `sales_order_id`, `account_job_order`) VALUES ('', '".$job_order_id."', '".$job_order_status."', '".$job_order_type."', '".$job_order_category_id."', '".$job_order_description."', '".$sales_quotation_id."', '".$department_id."', '".$supervisor."', '".$job_order_location."', '".$begin_date."', '".$end_date."', '".$notes."', '".$created_by."', '".$date."', NULL, NULL, '".$amount."', '".$budgeting_amount."', '".$max_pb_amount."', '".$material_amount."', '".$tools_amount."', '".$man_power_amount."', '".$cod_amount."', '".$wo_amount."', '".$material_return_amount."', '".$pb_amount."', '".$cpr_amount."', '".$expenses_amount."', '', NULL, NULL, '".$client_po_number."', '', NULL, NULL, '".$tax_type_id."', '".$sales_order_id."', NULL)";
        $qur = mysqli_query($conn, $sql);

        $row = "Success create data";

		mysqli_close($conn);
		return $row;
	}

	//get Job Order number
  	function getJobOrderNumber($conn){
  		$sql_number = "SELECT * FROM ki_job_order ORDER BY job_order_id DESC LIMIT 1";
		$qur_number = mysqli_query($conn, $sql_number);

		$row = mysqli_fetch_array($qur_number, MYSQLI_ASSOC);
		$OWN = $row["job_order_number"];

		if($OWN!='') {
			$OWNO = explode('-',$OWN);

			$no_temp = $OWNO[3];
			$date_temp = $OWNO[2];

			$year_now = date("y");
			$month_now = date("m");
			$year_last = substr($date_temp, 0, 2);
			$month_last = substr($date_temp, 2, 2);

			if ($year_now == $year_last) {
				$new_date = $year_now . $month_now;
				$new_numb = '';

				$no_temp += 1;

				if (strlen($no_temp) == 1)
					$new_numb = '000'.$no_temp;
				else if (strlen($no_temp) == 2)
					$new_numb = '00'.$no_temp;
				else if (strlen($no_temp) == 3)
					$new_numb = '0'.$no_temp;
				else $new_numb = $no_temp;

				$result = $new_date . '-' . $new_numb;
			} else {
				$new_date = $year_now . $month_now;
				$new_numb = '0001';

				$result = $new_date . '-' . $new_numb;
			}
		}

		mysqli_close($conn);
		return $result;
  	}

	//update job order
  	function updateJobOrder($conn, $job_order_id, $job_order_number, $job_order_status, $job_order_type, $job_order_category_id, $job_order_description, $sales_quotation_id, $department_id, $supervisor, $job_order_location, $begin_date, $end_date, $notes, $modified_by, $amount, $budgeting_amount, $material_amount, $tools_amount, $man_power_amount, $cod_amount, $wo_amount, $material_return_amount, $pb_amount, $cpr_amount, $expenses_amount, $client_po_number, $tax_type_id, $sales_order_id){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		$sql = "UPDATE ki_job_order SET job_order_number = '".$job_order_number."', job_order_status = '".$job_order_status."', job_order_type = '".$job_order_type."', job_order_category_id = '".$job_order_category_id."', job_order_description = '".$job_order_description."', sales_quotation_id = '".$sales_quotation_id."', department_id = '".$department_id."', supervisor = '".$supervisor."', job_order_location = '".$job_order_location."', begin_date = '".$begin_date."', end_date = '".$end_date."', notes = '".$notes."', modified_by = '".$modified_by."', modified_date = '".$date."', amount = '".$amount."', budgeting_amount = '".$budgeting_amount."', material_amount = '".$material_amount."', tools_amount = '".$tools_amount."', man_power_amount = '".$man_power_amount."', cod_amount = '".$cod_amount."', wo_amount = '".$wo_amount."', material_return_amount = '".$material_return_amount."', pb_amount = '".$pb_amount."', cpr_amount = '".$cpr_amount."', expenses_amount = '".$expenses_amount."', client_po_number = '".$client_po_number."', tax_type_id = '".$tax_type_id."', sales_order_id = '".$sales_order_id."' WHERE job_order_id = '".$job_order_id."'";
        $qur = mysqli_query($conn, $sql);
		
		$row = "Success";

		mysqli_close($conn);
		return $row;
	}


//function untuk semua view di modul AIS
    function checkViewAccess($conn, $user_id, $feature, $access, $id) {
	    $nilai = 0;

	    //pagar group usergroup
	    $sql_user = "SELECT u.usergroup_id, u.employee_id, e.department_id
	                 FROM ki_user u
	           INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
	           LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
	           WHERE u.user_id = '".$user_id."'";
	    $group_id = '';
	    $emp_id = '';
	    $department_id = '';
	    $qur_user = mysqli_query($conn, $sql_user);
	    while($r_user = mysqli_fetch_assoc($qur_user)) 
	    {           
	      $group_id     = $r_user['usergroup_id'];
	      $emp_id     = $r_user['employee_id'];
	      $department_id  = $r_user['department_id'];
	    }
	    
	    //pagar pertama cek hak akses  
	    $sql_access = "SELECT access_id, access_name, access_ispublic, access_isalluser, access_usergroup_ids 
	    	FROM ki_access
	    	WHERE access_name = '".$access."'";
	    $qur_access = mysqli_query($conn, $sql_access);
	    $is_public         = '';
	    $is_alluser       = '';
	    $access_usergroup_ids   = '';
	    while($r_access = mysqli_fetch_assoc($qur_access))
	    {
	      $is_public         = $r_access['access_ispublic'];
	      $is_alluser        = $r_access['access_isalluser'];
	      $access_usergroup_ids   = $r_access['access_usergroup_ids'];
	    }    

	    $tampung = explode(',',$access_usergroup_ids);
    	$jumlah = count($tampung);
      
    	for($i=0;$i<$jumlah;$i++)
	    {
	      if($tampung[$i]!=0)
	      {
	        $temp[] = $tampung[$i];
	      }  
	    }
	    
	    if($is_public==1)
	    {
	      $nilai = 1;
	    }
	    else if($is_alluser==1)
	    {
	      $nilai = 1;
	    }  
	    else if(in_array($group_id,$temp))
	    {
	      $nilai = 1;
	      //CRM  
	      if($feature=='monitor')  // sq - monitiring - CRM
	      {
	        if($user_id==266)
	        {
	          $nilai = 0;
	        }  
	      }
	      //Project
	      else if($feature=='job-order')
	      {
	        $sql_jo = "SELECT job_order_id, supervisor, department_id
	               FROM ki_job_order
	                 WHERE job_order_id = '".$id."'";
	        $qur_jo = mysqli_query($conn, $sql_jo);
	        $supervisor     = '';
	        $department_id_jo   = '';
	        while($r_jo = mysqli_fetch_assoc($qur_jo))
	        {
	          $supervisor     = $r_jo['supervisor'];
	          $department_id_jo  = $r_jo['department_id'];
	        }    
	      
	        if($user_id==5 || $user_id==34 || $user_id==107 || $user_id==17 
	        || $user_id==19 || $user_id==1 || $user_id==35 || $user_id==166 
	        || $user_id==181 || $user_id==154 || $user_id==300)
	        {
	          $nilai = 1;
	        }  
	        else
	        {
	          //employee yang bisa lihat JO
	          if($emp_id==1 || $emp_id==5 || $emp_id==393 || $emp_id==77 || $emp_id==149 
	          || $emp_id==85 || $emp_id==57 || $emp_id==3234 || $emp_id==3249
	          || $emp_id==1695 || $emp_id==2111 || $emp_id==1955 || $emp_id==3133 
	          || $emp_id==2063 || $emp_id==3533 || $emp_id==3535)
	          {
	            $nilai = 1;
	          }  
	          else
	          {
	            if($emp_id==$supervisor)
	            {
	              $nilai = 1;
	            }    
	            else
	            {
	              if($department_id==$department_id_jo)
	              {
	                $nilai = 1;
	            
	                //termasuk aria dingga prehatdhany $employee_id=1557 || 
	                if($emp_id==1354 || $emp_id==1275 || $emp_id==3202 || $emp_id==3538) //ecy andani dan umi
	                {
	                  $nilai = 1;
	                }  
	                else
	                {
	                  $nilai = 0;
	                }    
	              }  
	              else
	              {
	                if($emp_id==1354)
	                {
	                  if($department_id_jo==13)
	                  {
	                    $nilai = 1;
	                  }  
	                }  
	                else
	                {  
	                  $nilai = 0;
	                }
	              }  
	            }  
	          }  
	        }
	    
	        //jo khusus presiden direktur
	        if($id==1371)
	        {
	          //pak alfan dan admin
	          if($user_id==1 || $user_id==5 || $emp_id==1)
	          {
	          	$nilai = 1;
	          }  
	          else
	          {
	            $nilai = 0;  
	          }  
	        }  
	    
	        //special case pak bagus ardianto / pak totok mardianto
	        //branch pasuruan
	        if($department_id_jo==19) 
	        {
	          if($emp_id==149 || $emp_id==289)
	          {
	            $nilai = 1;
	          }
	          //else
	          //{
	          //  $nilai = 0;
	          //}  
	        }    
	      }
	      else if($feature=='cash-advance')
	      {
	        $sql_ca = "SELECT *, ca.job_order_id AS joid, 
	                  e.employee_id AS created_by 
	                FROM ki_cash_advance ca
	                INNER JOIN ki_user u ON(ca.created_by = u.user_id)
	                INNER JOIN ki_employee e ON(e.employee_id = u.employee_id)
	                INNER JOIN ki_department d ON(d.department_id = e.department_id)
	                INNER JOIN ki_usergroup UG ON(u.usergroup_id = UG.usergroup_id)
	                WHERE ca.cash_advance_id = '".$id."'";
	        $qur_ca = mysqli_query($conn, $sql_ca);
	        $department_created = '';
	        $department_created_head = '';  
	        $department_employee_id = '';
	        $job_id = '';
	        $created_by = '';
	        while($r_ca = mysqli_fetch_assoc($qur_ca))
	        {
	          $department_created     = $r_ca['department_name'];      
	          $department_created_head   = $r_ca['department_head_id'];
	          $department_employee_id   = $r_ca['employee_id'];
	          $job_id           = $r_ca['joid'];
	          $created_by          = $r_ca['created_by'];
	        }  
	      
	        $sql_jo = "SELECT job_order_id, supervisor, department_id
	               FROM ki_job_order
	                 WHERE job_order_id = '".$job_id."'";
	        $qur_jo = mysqli_query($conn, $sql_jo);
	        $supervisor     = '';
	        $department_id_jo   = '';
	        while($r_jo = mysqli_fetch_assoc($qur_jo))
	        {
	          $supervisor     = $r_jo['supervisor'];
	          $department_id_jo  = $r_jo['department_id'];
	        }  
	            
	        if(($department_id_jo==$department_id) || ($department_employee_id==$emp_id) 
	          || $group_id == 1 || $group_id == 51 || $group_id == 2  
	          || $group_id == 10 || $group_id == 11 || $group_id == 18 
	          || $group_id == 20 || $group_id == 22 || $group_id == 12 
	          || $group_id == 41 || $group_id == 32)
	        {
	          $nilai = 1;
	        }  
	        else
	        {
	          if($emp_id==$department_created_head)
	          {
	            $nilai = 1;
	          }  
	          else
	          {  
	            if($job_id==789 || $job_id==790 || $job_id==798 || $job_id==800 
	              || $job_id==791 || $job_id==792 || $job_id==794 || $job_id==830)
	            {
	              if($emp_id==289)
	              {
	                $nilai = 1;
	              }  
	              else
	              {
	                $nilai = 0;
	              }  
	            }  
	            else
	            {          
	              if($supervisor==$emp_id)
	              {
	                $nilai = 1;                  
	              }  
	              else
	              {  
	                $nilai = 0;      
	              }
	            }  
	          }
	      
	          //mbak ecy sementara bisa akses civil
	          if($emp_id==1354)
	          {
	            if($department_id_jo==13)
	            {
	              $nilai = 1;
	            }  
	          }  
	        }  
	      }
	      else if($feature=='respons-advance')
	      {
	        $sql_dept = "SELECT *,e.employee_id AS employee_create, ca.job_order_id AS joid 
	        	FROM ki_responsbility_advance ra
	        	INNER JOIN ki_cash_advance ca ON(ca.cash_advance_id = ra.cash_advance_id)
	        	INNER JOIN ki_user u ON(ra.created_by = u.user_id)
	        	INNER JOIN ki_employee e ON(e.employee_id = u.employee_id)
	        	INNER JOIN ki_department d ON(d.department_id = e.department_id)
	        	INNER JOIN ki_usergroup UG ON(u.usergroup_id = UG.usergroup_id)
	        	WHERE ra.responsbility_advance_id = '".$id."'";
	        $qur_dept = mysqli_query($conn, $sql_dept);
	        $department_created = '';
	        $department_created_head = '';  
	        $department_employee_id = '';
	        $job_id = '';
	        $employee_create = '';
	        while($r_dept = mysqli_fetch_assoc($qur_dept))
	        {        
	          $department_created     = $r_dept['department_name'];      
	          $department_created_head   = $r_dept['department_head_id'];
	          $department_employee_id   = $r_dept['employee_id'];
	          $job_id           = $r_dept['joid'];
	          $employee_create       = $r_dept['employee_create'];
	        }  
	          
	        $sql_jo = "SELECT job_order_id, supervisor, department_id
	               FROM ki_job_order
	                 WHERE job_order_id = '".$job_id."'";
	        $qur_jo = mysqli_query($conn, $sql_jo);
	        $supervisor     = '';
	        $department_id_jo   = '';
	        while($r_jo = mysqli_fetch_assoc($qur_jo))
	        {
	          $supervisor     = $r_jo['supervisor'];
	          $department_id_jo  = $r_jo['department_id'];
	        }  
	    
	        if( ($department_id_jo==$department_id) || ($employee_create==$emp_id) 
	          || ($department_employee_id==$emp_id) || $group_id == 1 
	          || $group_id == 51 || $group_id == 2  || $group_id == 10 
	          || $group_id == 11 || $group_id == 18 || $group_id == 20 
	          || $group_id == 22 || $group_id == 12 || $group_id == 41 
	          || $group_id == 32)
	        {
	          $nilai = 1;
	        }  
	        else
	        {
	          if($employee_id==$department_created_head)
	          {
	            $nilai = 1;
	          }  
	          else
	          {  
	            if($job_id==789 || $job_id==790 || $job_id==798 || $job_id==800 
	              || $job_id==791 || $job_id==792 || $job_id==794 || $job_id==830)
	            {
	              if($emp_id==289)
	              {
	                $nilai = 1;
	              }  
	              else
	              {
	                $nilai = 0;
	              }  
	            }  
	            else
	            {
	              $nilai = 0;      
	            }  
	          }
	          //mbak ecy sementara bisa akses civil
	          if($emp_id==1354)
	          {
	            if($department_id_jo==13)
	            {
	              $nilai = 1;
	            }  
	          }  
	        }    
	      }
	      //Marketing
	      else if($feature=='sales-quotation')
	      {
	        if($user_id==266)
	        {
	          $nilai = 0;
	        }        
	      }
	      //HR
	      else if($feature=='employee-leave')
	      {
	        $sql_el = "SELECT e.is_active 
	               FROM ki_employee_leave el
	               INNER JOIN ki_employee e ON(e.employee_id = el.employee_id)
	               WHERE employee_leave_id = '".$id."'";
	        $qur_el = mysqli_query($conn, $sql_el);
	        $is_active = '';
	        while($r_el = mysqli_fetch_assoc($qur_el))
	        {
	          $is_active = $r_el['is_active'];
	        }  
	        
	        if($is_active!=1)
	        {
	          $nilai = 0;
	        }  
	      }
	    }  
	    else
	    {
	      $nilai = 0;
	    }  

	    return $nilai;    
    }
?>
