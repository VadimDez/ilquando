<?php

namespace Resources;

use Tonic\Response, Tonic\Resource;

class NotFoundResource extends Resource
{

	/**
	 * @priority 1
	 */
	function get() {

        $page = $this->container["twig"]->render('404.html');

		return new Response(Response::NOTFOUND, $page, array(
				'content-type' => 'text/html'
		));
	}
}

?>