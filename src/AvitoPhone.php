<?php

/**
 * PHP version 7.1
 *
 * @package Agentzilla\phones
 */

namespace Agentzilla\Phones;

use \Agentzilla\HTTP\HTTPclient;
use \DOMDocument;
use \DOMXPath;

/**
 * Class for get phones from avito.ru
 *
 * @author  Andrey Mashukov <a.mashukoff@gmail.com>
 * @version SVN: $Date: 2018-02-12 18:47:40 +0000 (Mon, 12 Feb 2018) $ $Revision: 1 $
 * @link    $HeadURL: https://svn.agentzilla.ru/phones/trunk/src/AvitoPhone.php $
 */

class AvitoPhone
    {

	/**
	 * URL
	 *
	 * @var string
	 */
	private $_url;

	/**
	 * Html
	 *
	 * @var string
	 */
	public $html = null;

	/**
	 * DOM
	 *
	 * @var DOMDocument
	 */
	public $dom = null;

	/**
	 * DOMXPath
	 *
	 * @var DOMXPath
	 */
	public $xpath = null;

	/**
	 * Headers
	 *
	 * @var array
	 */
	public $headers = null;

	/**
	 * Prepare
	 *
	 * @param SimpleXMLElement $advert XML advert
	 *
	 * @return void
	 */

	public function __construct(string $url, array $headers = [])
	    {
		$this->_url = $url;

		if ($headers === [])
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
		    } //end if

		$this->headers = $headers;
		$this->_loadDOM();
	    } //end __construct()


	/**
	 * Load DOM from html
	 *
	 * @return void
	 */

	private function _loadDOM()
	    {
		$http       = new HTTPclient($this->_url, [], $this->headers);
		$this->html = gzdecode($http->getWithProxy());
		$this->dom  = new DOMDocument();
		@$this->dom->loadHTML($this->html);

		$this->xpath = new DOMXPath($this->dom);
	    } //end _loadDOM()


	/**
	 * Get phone
	 *
	 * @return void
	 */

	public function get()
	    {
		$phone   = null;
		$request = $this->_getJsonAvito();

		if (empty($request->{"phone"}) === false || empty($request->{"error"}) === false)
		    {
			if (empty($request->{"error"}) === false)
			    {
				if (preg_match("/пожалуйста, обновите страницу/ui", (string) $request->{"error"}) > 0)
				    {
					$this->_refreshDOM();
					$request = $this->_getJsonAvito();
				    } //end if

			    } //end if

			if (empty($request->{"phone"}) === false)
			    {
				$phone = preg_replace("/\D/ui", "", (string) $request->{"phone"});
			    } //end if

		    } //end if

		return $phone;
	    } //end get()


	/**
	 * Get avito json response
	 *
	 * @return json Response
	 */

	private function _getJsonAvito()
	    {
		$headers = [
		    "Accept"           => "application/json",
		    "Accept-Encoding"  => "gzip, deflate, sdch, br",
		    "Accept-Language"  => "ru,en;q=0.8",
		    "Connection"       => "keep-alive",
		    "Host"             => "m.avito.ru",
		    "Referer"          => $this->_url,
		    "User-Agent"       => "Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:49.0) Gecko/20100101 Firefox/49.0",
		    "X-Requested-With" => "XMLHttpRequest",
		];

		$list = $this->xpath->query("//a[contains(@class, 'js-action-show-number')]/@href");
		$url  = "https://m.avito.ru" . $list[0]->textContent . "?async";
		$http = new HTTPclient($url, array(), $headers);
		$phone = $http->getWithProxy();

		return  json_decode(gzdecode($phone));
	    } //end _getJsonAvito()


	/**
	 * Refresh DOM
	 *
	 * @return void
	 */

	private function _refreshDOM()
	    {
		$http       = new HTTPclient($this->link, array(), array_merge($this->headers, ["Referer" => $this->_url]));
		$this->html = gzdecode($http->getWithProxy());
		$this->_loadDOM();
	    } //end _refreshDOM()


    } //end class

?>
