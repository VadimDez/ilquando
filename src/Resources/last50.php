<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 4/29/13
 * Time: 10:06 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Resources;

use Tonic\Response, Tonic\Resource;

/**
 * @uri /last
 */
class last extends Resource
{
    /**
     * @method GET
     * @provides text/html
     */
    function init()
    {

        $sql = $this->container["conn"]->query("SELECT * FROM quando ORDER BY dateInsert DESC LIMIT 0, 50");

        $converter = new converter();
        $array = $converter->convert($sql);


        $page = $this->container["twig"]->render('last50.html', $array);

        return new Response(Response::OK, $page, array(
            'content-type' => 'text/html'
        ));
    }
}

?>