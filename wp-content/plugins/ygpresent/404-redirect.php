<?php
/**
 * Created by PhpStorm.
 * User: Zeter
 * Date: 9/18/2016
 * Time: 1:39 AM
 */

add_action('wp', 'redirect_to_404_page');

function redirect_to_404_page(){

    $exception_urls_not_to_redirect = ['/admin'];
    $curUri = $_SERVER['REQUEST_URI'];

    if(in_array($curUri, $exception_urls_not_to_redirect))
    {
        return;
    }


    if(is_404())
    {
        header ('HTTP/1.1 301 Moved Permanently');
        header ("Location: " . '/not-found');
        exit;
    }

}

function currentURL()
{
    $prt = $_SERVER['SERVER_PORT'];
    $sname = $_SERVER['SERVER_NAME'];

    if (array_key_exists('HTTPS',$_SERVER) && $_SERVER['HTTPS'] != 'off' && $_SERVER['HTTPS'] != '')
        $sname = "https://" . $sname;
    else
        $sname = "http://" . $sname;

    if($prt !=80)
    {
        $sname = $sname . ":" . $prt;
    }
    $path = $sname . $_SERVER["REQUEST_URI"];
    return $path ;
}
