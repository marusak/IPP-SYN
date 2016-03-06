<?php
    function checkForB($old){
    if (strpos($old, '++') !== false)
        return false;
    if (strpos($old, '*+') !== false)
        return false;
    if (strpos($old, '+*') !== false)
        return false;
    if (strpos($old, '**') !== false)
        return false;
    return true;

    }
        
//TODO !(a|b) , forum
    //Dostane regex z tvare v akom sa nachadza v konfgacnom subore a prerobi ho na format pre php 
        //regex
    function editRegex($old){
        if (!checkForB($old))
            error("Chyba v regularnom vyraze", 4);//TODO error code

        $pars = 0;//pocet zatvoriek
        $state = 0;
        $result = "";
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
                } else if (in_array($token, str_split(".)(!+*|"))){
                    $state = 4;
                } else if (in_array($token, str_split("/\\?[^]\${}=<>:-\""))){
                    $state = 3;
                    $result = $result."[^\\".$token."]";
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
                    $result = $result."[^[\s\S]]";
                    $state = 3;
                } else if ($token === "l"){
                    $result = $result."[^[a-z]";
                    $state = 3;
                } else if ($token === "L"){
                    $result = $result."[^[A-Z]";
                    $state = 3;
                } else if ($token === "w"){
                    $result = $result."[^[a-zA-Z]";
                    $state = 3;
                } else if ($token === "W"){
                    $result = $result."[^[a-zA-Z0-9]";
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
            }

        }
        if ($pars != 0 || $state === 0 || $state === 1 || $state === 2 || $state === 4 || $state === 5 || $state === 6)
            error("Chyba v regularnom vyraze", 4);//TODO error code
        return $result;
    }

?>
