<?php
include 'src/regexConverter.php';
include 'src/merger.php';
include 'src/tagEditor.php';
include 'src/getoptS.php';
include 'src/errors.php';

function main(){
    global $argv; 
    
    //parsuj vstup
    $opts = new getoptS($argv);
    if (!$opts->parseCmd())
        error("Nespravne udaje prikazovej riadky",1);
    if ($opts->isHelp){
        //TODO vypis helpu
        print ("HELP\n");
    }
    $formatFileRows = [];
    //pokusime sa otvorit formatovaci subor 
    if ($opts->format === ""){
        $formatFileRows = [];
    } else if (!is_readable($opts->format)){
        $formatFileRows = [];
    } else {
        $h = fopen($opts->format,'r');
        while (($ln = fgets($h)) !== false)
            if ($ln != "\n")
                array_push($formatFileRows,$ln);
        foreach ($formatFileRows as &$vl)
            $vl = preg_split('/\t+/',$vl,2);
    }

    //Upravime si nacitane udaje z formatovacieho suboru
    if ($formatFileRows != []){
        foreach ($formatFileRows as &$vl)
            if (count($vl) == 2){
                //upravime si regularne vyrazi vhodne pre php
                $vl[0] = editRegex($vl[0]);
                
                //a upravime si aj tagy 
                $vl[1] = editTags($vl[1]);
            } else if ($vl[0] != "")  {
                error("Vo formatovacom subore bolo daco spatne\n", 4);//TODO error code
            }
    }

    //nacitame vstupne date
    $origInput = "";
    if ($opts->input === ""){//stdin
        while($in = fgets(STDIN)){
            $in = rtrim($in, "\r\n");
            $origInput = $origInput.$in;
        }
    } else if (!is_readable($opts->input)){
        error("Napodarilo sa otvorit vstupny subor\n", 2);
    } else {
        $origInput = file_get_contents($opts->input);
    }
    
    //cez kazdy zaznam vo fomratovacom subore prejdeme
    $newRes = $origInput;
    foreach($formatFileRows as $f){
        $found  = [];
        //najdeme vsetky vyhovujuce retazce
        preg_match_all("/(".$f[0].")/",$origInput, $found, PREG_OFFSET_CAPTURE);
        //a pre kazdy vyskyt 
        if ($found != []){
            foreach($found[0] as $fone){
                //ale musi sa jednat o neprazdny retazec
                if ($fone[0] != ""){
                    //cast retazca pred najdenim podretazcom
                    $beginS = substr($origInput, 0, $fone[1]);
                    //cast za
                    $restS  = substr($origInput, $fone[1]+strlen($fone[0])); 
                    //spojime do tvaru -zaciatok+<zaciatocny tag>+retazec+<koncovy tag>+koniec
                    $newS   = $beginS.$f[1][0].$fone[0].$f[1][1].$restS;
                    //a spojime to do jedneho retazca so vsetkymi inymi vyskytmi a reg. vyrazmi
                    $newRes = merger($newRes, $newS);
                }
            }
        }
    }

    //ak je volba --br
    if ($opts->isBr === true){
        $newRes = str_replace("\n", "<br />\n", $newRes);
    } 
    
    // prekonvertovali sme, tak uz len to treba spravne ulozit
    if ($opts->output === ""){
        fwrite(STDOUT,$newRes);
    } else {
        $outF = @fopen($opts->output, "w");
        if ($outF){
            fwrite($outF,$newRes);
            fclose($outF);
        } else 
            error("Neda sa zapisat\n",3);
    }
    return 0;
    }


main();
return 0;
?>
