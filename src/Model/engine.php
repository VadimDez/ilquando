<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 4/13/13
 * Time: 5:24 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Resources;

use Tonic\Response, Tonic\Resource;

class parser
{
    function pars($textTosend)
    {
        // parsing
        $textTosend = str_replace("\\r\\n", " ", $textTosend);
        $textTosend = str_replace("\\n", " ", $textTosend);
        $textTosend = str_replace("\\r", " ", $textTosend);
        $textTosend = stripslashes($textTosend);
        $textTosend = strip_tags($textTosend);
        $textTosend = mysql_real_escape_string($textTosend);

        $textTosend = trim($textTosend, '\r\n');
        $textTosend = preg_replace('/\'/i','&#39;', $textTosend);
        $textTosend = preg_replace('/`/i','&#96;', $textTosend);
        $textTosend = mysql_real_escape_string($textTosend);

        return $textTosend;
    }

    function textParsingWithNL($textTosend)
    {
        // parsing lasciando <br />

        $textTosend = str_replace("\\r\\n", "<br />", $textTosend);
        $textTosend = str_replace("\\n", "<br />", $textTosend);
        $textTosend = str_replace("\\r", "<br />", $textTosend);
        $textTosend = stripslashes($textTosend);
        $textTosend = strip_tags($textTosend, '<br>');
        $textTosend = mysql_real_escape_string($textTosend);

        $textTosend = trim($textTosend, '\r\n');
        $textTosend = preg_replace('/\'/i','&#39;', $textTosend);
        $textTosend = preg_replace('/`/i','&#96;', $textTosend);
        $textTosend = mysql_real_escape_string($textTosend);

        return $textTosend;
    }

    function parsItalian($textTosend)
    {
        $textTosend = str_replace('Ã³', 'a', $textTosend);

        return $textTosend;
    }
}

class control
{
    public $dbConn;

    function start($string, $conn)
    {
        //$string = utf8_decode($string);
        $this->dbConn = $conn;

        $id = $this->ifExist($string);
        // se c'e' id - allora ritorno id
        // se non c'e' id - inserisco, restituisco id e lo ritorno
        if($id == NULL)
        {

            $this->insertIn($string);
            $id = $this->ifExist($string);
        }

        return $id;
    }

    function ifExist($string)
    {
        // funzione che controlla se esiste la tupla con una stringa uguale
        // se si - ritoorna id
        // altrimenti non ritorna nulla


        $sql   = "SELECT id FROM quando WHERE text= ?";
        $query = $this->dbConn->prepare($sql);
        $query->bindValue(1, $string);
        $query->execute();

        $query = $query->fetch();

        if($query != NULL)
        {
            return $query['id'];
        }

    }

    function insertIn($string)
    {
        $random = new randomize();
        $sql   = "INSERT INTO quando(text, response, dateInsert) VALUES(?, ?, now())";
        $query = $this->dbConn->prepare($sql);
        $query->bindValue(1, $string);
        $query->bindValue(2, $random->random());
        $query->execute();

    }

    function isExistByID($id)
    {
        $sql   = "SELECT id FROM quando WHERE id= ?";
        $query = $this->dbConn->prepare($sql);
        $query->bindValue(1, $id);
        $query->execute();

        $query = $query->fetch();

        if($query != NULL)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}


class pick
{
    // la classe che restituisce l'info
    function pickInfo($id, $conn)
    {

        $sql   = "SELECT * FROM quando WHERE id= ?";
        $query = $conn->prepare($sql);
        $query->bindValue(1, $id);
        $query->execute();

        $query = $query->fetch();

        return $query;
    }
}

class randomize
{
    // classe per generare randomicamente le date

    function random()
    {
        // prendo le date di oggi
        $day    = date("d");
        $month  = date("m");
        $year   = date("Y");

        $randomYaer = rand($year, $year+45);

        if($randomYaer == $year)
        {
            $randomMonth = rand($month, 12);

            if($randomMonth == $month)
            {
                if($randomMonth == 2)
                {
                    if($randomYaer%4 == 0)
                    {
                        $randomDay = rand($day, 29);
                    }
                    else
                    {
                        $randomDay = rand($day, 28);
                    }
                }
                else
                {
                    if($randomMonth%2 == 0)
                    {
                        $randomDay = rand($day, 30);
                    }
                    else
                    {
                        $randomDay = rand($day, 31);
                    }
                }

            }
            else
            {
                if($randomMonth%2 == 0)
                {
                    $randomDay = rand(1, 30);
                }
                else
                {
                    $randomDay = rand(1, 31);
                }
            }
        }
        else
        {
            $randomMonth = rand(1, 12);

            if($randomMonth%2 == 0)
            {
                $randomDay = rand(1, 30);
            }
            else
            {
                $randomDay = rand(1, 31);
            }
        }

        if($randomYaer == $year && $randomMonth == $month && $randomDay == $day)
        {
            $string = "Oggi!";
        }
        else
        {
            $randomMonth = $this->month($randomMonth);

            $string  = $randomDay . ' ' . $randomMonth . ' ' . $randomYaer;
        }


        return $string;
    }

    function month($randomMonth)
    {
        switch($randomMonth)
        {
            case 1: $randomMonth = "gennaio";
                break;
            case 2: $randomMonth = "febbraio";
                break;
            case 3: $randomMonth = "marzo";
                break;
            case 4: $randomMonth = "aprile";
                break;
            case 5: $randomMonth = "maggio";
                break;
            case 6: $randomMonth = "giugno";
                break;
            case 7: $randomMonth = "luglio";
                break;
            case 8: $randomMonth = "agosto";
                break;
            case 9: $randomMonth = "settembre";
                break;
            case 10: $randomMonth = "ottobre";
                break;
            case 11: $randomMonth = "novembre";
                break;
            case 12: $randomMonth = "dicembre";
                break;
        }

        return $randomMonth;
    }
}


class converter
{
    function convert($array, $name = 'element')
    {
        // function that pick fetch array and transform it into array for TWIG
        $arr = array();

        while ($row = $array->fetch())
        {
            array_push($arr, $row);
        }

        $arr = array($name => $arr);
        return $arr;
    }
}

?>