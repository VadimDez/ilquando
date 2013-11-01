<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 4/11/13
 * Time: 8:49 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Resources;

use Tonic\Response, Tonic\Resource;

/**
 * @uri /
 */
class start extends Resource
{

    /**
     * @method GET
     * @provides text/html
     */
    function init()
    {

        $page = $this->container["twig"]->render('start.html');

        return new Response(Response::OK, $page, array(
            'content-type' => 'text/html'
        ));
    }

    /**
     * @method POST
     */
    function post()
    {
        $data = array();
        parse_str($this->request->data, $data);

        $parser = new parser();
        $string = $data['when'];//$parser->pars($data['when']);
        $control = new control();
        $id = $control->start($string, $this->container["conn"]);
        /*$response = new Response();
        $response->code = Response::MOVEDPERMANENTLY;
        $response->headers( 'Location', 'google.com' );
        $response->body = "<b>Succesfully logged out!</b>";
        $_SESSION[ 'loggedin' ] = false;
        return $response;*/

        //$page = $this->container["twig"]->render('start.html');

        return new Response(Response::MOVEDPERMANENTLY, NULL, array(
            'location' => $this->container["burl"] . '/' . $id
        ));
    }
}

?>