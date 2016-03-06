<?php
/*Autor:Matej Marusak, VUT FIT 2016, IPP-SYN, proj. 1
#SYN:xmarus06
 * Modul pre upravu formatovacich prikazov na skutocne tagy.
 * Kazdy tag sa skontroluje ci existuje v mnozine povolenych formatov a ak hej, tak sa skonvertuje
 */

function editTags($formater){ 
    
    //rozdelime podla ciarok
    $listOfFormaters = explode(",",$formater);
    
    //odstranime vsetky whitespaces
    foreach($listOfFormaters as &$ft)
        $ft = trim($ft); 

    $beginTags = "";
    $endTags = "";
    
    foreach($listOfFormaters as $f){
        if ($f === "bold"){
            $beginTags = $beginTags."<b>";
            $endTags   = "</b>".$endTags;
        } else if ($f === "italic"){
            $beginTags = $beginTags."<i>";
            $endTags   = "</i>".$endTags;
        } else if ($f === "underline"){
            $beginTags = $beginTags."<u>";
            $endTags   = "</u>".$endTags;
        } else if ($f === "teletype"){
            $beginTags = $beginTags."<tt>";
            $endTags   = "</tt>".$endTags;
        } else if (strlen($f) === 6 && substr($f,0,5) === "size:"){
            $posNum = intval(substr($f,-1));
            if ($posNum < 1 || $posNum > 7){
                error("Nie je velkost fontu od 1 - 7\n",4);
            }
            else{
                $beginTags = $beginTags."<font size=".$posNum.">";
                $endTags   = "</font>".$endTags;
            } 
        }else if (strlen($f) === 12 && substr($f,0,6) === "color:"){
            $posNum = substr($f,-6);
            //kontrola ci je hexadecimalne !!TODO berie aj a-fA-F0-9
            if (!ctype_xdigit($posNum)){
                error("Nie je hexadecimalne zadana farba\n",4);
            } else {
                $beginTags = $beginTags."<font color=#".$posNum.">";
                $endTags   = "</font>".$endTags;
            }
        } else{
            error("Nespravny html kod\n",4);
        }
    
    }
        
    return array($beginTags, $endTags);
}


?>
