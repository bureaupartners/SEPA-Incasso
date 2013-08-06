<?php
/****
	*	@name		SEPA Incasso class
	*	@version	1.1 (06-08-2013)
	*	@author		Mark Hameetman <mark@bureaupartners.nl> / Rein Schaap <info@reinschaap.nl>
	*	@copyright	2013 BureauPartners B.V.
	*	@link		https://www.bureaupartners.nl/2013/07/sepa-incasso-open-source-php-class/
*/

class SEPA {
	
	private $sXML						=	NULL;
	private $iDate						=	NULL;
	private $aCollection				=	array();
	private $iSum						=	0.00;
	private $iCurrency					=	'EUR';
	private $aCollectionDestination		=	array();
	
	public function __construct(){
		$this->sXML = '<?xml version="1.0" encoding="utf-8"?><Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.008.001.02" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><CstmrDrctDbtInitn>';
		$this->setDate('+1 day');
	}
	
	public function setCollectionDestination($sName, $sIBAN, $sBIC, $sCREDITOR){
		$this->aCollectionDestination	=	array(
											'name'			=>	$sName,
											'iban'			=>	$sIBAN,
											'bic'			=>	$sBIC,
											'creditorid'		=>	$sCREDITOR
										);
	}
	
	public function setDate($sDate){
		$this->iDate = strtotime($sDate);
	}
	
	public function addCollection($sName, $sIBAN, $sBIC, $iAmount, $sDescription, $sSignedDate, $sMandateID){
		$iAmount = number_format($iAmount, 2, '.', '');
		$this->iSum += $iAmount;
		$this->aCollection[] = array(
										'name'			=>	$sName,
										'iban'			=>	$sIBAN,
										'bic'			=>	$sBIC,
										'amount'		=> 	$iAmount,
										'descr'			=>	$sDescription,
										'mandateid'		=>	$sMandateID,
										'endtoendid'		=>	$sMandateID,
										'signDate'		=>	strtotime($sSignedDate)
							);
	}
	
	private function addGroupHeader(){
		$this->sXML .= '<GrpHdr><MsgId>'.time().'</MsgId><CreDtTm>'.date(DATE_ATOM).'</CreDtTm><BtchBookg>true</BtchBookg><NbOfTxs>'.count($this->aCollection).'</NbOfTxs><CtrlSum>'.number_format($this->iSum, 2, '.', '').'</CtrlSum><InitgPty><Nm>'.$this->aCollectionDestination['name'].'</Nm></InitgPty></GrpHdr><PmtInf><PmtInfId>'.time().'</PmtInfId><PmtMtd>DD</PmtMtd><NbOfTxs>'.count($this->aCollection).'</NbOfTxs><CtrlSum>'.number_format($this->iSum, 2, '.', '').'</CtrlSum><PmtTpInf><SvcLvl><Cd>SEPA</Cd></SvcLvl><LclInstrm><Cd>CORE</Cd></LclInstrm><SeqTp>FRST</SeqTp></PmtTpInf><ReqdColltnDt>'.date('Y-m-d', $this->iDate).'</ReqdColltnDt><Cdtr><Nm>'.$this->aCollectionDestination['name'].'</Nm></Cdtr><CdtrAcct><Id><IBAN>'.$this->aCollectionDestination['iban'].'</IBAN></Id></CdtrAcct><CdtrAgt><FinInstnId><BIC>'.$this->aCollectionDestination['bic'].'</BIC></FinInstnId></CdtrAgt><ChrgBr>SLEV</ChrgBr><CdtrSchmeId><Id><PrvtId><Othr><Id>'.$this->aCollectionDestination['creditorid'].'</Id><SchmeNm><Prtry>SEPA</Prtry></SchmeNm></Othr></PrvtId></Id></CdtrSchmeId>';
	}
	
	private function addCollections(){
		foreach($this->aCollection as $aCollect){
			$this->sXML .=	'<DrctDbtTxInf><PmtId><EndToEndId>'.$aCollect['endtoendid'].'</EndToEndId></PmtId><InstdAmt Ccy="'.$this->iCurrency.'">'.$aCollect['amount'].'</InstdAmt><DrctDbtTx><MndtRltdInf><MndtId>'.$aCollect['mandateid'].'</MndtId><DtOfSgntr>'.date('Y-m-d', $aCollect['signDate']).'</DtOfSgntr></MndtRltdInf></DrctDbtTx><DbtrAgt><FinInstnId><BIC>'.$aCollect['bic'].'</BIC></FinInstnId></DbtrAgt><Dbtr><Nm>'.$aCollect['name'].'</Nm></Dbtr><DbtrAcct><Id><IBAN>'.$aCollect['iban'].'</IBAN></Id></DbtrAcct><RmtInf><Ustrd>'.$aCollect['descr'].'</Ustrd></RmtInf></DrctDbtTxInf>';
		}
	}
	
	public function getXML(){
		$this->addGroupHeader();
		$this->addCollections();
		$this->sXML .= ' </PmtInf></CstmrDrctDbtInitn></Document>';
		return $this->sXML;
	}
	
}

?>
