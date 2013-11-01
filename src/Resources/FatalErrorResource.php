<?php

namespace Resources;

use Tonic\Response, Tonic\Resource;

class FatalErrorResource extends Resource
{

	/**
	 * @priority 1
	 */
	function get() {
		
		$page = $this->container["twig"]->render('500.html');

		return new Response(Response::INTERNALSERVERERROR, $page, array(
				'content-type' => 'text/html'
		));
	}
}

?>