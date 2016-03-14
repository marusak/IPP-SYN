<?php
/*Autor:Matej Marusak, VUT FIT 2016, IPP-SYN, proj. 1
#SYN:xmarus06
 * Modul pre konverziu regex ala IPP do regex ala PHP.
 * Je to na principe konecneho automatu. Stavy podla znakov, ktore mozu nasledovat 
 */

    //KOntrola ++, **, *+, +* a pod, resp rozsirenie pre NQS
    function checkForB($old){
        //TODO rozsirenie NQS
        /*
        if (strpos($old, '++') !== false)
            error("Chyba v regularnom vyraze", 4);
        if (strpos($old, '*+') !== false)
            error("Chyba v regularnom vyraze", 4);
        if (strpos($old, '+*') !== false)
            error("Chyba v regularnom vyraze", 4);
        if (strpos($old, '**') !== false)
            error("Chyba v regularnom vyraze", 4);
         */
        $old = preg_replace('/(\+*\*+\+*)+/', '*', $old);
        $old = preg_replace('/(\\++)/', '+', $old);
        return $old;

    }
        
    //Dostane regex z tvare v akom sa nachadza v konfgacnom subore a prerobi ho na format pre php 
        //regex
    function editRegex($old){
        /*if (!checkForB($old))
            error("Chyba v regularnom vyraze", 4);
         */
        $old = checkForB($old);
        $pars = 0;//pocet zatvoriek
        $state = 0;
        $result = "";
        $parsInNeg = 0;
        $wasC = false;
        $old = str_split($old);
        foreach ($old as $token){
            switch($state){
            case 0://uvodny stav + nie unarny
                if ($token === "%"){
                    $state = 1;
                    $wasC = false;
                } else if ($token === "!"){
                    $state = 2;
                    $wasC = false;
                } else if (in_array($token, array(".","+","*","|"))){
                    $state = 4;
                } else if ($token === "("){
                    $result = $result.$token;
                    $state = 0;
                    $pars++;
                    $wasC = true;
                } else if ($token === ")"){
                    $result = $result.$token;
                    $state = 3;
                    $pars--;
                    if ($pars < 0 || $wasC)
                        $state = 4;
                } else if (in_array($token, str_split("/\\?\"[^]\${}=<>:-"))){
                    $result = $result."\\".$token;
                    $state = 3;
                    $wasC = false;
                } else {
                    $result = $result.$token;
                    $state = 3;
                    $wasC = false;
                    break;
                }
            break;
            case 1://%
                $wasC = false;
                if (in_array($token, str_split("sdtn.|!*+)("))){
                    $result = $result."\\".$token;
                    $state = 3;
                } else if ($token === "a"){
                    $result = $result."[\s\S]";//nie .?
                    $state = 3;
                } else if ($token === "l"){
                    $result = $result."[a-z]";
                    $state = 3;
                } else if ($token === "L"){
                    $result = $result."[A-Z]";
                    $state = 3;
                } else if ($token === "w"){
                    $result = $result."[a-zA-Z]";
                    $state = 3;
                } else if ($token === "W"){
                    $result = $result."[a-zA-Z0-9]";
                    $state = 3;
                } else if ($token === "%"){
                    $result = $result."%";
                    $state = 3;
                } else
                    $state = 4;
            break;
            case 2://! 1)single character or %character
                $wasC = false;
                if ($token === "!"){
                    $state = 3;//not sure here
                } else if ($token === "%"){
                    $state = 5;
                } else if (in_array($token, str_split(").+*|"))){
                    $state = 4;
                } else if (in_array($token, str_split("/\\?[^]\${}=<>:-\""))){
                    $state = 3;
                    $result = $result."[^\\".$token."]";
                } else if ($token === "("){//let the fun begin !(
                    $parsInNeg += 1;
                    $result = $result."[^(";
                    $wasC = true;
                    $state = 7;
                } else{
                    $state = 3;
                    $result = $result."[^".$token."]";
                }
                break;
            case 3://moze ist binarny
                if ($token === "%"){
                    $state = 1;
                    $wasC = false;
                } else if ($token === "!"){
                    $state = 2;
                    $wasC = false;
                } else if ($token === "."){
                    $state = 6;
                    $wasC = false;
                } else if ($token === "|"){
                    $state = 6;
                    $result = $result.$token;
                    $wasC = false;
                } else if ($token === "("){
                    $result = $result.$token;
                    $state = 0;
                    $pars++;
                    $wasC = true;
                } else if ($token === ")"){
                    $result = $result.$token;
                    $state = 3;
                    $pars--;
                    if ($pars < 0 || $wasC)
                        $state = 4;
                } else if (in_array($token, str_split("/\\?[^]\${}=<>:-\""))){
                    $result = $result."\\".$token;
                    $state = 3;
                    $wasC = false;
                } else {
                    $result = $result.$token;
                    $state = 3;
                    $wasC = false;
                    break;
                }
                break;
            case 4://Propadliste
                error("Chyba v regularnom vyraze", 4);//TODO error code
                $state = 4;
                break;
            case 5://% v negacii
                $wasC = false;
                if (in_array($token, str_split("sdtn.|!*+)("))){
                    $result = $result."[^\\".$token."]";
                    $state = 3;
                } else if ($token === "a"){
                    $result = $result."[^\s\S]";
                    $state = 3;
                } else if ($token === "l"){
                    $result = $result."[^a-z]";
                    $state = 3;
                } else if ($token === "L"){
                    $result = $result."[^A-Z]";
                    $state = 3;
                } else if ($token === "w"){
                    $result = $result."[^a-zA-Z]";
                    $state = 3;
                } else if ($token === "W"){
                    $result = $result."[^a-zA-Z0-9]";
                    $state = 3;
                } else if ($token === "%"){
                    $result = $result."[^%]";
                    $state = 3;
                } else
                    $state = 4;
                break;
            case 6://musi ist nieco normalne, bolo . |
                if ($token === "%"){
                    $state = 1;
                    $wasC = false;
                } else if ($token === "!"){
                    $state = 2;
                    $wasC = false;
                } else if (in_array($token, str_split(".|+*"))){
                    $state = 4;
                    $wasC = false;
                } else if ($token === "("){
                    $result = $result.$token;
                    $state = 0;
                    $pars++;
                    $wasC = true;
                } else if ($token === ")"){
                    $result = $result.$token;
                    $state = 4;
                    $pars--;
                    if ($pars < 0 || $wasC)
                        $state = 4;
                } else if (in_array($token, str_split("/\\?[^]\${}=<>:-\""))){
                    $result = $result."\\".$token;
                    $state = 3;
                    $wasC = false;
                } else {
                    $result = $result.$token;
                    $state = 3;
                    $wasC = false;
                    break;
                }
                break;
            case 7://!(
                //kontrola jedneho!
                if (in_array($token, str_split(".|+*!"))){
                    $state = 4;
                } else if ($token === "("){
                    $parsInNeg += 1;
                    $result = $result.$token;
                    $state = 7;
                    $wasC = true;
                } else if ($token === ")"){
                    $parsInNeg -= 1;
                    if ($parsInNew < 0 || $wasC)
                        $state = 4;
                    else if ($parsInNeg === 0){//koniec utrpenia
                        $result = $result.")]";
                        $state = 3;
                    } else {
                        $result = $result.$token;
                        $state = 8;
                    }
                    $wasC = false;
                } else if (in_array($token, str_split("/\\?[^]\${}=<>:-\""))){
                    $result = $result."\\".$token;
                    $state = 8;
                    $wasC = false;
                } else if ($token === "%"){
                    $state = 9;
                    $wasC = false;
                } else {
                    $result = $result.$token;
                    $state = 8;
                    $wasC = false;
                }
                break;
            case 8: //bol jeden znak alebo ), maximalne tak | alebo snad )
                if ($token === "|"){
                    $result = $result.$token;
                    $state = 7;
                    $wasC = false;
                } else if ($token === ")"){
                    $parsInNeg -= 1;
                    if ($parsInNeg < 0 || $wasC)
                        $state = 4;
                    else if ($parsInNeg === 0){//koniec utrpenia
                        $result = $result.")]";
                        $state = 3;
                    } else {
                        $result = $result.$token;
                        $state = 8;
                    }
                    $wasC = false;
                } else {
                    $state = 4;
                }
                break;
            case 9://% v !(
                $wasC = false;
                if (in_array($token, str_split("sdtn.|!*+)("))){
                    $result = $result."\\".$token;
                    $state = 8;
                } else if ($token === "a"){
                    $result = $result."\s\S";
                    $state = 8;
                } else if ($token === "l"){
                    $result = $result."a-z";
                    $state = 8;
                } else if ($token === "L"){
                    $result = $result."A-Z";
                    $state = 8;
                } else if ($token === "w"){
                    $result = $result."a-zA-Z";
                    $state = 8;
                } else if ($token === "W"){
                    $result = $result."a-zA-Z0-9";
                    $state = 8;
                } else if ($token === "%"){
                    $result = $result."%";
                    $state = 8;
                } else
                    $state = 4;
                break;

                //moze byt jeden znak, %znak, specialny_znak
                //nemoze byt binarny, unarny, !
                ///zavorky++

            }

        }
        if ($pars != 0 || $state != 3)
            error("Chyba v regularnom vyraze", 4);//TODO error code
        return $result;
    }

?>
