<?php
/*Autor:Matej Marusak, VUT FIT 2016, IPP-SYN, proj. 1
#SYN:xmarus06
 * Modul pre tlac chyb na stderr a ukoncenie skriptu s chybou.
 */


//otvori stderr a vytlaci hlasu zadanu parametrom
//ukonci s zadanym chybovym kodom cely skript
function error($message, $code){
    $stderr = fopen('php://stderr', 'a');
    fwrite($stderr, $message);
    exit($code);
}
?>
