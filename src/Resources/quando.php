<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 4/23/13
 * Time: 8:53 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Resources;

use Tonic\Response, Tonic\Resource;
/**
 * @uri /:id
 */
class quando extends Resource
{
    /**
     * @method GET
     * @provides text/html
     */

    function init($id)
    {
        // funzione per far vedere la pubblicazione di "quando"

        $control = new control();

        $control->dbConn = $this->container["conn"];

        if($control->isExistByID($id))
        {
            $pick       = new pick();

            $info       = $pick->pickInfo($id, $this->container["conn"]);

            $text       = $info['text'];//utf8_encode($info['text']);

            $dataInsert = strftime("%d %b %Y", strtotime($info['dateInsert']));

            $array      = array( "text" => $text,
                                 "data" => $info['response'],
                                 "dataInsert" => $dataInsert,
                                 "id" => $id);

            $page       = $this->container["twig"]->render('quando.html', $array);

            return new Response(Response::OK, $page, array(
                'content-type' => 'text/html'
            ));
        }
        else
        {
            $page = $this->container["twig"]->render('404.html');

            return new Response(Response::NOTFOUND, $page, array(
                'content-type' => 'text/html'
            ));
        }

    }
}

?>