<?php
function error($message, $code){
    $stderr = fopen('php://stderr', 'a');
    fwrite($stderr, $message);
    exit($code);
}
?>
