<?php

namespace Apix\Console;

use Apix\Console;

class SystemCheck extends Console
{

    public $software_name = "apix-server";

    public function help()
    {
        $args = $this->getArgs();
        $args[0] = 'php ' . $args[0];

        if ( $this->hasArgs(array('-h', '--help')) ) {
            echo <<<HELP
Usage: {$args[0]} [options]

Options:
   --help | -h      Display this help.

   --no-colors      Don't use colors in the outputs.

   --required       Run only the required checks.

   --optionals       Run only the optionals checks.

   --all            Run all the checks (default).


HELP;
        exit;
        }
    }

    public function run($quiet=false)
    {
        $args = $this->getArgs();

        #$this->out(PHP_EOL);
        $this->out("\tSystem check for " . $this->software_name . "\t", 'cyan', 'bold', 'on_blue');
        $this->out(PHP_EOL . PHP_EOL);

        $this->help();

        if ( !$this->hasArgs(array('--required', '--optionals')) ) {
            $this->args[] = '--all';
        }

        $this->display(
            'Minimum requirements (required to pass): ',
            $this->getRequired(),
            array('--required', '--all')
        );

        $this->out(PHP_EOL . PHP_EOL);

        $this->display(
            'Optionals (recommended to pass): ',
            $this->getOptionals(),
            array('--optionals', '--all')
        );

        $this->out(PHP_EOL . PHP_EOL);

        // if () {
        //     $this->out(PHP_EOL . PHP_EOL . "All your PHP Settings are extensions are fine. Well done, you are ready to roll!" . PHP_EOL . PHP_EOL, 'success');
        // } else {
        //     $this->out(PHP_EOL . PHP_EOL . "All your PHP Settings are extensions are fine. Well done, you are ready to roll!" . PHP_EOL . PHP_EOL, 'success');
        // }
    }

    public function display($title, array $checks, array $args)
    {
        $this->out($title, 'bold');
        if ( $this->hasArgs($args) ) {
            foreach ($checks as $key => $check) {
                $this->out(PHP_EOL . PHP_EOL);
                $this->check($key, $check);
            }
        } else {
            $this->out('skipped, not in the run.', 'failed');
        }
    }

    public function check($key, $check)
    {
        $this->out("   - {$key}");
        if ($check['fail'] !== true) {
            if ($this->verbose > 0 && isset($check['verbose'])) {
                $this->out(sprintf(' (%s): ', $check['verbose']));
            } else {
                $this->out(': ');
            }
            $this->out('pass', 'success');
        } else {
            $this->out(': ');
            $this->out('failed', 'failed');
            $this->out(PHP_EOL);
            foreach ($check['msgs'] as $msg) {
                $this->out(PHP_EOL);
                $this->out('     ---> ','red');
                $this->out($msg, 'red');
            }
        }
    }

    public function getRequired()
    {
        $suhosin = ini_get('suhosin.executor.include.whitelist');

        $required = array(
            'PHP version > 5.3.2' => array(
                'fail' => version_compare(PHP_VERSION, '5.3.2', '<'),
                'verbose' => 'currently '. PHP_VERSION,
                'msgs' => array(
                    "The version of PHP (" . PHP_VERSION .") installed is too old.",
                    "You must upgrade to PHP 5.3.2 or higher."
                )
            ),
            'Phar support' => array(
                'fail' => !extension_loaded('Phar'),
                'verbose' => 'on',
                'msgs' =>array(
                    "The phar extension is missing.",
                    "Install it or recompile PHP without using --disable-phar"
                )
            ),
            'Suhosin' => array(
                'fail' => false !== $suhosin && false === stripos($suhosin, 'phar'),
                'verbose' => 'off or whitelisted',
                'msgs' => array(
                    "The suhosin.executor.include.whitelist setting is incorrect.",
                    "Add the following to the end of your 'php.ini' or 'suhosin.ini':",
                    "    suhosin.executor.include.whitelist = phar " . $suhosin
                )
            ),
            'detect_unicode' => array(
                'fail' => ini_get('detect_unicode'),
                'verbose' => 'off',
                'msgs' => array(
                    "This setting must be disabled.",
                    "Add the following to the end of your 'php.ini':",
                    "    detect_unicode = Off"
                )
            ),
            'allow_url_fopen' => array(
                'fail' => !ini_get('allow_url_fopen'),
                'verbose' => 'on',
                'msgs' => array(
                    "The allow_url_fopen setting is incorrect.",
                    "Add the following to the end of your 'php.ini':",
                    "    allow_url_fopen = On"
                )
            ),
            'ionCube loader disabled' => array(
                'fail' => extension_loaded('ionCube Loader'),
                'verbose' => 'off',
                'msgs' => array(
                    "The ionCube Loader extension could be incompatible with Phar files.",
                    "Anything prior to 4.0.9 will not work too well with Phar archives.",
                    "Consider upgrading to 4.0.9 or newer OR comment the 'ioncube_loader_lin_5.3.so' line from your 'php.ini'."
                )
            )
        );

        return $required;
    }

    public function getOptionals()
    {
        // sigchild
        ob_start();
        phpinfo(INFO_GENERAL);
        $phpinfo = ob_get_clean();
        preg_match('{Configure Command(?: *</td><td class="v">| *=> *)(.*?)(?:</td>|$)}m', $phpinfo, $config);

        $optionals = array(
            // APC
            'apc_cli' => array(
                'fail' => ini_get('apc.enable_cli'),
                'verbose' => 'off',
                'msgs' => array(
                    "The apc.enable_cli setting is incorrect.",
                    "Add the following to the end of your 'php.ini':",
                    "    apc.enable_cli = Off"
                ),
            ),

            // sigchild
            'sigchild' => array(
                'fail' => false !== strpos($config[1], '--enable-sigchild'),
                'verbose' => 'off',
                'msgs' => array(
                    "PHP was compiled with --enable-sigchild which can cause issues on some platforms.",
                    "Recompile it without this flag if possible, see also:",
                    "    https://bugs.php.net/bug.php?id=22999"
                )
            ),

            // tidy
            'tidy' => array(
                'fail' => !extension_loaded('tidy'),
                'verbose' => 'off',
                'msgs' => array(
                    "You may want to enable the Tidy extension.",
                )
            ),

            // PHP > 5.4
            'PHP version > 5.4' => array(
                'fail' => version_compare(PHP_VERSION, '5.4.0', '<'),
                'msgs' => array(
                    "PHP 5.4 introduces lots of nifty additions and is generally faster.",
                    "You should consider upgrading to PHP 5.4 or higher."
                )
            ),
        );

        return $optionals;
    }

    public function out($msg, $type=null)
    {
        $msg = str_replace("{software.name}", $this->software_name, $msg);

        switch ($type):
            case 'error':
               parent::out($msg, 'red');
            break;

            case 'info':
               parent::out($msg, 'green');
            break;

            case 'success':
               parent::out($msg, 'black', 'green');
            break;

            case 'failed':
               parent::out($msg, 'black', 'red');
            break;

            default:
               parent::out(func_get_args());
        endswitch;
    }

}
