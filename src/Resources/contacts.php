<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 5/1/13
 * Time: 12:58 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Resources;

use Tonic\Response, Tonic\Resource;

/**
 * @uri /contacts
 */
class contact extends Resource
{
    /**
     * @method GET
     * @provides text/html
     */
    function init()
    {

        $page = $this->container["twig"]->render('contacts.html');

        return new Response(Response::OK, $page, array(
            'content-type' => 'text/html'
        ));
    }

    /**
     * @method POST
     * @provides text/html
     */
    function receive()
    {
        $data = array();
        parse_str($this->request->data, $data);

        /*
        $parser = new parser();
        $msg    = $parser->textParsingWithNL($data['message']);
        $name   = $parser->pars($data['name']);
        $lastname=$parser->pars($data['lastname']);
        $email  = $parser->pars($data['email']);
        */

        $msg    = $data['message'];
        $name   = $data['name'];
        $lastname=$data['lastname'];
        $email  = $data['email'];

        $to      = 'vasp3d@gmail.com';
        $subject = 'ilquando.it Contact';
        $message = $name . ' ' . $lastname . ': ' . $msg;
        $headers = 'From:' . $email . "\r\n";
        if(mail($to, $subject, $message, $headers))
        {
            // e' stato spedito
            $array = array('text' => 'Il tuo messaggio è spedito.');
            $page = $this->container["twig"]->render('contactsOK.html', $array);

            return new Response(Response::OK, $page, array(
                'content-type' => 'text/html'
            ));
        }
        else
        {
            $array = array('text' => 'Errore. Il tuo messaggio non è spedito.');
            $page = $this->container["twig"]->render('contactsOK.html', $array);

            return new Response(Response::OK, $page, array(
                'content-type' => 'text/html'
            ));
        }
    }
}


?>