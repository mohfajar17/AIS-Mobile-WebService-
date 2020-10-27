<?php
	
	function getApprovalAssign($conn, $empId){
		$sql = "SELECT * FROM ki_approval_assign WHERE employee_id = '".$empId."'";
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
		// mysqli_close($conn);
	    return $row;
	}

	function getMaterialRequisition($conn){
		$sql = "SELECT a.material_request_id, a.material_request_number, a.material_request_status, b.job_order_number AS job_order_id, b.job_order_description, c.sales_quotation_number AS sales_quotation_id, DATE(a.requisition_date) AS requisition_date, DATE_FORMAT(a.requisition_date,'%d-%m-%Y') AS created_date, a.usage_date, a.notes, a.version, a.priority, e1.fullname AS created_by, e2.fullname AS modified_by, a.modified_date, e3.fullname AS checked_by, a.checked_date, a.checked_comment, IF(a.approval1!='',e4.fullname,'') AS approval1, a.approval_date1, a.comment1, IF(a.approval2!='',e5.fullname,'') AS approval2, a.approval_date2, a.comment2, IF(a.approval3!='',e6.fullname,'') AS approval3, a.approval_date3, a.comment3, a.material_request_discount_type 
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
		WHERE (a.approval1 IS NULL OR a.approval2 IS NULL OR a.approval3 IS NULL) AND a.material_request_status = 'New'
		ORDER BY material_request_id DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updateMaterialRequisition($conn, $id, $approve1, $approve2, $approve3){
		$sql = "UPDATE ki_material_request_detail mr SET mr.approval_status1 = '".$approve1."', mr.po_app1 = '".$approve1."', mr.approval_status2 = '".$approve2."', mr.po_app2 = '".$approve2."', mr.approval_status3 = '".$approve3."', mr.po_app3 = '".$approve3."' WHERE mr.material_request_detail_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function updateMaterialRequisitionId($conn, $id, $user, $command, $code){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		if ($code == 1) {
			$sql = "UPDATE ki_material_request mr SET mr.approval1 = '".$user."', mr.approval_date1 = '".$date."', mr.comment1 = '".$command."' WHERE mr.material_request_id = '".$id."'";
		} else if ($code == 2) {
			$sql = "UPDATE ki_material_request mr SET mr.approval2 = '".$user."', mr.approval_date2 = '".$date."', mr.comment2 = '".$command."' WHERE mr.material_request_id = '".$id."'";
		} else if ($code == 3) {
			$sql = "UPDATE ki_material_request mr SET mr.approval3 = '".$user."', mr.approval_date3 = '".$date."', mr.comment3 = '".$command."' WHERE mr.material_request_id = '".$id."'";
		}
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function getWorkOrder($conn){
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
			WHERE (a.approval1 IS NULL OR a.approval2 IS NULL OR a.approval3 IS NULL)
			ORDER BY work_order_id DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updateWorkOrder($conn, $id, $approve1, $approve2, $approve3){
		$sql = "UPDATE ki_work_order_detail wo SET wo.wo_app1 = '".$approve1."', wo.wo_app2 = '".$approve2."', wo.wo_app3 = '".$approve3."' WHERE wo.work_order_detail_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function updateWorkOrderId($conn, $id, $user, $command, $code){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		if ($code == 1) {
			$sql = "UPDATE ki_work_order wo SET wo.approval1 = '".$user."', wo.approval_date1 = '".$date."', wo.approval_comment1 = '".$command."' WHERE wo.work_order_id = '".$id."'";
		} else if ($code == 2) {
			$sql = "UPDATE ki_work_order wo SET wo.approval2 = '".$user."', wo.approval_date2 = '".$date."', wo.approval_comment2 = '".$command."' WHERE wo.work_order_id = '".$id."'";
		} else if ($code == 3) {
			$sql = "UPDATE ki_work_order wo SET wo.approval3 = '".$user."', wo.approval_date3 = '".$date."', wo.approval_comment3 = '".$command."' WHERE wo.work_order_id = '".$id."'";
		}
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}
	
	function getSpkl($conn){
		$sql = "SELECT a.overtime_workorder_id, a.overtime_workorder_number, DATE_FORMAT(a.proposed_date,'%d-%m-%Y') AS proposed_date, a.work_description, cw.company_workbase_name AS work_location, b.job_order_number AS job_order_id, c.department_name AS department_id, i.fullname AS requested_id, a.request_date, a.ordered_by, a.ordered_date, IF(a.approval1_by!='',d.fullname,'-') AS approval1_by, a.approval1_date, IF(a.approval2_by!='',e.fullname,'-') AS approval2_by, a.approval2_date, IF(a.verified_by!='',f.fullname,'-') AS verified_by, a.verified_date, IF(a.created_by!='',g.fullname,'') AS created_by, a.created_date, IF(a.modified_by!='',h.fullname,'') AS modified_by, a.modified_date, IFNULL(a.overtime_archive,'') AS overtime_archive, IFNULL(a.overtime_file_name,'') AS overtime_file_name, IFNULL(a.overtime_file_type,'') AS overtime_file_type
			FROM ki_overtime_workorder a
			LEFT JOIN ki_job_order b ON(a.job_order_id = b.job_order_id)
			LEFT JOIN ki_department c ON(a.department_id = c.department_id)
			LEFT JOIN ki_company_workbase cw ON(cw.company_workbase_id = a.work_location)
			LEFT JOIN ki_user u1 ON(u1.user_id = a.approval1_by)
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
            WHERE (a.approval1_by IS NULL OR a.approval2_by IS NULL OR a.verified_by IS NULL)
			ORDER BY overtime_workorder_id DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updateSpkl($conn, $id, $approve1, $approve2){
		$sql = "UPDATE ki_overtime_workorder_detail ow SET ow.approval_status1 = '".$approve1."', ow.approval_status2 = '".$approve2."' WHERE ow.otwo_detail_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function updateSpklId($conn, $id, $user, $code){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		if ($code == 1) {
			$sql = "UPDATE ki_overtime_workorder wo SET wo.approval1_by = '".$user."', wo.approval1_date = '".$date."' WHERE wo.overtime_workorder_id = '".$id."'";
		} else if ($code == 2) {
			$sql = "UPDATE ki_overtime_workorder wo SET wo.approval2_by = '".$user."', wo.approval2_date = '".$date."' WHERE wo.overtime_workorder_id = '".$id."'";
		} else if ($code == 3) {
			$sql = "UPDATE ki_overtime_workorder wo SET wo.verified_by = '".$user."', wo.verified_date = '".$date."' WHERE wo.overtime_workorder_id = '".$id."'";
		}
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}
	
	function getProposedBudget($conn){
		$sql = "SELECT a.cash_advance_id, a.cash_advance_number, b.job_order_number AS job_order_id, b.job_order_description, c.fullname AS person_in_charge, a.requisition_date, IFNULL(a.due_date,'') AS due_date, a.payment_date, a.rest_value, a.rest_from, a.notes, e1.fullname AS created_by, a.created_date, IFNULL(e2.fullname, '') AS modified_by, a.modified_date, IFNULL(e3.fullname, '-') AS approval1, a.approval_date1, a.approval_comment1, a.approval1_status, IFNULL(e4.fullname, '-') AS approval2, a.approval_date2, a.approval_comment2, a.approval2_status, IFNULL(e5.fullname, '-') AS approval3, a.approval_date3, a.approval_comment3, a.approval3_status, e6.fullname AS checked_by, a.checked_date, e7.fullname AS recipient_by, a.done, bt.bank_transaction_type_name AS bank_transaction_type_id, bt.category
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
			WHERE (a.approval1 IS NULL OR a.approval2 IS NULL OR a.approval3 IS NULL)
			ORDER BY cash_advance_id DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function UpdateProposedBudget($conn, $id, $approve1, $approve2, $approve3){
		$sql = "UPDATE ki_cash_advance_detail ca SET ca.advance_app1 = '".$approve1."', ca.advance_app2 = '".$approve2."', ca.advance_app3 = '".$approve3."' WHERE ca.cash_advance_detail_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function UpdateProposedBudgetId($conn, $id, $user, $command, $code){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		if ($code == 1) {
			$sql = "UPDATE ki_cash_advance ca SET ca.approval1 = '".$user."', ca.approval_date1 = '".$date."', ca.approval_comment1 = '".$command."' WHERE ca.cash_advance_id = '".$id."'";
		} else if ($code == 2) {
			$sql = "UPDATE ki_cash_advance ca SET ca.approval2 = '".$user."', ca.approval_date2 = '".$date."', ca.approval_comment2 = '".$command."' WHERE ca.cash_advance_id = '".$id."'";
		} else if ($code == 3) {
			$sql = "UPDATE ki_cash_advance ca SET ca.approval3 = '".$user."', ca.approval_date3 = '".$date."', ca.approval_comment3 = '".$command."' WHERE ca.cash_advance_id = '".$id."'";
		}
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}
	
	function getCashProjectReport($conn){
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
			WHERE (a.approval1 IS NULL OR a.approval2 IS NULL OR a.approval3 IS NULL)
			ORDER BY responsbility_advance_id DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updateCashProjectReport($conn, $id, $approve1, $approve2, $approve3){
		$sql = "UPDATE ki_responsbility_advance_detail ca SET ca.respons_advance_app1 = '".$approve1."', ca.respons_advance_app2 = '".$approve2."', ca.respons_advance_app3 = '".$approve3."' WHERE ca.responsbility_advance_detail = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function updateCashProjectReportId($conn, $id, $user, $command, $code){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		if ($code == 1) {
			$sql = "UPDATE ki_responsbility_advance ca SET ca.approval1 = '".$user."', ca.approval_date1 = '".$date."', ca.approval_comment1 = '".$command."' WHERE ca.responsbility_advance_id = '".$id."'";
		} else if ($code == 2) {
			$sql = "UPDATE ki_responsbility_advance ca SET ca.approval2 = '".$user."', ca.approval_date2 = '".$date."', ca.approval_comment2 = '".$command."' WHERE ca.responsbility_advance_id = '".$id."'";
		} else if ($code == 3) {
			$sql = "UPDATE ki_responsbility_advance ca SET ca.approval3 = '".$user."', ca.approval_date3 = '".$date."', ca.approval_comment3 = '".$command."' WHERE ca.responsbility_advance_id = '".$id."'";
		}
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}
	
	function getTunjanganKaryawan($conn){
		$sql = "SELECT a.employee_allowance_id, a.employee_allowance_number, b.fullname AS employee_id, eg.employee_grade_name, IF(a.additional_allowance_type='9','Tunjangan Lokasi','Tunjangan Perjalanan Dinas') AS additional_allowance_type, d.kab_name AS kab_id, e.job_order_number AS job_order_id, e.job_order_description, e.job_order_location,  DATE_FORMAT(a.begin_date,'%d-%m-%Y') AS begin_date, DATE_FORMAT(a.end_date,'%d-%m-%Y') AS end_date, a.days, a.notes, a.amount_perday, IF(a.approval1_status!='',a.approval1_status,'-') AS approval1_status, IF(a.approval2_status!='',a.approval2_status,'-') AS approval2_status, IF(a.requested_by!='',b.fullname,'') AS requested_by, IF(a.approval1_by!='',e2.fullname,'-') AS approval1_by, a.approval1_date, a.approval1_comment, IF(a.approval2_by!='',e3.fullname,'-') AS approval2_by, a.approval2_date, a.approval2_comment, IF(a.verified_by!='',e1.fullname,'-') AS verified_by, a.verified_date, IF(a.checked_by!='',e5.fullname,'') AS checked_by, a.checked_date, a.checked_comment, IF(a.paid='1','Ya','Tidak') AS paid, IF(a.created_by!='',e6.fullname,'') AS created_by, a.created_date, IF(a.modified_by!='',e7.fullname,'') AS modified_by, a.modified_date
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
			WHERE (a.approval1_by IS NULL OR a.approval2_by IS NULL OR a.verified_by IS NULL)
			ORDER BY employee_allowance_id DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updateTunjanganKaryawan($conn, $id, $approve1, $approve2){
		$sql = "UPDATE ki_employee_allowance ea SET ea.approval1_status = '".$approve1."', ea.approval2_status = '".$approve2."' WHERE ea.employee_allowance_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function updateTunjanganKaryawanId($conn, $id, $user, $command, $code){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		if ($code == 1) {
			$sql = "UPDATE ki_employee_allowance ea SET ea.approval1_by = '".$user."', ea.approval1_date = '".$date."', ea.approval1_comment = '".$command."' WHERE ea.employee_allowance_id = '".$id."'";
		} else if ($code == 2) {
			$sql = "UPDATE ki_employee_allowance ea SET ea.approval1_by = '".$user."', ea.approval2_date = '".$date."', ea.approval2_comment = '".$command."' WHERE ea.employee_allowance_id = '".$id."'";
		} else if ($code == 3) {
			$sql = "UPDATE ki_employee_allowance ea SET ea.verified_by = '".$user."', ea.verified_date = '".$date."' WHERE ea.employee_allowance_id = '".$id."'";
		}
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}
	
	function getTunjanganTemporary($conn){
		$sql = "SELECT a.employee_allowance_id, a.employee_allowance_number, a.employee_name, IF(a.additional_allowance_type='9','Tunjangan Lokasi','Tunjangan Perjalanan Dinas') AS additional_allowance_type, c.kab_name AS kab_id, d.job_order_number AS job_order_id, d.job_order_location, d.job_order_description, DATE_FORMAT(a.begin_date,'%d-%m-%Y') AS begin_date, DATE_FORMAT(a.end_date,'%d-%m-%Y') AS end_date, a.days, a.notes, a.amount_perday, jg.job_grade_name AS employee_grade_id, IF(a.approval1_status!='',a.approval1_status,'-') AS approval1_status, IF(a.approval2_status!='',a.approval2_status,'-') AS approval2_status, e.fullname AS requested_by, IF(a.approval1_by!='',e2.fullname,'-') AS approval1_by, a.approval1_date, a.approval1_comment, IF(a.approval2_by!='',e3.fullname,'-') AS approval2_by, a.approval2_date, a.approval2_comment, IF(a.verified_by!='',e1.fullname,'-') AS verified_by, a.verified_date, IF(a.checked_by!='',e5.fullname,'') AS checked_by, a.checked_date, a.checked_comment, IF(a.paid='1','Ya','Tidak') AS paid, IF(a.created_by!='',e6.fullname,'') AS created_by, a.created_date, IF(a.modified_by!='',e7.fullname,'') AS modified_by, a.modified_date
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
			WHERE (a.approval1_by IS NULL OR a.approval2_by IS NULL OR a.verified_by IS NULL)
			ORDER BY employee_allowance_id DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updateTunjanganTemporary($conn, $id, $approve1, $approve2){
		$sql = "UPDATE ki_employee_allowance_temporary ea SET ea.approval1_status = '".$approve1."', ea.approval2_status = '".$approve2."' WHERE ea.employee_allowance_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function updateTunjanganTemporaryId($conn, $id, $user, $command, $code){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		if ($code == 1) {
			$sql = "UPDATE ki_employee_allowance_temporary ea SET ea.approval1_by = '".$user."', ea.approval1_date = '".$date."', ea.approval1_comment = '".$command."' WHERE ea.employee_allowance_id = '".$id."'";
		} else if ($code == 2) {
			$sql = "UPDATE ki_employee_allowance_temporary ea SET ea.approval2_by = '".$user."', ea.approval2_date = '".$date."', ea.approval2_comment = '".$command."' WHERE ea.employee_allowance_id = '".$id."'";
		} else if ($code == 3) {
			$sql = "UPDATE ki_employee_allowance_temporary ea SET ea.verified_by = '".$user."', ea.verified_date = '".$date."' WHERE ea.employee_allowance_id = '".$id."'";
		}
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}
	
	function getPurchaseOrder($conn){
		$sql = "SELECT a.purchase_order_id, a.purchase_order_number, pot.purchase_order_type_name AS purchase_order_type_id, tt.tax_type_name AS tax_type_id, a.purchase_order_status_id, a.contract_agreement_id, ca.agreement_number AS contract_agreement_id, s.supplier_name AS supplier_id, a.purchase_quotation_number, a.purchase_quotation_date, a.begin_date, a.end_date, pt.term AS payment_term_id, a.payment_desc, a.delivery_address, a.delivery_address2, IFNULL(e0.fullname, '') AS approval_assign_id, a.notes, a.version, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, IFNULL(e4.fullname, '-') AS po_approval1, a.po_approval_date1, a.po_comment1, e5.fullname AS po_approval2, a.po_approval_date2, a.po_comment2, e6.fullname AS po_approval3, a.po_approval_date3, a.po_comment3, IFNULL(e3.fullname, '-') AS checked_by, IFNULL(a.checked_date, '-') AS checked_date, a.purchase_archive, a.purchase_file_name, a.purchase_file_type, a.purchase_order_discount_type, jo.job_order_number, IFNULL(tt.tax_type_rate, '0') AS tax_type_rate
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
			WHERE (a.po_approval1 IS NULL OR a.po_approval2 IS NULL OR a.po_approval3 IS NULL) AND a.purchase_order_status_id = 1
			ORDER BY purchase_order_id DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updatePurchaseOrder($conn, $id, $approve1){
		$sql = "UPDATE ki_material_request_detail mr SET mr.po_app1 = '".$approve1."' WHERE mr.material_request_detail_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function updatePurchaseOrderId($conn, $id, $user, $command, $code){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		if ($code == 1) {
			$sql = "UPDATE ki_purchase_order po SET po.po_approval1 = '".$user."', po.po_approval_date1 = '".$date."', po.po_comment1 = '".$command."' WHERE po.purchase_order_id = '".$id."'";
		} else if ($code == 2) {
			$sql = "UPDATE ki_purchase_order po SET po.approval_assign_id = '".$user."' WHERE po.purchase_order_id = '".$id."'";
		}
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}
	
	function getPurchaseService($conn){
		$sql = "SELECT a.purchase_service_id, a.purchase_service_number, pot.purchase_order_type_name AS purchase_order_type_id, tt.tax_type_name AS tax_type_id, a.purchase_order_status_id, ca.agreement_number AS contract_agreement_id, s.supplier_name AS supplier_id, a.purchase_quotation_number, a.purchase_quotation_date, a.begin_date, a.end_date, pt.term AS payment_term_id, a.payment_desc, IFNULL(e0.fullname, '') AS approval_assign_id, a.notes, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, IFNULL(e4.fullname, '') AS po_approval1, a.po_approval_date1, a.po_comment1, IFNULL(e3.fullname, '-') AS checked_by, IFNULL(a.checked_date, '-') AS checked_date, a.purchase_service_archive, a.purchase_service_file_name, a.purchase_service_file_type, a.purchase_service_discount_type, IFNULL(tt.tax_type_rate, '0') AS tax_type_rate
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
			WHERE (a.approval_assign_id IS NULL OR a.po_approval1 IS NULL)
				  AND a.purchase_order_status_id = 1
			ORDER BY purchase_service_id DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updatePurchaseService($conn, $id, $approve1){
		$sql = "UPDATE ki_work_order_detail wo SET wo.ps_app1 = '".$approve1."' WHERE wo.work_order_detail_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function updatePurchaseServiceId($conn, $id, $user, $command, $code){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		if ($code == 1) {
			$sql = "UPDATE ki_purchase_service po SET po.po_approval1 = '".$user."', po.po_approval_date1 = '".$date."', po.po_comment1 = '".$command."' WHERE po.purchase_service_id = '".$id."'";
		} else if ($code == 2) {
			$sql = "UPDATE ki_purchase_service po SET po.approval_assign_id = '".$user."' WHERE po.purchase_service_id = '".$id."'";
		}
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}
	
	function getCashOnDelivery($conn){
		$sql = "SELECT a.cash_on_delivery_id, a.cash_on_delivery_number, jo.job_order_number AS job_order_id, jo.job_order_description, ub.fullname AS used_by, pot.purchase_order_type_name AS purchase_order_type_id, tt.tax_type_name AS tax_type_id, a.purchase_order_status_id, s.supplier_name AS supplier_id, a.begin_date, a.end_date, pt.term AS payment_term_id, a.payment_desc, a.delivery_address, a.delivery_address2, a.notes, a.version, e1.fullname AS created_by, a.created_date, e2.fullname AS modified_by, a.modified_date, IFNULL(e0.fullname, '') AS approval_assign_id, IFNULL(e4.fullname, '-') AS approval1, a.approval_date1, a.approval_comment1, IFNULL(e3.fullname, '-') AS checked_by, IFNULL(a.checked_date, '-') AS checked_date, a.cod_archive, a.cod_file_name, a.cod_file_type, a.cod_discount_type, IFNULL(tt.tax_type_rate, '0') AS tax_type_rate
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
			WHERE a.purchase_order_status_id = 1 AND (a.approval1 IS NULL OR a.approval_assign_id IS NULL)
			ORDER BY cash_on_delivery_id DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updateCashOnDelivery($conn, $id, $approve1){
		$sql = "UPDATE ki_cash_on_delivery_detail cod SET cod.cod_app1 = '".$approve1."' WHERE cod.cash_on_delivery_detail_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function updateCashOnDeliveryId($conn, $id, $user, $command, $code){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		if ($code == 1) {
			$sql = "UPDATE ki_cash_on_delivery cod SET cod.approval1 = '".$user."', cod.approval_date1 = '".$date."', cod.approval_comment1 = '".$command."' WHERE cod.cash_on_delivery_id = '".$id."'";
		} else if ($code == 2) {
			$sql = "UPDATE ki_cash_on_delivery cod SET cod.approval_assign_id = '".$user."' WHERE cod.cash_on_delivery_id = '".$id."'";
		}
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}
	
	function getMaterialReturn($conn){
		$sql = "SELECT t.material_return_id, t.material_return_number, jo.job_order_number, jo.job_order_description, DATE_FORMAT(t.return_date,'%d-%m-%Y') AS return_date, IF(t.created_by!='',e.fullname,'') AS created_by, IFNULL(t.notes, '') AS notes, IF(t.recognized=1,'Ya','Tidak') AS recognized, IFNULL(t.created_date, '') AS created_date, IFNULL(e2.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date
	      	FROM ki_material_return t
	      	LEFT JOIN ki_job_order jo ON(jo.job_order_id = t.job_order_id)
	     	LEFT JOIN ki_user u ON(u.user_id = t.created_by)
	      	LEFT JOIN ki_employee e ON(u.employee_id = e.employee_id)  
            LEFT JOIN ki_user u2 ON u2.user_id = t.modified_by
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
            WHERE t.recognized != 1
	      	ORDER BY t.material_return_id DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updateMaterialReturnId($conn, $id){
		$sql = "UPDATE ki_material_return mr SET mr.recognized = 1 WHERE mr.material_return_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}
	
	function getStockAdjustment($conn){
		$sql = "SELECT t.stock_adjustment_id, t.adjustment_number, DATE_FORMAT(t.adjustment_date,'%d-%m-%Y') AS adjustment_date, t.short_description, t.notes, IF(t.approval_by!='',e.fullname,'-') AS approval_by, IFNULL(t.approval_date, '-') AS approval_date, t.approval_notes, IFNULL(e1.fullname, '-') AS created_by, IFNULL(t.created_date, '-') AS created_date, IFNULL(e2.fullname, '-') AS modified_by, IFNULL(t.modified_date, '-') AS modified_date
	      	FROM ki_stock_adjustment t
	      	LEFT JOIN ki_user u ON(u.user_id = t.approval_by)
	      	LEFT JOIN ki_employee e ON(u.employee_id = e.employee_id)
            LEFT JOIN ki_user u1 ON u1.user_id = t.created_by
            LEFT JOIN ki_user u2 ON u2.user_id = t.modified_by
            LEFT JOIN ki_employee e1 ON e1.employee_id = u1.employee_id
            LEFT JOIN ki_employee e2 ON e2.employee_id = u2.employee_id
            WHERE (t.approval_by IS NULL)
	      	ORDER BY t.stock_adjustment_id DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updateStockAdjustment($conn, $id, $command){
		$sql = "UPDATE ki_stock_adjustment_detail sa SET sa.notes = '".$command."' WHERE sa.stock_adjustment_detail_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function updateStockAdjustmentId($conn, $id, $user, $command){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		$sql = "UPDATE ki_stock_adjustment sa SET sa.approval_by = '".$user."', sa.approval_date = '".$date."', sa.approval_notes = '".$command."' WHERE sa.stock_adjustment_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}
	
	function getBudgeting($conn){
		$sql = "SELECT t.budget_id, t.budget_number, IF(t.created_by!='',e1.fullname,'-') AS created_by, DATE_FORMAT(t.start_date,'%d-%m-%Y') AS start_date, DATE_FORMAT(t.end_date,'%d-%m-%Y') AS end_date, IF(t.checked_by!='',e2.fullname,'-') AS checked_by, IF(t.approval1!='',e3.fullname,'-') AS approval1, IF(t.approval2!='',e4.fullname,'-') AS approval2, IF(t.approval3!='',e5.fullname,'-') AS approval3, IF(t.done=1,'Ya','Tidak') AS done, IFNULL(t.checked_date, '-') AS checked_date, IFNULL(t.approval_date1, '-') AS approval_date1, t.approval_comment1, IFNULL(t.approval_date2, '-') AS approval_date2, t.approval_comment2, IFNULL(t.approval_date3, '-') AS approval_date3, t.approval_comment3, t.notes, IFNULL(e6.fullname, '-') AS created_by, t.created_date, IFNULL(e7.fullname, '-') AS modified_by, IFNULL(t.modified_date, '-') AS modified_date
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
			WHERE (t.approval1 IS NULL OR t.approval2 IS NULL OR t.approval3 IS NULL)
			ORDER BY t.budget_id DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updateBudgeting($conn, $id, $approve1, $approve2, $approve3){
		$sql = "UPDATE ki_budgeting_detail bg SET bg.budget_app1 = '".$approve1."', bg.budget_app2 = '".$approve2."', bg.budget_app3 = '".$approve3."' WHERE bg.budget_detail_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function updateBudgetingId($conn, $id, $user, $command, $code){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		if ($code == 1) {
			$sql = "UPDATE ki_budgeting bg SET bg.approval1 = '".$user."', bg.approval_date1 = '".$date."', bg.approval_comment1 = '".$command."' WHERE bg.budget_id = '".$id."'";
		} else if ($code == 2) {
			$sql = "UPDATE ki_budgeting bg SET bg.approval2 = '".$user."', bg.approval_date2 = '".$date."', bg.approval_comment2 = '".$command."' WHERE bg.budget_id = '".$id."'";
		} else if ($code == 3) {
			$sql = "UPDATE ki_budgeting bg SET bg.approval3 = '".$user."', bg.approval_date3 = '".$date."', bg.approval_comment3 = '".$command."' WHERE bg.budget_id = '".$id."'";
		}
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}
	
	function getPaymentSupplier($conn){
		$sql = "SELECT t.budget_supplier_id, t.budget_supplier_number, DATE_FORMAT(t.start_date,'%d-%m-%Y') AS start_date, DATE_FORMAT(t.end_date,'%d-%m-%Y') AS end_date, IF(t.checked_by!='',e1.fullname,'-') AS checked_by, IF(t.approval1!='',e2.fullname,'-') AS approval1, IF(t.approval2!='',e3.fullname,'-') AS approval2, IF(t.approval3!='',e4.fullname,'-') AS approval3, IF(t.done=1,'Ya','Tidak') AS done, DATE_FORMAT(t.checked_date,'%d-%m-%Y') AS checked_date, DATE_FORMAT(t.approval_date1,'%d-%m-%Y') AS approval_date1, IFNULL(t.approval_comment1, '') AS approval_comment1, DATE_FORMAT(t.approval_date2,'%d-%m-%Y') AS approval_date2, IFNULL(t.approval_comment2, '') AS approval_comment2, DATE_FORMAT(t.approval_date3,'%d-%m-%Y') AS approval_date3, IFNULL(t.approval_comment3, '') AS approval_comment3, t.notes, IFNULL(e5.fullname, '') AS created_by, IFNULL(t.created_date, '-') AS created_date, IFNULL(e6.fullname, '-') AS modified_by, IFNULL(t.modified_date, '-') AS modified_date
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
			WHERE (t.approval1 IS NULL OR t.approval2 IS NULL OR t.approval3 IS NULL)
			ORDER BY budget_supplier_id DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updatePaymentSupplier($conn, $id, $approve1, $approve2, $approve3){
		$sql = "UPDATE ki_budgeting_supplier_detail bg SET bg.budget_supplier_app1 = '".$approve1."', bg.budget_supplier_app2 = '".$approve2."', bg.budget_supplier_app3 = '".$approve3."' WHERE bg.budget_supplier_detail_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function updatePaymentSupplierId($conn, $id, $user, $command, $code){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		if ($code == 1) {
			$sql = "UPDATE ki_budgeting_supplier bg SET bg.approval1 = '".$user."', bg.approval_date1 = '".$date."', bg.approval_comment1 = '".$command."' WHERE bg.budget_supplier_id = '".$id."'";
		} else if ($code == 2) {
			$sql = "UPDATE ki_budgeting_supplier bg SET bg.approval2 = '".$user."', bg.approval_date2 = '".$date."', bg.approval_comment2 = '".$command."' WHERE bg.budget_supplier_id = '".$id."'";
		} else if ($code == 3) {
			$sql = "UPDATE ki_budgeting_supplier bg SET bg.approval3 = '".$user."', bg.approval_date3 = '".$date."', bg.approval_comment3 = '".$command."' WHERE bg.budget_supplier_id = '".$id."'";
		}
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}
	
	function getBankTransaction($conn){
		$sql = "SELECT bt.bank_transaction_id, bt.bank_transaction_number, IF(bt.checked_by!='',e1.fullname,'-') AS checked_by, IF(bt.approval1!='',e2.fullname,'-') AS approval1, IF(bt.approval2!='',e3.fullname,'-') AS approval2, DATE_FORMAT(bt.transaction_date,'%d-%m-%Y') AS transaction_date, bt.total_amount, IF(bt.status='1','Debet','Kredit') AS status, IF(bt.reconciled='1','Ya','Tidak') AS reconciled, bt.bank_transaction_description, ba.bank_name, ba.bank_account_number, bt.transaction_number, IFNULL(e4.fullname, '') AS reconciled_by, IFNULL(bt.reconciled_date, '') AS reconciled_date, IFNULL(bt.checked_date, '') AS checked_date, IFNULL(bt.checked_comment, '') AS checked_comment, IFNULL(bt.approval_date1, '') AS approval_date1, IFNULL(bt.approval_comment1, '') AS approval_comment1, IFNULL(bt.approval_date2, '') AS approval_date2, IFNULL(bt.approval_comment2, '') AS approval_comment2, e5.fullname AS created_by, bt.created_date, e6.fullname AS modified_by, bt.modified_date, bt.notes, bt.bank_transaction_file_name
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
			WHERE (bt.approval1 IS NULL OR bt.approval2 IS NULL) AND bt.reconciled != 1
			ORDER BY bt.transaction_date DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updateBankTransaction($conn, $id){
		$sql = "UPDATE ki_bank_transaction_detail bt SET bt.ba_app1 = 'Approved', bt.ba_app2 = 'Approved' WHERE bt.bank_transaction_detail_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function updateBankTransactionId($conn, $id, $user, $command, $code){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		if ($code == 1) {
			$sql = "UPDATE ki_bank_transaction bt SET bt.approval1 = '".$user."', bt.approval_date1 = '".$date."', bt.approval_comment1 = '".$command."' WHERE bt.bank_transaction_id = '".$id."'";
		} else if ($code == 2) {
			$sql = "UPDATE ki_bank_transaction bt SET bt.approval2 = '".$user."', bt.approval_date2 = '".$date."', bt.approval_comment2 = '".$command."' WHERE bt.bank_transaction_id = '".$id."'";
		} else if ($code == 3) {
			$sql = "UPDATE ki_bank_transaction bt SET bt.checked_by = '".$user."', bt.checked_date = '".$date."', bt.checked_comment = '".$command."' WHERE bt.bank_transaction_id = '".$id."'";
		}
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}
	
	function getExpense($conn){
		$sql = "SELECT t.expenses_id, t.expenses_number, t.expenses_desc, IF(t.checked_by!='',e1.fullname,'-') AS checked_by, IF(t.approval1!='',e2.fullname,'-') AS approval1, IF(t.approval2!='',e3.fullname,'-') AS approval2, IF(t.advanced_id!='',ad.advanced_number,'') AS advanced_number, DATE_FORMAT(t.begin_date,'%d-%m-%Y') AS expenses_date, SUM(ED.amount) as total_amount, ba.bank_account_name, IF(t.done=1,'Ya','Tidak') AS done, t.notes, IFNULL(t.approval_date1, '') AS approval_date1, IFNULL(t.approval_comment1, '') AS approval_comment1, IFNULL(t.approval_date2, '') AS approval_date2, IFNULL(t.approval_comment2, '') AS approval_comment2, IFNULL(t.checked_date, '') AS checked_date, IFNULL(t.checked_comment, '') AS checked_comment, e4.fullname AS created_by, t.created_date, IFNULL(e5.fullname, '') AS modified_by, IFNULL(t.modified_date, '') AS modified_date, t.expenses_file_name
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
			WHERE (t.approval1 IS NULL OR t.approval2 IS NULL) AND t.done != 1
			GROUP BY t.expenses_id
			ORDER BY t.begin_date DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updateExpense($conn, $id, $approve1, $approve2, $approve3){
		$sql = "UPDATE ki_expenses_detail bg SET bg.expenses_app1 = '".$approve1."', bg.expenses_app2 = '".$approve2."', bg.checked_app = '".$approve3."' WHERE bg.expenses_detail_id = '".$id."'";
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}

	function updateExpenseId($conn, $id, $user, $command, $code){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		if ($code == 1) {
			$sql = "UPDATE ki_expenses bg SET bg.approval1 = '".$user."', bg.approval_date1 = '".$date."', bg.approval_comment1 = '".$command."' WHERE bg.expenses_id = '".$id."'";
		} else if ($code == 2) {
			$sql = "UPDATE ki_expenses bg SET bg.approval2 = '".$user."', bg.approval_date2 = '".$date."', bg.approval_comment2 = '".$command."' WHERE bg.expenses_id = '".$id."'";
		} else if ($code == 3) {
			$sql = "UPDATE ki_expenses bg SET bg.checked_by = '".$user."', bg.checked_date = '".$date."', bg.checked_comment = '".$command."' WHERE bg.expenses_id = '".$id."'";
		}
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}
	
	function getCashAdvance($conn){
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
			WHERE (t.approved_by IS NULL) AND t.status = 'New'
			ORDER BY advanced_id DESC";
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
		// mysqli_close($conn);
	    return $row;
	}

	function updateCashAdvance($conn, $id, $user, $command, $code){
		date_default_timezone_set('Asia/Jakarta');
		$date = date("Y-m-d H:i:s");

		if ($code == 1) {
			$sql = "UPDATE ki_advanced ad SET ad.approved_by = '".$user."', ad.approved_date = '".$date."', ad.approved_status = 'Approved', ad.approved_comment = '".$command."' WHERE ad.advanced_id = '".$id."'";
		} else if ($code == 2) {
			$sql = "UPDATE ki_advanced ad SET ad.reconciled_by = '".$user."', ad.reconciled_date = '".$date."' WHERE ad.advanced_id = '".$id."'";
		}
        $qur = mysqli_query($conn, $sql);

		$row = "Success";
	    return $row;
	}



//-----------------------------------APPROVAL ACCESS------------------------------------------------------------------

	//akses approval 1 MR
  function cekMaterialRequestApproval1($conn, $user_id, $mr_id)
  {
    $sql_mr = "SELECT material_request_id, job_order_id 
           FROM ki_material_request
           WHERE material_request_id ='".$mr_id."'";
    $qur_mr = mysqli_query($conn, $sql_mr);
    $job_order_id = '';
    while($r_mr = mysqli_fetch_assoc($qur_mr))
    {
      $job_order_id = $r_mr['job_order_id'];
    }    

    //$row = mysqli_fetch_array($qur, MYSQLI_ASSOC);
    //$count = mysqli_num_rows($qur);
    
    $sql_user = "SELECT u.usergroup_id, u.employee_id
                 FROM ki_user u
           INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
           LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
           WHERE u.user_id = '".$user_id."'";
    $group_id = '';
    $emp_id = '';
    $qur_user = mysqli_query($conn, $sql_user);
    while($r_user = mysqli_fetch_assoc($qur_user)) 
    {           
      $group_id   = $r_user['usergroup_id'];
      $emp_id   = $r_user['employee_id'];      
    }
    
    $sql_jo = "SELECT jo.job_order_number,jo.department_id,d.department_code,
                      d.department_head_id
           FROM ki_job_order jo
           INNER JOIN ki_material_request mr ON(mr.job_order_id = jo.job_order_id)
           LEFT JOIN ki_department d ON(d.department_id = jo.department_id)
           WHERE mr.material_request_id = '".$mr_id."'";  
    $jo_no = '';
    $department_code = '';
    $department_head_id = '';
    $qur_jo = mysqli_query($conn, $sql_jo);
    while($r_jo = mysqli_fetch_assoc($qur_jo)) 
    {           
      $jo_no         = $r_jo['job_order_number'];
      $department_code   = $r_jo['department_code'];
      $department_head_id = $r_jo['department_head_id'];
    }  
    
    $exp_jo = explode("-",$jo_no);  

    $jum_array = count($exp_jo);

    $code_new = '';
    if($jum_array>2)
    {
      if($department_code!='')
      {
        $code_new = $department_code;
      }
      else
      {    
        $code_new = $exp_jo[1];
      }
    }
    
    $nilai = 0;
    if($job_order_id==789 || $job_order_id==790)
    {
      if($emp_id==7)
      {
        $nilai = 1;
      }    
      else
      {
        if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41)
        {
          $nilai = 1;  
        }
        else
        {
          if($emp_id==$department_head_id)
          {
            $nilai = 1;
          }  
          else
          {
            $nilai = 0;
          }
        }  
      }  
    }
    else if($job_order_id==798 || $job_order_id==800 || $job_order_id==791 
        || $job_order_id==792  || $job_order_id==794 || $job_order_id==830)
    {    
      if($emp_id==289)
      {
        $nilai = 1;
      }
      else 
      {
        if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41)
        {
          $nilai = 1;  
        }  
        else
        {
          if($emp_id==$department_head_id)
          {
            $nilai = 1;
          }  
          else
          {
            $nilai = 0;
          }          
        }  
      }  
    }
    else if($job_order_id==995)
    {
      if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41)
      {
        $nilai = 1;  
      }  
      else
      {
        if($emp_id==$department_head_id)
        {
          $nilai = 1;
        }  
        else
        {
          $nilai = 0;
        }          
      }  
    }  
    else
    {
      if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41)
      {
        $nilai = 1;  
      }  
      else
      {
        if($emp_id==$department_head_id)
        {
          $nilai = 1;
        }  
        else
        {
          if($code_new=='CVL')
          {
            if($emp_id==363)
            {
              $nilai = 1;
            }  
            else
            {
              $nilai = 0;
            }  
          }  
          else if($code_new=='MEC')
          {
            //pak gerrard untuk sementara pengganti pak sulis
            if($emp_id==886 || $emp_id==3469)
            {
            	$nilai = 1;
            }  
            else
            {
            	$nilai = 0;
            }  
          }
          else if($code_new=='ELC')
          {
            if($emp_id==217)
            {
              $nilai = 1;
            }  
            else
            {
              $nilai = 0;
            }  
          }
          
          else if($code_new=='CM')
          {
            //sementara pengganti pak sulis adalah pak gerrard
            if($emp_id==3234 || $emp_id==149 || $emp_id==3469)
            {
              $nilai = 1;
            }    
            else
            {
              $nilai = 0;
            }  
          }
          
          else if($code_new=='HSE')
          {
            if($emp_id==45)
            {
              $nilai = 1;
            }  
            else
            {
              $nilai = 0;
            }  
          }
          else if($code_new=='PRC')
          {
            if($emp_id==432)
            {
              $nilai = 1;
            }  
            else
            {
              $nilai = 0;
            }  
          }  
          else if($code_new=='AFI')
          {
            if($emp_id==5)
            {
              $nilai = 1;
            }  
            else
            {
              $nilai = 0;
            }  
          }  
          else if($code_new=='DRK')
          {
            if($job_order_id==819)
            {
              if($emp_id==393 || $group_id==1 || $group_id==10 || $group_id==51 || $group_id==41)
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
              if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41 || $group_id==12)
              {
                $nilai = 1;
              }  
              else
              {
                $nilai = 0;
              }  
            }
          }  
          else if($code_new=='PTN')
          {
            if($emp_id==296)
            {
              $nilai = 1;
            }  
            else
            {
              $nilai = 0;
            }  
          }  
          else if($code_new=='PSR')
          {
            //hanya pak bagus dan pak totok yang bisa akses JO pasuruan
            if($emp_id==149 || $emp_id==92 || $emp_id==289)
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
      }        
    }

    return $nilai;
  }

//akses approval 2 MR
  function cekMaterialRequestApproval2($conn, $user_id, $mr_id)
  {
    $sql_mr_approval = "SELECT IFNULL(approval1,0) AS approval1, material_request_id, job_order_id FROM ki_material_request WHERE material_request_id ='".$mr_id."'";
    $qur_mr_approval = mysqli_query($conn, $sql_mr_approval);
    $approval1 = 0;
    $job_order_id = '';
    while($r_mr_approval = mysqli_fetch_assoc($qur_mr_approval))
    {
      $approval1     = $r_mr_approval['approval1'];
      $job_order_id   = $r_mr_approval['job_order_id'];
    }    
    
    $nilai = 0;
    if($approval1==0)
    {
      $nilai = 0;
      //output error karena belum approval 1
    }  
    
    $sql_user = "SELECT u.usergroup_id, u.employee_id
                 FROM ki_user u
           INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
           LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
           WHERE u.user_id = '".$user_id."'";
    $group_id = '';
    $emp_id = '';
    $qur_user = mysqli_query($conn, $sql_user);
    while($r_user = mysqli_fetch_assoc($qur_user)) 
    {           
      $group_id   = $r_user['usergroup_id'];
      $emp_id   = $r_user['employee_id'];      
    }
    
    $sql_jo = "SELECT jo.job_order_number,jo.department_id,d.department_code,
                      d.department_head_id
           FROM ki_job_order jo
           INNER JOIN ki_material_request mr ON(mr.job_order_id = jo.job_order_id)
           LEFT JOIN ki_department d ON(d.department_id = jo.department_id)
           WHERE mr.material_request_id = '".$mr_id."'";  
    $jo_no = '';
    $department_code = '';
    $department_head_id = '';
    $qur_jo = mysqli_query($conn, $sql_jo);
    while($r_jo = mysqli_fetch_assoc($qur_jo)) 
    {           
      $jo_no         = $r_jo['job_order_number'];
      $department_code   = $r_jo['department_code'];
      $department_head_id = $r_jo['department_head_id'];
    }  
            
    $exp_jo = explode("-",$jo_no);  

    $jum_array = count($exp_jo);
    if($job_order_id==789 || $job_order_id==790)
    {
      if($emp_id==289)
      {
        $nilai = 1;
      }    
      else
      {
        if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41 || $group_id==12)
        {
          $nilai = 1;  
        }
        else
        {
          if($emp_id==$department_head_id)
          {
            $nilai = 1;
          }  
          else
          {
            //if($emp_id==149 || $emp_id==3234 || $emp_id==3469)
            //sementara pengganti pak sulis adalah pak gerrard  
            if($emp_id==3234 || $emp_id==3469)
            {  
              $nilai = 1;
            }
            else
            {  
              $nilai = 0;
            }
          }
        }  
      }  
    }
    else if($job_order_id==798 || $job_order_id==800 || $job_order_id==791 
        || $job_order_id==792  || $job_order_id==794 || $job_order_id==830)
    {    
      //sementara pengganti pak sulis adalah pak gerrard
      if($emp_id==149 || $emp_id==3234 || $emp_id==3469)
      {
        $nilai = 1;
      }
      else 
      {
        if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41 || $group_id==12)
        {
          $nilai = 1;  
        }  
        else
        {
          if($emp_id==$department_head_id)
          {
            $nilai = 1;
          }  
          else
          {
            $nilai = 0;
          }          
        }  
      }  
    }
    else if($job_order_id==819)
    {
      if($group_id==1 || $group_id==10 || $emp_id==393 || $group_id==51 || $group_id==41 || $group_id==12)
      {
        $nilai = 1;
      }  
      else
      {
        $nilai = 0;
      }  
    }  
    else if($job_order_id==995)
    {
      if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41 || $group_id==12)
      {
        $nilai = 1;  
      }  
      else
      {
        if($emp_id==$department_head_id)
        {
          $nilai = 1;
        }  
        else
        {
          $nilai = 0;
        }          
      }  
    }  
    else
    {
      if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41 || $group_id==12)
      {
        $nilai = 1;  
      }  
      else
      {
        if($emp_id==$department_head_id)
        {
          $nilai = 1;
        }  
        else
        {
          //if($emp_id==149 || $emp_id==3234)
          //sementara pengganti pak sulisa adalah pak gerrard  
          if($emp_id==3234 || $emp_id==3469)
          {
            $nilai = 1;            
          }  
          else
          {  
            $nilai = 0;
          }
        }          
      }        
    }
    return $nilai;
  }

//akses approval 3 MR
  function cekMaterialRequestApproval3($conn, $user_id, $mr_id)
  {
    $sql_mr_approval = "SELECT IFNULL(approval2,0) AS approval2, 
                   material_request_id, job_order_id 
                FROM ki_material_request
                WHERE material_request_id ='".$mr_id."'";
    $qur_mr_approval = mysqli_query($conn, $sql_mr_approval);
    $approval2 = 0;
    $job_order_id = '';
    while($r_mr_approval = mysqli_fetch_assoc($qur_mr_approval))
    {
      $approval2     = $r_mr_approval['approval2'];
      $job_order_id   = $r_mr_approval['job_order_id'];
    }    
    
    $nilai = 0;
    if($approval2==0)
    {
      $nilai = 0;
      //output error karena belum approval 2
    }  
    
    $sql_user = "SELECT u.usergroup_id, u.employee_id
                 FROM ki_user u
           INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
           LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
           WHERE u.user_id = '".$user_id."'";
    $group_id = '';
    $emp_id = '';
    $qur_user = mysqli_query($conn, $sql_user);
    while($r_user = mysqli_fetch_assoc($qur_user)) 
    {           
      $group_id   = $r_user['usergroup_id'];
      $emp_id   = $r_user['employee_id'];      
    }
    
    $sql_jo = "SELECT jo.job_order_number,jo.department_id,d.department_code,
                      d.department_head_id
           FROM ki_job_order jo
           INNER JOIN ki_material_request mr ON(mr.job_order_id = jo.job_order_id)
           LEFT JOIN ki_department d ON(d.department_id = jo.department_id)
           WHERE mr.material_request_id = '".$mr_id."'";  
    $jo_no = '';
    $department_code = '';
    $department_head_id = '';
    $qur_jo = mysqli_query($conn, $sql_jo);
    while($r_jo = mysqli_fetch_assoc($qur_jo)) 
    {           
      $jo_no         = $r_jo['job_order_number'];
      $department_code   = $r_jo['department_code'];
      $department_head_id = $r_jo['department_head_id'];
    }  
        
    $exp_jo = explode("-",$jo_no);  

    $jum_array = count($exp_jo);

    if($job_order_id==789 || $job_order_id==790)
    {
       //sementara pengganti pak sulis adalah pak gerard
      if($emp_id==149 || $emp_id==3234 || $emp_id==3469)
      {
        $nilai = 1;
      }    
      else
      {
        if($group_id==1 || $group_id==10 || $group_id==12 || $group_id==51 || $group_id==41)
        {
          $nilai = 1;  
        }
        else
        {
          $nilai = 0;
        }  
      }  
    }
    else if($job_order_id==819)
    {
      if($group_id==1 || $group_id==10 || $emp_id==393 || $group_id==51 || $group_id==41)
      {
        $nilai = 1;
      }  
      else
      {
        $nilai = 0;
      }  
    }  
    else if($job_order_id==995)
    {
      if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41)
      {
        $nilai = 1;  
      }  
      else
      {
        if($emp_id==$department_head_id)
        {
          $nilai = 1;
        }  
        else
        {
          $nilai = 0;
        }          
      }  
    }  
    else
    {
      if($group_id==1 || $group_id==10 || $group_id==12 || $group_id==51 || $group_id==41)
      {
        $nilai = 1;  
      }
      else
      {
        $nilai = 0;
      }
    }  
    
    return $nilai;
  }


//akses approval 1 WR
  function cekWorkRequestApproval1($conn, $user_id, $wr_id)
  {
    $sql_wr_approval = "SELECT IFNULL(checked_by,0) AS checked_by, 
                   work_order_id, job_order_id 
                FROM ki_work_order
                WHERE work_order_id ='".$wr_id."'";
    $qur_wr_approval = mysqli_query($conn, $sql_wr_approval);
    $checked_by = 0;
    $job_order_id = '';
    while($r_wr_approval = mysqli_fetch_assoc($qur_wr_approval))
    {
      $checked_by     = $r_wr_approval['checked_by'];
      $job_order_id     = $r_wr_approval['job_order_id'];
    }  

    $nilai = 0;
    if($checked_by==0)
    {
      $nilai = 0;
      //output error karena belum di correction
    }  
    
    $sql_user = "SELECT u.usergroup_id, u.employee_id
                 FROM ki_user u
           INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
           LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
           WHERE u.user_id = '".$user_id."'";
    $group_id = '';
    $emp_id = '';
    $qur_user = mysqli_query($conn, $sql_user);
    while($r_user = mysqli_fetch_assoc($qur_user)) 
    {           
      $group_id   = $r_user['usergroup_id'];
      $emp_id   = $r_user['employee_id'];      
    }
    
    $sql_jo = "SELECT jo.job_order_number,jo.department_id,d.department_code,
                      d.department_head_id
           FROM ki_job_order jo
           INNER JOIN ki_work_order wr ON(wr.job_order_id = jo.job_order_id)
           LEFT JOIN ki_department d ON(d.department_id = jo.department_id)
           WHERE wr.work_order_id = '".$wr_id."'";  
    $jo_no = '';
    $department_code = '';
    $department_head_id = '';
    $qur_jo = mysqli_query($conn, $sql_jo);
    while($r_jo = mysqli_fetch_assoc($qur_jo)) 
    {           
      $jo_no         = $r_jo['job_order_number'];
      $department_code   = $r_jo['department_code'];
      $department_head_id = $r_jo['department_head_id'];
    }  
    
    $exp_jo = explode("-",$jo_no);  

    $jum_array = count($exp_jo);
    $code_new = '';
    if($jum_array>2)
    {
      if($department_code!='')
      {
        $code_new = $department_code;
      }
      else
      {    
        $code_new = $exp_jo[1];
      }
    }
    if($job_order_id==789 || $job_order_id==790)
    {
      if($emp_id==7)
      {
        $nilai = 1;
      }    
      else
      {
        if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41 || $group_id==12)
        {
          $nilai = 1;  
        }
        else
        {
          if($emp_id==$department_head_id)
          {
            $nilai = 1;
          }  
          else
          {
            $nilai = 0;
          }
        }  
      }  
    }
    else if($job_order_id==798 || $job_order_id==800 || $job_order_id==791 
      || $job_order_id==792  || $job_order_id==794 || $job_order_id==830)
    {    
      if($emp_id==289)
      {
        $nilai = 1;
      }
      else 
      {
        if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41 || $group_id==12)
        {
          $nilai = 1;  
        }  
        else
        {
          if($emp_id==$department_head_id)
          {
            $nilai = 1;
          }  
          else
          {
            $nilai = 0;
          }          
        }  
      }  
    }
    else if($job_order_id==995)
    {
      if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41 || $group_id==12)
      {
        $nilai = 1;  
      }  
      else
      {
        if($emp_id==$department_head_id)
        {
          $nilai = 1;
        }  
        else
        {
          $nilai = 0;
        }          
      }  
    }  
    else
    {
      if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41 || $group_id==12)
      {
        $nilai = 1;  
      }  
      else
      {
        if($emp_id==$department_head_id)
        {
          $nilai = 1;
        }  
        else
        {
          if($code_new=='CVL')
          {
            if($emp_id==363)
            {
              $nilai = 1;
            }  
            else
            {
              $nilai = 0;
            }  
          }  
          else if($code_new=='MEC')
          {
            if($emp_id==886)
            {
              $nilai = 1;
            }  
            else
            {
              $nilai = 0;
            }  
          }
          else if($code_new=='ELC')
          {
            if($emp_id==217)
            {
              $nilai = 1;
            }  
            else
            {
              $nilai = 0;
            }  
          }
          
          else if($code_new=='CM')
          {
            //sementara pengganti pak sulis adalah pak gerrard
            if($emp_id==149 || $emp_id=3234 || $emp_id==3469)
            {
              $nilai = 1;
            }  
            else
            {
              $nilai = 0;
            }              
          }
          
          else if($code_new=='HSE')
          {
            if($emp_id==45)
            {
              $nilai = 1;
            }  
            else
            {
              $nilai = 0;
            }  
          }
          else if($code_new=='PRC')
          {
            if($emp_id==432)
            {
              $nilai = 1;
            }  
            else
            {
              $nilai = 0;
            }  
          }  
          else if($code_new=='AFI')
          {
            if($emp_id==5)
            {
              $nilai = 1;
            }  
            else
            {
              $nilai = 0;
            }  
          }  
          else if($code_new=='DRK')
          {
            if($job_order_id==819)
            {
              if($emp_id==1554 || $emp_id==393 || $group_id==1 || $group_id==10 || $group_id==51 || $group_id==41)
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
              if($group_id==1 || $group_id==10 || $emp_id==1554 || $group_id==51 || $group_id==41)
              {
                $nilai = 1;
              }  
              else
              {
                $nilai = 0;
              }  
            }
          }  
          else if($code_new=='PTN')
          {
if($emp_id==296)
            {
              $nilai = 1;
            }  
            else
            {
              $nilai = 0;
            }  
          }  
          else if($code_new=='PSR')
          {
            //hanya pak bagus dan pak totok yang bisa akses JO pasuruan
            if($emp_id==149 || $emp_id==92 || $emp_id==289)
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
      }        
    }
    
    //sementara gawe purchasing spv
    if($emp_id==432)
    {
      $nilai = 1;
    }  
      
    return $nilai;  
  }

//akses approval 2 WR
  function cekWorkRequestApproval2($conn, $user_id, $wr_id)
  {
    $sql_wr_approval = "SELECT IFNULL(approval1,0) AS approval1, 
                   work_order_id, job_order_id 
                FROM ki_work_order
                WHERE work_order_id ='".$wr_id."'";
    $qur_wr_approval = mysqli_query($conn, $sql_wr_approval);
    $approval1 = 0;
    $job_order_id = '';
    while($r_wr_approval = mysqli_fetch_assoc($qur_wr_approval))
    {
      $approval1       = $r_wr_approval['approval1'];
      $job_order_id     = $r_wr_approval['job_order_id'];
    }  

    $nilai = 0;
    if($approval1==0)
    {
      $nilai = 0;
      //output error karena belum di approve 1
    }  
    
    $sql_user = "SELECT u.usergroup_id, u.employee_id
                 FROM ki_user u
           INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
           LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
           WHERE u.user_id = '".$user_id."'";
    $group_id = '';
    $emp_id = '';
    $qur_user = mysqli_query($conn, $sql_user);
    while($r_user = mysqli_fetch_assoc($qur_user)) 
    {           
      $group_id   = $r_user['usergroup_id'];
      $emp_id   = $r_user['employee_id'];      
    }
    
    $sql_jo = "SELECT jo.job_order_number,jo.department_id,d.department_code,
                      d.department_head_id
           FROM ki_job_order jo
           INNER JOIN ki_work_order wr ON(wr.job_order_id = jo.job_order_id)
           LEFT JOIN ki_department d ON(d.department_id = jo.department_id)
           WHERE wr.work_order_id = '".$wr_id."'";  
    $jo_no = '';
    $department_code = '';
    $department_head_id = '';
    $qur_jo = mysqli_query($conn, $sql_jo);
    while($r_jo = mysqli_fetch_assoc($qur_jo)) 
    {           
      $jo_no         = $r_jo['job_order_number'];
      $department_code   = $r_jo['department_code'];
      $department_head_id = $r_jo['department_head_id'];
    }  
    
    $exp_jo = explode("-",$jo_no);  

    $jum_array = count($exp_jo);
    
    if($job_order_id==789 || $job_order_id==790)
    {
      if($emp_id==289)
      {
        $nilai = 1;
      }    
      else
      {
        if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41 || $group_id==12)
        {
          $nilai = 1;  
        }
        else
        {
          if($emp_id==$department_head_id)
          {
            $nilai = 1;
          }  
          else
          {
            //sementara pengganti pak sulis adalah pak gerrard
            if($emp_id==149 || $emp_id=3234 || $emp_id==3469)
            {
              $nilai = 1;
            }  
            else
            {  
              $nilai = 0;
            }
          }
        }  
      }  
    }
    else if($job_order_id==798 || $job_order_id==800 || $job_order_id==791 
        || $job_order_id==792  || $job_order_id==794 || $job_order_id==830)
    {    
      //sementara pengganti pak sulis adalah pak gerrard
      if($emp_id==149 || $emp_id==3234 || $emp_id==3469)
      {
        $nilai = 1;
      }
      else 
      {
        if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41 || $group_id==12)
        {
          $nilai = 1;  
        }  
        else
        {
          if($emp_id==$department_head_id)
          {
            $nilai = 1;
          }  
          else
          {
            $nilai = 0;
          }          
        }  
      }  
    }
    else if(job_order_id==819)
    {
      if($group_id==1 || $group_id==10 || $emp_id==393 || $group_id==51 || $group_id==41 || $group_id==12)
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
      if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41 || $group_id==12)
      {
        $nilai = 1;  
      }  
      else
      {
        if($emp_id==$department_head_id)
        {
          $nilai = 1;
        }  
        else
        {
          //if($emp_id==149 || $emp_id==3234)
          //sementara pengganti pak sulis adalah pak gerrard
          if($emp_id==3234 || $emp_id==3469)
          {
            $nilai = 1;
          }  
          else
          {  
            $nilai = 0;
          }
        }          
      }        
    }
    return $nilai;
  }

//akses approval 3 WR
  function cekWorkRequestApproval3($conn, $user_id, $wr_id)
  {
    $sql_wr_approval = "SELECT IFNULL(approval2,0) AS approval2, 
                   work_order_id, job_order_id 
                FROM ki_work_order
                WHERE work_order_id ='".$wr_id."'";
    $qur_wr_approval = mysqli_query($conn, $sql_wr_approval);
    $approval2 = 0;
    $job_order_id = '';
    while($r_wr_approval = mysqli_fetch_assoc($qur_wr_approval))
    {
      $approval2       = $r_wr_approval['approval2'];
      $job_order_id     = $r_wr_approval['job_order_id'];
    }  

    $nilai = 0;
    if($approval2==0)
    {
      $nilai = 0;
      //output error karena belum di approve 1
    }  
    
    $sql_user = "SELECT u.usergroup_id, u.employee_id
                 FROM ki_user u
           INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
           LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
           WHERE u.user_id = '".$user_id."'";
    $group_id = '';
    $emp_id = '';
    $qur_user = mysqli_query($conn, $sql_user);
    while($r_user = mysqli_fetch_assoc($qur_user)) 
    {           
      $group_id   = $r_user['usergroup_id'];
      $emp_id   = $r_user['employee_id'];      
    }
    
    $sql_jo = "SELECT jo.job_order_number,jo.department_id,d.department_code,
                      d.department_head_id
           FROM ki_job_order jo
           INNER JOIN ki_work_order wr ON(wr.job_order_id = jo.job_order_id)
           LEFT JOIN ki_department d ON(d.department_id = jo.department_id)
           WHERE wr.work_order_id = '".$wr_id."'";  
    $jo_no = '';
    $department_code = '';
    $department_head_id = '';
    $qur_jo = mysqli_query($conn, $sql_jo);
    while($r_jo = mysqli_fetch_assoc($qur_jo)) 
    {           
      $jo_no         = $r_jo['job_order_number'];
      $department_code   = $r_jo['department_code'];
      $department_head_id = $r_jo['department_head_id'];
    }  
    
    $exp_jo = explode("-",$jo_no);  

    $jum_array = count($exp_jo);
    
    if($job_order_id==789 || $job_order_id==790)
    {
      //sementara pengganti pak sulis adalah pak gerrard
      if($emp_id==149 || $emp_id==3234 || $emp_id==3469)
      {
        $nilai = 1;
      }    
      else
      {
        if($group_id==1 || $group_id==10 || $group_id==12 || $group_id==51 || $group_id==41 || $group_id==12)
        {
          $nilai = 1;  
        }
        else
        {
          $nilai = 0;
        }  
      }  
    }
    else if($job_order_id==819)
    {
      if($group_id==1 || $group_id==10 || $emp_id==393 || $group_id==51 || $group_id==41 || $group_id==12)
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
      if($group_id==1 || $group_id==10 || $group_id==12 || $group_id==51 || $group_id==41 || $group_id==12)
      {
        $nilai = 1;  
      }  
      else
      {
        $nilai = 0;
      }        
    }
    return $nilai;
  }

  	//akses approval 1 SPKL
	function cekSPKLApproval1($conn, $user_id, $spkl_id)
	{
		$sql_user = "SELECT u.usergroup_id, u.employee_id
		             FROM ki_user u
					 INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
					 LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
					 WHERE u.user_id = '".$user_id."'";
		$group_id = '';
		$emp_id = '';
		$qur_user = mysqli_query($conn, $sql_user);
		while($r_user = mysqli_fetch_assoc($qur_user)) 
		{					 
			$group_id 	= $r_user['usergroup_id'];
			$emp_id 	= $r_user['employee_id'];			
		}
		
		$sql_jo = "SELECT jo.job_order_number,jo.department_id,d.department_code,
		                  d.department_head_id, 
						  IFNULL(ow.approval2_by,0) AS approval2_by 
				   FROM ki_job_order jo
				   INNER JOIN ki_overtime_workorder ow ON(ow.job_order_id = jo.job_order_id)
				   LEFT JOIN ki_department d ON(d.department_id = jo.department_id)
				   WHERE ow.overtime_workorder_id = '".$spkl_id."'";	
		$jo_no = '';
		$department_code = '';
		$department_head_id = '';
		$approval2_by = '';
		$qur_jo = mysqli_query($conn, $sql_jo);
		while($r_jo = mysqli_fetch_assoc($qur_jo)) 
		{					 
			$jo_no 				= $r_jo['job_order_number'];
			$department_code 	= $r_jo['department_code'];
			$department_head_id = $r_jo['department_head_id'];
			$approval2_by		= $r_jo['approval2_by'];
		}	
		
		$exp_jo = explode("-",$jo_no);	

		$jum_array = count($exp_jo);
		
		$code_new = '';
		if($jum_array>2)
		{
			if($department_code!='')
			{
				$code_new = $department_code;
			}
			else
			{		
				$code_new = $exp_jo[1];
			}
		}

		$nilai = 0;
		
		if($group_id==1 || $group_id==10 || $group_id==51)
		{
			$nilai = 1;	
		}
		else
		{
			if($emp_id==$department_head_id)
			{
				$nilai = 1;
			}	
			else
			{
				if($code_new=='CVL')
				{
					if($emp_id==363)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}	
				else if($code_new=='MEC')
				{
					if($emp_id==886 || $emp_id==1691)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}
				else if($code_new=='ELC')
				{
					if($emp_id==217)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}
				
				else if($code_new=='CM')
				{
					//sementara pengganti pak sulis adalah pak gerrard
					if($emp_id==149 || $emp_id==3234 || $emp_id==3469)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}
				
				else if($code_new=='HSE')
				{
					if($emp_id==45)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}
				else if($code_new=='PRC')
				{
					if($emp_id==432)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}	
				else if($code_new=='AFI')
				{
					if($emp_id==5)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}
				else if($code_new=='PTN')
				{
					if($emp_id==296)
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
		}

		if($user_id!=1)
		{	
			if($approval2_by!='')
			{
				$nilai = 0;									
			}
		}	
	
		return $nilai;
	}	

	//akses approval 2 SPKL
	function cekSPKLApproval2($conn, $user_id, $spkl_id)
	{
		$sql_user = "SELECT u.usergroup_id, u.employee_id
		             FROM ki_user u
					 INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
					 LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
					 WHERE u.user_id = '".$user_id."'";
		$group_id = '';
		$emp_id = '';
		$qur_user = mysqli_query($conn, $sql_user);
		while($r_user = mysqli_fetch_assoc($qur_user)) 
		{					 
			$group_id 	= $r_user['usergroup_id'];
			$emp_id 	= $r_user['employee_id'];			
		}
				
		$nilai = 0;
		if($group_id==1 || $group_id==10 || $group_id==12)
		{
			$nilai = 1;
		}	
		else
		{
			//sementara pengganti pak sulis adalah pak gerrard
			if($emp_id==149 || $emp_id==3234 || $emp_id==3469)
			{
				$nilai = 1;
			}	
			else
			{
				$nilai = 0;
			}	
		}
		
		$sql_jo = "SELECT IFNULL(ow.approval1_by,0) AS approval1_by 
				   FROM ki_overtime_workorder ow
				   WHERE ow.overtime_workorder_id = '".$spkl_id."'";	
		$approval1_by = '';
		$qur_jo = mysqli_query($conn, $sql_jo);
		while($r_jo = mysqli_fetch_assoc($qur_jo)) 
		{					 
			$approval1_by	= $r_jo['approval1_by'];
		}	
		
		if($approval1_by==0)
		{
			$nilai = 0;	
		}
		
		return $nilai;	
						
	}	

	//akses approval 1 PB
	function cekProposedBudgetApproval1($conn, $user_id, $pb_id)
	{
		$sql_pb_approval = "SELECT IFNULL(checked_by,0) AS checked_by,
								   IFNULL(approval1,0) AS approval1, 
								   cash_advance_id, job_order_id 
						    FROM ki_cash_advance
						    WHERE cash_advance_id ='".$pb_id."'";
		$qur_pb_approval = mysqli_query($conn, $sql_pb_approval);
		$checked_by = 0;
		$approval1 = 0;
		$job_order_id = '';
		while($r_pb_approval = mysqli_fetch_assoc($qur_pb_approval))
		{
			$checked_by 		= $r_pb_approval['checked_by'];			
			$approval1 			= $r_pb_approval['approval1'];
			$job_order_id 		= $r_pb_approval['job_order_id'];
		}	
		
		$nilai = 0;
		if($checked_by != 0 && $approval1 == 0)
		{
			$nilai = 1;
		}	
		
		$sql_user = "SELECT u.usergroup_id, u.employee_id
		             FROM ki_user u
					 INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
					 LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
					 WHERE u.user_id = '".$user_id."'";
		$group_id = '';
		$emp_id = '';
		$qur_user = mysqli_query($conn, $sql_user);
		while($r_user = mysqli_fetch_assoc($qur_user)) 
		{					 
			$group_id 	= $r_user['usergroup_id'];
			$emp_id 	= $r_user['employee_id'];			
		}

		$sql_jo = "SELECT jo.job_order_number,jo.department_id,d.department_code,
		                  d.department_head_id
				   FROM ki_job_order jo
				   INNER JOIN ki_cash_advance ca ON(ca.job_order_id = jo.job_order_id)
				   LEFT JOIN ki_department d ON(d.department_id = jo.department_id)
				   WHERE ca.cash_advance_id = '".$pb_id."'";	
		$jo_no = '';
		$department_code = '';
		$department_head_id = '';
		$qur_jo = mysqli_query($conn, $sql_jo);
		while($r_jo = mysqli_fetch_assoc($qur_jo)) 
		{					 
			$jo_no 				= $r_jo['job_order_number'];
			$department_code 	= $r_jo['department_code'];
			$department_head_id = $r_jo['department_head_id'];
		}	

		$exp_jo = explode("-",$jo_no);	

		$jum_array = count($exp_jo);
		
		$code_new = '';
		if($jum_array>2)
		{
			if($department_code!='')
			{
				$code_new = $department_code;
			}
			else
			{		
				$code_new = $exp_jo[1];
			}
		}

		if($emp_id == 886)//Eko Apriansyah ::Fuad 20-juli-2017
		{
			$nilai =1;
		}
		
		if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41)
		{
			$nilai = 1;	
		}
		else
		{
			if($emp_id==$department_head_id)
			{
				$nilai = 1;
			}	
			else
			{	
				if($code_new=='CVL')
				{
					if($emp_id==363)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}	
				else if($code_new=='MEC')
				{
					if($emp_id==886)
					{
						$nilai = 1;
					}	
					else
					{
						if($job_order_id==791 || $job_order_id==792 
							|| $job_order_id==794 || $job_order_id==830)
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
				}
				else if($code_new=='ELC')
				{
					if($emp_id==217)
					{
						$nilai = 1;
					}	
					else
					{ 
						if($job_order_id==789 || $job_order_id==790 
							|| $job_order_id==798 || $job_order_id==800)
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
				}
				
				else if($code_new=='CM')
				{
					//sementara pengganti pak sulis adalah pak gerrard
					if($emp_id==149 || $emp_id==3234 || $emp_id==3469)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}
				
				else if($code_new=='HSE')
				{
					if($emp_id==45)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}
				else if($code_new=='PRC')
				{
					if($emp_id==432)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}	
				else if($code_new=='AFI')
				{
					if($emp_id==5)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}
				else if($code_new=='PTN')
				{
					if($emp_id==296)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}	
				else if($code_new=='PSR')
				{
					//hanya pak bagus yang bisa akses JO pasuruan
					if($emp_id==149 || $emp_id==92)
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
		}	
		
		return $nilai;
	}	

	//akses approval 2 PB
	function cekProposedBudgetApproval2($conn, $user_id, $pb_id)
	{
		$sql_pb_approval = "SELECT IFNULL(checked_by,0) AS checked_by,
								   IFNULL(approval2,0) AS approval2, 
								   cash_advance_id, job_order_id 
						    FROM ki_cash_advance
						    WHERE cash_advance_id ='".$pb_id."'";
		$qur_pb_approval = mysqli_query($conn, $sql_pb_approval);
		$checked_by = 0;
		$approval2 = 0;
		$job_order_id = '';
		while($r_pb_approval = mysqli_fetch_assoc($qur_pb_approval))
		{
			$checked_by 		= $r_pb_approval['checked_by'];			
			$approval2 			= $r_pb_approval['approval2'];
			$job_order_id 		= $r_pb_approval['job_order_id'];
		}	
		
		$nilai = 0;
		if($checked_by != 0 && $approval2 == 0)
		{
			$nilai = 1;
		}	
		
		$sql_user = "SELECT u.usergroup_id, u.employee_id
		             FROM ki_user u
					 INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
					 LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
					 WHERE u.user_id = '".$user_id."'";
		$group_id = '';
		$emp_id = '';
		$qur_user = mysqli_query($conn, $sql_user);
		while($r_user = mysqli_fetch_assoc($qur_user)) 
		{					 
			$group_id 	= $r_user['usergroup_id'];
			$emp_id 	= $r_user['employee_id'];			
		}
		
		$sql_jo = "SELECT jo.job_order_number,jo.department_id,d.department_code,
		                  d.department_head_id
				   FROM ki_job_order jo
				   INNER JOIN ki_cash_advance ca ON(ca.job_order_id = jo.job_order_id)
				   LEFT JOIN ki_department d ON(d.department_id = jo.department_id)
				   WHERE ca.cash_advance_id = '".$pb_id."'";	
		$jo_no = '';
		$department_code = '';
		$department_head_id = '';
		$dept_id = '';
		$qur_jo = mysqli_query($conn, $sql_jo);
		while($r_jo = mysqli_fetch_assoc($qur_jo)) 
		{					 
			$jo_no 				= $r_jo['job_order_number'];
			$department_code 	= $r_jo['department_code'];
			$department_head_id = $r_jo['department_head_id'];
			$dept_id			= $r_jo['department_id'];
		}	
		
		$exp_jo = explode("-",$jo_no);	

		$jum_array = count($exp_jo);
		
		$code_new = '';
		if($jum_array>2)
		{
			if($department_code!='')
			{
				$code_new = $department_code;
			}
			else
			{		
				$code_new = $exp_jo[1];
			}
		}
		
		//grup developer, presiden director, director
		if($group_id==1 || $group_id==10 || $group_id==41)
		{
			$nilai = 1;
		}	
		else
		{
			if($group_id ==32) //project manager
			{
				if($emp_id==3234) //sulis nur wahyudi
				{
					//electrical mechanical civil construction manager
					if($dept_id==6 || $dept_id==7 || $dept_id==13 || $dept_id==29)
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
			else if($group_id==12) //technical manager
			{
				if($emp_id==3469) //gerard reinhard datau
				{
					//hse maintenance hrd pec finance purchasing marketing pasuruan paiton
					//electrical mechanical civil (karena pak sulis sakit)	
					if($dept_id==18 || $dept_id==28 || $dept_id==15 || $dept_id==4 || $dept_id==17 
						|| $dept_id==14 || $dept_id==16 
						|| $dept_id==19 || $dept_id==30
						|| $dept_id==6 || $dept_id==7 || $dept_id==13 || $dept_id==29)	
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
			else
			{
				$nilai = 0;
			}	
		}	
		
		return $nilai;
	}	
	
	//akses approval 3 PB
	function cekProposedBudgetApproval3($conn, $user_id, $pb_id)
	{
		$sql_pb_approval = "SELECT IFNULL(checked_by,0) AS checked_by,
								   IFNULL(approval1,0) AS approval1, 
								   IFNULL(approval2,0) AS approval2, 
								   IFNULL(approval3,0) AS approval3, 
								   cash_advance_id, job_order_id 
						    FROM ki_cash_advance
						    WHERE cash_advance_id ='".$pb_id."'";
		$qur_pb_approval = mysqli_query($conn, $sql_pb_approval);
		$checked_by = 0;
		$approval1 = 0;
		$approval2 = 0;
		$approval3 = 0;
		$job_order_id = '';
		while($r_pb_approval = mysqli_fetch_assoc($qur_pb_approval))
		{
			$checked_by 		= $r_pb_approval['checked_by'];			
			$approval1 			= $r_pb_approval['approval1'];
			$approval2 			= $r_pb_approval['approval2'];
			$approval3 			= $r_pb_approval['approval3'];
			$job_order_id 		= $r_pb_approval['job_order_id'];
		}	
		
		$nilai = 0;
		if($checked_by != 0 && $approval1 != 0 && $approval2 != 0 && $approval3 == 0)
		{
			$nilai = 1;
		}	
		
		$sql_user = "SELECT u.usergroup_id, u.employee_id
		             FROM ki_user u
					 INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
					 LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
					 WHERE u.user_id = '".$user_id."'";
		$group_id = '';
		$emp_id = '';
		$qur_user = mysqli_query($conn, $sql_user);
		while($r_user = mysqli_fetch_assoc($qur_user)) 
		{					 
			$group_id 	= $r_user['usergroup_id'];
			$emp_id 	= $r_user['employee_id'];			
		}
		
		$sql_jo = "SELECT jo.job_order_number,jo.department_id,d.department_code,
		                  d.department_head_id
				   FROM ki_job_order jo
				   INNER JOIN ki_cash_advance ca ON(ca.job_order_id = jo.job_order_id)
				   LEFT JOIN ki_department d ON(d.department_id = jo.department_id)
				   WHERE ca.cash_advance_id = '".$pb_id."'";	
		$jo_no = '';
		$department_code = '';
		$department_head_id = '';
		$dept_id = '';
		$qur_jo = mysqli_query($conn, $sql_jo);
		while($r_jo = mysqli_fetch_assoc($qur_jo)) 
		{					 
			$jo_no 				= $r_jo['job_order_number'];
			$department_code 	= $r_jo['department_code'];
			$department_head_id = $r_jo['department_head_id'];
			$dept_id			= $r_jo['department_id'];
		}	
		
		$exp_jo = explode("-",$jo_no);	

		$jum_array = count($exp_jo);
		
		$code_new = '';
		if($jum_array>2)
		{
			if($department_code!='')
			{
				$code_new = $department_code;
			}
			else
			{		
				$code_new = $exp_jo[1];
			}
		}
		
		//director
		if($group_id==41)
		{
			//mechanical electrical civil maintenance pasuruan paiton construction manager
			if($dept_id==7 || $dept_id==6 || $dept_id==13 || $dept_id==28 || $dept_id==19 
				|| $dept_id==30 || $dept_id==29)
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
			$nilai = 1;
		}	
		
		return $nilai;		
	}	
	
	//akses approval 1 CPR
	function cekCPRApproval1($conn, $user_id, $cpr_id)
	{
		$sql_cpr_approval = "SELECT IFNULL(ra.checked_by,0) AS checked_by,
								   ra.responsbility_advance_id, ca.job_order_id 
						    FROM ki_cash_advance ca
							INNER JOIN ki_responsbility_advance ra 
							ON(ca.cash_advance_id = ra.cash_advance_id)
						    WHERE ra.responsbility_advance_id ='".$cpr_id."'";
		$qur_cpr_approval = mysqli_query($conn, $sql_cpr_approval);
		$checked_by = 0;
		$job_order_id = '';
		while($r_cpr_approval = mysqli_fetch_assoc($qur_cpr_approval))
		{
			$checked_by 		= $r_cpr_approval['checked_by'];			
			$job_order_id 		= $r_cpr_approval['job_order_id'];
		}	
		
		$nilai = 0;
		if($checked_by!=0)
		{
			$nilai = 1;
		}	
		
		$sql_user = "SELECT u.usergroup_id, u.employee_id
		             FROM ki_user u
					 INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
					 LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
					 WHERE u.user_id = '".$user_id."'";
		$group_id = '';
		$emp_id = '';
		$qur_user = mysqli_query($conn, $sql_user);
		while($r_user = mysqli_fetch_assoc($qur_user)) 
		{					 
			$group_id 	= $r_user['usergroup_id'];
			$emp_id 	= $r_user['employee_id'];			
		}

		$sql_jo = "SELECT jo.job_order_number,jo.department_id,d.department_code,
		                  d.department_head_id
				   FROM ki_job_order jo
				   INNER JOIN ki_cash_advance ca ON(ca.job_order_id = jo.job_order_id)
				   INNER JOIN ki_responsbility_advance ra 
				   ON(ra.cash_advance_id = ca.cash_advance_id)
				   LEFT JOIN ki_department d ON(d.department_id = jo.department_id)
				   WHERE ra.responsbility_advance_id = '".$cpr_id."'";	
		$jo_no = '';
		$department_code = '';
		$department_head_id = '';
		$dept_id = '';
		$qur_jo = mysqli_query($conn, $sql_jo);
		while($r_jo = mysqli_fetch_assoc($qur_jo)) 
		{					 
			$jo_no 				= $r_jo['job_order_number'];
			$department_code 	= $r_jo['department_code'];
			$department_head_id = $r_jo['department_head_id'];
			$dept_id			= $r_jo['department_id'];
		}	
		
		$exp_jo = explode("-",$jo_no);	

		$jum_array = count($exp_jo);
		
		$code_new = '';
		if($jum_array>2)
		{
			if($department_code!='')
			{
				$code_new = $department_code;
			}
			else
			{		
				$code_new = $exp_jo[1];
			}
		}

		$nilai = 0;
		
		if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41)
		{
			$nilai = 1;	
		}
		else
		{
			if($emp_id==$department_head_id)
			{
				$nilai = 1;
			}	
			else
			{
				if($code_new=='CVL')
				{
					if($emp_id==363)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}	
				else if($code_new=='MEC')
				{
					if($emp_id==886)
					{
						$nilai = 1;
					}	
					else
					{
						if($job_order_id==791 || $job_order_id==792 
							|| $job_order_id==794 || $job_order_id==830)
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
				}
				else if($code_new=='ELC')
				{
					if($emp_id==217)
					{
						$nilai = 1;
					}	
					else
					{
						if($job_order_id==789 || $job_order_id==790 
							|| $job_order_id==798 || $job_order_id==800)
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
				}
				//department baru	
				else if($code_new=='CM')
				{
					//sementara pengganti pak sulis adalah pak gerrard
					if($emp_id==149 || $emp_id==3234 || $emp_id==3469)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}
				
				else if($code_new=='HSE')
				{
					if($emp_id==45)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}
				else if($code_new=='PRC')
				{
					if($emp_id==432)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}	
				else if($code_new=='AFI')
				{
					if($emp_id==5)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}	
				else if($code_new=='PTN')
				{
					if($emp_id==296)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}
				else if($code_new=='PSR')
				{
					//hanya pak bagus yang bisa akses JO pasuruan
					if($emp_id==149 || $emp_id==92)
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
		}	
		
		return $nilai;		
	}	

	//akses approval 2 CPR
	function cekCPRApproval2($conn, $user_id, $cpr_id)
	{
		$sql_cpr_approval = "SELECT IFNULL(ra.approval1,0) AS approval1,
								   ra.responsbility_advance_id, ca.job_order_id 
						    FROM ki_cash_advance ca
							INNER JOIN ki_responsbility_advance ra 
							ON(ca.cash_advance_id = ra.cash_advance_id)
						    WHERE ra.responsbility_advance_id ='".$cpr_id."'";
		$qur_cpr_approval = mysqli_query($conn, $sql_cpr_approval);
		$approval1 = 0;
		$job_order_id = '';
		while($r_cpr_approval = mysqli_fetch_assoc($qur_cpr_approval))
		{
			$approval1 			= $r_cpr_approval['approval1'];			
			$job_order_id 		= $r_cpr_approval['job_order_id'];
		}	
		
		$nilai = 0;
		if($approval1!=0)
		{
			$nilai = 1;
		}	
		
		$sql_user = "SELECT u.usergroup_id, u.employee_id
		             FROM ki_user u
					 INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
					 LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
					 WHERE u.user_id = '".$user_id."'";
		$group_id = '';
		$emp_id = '';
		$qur_user = mysqli_query($conn, $sql_user);
		while($r_user = mysqli_fetch_assoc($qur_user)) 
		{					 
			$group_id 	= $r_user['usergroup_id'];
			$emp_id 	= $r_user['employee_id'];			
		}

		$sql_jo = "SELECT jo.job_order_number,jo.department_id,d.department_code,
		                  d.department_head_id
				   FROM ki_job_order jo
				   INNER JOIN ki_cash_advance ca ON(ca.job_order_id = jo.job_order_id)
				   INNER JOIN ki_responsbility_advance ra 
				   ON(ra.cash_advance_id = ca.cash_advance_id)
				   LEFT JOIN ki_department d ON(d.department_id = jo.department_id)
				   WHERE ra.responsbility_advance_id = '".$cpr_id."'";	
		$jo_no = '';
		$department_code = '';
		$department_head_id = '';
		$dept_id = '';
		$qur_jo = mysqli_query($conn, $sql_jo);
		while($r_jo = mysqli_fetch_assoc($qur_jo)) 
		{					 
			$jo_no 				= $r_jo['job_order_number'];
			$department_code 	= $r_jo['department_code'];
			$department_head_id = $r_jo['department_head_id'];
			$dept_id			= $r_jo['department_id'];
		}	
		
		$exp_jo = explode("-",$jo_no);	

		$jum_array = count($exp_jo);
		
		$code_new = '';
		if($jum_array>2)
		{
			if($department_code!='')
			{
				$code_new = $department_code;
			}
			else
			{		
				$code_new = $exp_jo[1];
			}
		}

		$nilai = 0;
		//grup developer, presiden director, director
		if($group_id==1 || $group_id==10 || $group_id==41)
		{
			$nilai = 1;
		}	
		else
		{
			if($group_id ==32 ) //project manager
			{
				if($emp_id==3234) //sulis nur wahyudi
				{
					//electrical mechanical civil construction manager
					if($dept_id==6 || $dept_id==7 || $dept_id==13 || $dept_id==29)
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
			else if($group_id==12) //technical manager
			{
				if($emp_id==3469) //gerard reinhard datau
				{
					//hse maintenance hrd pec finance purchasing marketing pasuruan paiton
					//electrical mechanical civil (karena pak sulis sakit)	
					if($dept_id==18 || $dept_id==28 || $dept_id==15 || $dept_id==4 || $dept_id==17 
						|| $dept_id==14 || $dept_id==16 
						|| $dept_id==19 || $dept_id==30
						|| $dept_id==6 || $dept_id==7 || $dept_id==13 || $dept_id==29)	
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
			else
			{
				$nilai = 0;
			}	
		}

		return $nilai;	
	}	

	//akses approval 3 CPR
	function cekCPRApproval3($conn, $user_id, $cpr_id)
	{
		$sql_cpr_approval = "SELECT IFNULL(ra.approval2,0) AS approval2,
								   ra.responsbility_advance_id, ca.job_order_id 
						    FROM ki_cash_advance ca
							INNER JOIN ki_responsbility_advance ra 
							ON(ca.cash_advance_id = ra.cash_advance_id)
						    WHERE ra.responsbility_advance_id ='".$cpr_id."'";
		$qur_cpr_approval = mysqli_query($conn, $sql_cpr_approval);
		$approval2 = 0;
		$job_order_id = '';
		while($r_cpr_approval = mysqli_fetch_assoc($qur_cpr_approval))
		{
			$approval2 			= $r_cpr_approval['approval2'];			
			$job_order_id 		= $r_cpr_approval['job_order_id'];
		}	
		
		$nilai = 0;
		if($approval2!=0)
		{
			$nilai = 1;
		}	
		
		$sql_user = "SELECT u.usergroup_id, u.employee_id
		             FROM ki_user u
					 INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
					 LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
					 WHERE u.user_id = '".$user_id."'";
		$group_id = '';
		$emp_id = '';
		$qur_user = mysqli_query($conn, $sql_user);
		while($r_user = mysqli_fetch_assoc($qur_user)) 
		{					 
			$group_id 	= $r_user['usergroup_id'];
			$emp_id 	= $r_user['employee_id'];			
		}
		
		$sql_jo = "SELECT jo.job_order_number,jo.department_id,d.department_code,
		                  d.department_head_id
				   FROM ki_job_order jo
				   INNER JOIN ki_cash_advance ca ON(ca.job_order_id = jo.job_order_id)
				   INNER JOIN ki_responsbility_advance ra 
				   ON(ra.cash_advance_id = ca.cash_advance_id)
				   LEFT JOIN ki_department d ON(d.department_id = jo.department_id)
				   WHERE ra.responsbility_advance_id = '".$cpr_id."'";	
		$jo_no = '';
		$department_code = '';
		$department_head_id = '';
		$dept_id = '';
		$qur_jo = mysqli_query($conn, $sql_jo);
		while($r_jo = mysqli_fetch_assoc($qur_jo)) 
		{					 
			$jo_no 				= $r_jo['job_order_number'];
			$department_code 	= $r_jo['department_code'];
			$department_head_id = $r_jo['department_head_id'];
			$dept_id			= $r_jo['department_id'];
		}	
		
		$exp_jo = explode("-",$jo_no);	

		$jum_array = count($exp_jo);
		
		$code_new = '';
		if($jum_array>2)
		{
			if($department_code!='')
			{
				$code_new = $department_code;
			}
			else
			{		
				$code_new = $exp_jo[1];
			}
		}

		$nilai = 0;
		//director
		if($group_id==41)
		{
			//mechanical electrical civil maintenance pasuruan paiton 
			//mechanical electrical civil maintenance pasuruan paiton contrusction manager
			if($dept_id==7 || $dept_id==6 || $dept_id==13 || $dept_id==28 || $dept_id==19 
				|| $dept_id==30 || $dept_id==29)
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
			$nilai = 1;
		}	
		
		return $nilai;
		
	}	
	
	//akses approval 1 Tunjangan Karyawan
	function cekTunjanganKaryawanApproval1($conn, $user_id, $ea_id)
	{
		$sql_ea_approval = "SELECT IFNULL(verified_by,0) AS verified_by,
								   employee_allowance_id, job_order_id 
						    FROM ki_employee_allowance
						    WHERE employee_allowance_id ='".$ea_id."'";
		$qur_ea_approval = mysqli_query($conn, $sql_ea_approval);
		$verified_by = 0;
		$job_order_id = '';
		while($r_ea_approval = mysqli_fetch_assoc($qur_ea_approval))
		{
			$verified_by 		= $r_ea_approval['verified_by'];			
			$job_order_id 		= $r_ea_approval['job_order_id'];
		}	
		
		$nilai = 0;
		if($verified_by!=0)
		{
			$nilai = 1;
		}	
		
		$sql_user = "SELECT u.usergroup_id, u.employee_id
		             FROM ki_user u
					 INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
					 LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
					 WHERE u.user_id = '".$user_id."'";
		$group_id = '';
		$emp_id = '';
		$qur_user = mysqli_query($conn, $sql_user);
		while($r_user = mysqli_fetch_assoc($qur_user)) 
		{					 
			$group_id 	= $r_user['usergroup_id'];
			$emp_id 	= $r_user['employee_id'];			
		}
		
		$sql_jo = "SELECT jo.job_order_number,jo.department_id,d.department_code,
		                  d.department_head_id, jo.supervisor
				   FROM ki_job_order jo
				   INNER JOIN ki_employee_allowance ea ON(ea.job_order_id = jo.job_order_id)
				   LEFT JOIN ki_department d ON(d.department_id = jo.department_id)
				   WHERE ea.employee_allowance_id = '".$ea_id."'";	
		$jo_no = '';
		$department_code = '';
		$department_head_id = '';
		$dept_id = '';
		$supervisor = '';
		$qur_jo = mysqli_query($conn, $sql_jo);
		while($r_jo = mysqli_fetch_assoc($qur_jo)) 
		{					 
			$jo_no 				= $r_jo['job_order_number'];
			$department_code 	= $r_jo['department_code'];
			$department_head_id = $r_jo['department_head_id'];
			$dept_id			= $r_jo['department_id'];
			$supervisor			= $r_jo['supervisor'];
		}	
		
		$exp_jo = explode("-",$jo_no);	

		$jum_array = count($exp_jo);
		
		$code_new = '';
		if($jum_array>2)
		{
			if($department_code!='')
			{
				$code_new = $department_code;
			}
			else
			{		
				$code_new = $exp_jo[1];
			}
		}

		$nilai = 0;
		
		if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41)
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
				//marketing maintenance pec hrd bisa diapproval pak gery
				if($code_new=='MRK' || $code_new=='MTC' || $code_new=='PEC' || $code_new=='HRD')
				{
					if($group_id==12)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}
				else if($code_new=='PSR')
				{
					//hanya pak bagus dan pak totok yang bisa akses JO pasuruan
					if($emp_id==149 || $emp_id==92 || $emp_id==289)
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
		}	
		
		return $nilai;		
		
	}

	//akses approval 2 Tunjangan Karyawan
	function cekTunjanganKaryawanApproval2($conn, $user_id, $ea_id)
	{
		$sql_ea_approval = "SELECT IFNULL(approval1_by,0) AS approval1_by, employee_allowance_id, job_order_id FROM ki_employee_allowance WHERE employee_allowance_id ='".$ea_id."'";
		$qur_ea_approval = mysqli_query($conn, $sql_ea_approval);
		$approval1_by = 0;
		$job_order_id = '';
		while($r_ea_approval = mysqli_fetch_assoc($qur_ea_approval))
		{
			$approval1_by 		= $r_ea_approval['approval1_by'];
			$job_order_id 		= $r_ea_approval['job_order_id'];
		}	
		
		$nilai = 0;
		if($approval1_by!=0)
		{
			$nilai = 1;
		}	
		
		return $nilai;
	}

	//akses approval 1 Tunjangan Temporary
	function cekTunjanganTemporaryApproval1($conn, $user_id, $ea_id)
	{
		$sql_ea_approval = "SELECT IFNULL(verified_by,0) AS verified_by,
								   employee_allowance_id, job_order_id 
						    FROM ki_employee_allowance_temporary
						    WHERE employee_allowance_id ='".$ea_id."'";
		$qur_ea_approval = mysqli_query($conn, $sql_ea_approval);
		$verified_by = 0;
		$job_order_id = '';
		while($r_ea_approval = mysqli_fetch_assoc($qur_ea_approval))
		{
			$verified_by 		= $r_ea_approval['verified_by'];
			$job_order_id 		= $r_ea_approval['job_order_id'];
		}	
		
		$nilai = 0;
		if($verified_by!=0)
		{
			$nilai = 1;
		}	
		$sql_user = "SELECT u.usergroup_id, u.employee_id
		             FROM ki_user u
					 INNER JOIN ki_usergroup ug ON(u.usergroup_id = ug.usergroup_id)
					 LEFT JOIN ki_employee e ON(e.employee_id = u.employee_id)
					 WHERE u.user_id = '".$user_id."'";
		$group_id = '';
		$emp_id = '';
		$qur_user = mysqli_query($conn, $sql_user);
		while($r_user = mysqli_fetch_assoc($qur_user)) 
		{					 
			$group_id 	= $r_user['usergroup_id'];
			$emp_id 	= $r_user['employee_id'];			
		}
		
		$sql_jo = "SELECT jo.job_order_number,jo.department_id,d.department_code,
		                  d.department_head_id, jo.supervisor
				   FROM ki_job_order jo
				   INNER JOIN ki_employee_allowance_temporary ea 
				   ON(ea.job_order_id = jo.job_order_id)
				   LEFT JOIN ki_department d ON(d.department_id = jo.department_id)
				   WHERE ea.employee_allowance_id = '".$ea_id."'";	
		$jo_no = '';
		$department_code = '';
		$department_head_id = '';
		$dept_id = '';
		$supervisor = '';
		$qur_jo = mysqli_query($conn, $sql_jo);
		while($r_jo = mysqli_fetch_assoc($qur_jo)) 
		{					 
			$jo_no 				= $r_jo['job_order_number'];
			$department_code 	= $r_jo['department_code'];
			$department_head_id = $r_jo['department_head_id'];
			$dept_id			= $r_jo['department_id'];
			$supervisor			= $r_jo['supervisor'];
		}	
		$exp_jo = explode("-",$jo_no);	

		$jum_array = count($exp_jo);
		
		$code_new = '';
		if($jum_array>2)
		{
			if($department_code!='')
			{
				$code_new = $department_code;
			}
			else
			{		
				$code_new = $exp_jo[1];
			}
		}

		$nilai = 0;
		
		if($group_id==1 || $group_id==10 || $group_id==51 || $group_id==41)
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
				//marketing maintenance pec hrd bisa diapproval pak gery
				if($code_new=='MRK' || $code_new=='MTC' || $code_new=='PEC' || $code_new=='HRD')
				{
					if($group_id==12)
					{
						$nilai = 1;
					}	
					else
					{
						$nilai = 0;
					}	
				}
				else if($code_new=='PSR')
				{
					//hanya pak bagus dan pak totok yang bisa akses JO pasuruan
					if($emp_id==149 || $emp_id==92 || $emp_id==289)
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
		}	
		
		return $nilai;		
	}

	//akses approval 2 Tunjangan Temporary
	function cekTunjanganTemporaryApproval2($conn, $user_id, $ea_id)
	{
		$sql_ea_approval = "SELECT IFNULL(approval1_by,0) AS approval1_by,
								   employee_allowance_id, job_order_id 
						    FROM ki_employee_allowance_temporary
						    WHERE employee_allowance_id ='".$ea_id."'";
		$qur_ea_approval = mysqli_query($conn, $sql_ea_approval);
		$approval1_by = 0;
		$job_order_id = '';
		while($r_ea_approval = mysqli_fetch_assoc($qur_ea_approval))
		{
			$approval1_by = $r_ea_approval['approval1_by'];			
			$job_order_id = $r_ea_approval['job_order_id'];
		}	
		
		$nilai = 0;
		if($approval1_by!=0)
		{
			$nilai = 1;
		}	
		
		return $nilai;
	}
?>