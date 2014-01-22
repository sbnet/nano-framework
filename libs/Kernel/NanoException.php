<?php
namespace NanoFramework\Kernel;
use NanoFramework\Utilities;

/**
* Exception handling class
*
* @package NanoFramework\Kernel
* @author Stéphane BRUN
*/
class NanoException extends \Exception
{
    /**
    * Constructor
    * @param string $stringException
    * @param integer $levelException http://fr.php.net/manual/fr/ref.errorfunc.php#errorfunc.constants
    */
    function __construct($string, $level=E_NOTICE)
    {
        parent::__construct($string);
        $this->_level = $level;
        $this->log = Seringue::get_instance()->log_nano;
    }

    /**
    * Exeception handler
    *
    * @author Stéphane BRUN
    * @param Exception $exception
    */
    static function exception_handler($exception)
    {
        if(isset($exception->_level))
        {
            $db = $exception->getTrace();
            $file = $db[2]['file'];
            $line = $db[2]['line'];

            switch($exception->_level)
            {
                case E_NOTICE:
                    // $this->log->addNotice(rtrim("[IN $file AT LINE $line] : ".$exception->getMessage()));
                break;

                case E_WARNING:
                    // $this->log->addWarning(rtrim("[IN $file AT LINE $line] : ".$exception->getMessage()));
                break;

                default:
                case E_ERROR:
                    $message  = "[IN $file AT LINE $line] : ".$exception->getMessage();
                    $message .= self::get_trace($exception->getTrace());
                    // $this->log->addError(rtrim($message));
                    die(nl2br($message));
                break;
            }
        }
        else
        {
            die($exception->getMessage());
        }
    }

    /**
    * Get the trace reformatted in html
    *
    * @author Guillaume Hocine & Adrian Galewski http://www.arfooo.com/
    * @param array $traceArr An array of the backtrace the one returned by $exception->getTrace()
    */
    private static function get_trace($traceArr)
    {
        $MAXSTRLEN = 64;
        $s = '<pre align="left" style="background-color: 000; font-size:13px;">';
        array_shift($traceArr);
        array_shift($traceArr);
        $traceArr = array_reverse($traceArr);
        $tabs = 0;

        foreach($traceArr as $arr)
        {
            for($i = 0; $i < $tabs; $i++)
            {
                $s .= '&nbsp; ';
            }
            $s .= "";
            $tabs++;
            $s .= '<font face="Courier New,Courier">';
            if(isset($arr['class']))
            {
                $s .= "<font color=yellow><b>" . $arr['class'] . '</b></font><font color=#AAAAAA>-></font>';
            }
            $args = array();
            if(!empty($arr['args']))
            {
                foreach ($arr['args'] as $v) {
                    if (is_null($v)) {
                        $args[] = 'null';
                    }
                    else
                        if (is_array($v)) {
                            $args[] = 'Array[' . sizeof($v) . ']';
                        } else {
                            if (is_object($v)) {
                                $args[] = 'Object:' . get_class($v);
                            }
                            else {
                                if (is_bool($v)) {
                                    $args[] = $v ? 'true' : 'false';
                                }
                                else {
                                    $v = (string) @$v;
                                    $str = htmlspecialchars(substr($v, 0, $MAXSTRLEN));
                                    if (strlen($v) > $MAXSTRLEN) {
                                        $str .= '...';
                                    }
                                    $args[] = "\"" . $str . "\"";
                                }
                            }
                        }
                }
            }
            $color = isset($arr['class']) ? "#FFFFFF" : "#00FF00";
            $s .= '<font color=' . $color . '>' . $arr['function'] . '</font><font color=#AAAAAA>(</font><font color=#9BAFFF>' . implode(', ', $args) . '</font><font color=#AAAAAA>)</font>';
            $Line = (isset($arr['line']) ? $arr['line'] : "unknown");
            $File = (isset($arr['file']) ? $arr['file'] : "unknown");
            $s .= sprintf("<font color=#AAAAAA size=-1> # line %4d, file: <font color=#ABCCDD>%s</font>", $Line, $File);
            $s .= "<br />";
        }
        $s .= '</pre>';
        return $s;
    }
}
