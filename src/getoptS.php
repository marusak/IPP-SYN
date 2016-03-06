<?php
class getoptS {
    public $cmdLine;
    
    function __construct($cmdline){
        $this->cmdLine = array_slice($cmdline,1);
    }

    public $isHelp  = false;
    public $isBr    = false;
    public $format  = "";
    public $input   = "";
    public $output  = "";

    function parseCmd(){
        //TODO shortOpts
        //ak je na commandline --help
        if (in_array("--help",$this->cmdLine)){
            if (count($this->cmdLine) === 1){
                $this->isHelp = true;
                return true;
            } 
            else
                return false;
        } else { //nie je tam help
            if (count($this->cmdLine) > 4)
                return false;//viac argumento nez je mozne
            else {//argumentov teoreticky akura
                foreach($this->cmdLine as $arg){
                    if (substr($arg, 0, 4) === "--fo"){//possibly --format=filename
                        if (strlen($arg) <= 9 ){ // nebude to --format=
                            return false;
                        } else {
                            if ($this->format != "" || substr($arg, 0, 9) != "--format=")
                                return false;
                            $this->format = substr($arg, 9);
                        }
                    } else if (substr($arg, 0, 4) === "--in"){ // possibly --input=filename
                        if (strlen($arg) <= 8 ){ // nebude to --input=
                            return false;
                        } else {
                            if ($this->input != "" || substr($arg, 0, 8) != "--input=")
                                return false;
                            $this->input = substr($arg, 8);
                        }
                    } else if (substr($arg, 0, 4) === "--ou"){ // possibly --output=filename
                        if (strlen($arg) <= 9 ){ // nebude to --output=
                            return false;
                        } else {
                            if ($this->output != "" || substr($arg, 0, 9) != "--output=")
                                return false;
                            $this->output = substr($arg, 9);
                        }
                    } else if (substr($arg, 0, 4) === "--br"){ // possibly --br
                        if (strlen($arg) != 4 ){ // nebude to --output=
                            return false;
                        } else {
                            if ($this->isBr != false)
                                return false;
                            $this->isBr = true;
                        }
                    } else {
                        return false;
                    }
                }
            }
        }
        return true;
    }
}

?>
