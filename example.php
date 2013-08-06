<?php
/****
	*	@name		SEPA Incasso voorbeeld
	*	@version	1.1 (06-08-2013)
	*	@author		Mark Hameetman <mark@bureaupartners.nl> / Rein Schaap <info@reinschaap.nl>
	*	@copyright	2013 BureauPartners B.V.
	*	@link		https://www.bureaupartners.nl/2013/07/sepa-incasso-open-source-php-class/
*/
header("Content-type: text/xml");

include_once('sepa.class.php');

$oSEPA = new SEPA;
$oSEPA->setCollectionDestination('BureauPartners B.V.', 'NL00RABO0123456789', 'RABONL2U', 'NL87ZZZ573174610000');
$oSEPA->addCollection('M. Hameetman', 'NL00RABO0123456789', 'RABONL2U', 99.50, 'Abonnement Juni 2013 (Factuur 2013-0001)', date('Y-m-d'), '0000001' );
$oSEPA->addCollection('M. Hameetman', 'NL00RABO0123456789', 'RABONL2U', 99.50, 'Abonnement Juli 2013 (Factuur 2013-0002)', date('Y-m-d'), '0000002' );

echo $oSEPA->getXML();

?>
