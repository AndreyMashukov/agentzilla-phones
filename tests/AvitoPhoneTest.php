<?php

/**
 * PHP version 7.1
 *
 * @package Agentzilla\phones
 */

namespace Tests;

use \Agentzilla\Phones\AvitoPhone;
use \PHPUnit\Framework\TestCase;
use \SimpleXMLElement;

/**
 * Avito phone test
 *
 * @author  Andrey Mashukov <a.mashukoff@gmail.com>
 * @version SVN: $Date: 2018-02-12 18:47:40 +0000 (Mon, 12 Feb 2018) $ $Revision: 1 $
 * @link    $HeadURL: https://svn.agentzilla.ru/phones/trunk/tests/AvitoPhoneTest.php $
 *
 * @runTestsInSeparateProcesses
 */

class AvitoPhoneTest extends TestCase
    {

	/**
	 * Should collect phone number of seller from avito
	 *
	 * @return void
	 */

	public function testShouldCollectPhoneNumberOfSellerFromAvito()
	    {
		$headers = [
		    "Host"                     => "m.avito.ru",
		    "Accept"                   => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
		    "Accept-Language"          => "ru,en-US;q=0.7,en;q=0.3",
		    "Accept-Encoding"          => "gzip, deflate, br",
		    "Connection"               => "keep-alive",
		    "Upgrade-Insecure-Request" => "1",
		    "Cache-Control"            => "max-age=0",
		];

		$url        = "https://m.avito.ru/moskva/kvartiry/2-k_kvartira_38_m_1112_et._856769841";
		$avitophone = new AvitoPhone($url, $headers);
		$phone      = $avitophone->get();
		$this->assertEquals("79037230205", $phone);

		$avitophone = new AvitoPhone($url);
		$phone      = $avitophone->get();
		$this->assertEquals("79037230205", $phone);
	    } //end testShouldCollectPhoneNumberOfSellerFromAvito()


    } //end class

?>
