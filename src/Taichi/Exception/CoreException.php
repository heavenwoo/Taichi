<?php
namespace Taichi\Exception;
use Exception;
use SplFileObject;

class CoreException extends Exception
{
    public function __construct($message, $code = null)
    {
        parent::__construct($message, $code);
        set_exception_handler([$this, 'exceptionHandler']);
    }

    public function exceptionHandler(Exception $e)
    {
        $message = $this->cubeExceptionSource($e);

        ob_start();
        require dirname(__FILE__) . "/_tpl/front_error.tpl.php";
        $content = ob_get_contents();
        ob_clean();
        die($content);
    }
    
    private function cubeExceptionSource(Exception $e)
    {
        $trace = $e->getTrace();
    
        $result = [];
        $result['main'] = ['file' => $e->getFile(), 'line' => $e->getLine(), 'message' => $e->getMessage()];
    
        foreach ($result as &$_i) {
            $file = new SplFileObject($_i['file']);
    
            foreach ($file as $line => $code) {
                if ($line < $_i['line'] + 6 && $line > $_i['line'] - 7) {
                    $h_string = highlight_string("<?php{$code}", true);
                    $_i['source'][$line] = str_replace("&lt;?php", "", $h_string);
                }
            }
        }
    
        if (!empty($trace)) {
            foreach ($trace as $tn => & $t) {
                if (isset($t['file'])) {
                    $trace_fileinfo = new SplFileObject($t['file']);
                    foreach ($trace_fileinfo as $t_line => $t_code) {
                        if ($t_line < $t['line'] + 6 && $t_line > $t['line'] - 7) {
                            $h_string = highlight_string("<?php{$t_code}", true);
                            $t['source'][$t_line] = str_replace("&lt;?php", "", $h_string);
                        }
                    }
                    $result ['trace'] [$tn] = $t;
                }
            }
        }
    
        return $result;
    }
}