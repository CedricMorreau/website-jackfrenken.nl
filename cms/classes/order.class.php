<?php

class Order {
	
	private $db;
	
	public function __construct($db) {
		
		$this->db = $db;
	}

	/**
	 * Figures out if an order is currently active for this 'session' (IP based, session ID based)
	 * @param string $ip
	 * @param string $sessId
	 */
	public function activeOrder($ip, $sessId = 'noId') {
		
		$order = $this->db->prepare("SELECT * FROM `tbl_ws_temp_orders` WHERE `wo_ip`=? AND `wo_sessionId`=? AND `wo_tempOrder`=1", "ss", array($ip, $sessId));
		
		if (count($order) <= 0) {
			
			$order = $this->createOrder($ip);
		}
		
		return $order[0];
	}
	
	/**
	 * Creates an order based on a given IP, sets session ID
	 * @param string $ip
	 */
	public function createOrder($ip) {
		
		// Create session ID based on ip + time
		$sessionId = md5($ip . time());
		
		$_SESSION['ws_sessionId'] = $sessionId;
		
		$this->db->prepare("INSERT INTO `tbl_ws_temp_orders` (`wo_ip`, `wo_sessionId`, `wo_dateAdded`) VALUES(?, ?, NOW())", "ss", array($ip, $sessionId));
		$lastId = $this->db->lastId();

		// Check if user is logged in; if so, connect userdata
        $this->addUserData($lastId);
		
		$order = $this->db->prepare("SELECT * FROM `tbl_ws_temp_orders` WHERE `wo_id`=?", "i", array($lastId));
		
		return $order;
	}

	public function addUserData($orderId) {

	    if (isset($_SESSION['lcs']['login'])) {

            // Set the address data with the session data
            $this->db->prepare("UPDATE `tbl_ws_temp_orders` SET 
                                        `wo_client_id`=?,
                                        `wo_client_firstName`=?,
                                        `wo_client_lastName`=?,
                                        `wo_client_emailAddress`=?,
                                        `wo_client_phone`=?,
                                        `wo_client_company_tav`=?,
                                        `wo_client_alt_address`=?,
                                        `wo_client_address_factuur`=?,
                                        `wo_client_houseNumber_factuur`=?,
                                        `wo_client_houseNumberAdd_factuur`=?,
                                        `wo_client_zipcode_factuur`=?,
                                        `wo_client_place_factuur`=?,
                                        `wo_client_country_factuur`=?,
                                        `wo_client_company_factuur`=?,
                                        `wo_client_company`=?,
                                        `wo_client_kvk`=?,
                                        `wo_client_btw`=?,
                                        `wo_client_reference`=?,
                                        `wo_client_type`=?
                                        WHERE `wo_id`=?
                                        ", "issssssssssssssssssi",
                array(
                    $_SESSION['lcs']['login']['wsa_id'],
                    $_SESSION['lcs']['login']['wsa_client_firstName'],
                    $_SESSION['lcs']['login']['wsa_client_lastName'],
                    $_SESSION['lcs']['login']['wsa_client_emailAddress'],
                    $_SESSION['lcs']['login']['wsa_client_phone'],
                    $_SESSION['lcs']['login']['wsa_client_company_tav'],
                    $_SESSION['lcs']['login']['wsa_client_alt_address'],
                    $_SESSION['lcs']['login']['wsa_client_address_factuur'],
                    $_SESSION['lcs']['login']['wsa_client_houseNumber_factuur'],
                    $_SESSION['lcs']['login']['wsa_client_houseNumberAdd_factuur'],
                    $_SESSION['lcs']['login']['wsa_client_zipcode_factuur'],
                    $_SESSION['lcs']['login']['wsa_client_place_factuur'],
                    $_SESSION['lcs']['login']['wsa_client_country_factuur'],
                    $_SESSION['lcs']['login']['wsa_client_company_factuur'],
                    $_SESSION['lcs']['login']['wsa_client_company'],
                    $_SESSION['lcs']['login']['wsa_client_kvk'],
                    $_SESSION['lcs']['login']['wsa_client_btw'],
                    $_SESSION['lcs']['login']['wsa_client_reference'],
                    $_SESSION['lcs']['login']['wsa_client_type'],
                    $orderId
                ));
            
            
            
            /*
             * 
                                        `wo_client_address`=?,
                                        `wo_client_houseNumber`=?,
                                        `wo_client_houseNumberAdd`=?,
                                        `wo_client_zipcode`=?,
                                        `wo_client_place`=?,
                                        `wo_client_country`=?,
                                        
                                        
                    $_SESSION['lcs']['login']['wsa_client_address'],
                    $_SESSION['lcs']['login']['wsa_client_houseNumber'],
                    $_SESSION['lcs']['login']['wsa_client_houseNumberAdd'],
                    $_SESSION['lcs']['login']['wsa_client_zipcode'],
                    $_SESSION['lcs']['login']['wsa_client_place'],
                    $_SESSION['lcs']['login']['wsa_client_country'],
             */
        }
    }
    
    public function addLog($orderId, $status) {
    	
    	$this->db->prepare("INSERT INTO `tbl_ws_orderLog` (`ol_dateAdded`, `ol_dateAddedBy`, `ol_orderId`, `ol_status`) VALUES(NOW(), 0, ?, ?)", "ii", array($orderId, $status));
    }
	
	public function getOrderByHash($hash) {
		
		$order = $this->db->prepare("SELECT * FROM `tbl_ws_temp_orders` WHERE `wo_sessionId`=?", "s", array($hash));
		
		if (count($order) > 0) {
			
			return $order[0];
		}
		
		return false;
	}
	
	public function getFinalOrderByHash($hash) {
		
		$order = $this->db->prepare("SELECT * FROM `tbl_ws_orders` WHERE `mo_orderHash`=?", "s", array($hash));
		
		if (count($order) > 0) {
			
			return $order[0];
		}
		
		return false;
	}
	
	public function getTempRulesCount($orderId, $all = 0) {
		
		$productParent = ($all == 0) ? ' AND `wod_productParent`=0' : '';
		
		$rules = $this->db->prepare("SELECT * FROM `tbl_ws_temp_orderData` WHERE `wod_orderId`=?" . $productParent . " ORDER BY `wod_productParent` ASC, `wod_dateAdded` ASC", "i", array($orderId));
		
		$totalCount = 0;
		
		if (count($rules) > 0) {
			
			foreach ($rules as $key => $val) {
				
				$totalCount += $val['wod_productAmount'];
			}
		}
		
		return $totalCount;
	}
	
	public function getTempRules($orderId, $all = 0) {
		
		$productParent = ($all == 0) ? ' AND `wod_productParent`=0' : '';
		
		$rules = $this->db->prepare("SELECT * FROM `tbl_ws_temp_orderData` WHERE `wod_orderId`=?" . $productParent . " ORDER BY `wod_productParent` ASC, `wod_dateAdded` ASC", "i", array($orderId));
		
		if (count($rules) > 0) {
			
			return $rules;
		}
			
		return array();
	}
	
	public function getLinkedRule($ruleId) {
		
		$rule = $this->db->prepare("SELECT * FROM `tbl_ws_temp_orderData` WHERE `wod_productParent`=?", "i", array($ruleId));
		
		if (count($rule) > 0) {
			
			return $rule;
		}
		
		return array();
	}
	
	public function getFinalLinkedRule($ruleId) {
		
		$rule = $this->db->prepare("SELECT * FROM `tbl_ws_orderData` WHERE `mod_productParent`=?", "i", array($ruleId));
		
		if (count($rule) > 0) {
			
			return $rule;
		}
		
		return array();
	}
	
	/**
	 * Fetches order data from database
	 * @param Order $order
	 */
	public function getOrderData($order) {
		
		$data = $this->db->prepare("SELECT * FROM `tbl_ws_temp_orderData` WHERE `wod_orderId`=? ORDER BY `wod_productName` ASC", "i", array($order['wo_id']));
		
		return $data;
	}
	
	public function getFinalOrderData($order) {
		
		$data = $this->db->prepare("SELECT * FROM `tbl_ws_orderData` WHERE `mod_orderId`=? AND `mod_productParent`=0 ORDER BY `mod_productName` ASC", "i", array($order['mo_id']));
		
		return $data;
	}
	
	public function generateOrderId() {
		
		// See if an ID for this year exists
		$currentYear = date('y');
		
		$check = $this->db->prepare("SELECT * FROM `tbl_ws_orders` WHERE `mo_orderNumber` LIKE '" . $currentYear . "%' ORDER BY `mo_orderNumber` DESC LIMIT 1");
		
		if (count($check) > 0) {
			
			$split = array();
			$split[0] = substr($check[0]['mo_orderNumber'], 0, 2);
			$split[1] = substr($check[0]['mo_orderNumber'], -6);
			
			$orderNumber = $currentYear . str_pad(((ltrim($split[1], '0') * 1) + 1), 6, '0', STR_PAD_LEFT);
		}
		else {
			
			$orderNumber = $currentYear . str_pad('1', 6, '0', STR_PAD_LEFT);
		}
		
		return $orderNumber;
	}
	
	public function generateInvoiceId() {
		
		// See if an ID for this year exists
		$currentYear = date('y');
		
		$check = $this->db->prepare("SELECT * FROM `tbl_ws_orders` WHERE `mo_orderInvoice` LIKE '" . $currentYear . "%' ORDER BY `mo_orderInvoice` DESC LIMIT 1");
		
		if (count($check) > 0) {
			
			$split = array();
			$split[0] = substr($check[0]['mo_orderInvoice'], 0, 2);
			$split[1] = substr($check[0]['mo_orderInvoice'], -6);
			
			$orderInvoice = $currentYear . str_pad(((ltrim($split[1], '0') * 1) + 1), 6, '0', STR_PAD_LEFT);
		}
		else {
			
			$orderInvoice = $currentYear . str_pad('1', 6, '0', STR_PAD_LEFT);
		}
		
		return $orderInvoice;
	}
	
	public function generatePin($order) {
		
		$newPin = mt_rand(100000, 999999);
		
		// Save it
		$this->db->prepare("UPDATE `tbl_ws_orders` SET `mo_pinCode`=? WHERE `mo_id`=?", "ii", array($newPin, $order['mo_id']));
		
		return $newPin;
	}
	
	public function updateStatus($order, $fields) {
		
		if ($order['mo_statusId'] != $fields['status']) {
			
			$this->addLog($order['mo_id'], $fields['status']);
			
			$this->db->prepare("UPDATE `tbl_ws_orders` SET `mo_statusId`=?, `mo_orderDate`=NOW() WHERE `mo_id`=?", "ii", array($fields['status'], $order['mo_id']));
		}
	}
	
	public function updateOrderDate($order) {
		
		$date = date('Y-m-d H:i:s');
		
		$this->db->prepare("UPDATE `tbl_ws_orders` SET `mo_orderDate`=? WHERE `mo_id`=?", "si", array($date, $order['mo_id']));
		
		return $date;
	}
	
	public function updatePaymentMethod($order, $fields) {
		
		$this->db->prepare("UPDATE `tbl_ws_orders` SET `mo_paymentMethod`=? WHERE `mo_id`=?", "si", array($fields['method'], $order['mo_id']));
		
		return $fields['method'];
	}
	
	public function updatePaymentId($order, $fields) {
		
		$this->db->prepare("UPDATE `tbl_ws_orders` SET `mo_paymentId`=? WHERE `mo_id`=?", "si", array($fields['paymentId'], $order['mo_id']));
		
		return $fields['paymentId'];
	}
	
	public function updateInvoice($order, $number) {
		
		$this->db->prepare("UPDATE `tbl_ws_orders` SET `mo_orderInvoice`=? WHERE `mo_id`=?", "si", array($number, $order['mo_id']));
	}
	
	public function savePdfTemplate($order, $html) {
		
		$this->db->prepare("UPDATE `tbl_ws_orders` SET `mo_orderInvoiceTemplate`=? WHERE `mo_id`=?", "si", array($html, $order['mo_id']));
	}
	
	public function removeTemp($order) {
		
		// Find the temp order based on the order ID
		$tempOrder = $this->db->prepare("SELECT * FROM `tbl_ws_temp_orders` WHERE `wo_id`=?", "i", array($order['mo_tempId']));
		
		if (count($tempOrder) > 0) {
			
			// Delete temp order
			$this->db->prepare("DELETE FROM `tbl_ws_temp_orders` WHERE `wo_id`=?", "i", array($tempOrder[0]['wo_id']));
			
			// Delete rules
			$this->db->prepare("DELETE FROM `tbl_ws_temp_orderData` WHERE `wod_orderId`=?", "i", array($tempOrder[0]['wo_id']));
		}
	}
	
	public function validateOrder($order) {
		
		$required = array('wo_client_firstName', 'wo_client_lastName', 'wo_client_emailAddress', 'wo_client_phone', 'wo_client_address', 'wo_client_houseNumber', 'wo_client_zipcode', 'wo_client_place', 'wo_client_country');
		
		foreach ($required as $key => $val) {
			
			if (empty($order[$val]))
				return false;
		}
		
		return true;
	}
	
	static function addBtw($amt, $btw, $type = 'amt') {
		
		$newAmt = $amt * ((100 + $btw) / 100);
		
		if ($type == 'btw') {
			
			return ($newAmt - $amt);
		}
		
		return $newAmt;
	}
	
	static function subBtw($amt, $btw, $type = 'amt') {
		
		// The price without BTW
		$btw = ($amt / (100 + $btw)) * 100;
		
		// How much BTW there is
		$btwAmt = $amt - $btw;
		
		if ($type == 'amt')
			return $btw;
		
		return $btwAmt;
	}
	
	static function getBtwHoog($db) {
		
		// Get value of btw_hoog
		$checkHoog = $db->prepare("SELECT * FROM `tbl_ws_config` WHERE `wc_config_name`='btw_hoog'");
		
		// Hardcoded, overruled by query above unless not found
		$btwHoog = 21;
		
		if (count($checkHoog) > 0)
			$btwHoog = $checkHoog[0]['wc_config_value'];
		
		return $btwHoog;
	}
	
	static function getLeverTermijn($db) {
		
		// Get value of btw_hoog
		$checkTermijn = $db->prepare("SELECT * FROM `tbl_ws_config` WHERE `wc_config_name`='max_leverdatum'");
		
		// Hardcoded, overruled by query above unless not found
		$leverTermijn = 6;
		
		if (count($checkTermijn) > 0)
			$leverTermijn= $checkTermijn[0]['wc_config_value'];
			
		return $leverTermijn;
	}
	
	public function sendMail($order, $data, $fields, $attachment = '') {
		
		if ($order['mo_statusId'] != $fields['status']) {
			
			if ($fields['status'] >= 100 && $fields['status'] < 200) {
				
				$mailTemplate = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/paymentPixelplus/mailtemplates/mail_template_shop.txt');
				
				$placeholders = array();
				
				$placeHolders['fullName'] = $order['mo_client_firstName'] . ' ' . $order['mo_client_lastName'];
				$placeHolders['orderNumber'] = $order['mo_orderNumber'];
				
				$placeHolders['orderRules'] = '';
				
				// Pricing rules
				if (count($data) > 0) {
					
					foreach ($data as $key => $val) {
						
						// If next, insert empty row
						if ($key > 0)
							$placeHolders['orderRules'] .= '<tr><td colspan="2" height="5"><!-- -rowspacer- --></td></tr>';
						
						$placeHolders['orderRules'] .= '<tr><td><span style="font-size:14px;">';
						
						$date = new PP_DateTime($val['mod_leverDatum']);
						$subData = unserialize($val['mod_productData']);
						
						$dateOphaal = (!empty($subData['dataOphaal'])) ? new PP_DateTime($subData['dataOphaal']) : '';
						
						$placeHolders['orderRules'] .= '&rsaquo; Leverdatum: ' . $date->format('d/m/Y') . ' (' . $subData['dataTijd'] . ' uur)';
						
						if (!empty($dateOphaal))
							$placeHolders['orderRules'] .= ' | Ophaaldatum: ' . $dateOphaal->format('d/m/Y');
						
						$type = ($val['mod_productType'] == 'afval') ? '<b>Afval</b>' : '<b>Bouwstoffen kopen</b>';
						
						$placeHolders['orderRules'] .= '<br>- ' . $type . ' ' . $val['mod_productName'];
						
						if (isset($val['linked_rule'])) {
							
							$placeHolders['orderRules'] .= '<br>- <b>Bouwstoffen</b> ' . $val['linked_rule']['mod_productName'];
						}
						
						$placeHolders['orderRules'] .= '</span></td></tr>';
					}
				}
				
				// Addresses
				$placeHolders['address'] = $order['mo_client_address'] . ' ' . $order['mo_client_houseNumber'] . $order['mo_client_houseNumberAdd'];
				$placeHolders['address'] .= '<br>' . $order['mo_client_zipcode'] .  ' ' . $order['mo_client_place'];
				$placeHolders['address'] .= '<br>' . $order['mo_client_country'];
				$placeHolders['address'] .= '<br>' . $order['mo_client_phone'];
				$placeHolders['address'] .= '<br>' . $order['mo_client_emailAddress'];
				
				if ($order['mo_client_type'] == 'zakelijk') {
					
					$placeHolders['address'] = $order['mo_client_company'] . '<br>' . $placeHolders['address'];
				}
				
				if ($order['mo_paymentMethod'] == 'ideal')
					$order['mo_paymentMethod'] = 'iDeal';
				
				$placeHolders['opmerkingen'] = (!empty($order['mo_client_notes'])) ? $order['mo_client_notes'] : '-';
				
				$placeHolders['paymentState'] = 'betaald via ' . $order['mo_paymentMethod'];
				
				$placeHolders['totalPrice'] = Core::formatNum($order['mo_totalPrice_inc'], 'valuta');
				
				$placeHolders['pinCode'] = $order['mo_pinCode'];
				
				foreach ($placeHolders as $key => $val) {
					
					$mailTemplate = str_replace('{{placeHolder_' . $key . '}}', $val, $mailTemplate);
				}
				
				// Basic data
				$mail_subject = 'Uw bestelling bij Lortyeshop.eu';
				
				$mail_ontvanger = $order['mo_client_emailAddress'];
				
				$mail_bcc = 'planning@lortye.eu,administratie@lortye.eu';
				
				// Use mailgun to shoot mail the door out
				$mail = new PP_Mailer();
				
				$mail->addField('apiKey', 'ad8800d0c6d19ba0461991048f0bffe3');
				$mail->addField('base64', 1);
				$mail->addField('type', 'send');
				$mail->addField('from', 'info@lortyeshop.eu');
				$mail->addField('fromName', 'Lortyeshop.eu');
				$mail->addField('to', $mail_ontvanger);
				$mail->addField('bcc', $mail_bcc);
				$mail->addField('subject', $mail_subject);
				$mail->addField('message', base64_encode($mailTemplate));

				if (!empty($attachment))
					$mail->addField('attachments', urlencode($attachment));
				
				$mail->send();
				
// 				echo $mailTemplate;
			}
		}
	}
}