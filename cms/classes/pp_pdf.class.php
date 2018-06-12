<?php

class PP_PDF {
	
	private $invoiceNumber = '';
	private $template = '';
	
	public function __construct($invoiceNumber) {
		
		$this->invoiceNumber = $invoiceNumber;
	}
	
	public function loadTemplate($template) {
		
		$this->template = file_get_contents($template);
	}
	
	public function generatePdf($type) {
		
		if ($type == 'tools') {
			
			$postData = array(
				
				'apiKey' => 'c5dac71a9c43282df1b6bea5a8197090',
				'clientKey' => 'lortye',
				'html' => base64_encode($this->template),
				'paperSize' => 'A4', // Optioneel: Indien niet nodig, weglaten, default = A4
				'orientation' => 'portrait', // Optioneel: Indien niet nodig, weglaten, default = portrait
				'fileName' => $this->invoiceNumber // Optioneel: Geef een andere filename aan het bestand (.pdf niet vereist)
			);
			
			$url = 'http://tools.pixelplus.nl/html-to-pdf/';
			
			$data = $this->loadPage($url, 1, $postData);
			
			return $data;
		}
		else {
			
			// Only used if it has its own generator (not coded)
		}
	}
	
	public function getTemplate() {
		
		return $this->template;
	}
	
	public function handleFactuur($data, $cms) {
		
		$placeholders = array();
		
		$btwHoog = Order::getBtwHoog($cms['database']);
		
		$date = new PP_DateTime($data[0]['mo_orderDate']);
		
		$placeHolders['factuurdatum'] = $date->format('d-m-Y');
		$placeHolders['factuurnummer'] = $this->invoiceNumber;
		
		// Addresses
		$placeHolders['altAdres'] = $data[0]['mo_client_address'] . ' ' . $data[0]['mo_client_houseNumber'] . $data[0]['mo_client_houseNumberAdd'];
		$placeHolders['altAdres'] .= '<br>' . $data[0]['mo_client_zipcode'] .  ' ' . $data[0]['mo_client_place'];
		$placeHolders['altAdres'] .= '<br>' . $data[0]['mo_client_country'];
		
		if ($data[0]['mo_client_type'] == 'zakelijk') {
			
			$placeHolders['altAdres'] = $data[0]['mo_client_company'] . '<br>' . $placeHolders['altAdres'];
		}
		
		if ($data[0]['mo_client_alt_address'] == 1) {
			
			$placeHolders['adres'] = $data[0]['mo_client_address_factuur'] . ' ' . $data[0]['mo_client_houseNumber_factuur'] . $data[0]['mo_client_houseNumberAdd_factuur'];
			$placeHolders['adres'] .= '<br>' . $data[0]['mo_client_zipcode_factuur'] .  ' ' . $data[0]['mo_client_place_factuur'];
			$placeHolders['adres'] .= '<br>' . $data[0]['mo_client_country_factuur'];
			
			if (!empty($data[0]['mo_client_company_tav'])) {
				
				$placeHolders['adres'] = 't.a.v. ' . $data[0]['mo_client_company_tav'] . '<br>' . $placeHolders['adres'];
			}
			
			if (!empty($data[0]['mo_client_company_factuur'])) {
				
				$placeHolders['adres'] = $data[0]['mo_client_company_factuur'] . '<br>' . $placeHolders['adres'];
			}
			else {
				
				$fullName = $data[0]['mo_client_firstName'];
				
				$fullName .= ' ' . $data[0]['mo_client_lastName'];
				
				$placeHolders['adres'] = $fullName . '<br>' . $placeHolders['adres'];
			}
		}
		else {
			
			$placeHolders['adres'] = $data[0]['mo_client_address'] . ' ' . $data[0]['mo_client_houseNumber'] . $data[0]['mo_client_houseNumberAdd'];
			$placeHolders['adres'] .= '<br>' . $data[0]['mo_client_zipcode'] .  ' ' . $data[0]['mo_client_place'];
			$placeHolders['adres'] .= '<br>' . $data[0]['mo_client_country'];
			
			if ($data[0]['mo_client_type'] == 'zakelijk') {
				
				$placeHolders['adres'] = $data[0]['mo_client_company'] . '<br>' . $placeHolders['adres'];
			}
			else {
				
				$fullName = $data[0]['mo_client_firstName'];
					
				$fullName .= ' ' . $data[0]['mo_client_lastName'];
				
				$placeHolders['adres'] = $fullName . '<br>' . $placeHolders['adres'];
			}
		}
		
		// Pricing
		$placeHolders['exBtwTotaal'] = Core::formatNum($data[0]['mo_totalPrice_ex'], 'valuta');
		$placeHolders['btwBedrag'] = Core::formatNum(($data[0]['mo_totalPrice_inc'] - $data[0]['mo_totalPrice_ex']), 'valuta');
		$placeHolders['incBtwTotaal'] = Core::formatNum($data[0]['mo_totalPrice_inc'], 'valuta');
		
		$placeHolders['regels'] = '';
		
		// Pricing rules
		if (count($data[1]) > 0) {
			
			foreach ($data[1] as $key => $val) {
				
				$date = new PP_DateTime($val['mod_leverDatum']);
				
				$borderBottom = '';
				$borderTop = '';
				
// 				if (isset($val['linked_rule'])) {
					
// 					$borderBottom = ' style="border-bottom: none;"';
// 					$borderTop = ' style="border-top: none;"';
// 				}
				
				$placeHolders['regels'] .= '<tr>';
				
// 				$placeHolders['regels'] .= '<td' . $borderBottom . '>' . $date->format('d-m-Y') . '</td>';
				
				$placeHolders['regels'] .= '<td colspan="2"><span style="font-family:\'Arial, Helvetica\';font-weight: normal;">';
				
				$placeHolders['regels'] .= '- ' . $val['mod_productName'] . ' (' . Core::formatNum(Order::addBtw($val['mod_productPrice'], $val['mod_vat']), 'valuta') . ')';
				
				$totalPrice = $val['mod_productPrice'];
				$totalPriceBtw = Order::addBtw($val['mod_productPrice'], $val['mod_vat']);
				
				if (isset($val['linked_rule'])) {
					
					$placeHolders['regels'] .= '<br>- ' . $val['linked_rule']['mod_productName'] . ' (' . Core::formatNum(Order::addBtw($val['linked_rule']['mod_productPrice'], $val['mod_vat']), 'valuta') . ')';
					
					$totalPrice += $val['linked_rule']['mod_productPrice'];
					$totalPriceBtw += Order::addBtw($val['linked_rule']['mod_productPrice'], $val['linked_rule']['mod_vat']);
				}
				
				if ($val['mod_meerkosten'] > 0) {
					
					$placeHolders['regels'] .= '<br>- Meerkosten t.b.v. langere huurtermijn (' . Core::formatNum(Order::addBtw($val['mod_meerkosten'], $btwHoog), 'valuta') . ')';
					
					$totalPrice += $val['mod_meerkosten'];
					$totalPriceBtw += Order::addBtw($val['mod_meerkosten'], $btwHoog);
				}
				
				if ($val['mod_korting'] > 0) {
					
					$placeHolders['regels'] .= '<br>- Combinatiekorting (' . Core::formatNum(Order::addBtw($val['mod_korting'], $btwHoog), 'valuta') . ')';
					
					$totalPrice -= $val['mod_korting'];
					$totalPriceBtw -= Order::addBtw($val['mod_korting'], $btwHoog);
				}
				
				$placeHolders['regels'] .= '</span></td>';
				
/*				$placeHolders['regels'] .= '<td>1</td>';
				$placeHolders['regels'] .= '<td>' . Core::formatNum($totalPriceBtw, 'valuta') . '</td>';
				$placeHolders['regels'] .= '<td>stuk</td>';*/

				$placeHolders['regels'] .= '<td><span style="font-family:\'Arial, Helvetica\';font-weight: normal;">' . Core::formatNum($totalPriceBtw, 'valuta') . '</span></td>';
				$placeHolders['regels'] .= '<td><span style="font-family:\'Arial, Helvetica\';font-weight: normal;">' . Core::formatNum($val['mod_vat']) . '%</td></td>';
				
				$placeHolders['regels'] .= '</tr>';
				
				/*
				 * <tr>
                    <td>18-11-2016</td>
                    <td>Container 6m<sup>3</sup> (Grond)</td>
                    <td>1</td>
                    <td>€ 140,00</td>
                    <td>stuk</td>
                    <td>€ 140,00</td>
                    <td>21%</td>
                </tr>
                */
			}
		}
		
		foreach ($placeHolders as $key => $val) {
			
			$this->template = str_replace('{{placeHolder_' . $key . '}}', $val, $this->template);
		}
	}
	
	private function loadPage($page, $isPost = 0, $postData = '') {
		
		$ch = curl_init();
		
		// curl_setopt($ch, CURLOPT_USERPWD, "api:" . $key);
		$res = curl_setopt($ch, CURLOPT_URL, $page);
		if ($isPost == 1) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		}
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		$result = curl_exec($ch);
		
		if (curl_error($ch)) {
			die('error:' . curl_error($ch));
		}
		
		curl_close($ch);
		
		return $result;
	}
}

?>