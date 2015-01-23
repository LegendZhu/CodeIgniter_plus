<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
require ROOTPATH . '/vendor/autoload.php';
/*
 * CodeIgniter Monolog integration
 *
 * Legend <zcq.0@163.com>
 */
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\ErrorHandler;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\BufferHandler;

use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\ProcessIdProcessor;

use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\MemoryPeakUsageProcessor;

// ------------------------------------------------------------------------

/**
 * replaces CI's Logger class, use Monolog instead
 * see https://github.com/LegendZhu/Codeigniter_plus & https://github.com/Seldaek/monolog
 */
class CI_Log {

    private $log;
    private $log_wf;

    protected $_log_path;
    protected $_log_name;
    protected $_log_config_file = 'log.php';
    protected $_log_cut;

    protected $_threshold;
    protected $_threshold_default = 4; //默认日志级别
    protected $_threshold_extra = TRUE; //默认开启扩展信息级别

    protected $_record_memory_info = TRUE;

    /*
     * the default date format is "Y-m-d H:i:s"
     */
    protected $_date_fmt = 'Y-m-d H:i:s';

    /*
     * the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
     */
    protected $output = "[%level_name%] [%datetime%] [%message%] |%context%\n";

    protected $_enabled = TRUE;

    public $config;

    private $formatter;
    /*
        emergency
        System is unusable.

        alert
        Action must be taken immediately.
        Example: Entire website down, database unavailable, etc. This should trigger the SMS alerts and wake you up.

        critical
        Critical conditions.
        Example: Application component unavailable, unexpected exception.

        error
        Runtime errors that do not require immediate action but should typically be logged and monitored.

        warning
        Exceptional occurrences that are not errors.
        Example: Use of deprecated APIs, poor use of an API, undesirable things that are not necessarily wrong.

        notice
        Normal but significant events.

        info
        Interesting events.
        Example: User logs in, SQL logs.

        debug
        Detailed debug information.
     */
    protected $_levels = array('ERROR' => '1', 'WARNING' => '2', 'NOTICE' => '3', 'INFO' => '4', 'DEBUG' => '5', 'ALL' => '6');

    static protected $log_unique_id;

    /**
     * Constructor
     */
    public function __construct() {
        if (empty(self::$log_unique_id)) {
            self::$log_unique_id = $config['log_unique_id'] = substr(md5(uniqid('', true)), 2, 8) . mt_rand(1000, 9999);
        }

        if (!defined('ENVIRONMENT') OR !file_exists($file_path = APPPATH . 'config/' . ENVIRONMENT . '/' . $this->_log_config_file)) {
            $log_config_path = APPPATH . 'config/' . $this->_log_config_file;
        }

        if (!file_exists($log_config_path)) {
            exit('log config file does not exist');
        }

        require($log_config_path);

        // make $config from config/log.php accessible to $this->write_log()
        $this->config = $config;

        if (isset($this->config['log_path']) || empty($this->config['log_path'])) {
            $this->config['log_path'] = APPPATH . 'logs/';
        }
        $this->_log_path = $this->config['log_path'];

        if (!is_dir($this->_log_path) OR !is_really_writable($this->_log_path)) {
            $this->_enabled = FALSE;
        }

        if (isset($this->config['log_path']) || empty($this->config['log_path'])) {
            $this->config['log_name'] = 'log';
        }
        $this->_log_name = $this->config['log_name'];
        $this->_log_cut = $this->config['log_cut'];

        if (isset($this->config['threshold']) && is_numeric($this->config['threshold'])) {
            $this->_threshold = $this->config['threshold'];
        } else {
            $this->_threshold = $this->config['threshold'] = $this->_threshold_default;
        }

        $this->_threshold_extra = isset($this->config['threshold_extra']) ? $this->config['threshold_extra'] : $this->_threshold_extra;
        if ($this->_threshold_extra) {
            $this->output = "[%level_name%] [%datetime%] [%message%] | %context% |%extra%\n";
        }

        $this->_record_memory_info = isset($this->config['record_memory_info']) ? $this->config['record_memory_info'] : $this->_record_memory_info;

        if (is_null($this->log)) {
            $this->initialize();
        }
    }

    // --------------------------------------------------------------------

    private function initialize() {
        $this->formatter = $formatter = new LineFormatter($this->output, $this->_date_fmt);
        $this->log = new Logger($this->_log_name);

        $error_handle = new MY_ErrorHandler($this->log);
        $error_handle->register($this->log);

        if ($this->_threshold_extra) {
            $this->log->pushProcessor(new UidProcessor());
            $this->log->pushProcessor(new WebProcessor());
            $this->log->pushProcessor(new ProcessIdProcessor());
            if ($this->_record_memory_info) {
                $this->log->pushProcessor(new MemoryUsageProcessor());
                $this->log->pushProcessor(new MemoryPeakUsageProcessor());
            }
        }

        $handler = new RotatingFileHandler($this->_log_path . $this->_log_name);
        if ('H' === strtoupper($this->_log_cut)) {
            $handler->setFilenameFormat('{filename}-{date}', 'Y-m-d-H');
        }

        $handler->setFormatter($formatter);
        $this->log->pushHandler($handler);

        $this->write_log('DEBUG', 'Log Class Initialized');
    }

    // --------------------------------------------------------------------

    /**
     * @param string $level
     * @param $msg
     * @param array $context
     * @param bool $php_error
     *
     * @return bool
     */
    public function write_log($level = 'error', $msg, $context = array(), $php_error = FALSE) {
        $level = strtoupper($level);

        if ($this->_enabled === FALSE) {
            return FALSE;
        }

        // verify error level
        if (!isset($this->_levels[$level])) {
            $this->log->addError('unknown error level: ' . $level);
            $level = 'ALL';
        }

        if (!is_array($context)) {
            $context = array($context);
        }

        if (self::$log_unique_id) {
            $msg = 'log_id:' . self::$log_unique_id . ',' . trim($msg);
        }

        // filter out anything in $this->config['exclusion_list']
        if (!empty($this->config['exclusion_list'])) {
            foreach ($this->config['exclusion_list'] as $findme) {
                $pos = strpos($msg, $findme);

                if ($pos !== false) {
                    // just exit now - we don't want to log this error
                    return true;
                }
            }
        }

        if ($this->config['introspection_processor']) {
            $this->log->pushProcessor(new IntrospectionProcessor($level, $skipClassesPartials = array('Monolog\\', 'CI_Log', 'null')));
        }

        //'ERROR' => '1', 'WARNING' => '2', 'NOTICE' => '3', 'INFO' => '4', 'DEBUG' => '5', 'ALL' => '6'
        if ($this->_levels[$level] <= $this->_threshold) {
            switch ($level) {
                case 'EMERGENCY':
                case 'ALERT':
                case 'CRITICAL':
                case 'ERROR':
                    $this->log->addError($msg, $context);
                    break;
                case 'WARNING':
                    $this->log->addWaring($msg, $context);
                    break;
                case 'NOTICE':
                    $this->log->addNotice($msg, $context);
                    break;
                case 'INFO':
                    $this->log->addInfo($msg, $context);
                    break;
                case 'DEBUG':
                    $this->log->addDebug($msg, $context);
                    break;
                case 'ALL':
                    $this->log->addInfo($msg, $context);
                    break;
            }
        }

        return true;
    }

    static function get_log_id() {
        if (self::$log_unique_id) {
            return 'log_id:' . self::$log_unique_id . ',';
        } else {
            return '';
        }
    }

}


use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class MY_ErrorHandler extends ErrorHandler {
    private $log_id;
    private $logger;

    private $previousExceptionHandler;
    private $uncaughtExceptionLevel;

    private $previousErrorHandler;
    private $errorLevelMap;

    private $fatalLevel;
    private $reservedMemory;
    private static $fatalErrors = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);

    public function __construct(LoggerInterface $logger) {
        parent::__construct($logger);
        $this->logger = $logger;
        $this->log_id = CI_Log::get_log_id();
    }

    public static function register(LoggerInterface $logger, $errorLevelMap = array(), $exceptionLevel = null, $fatalLevel = null) {
        $handler = new static($logger);
        if ($errorLevelMap !== false) {
            $handler->registerErrorHandler($errorLevelMap);
        }
        if ($exceptionLevel !== false) {
            $handler->registerExceptionHandler($exceptionLevel);
        }
        if ($fatalLevel !== false) {
            $handler->registerFatalHandler($fatalLevel);
        }

        return $handler;
    }

    public function registerExceptionHandler($level = null, $callPrevious = true) {
        $prev = set_exception_handler(array($this, 'handleException'));
        $this->uncaughtExceptionLevel = $level;
        if ($callPrevious && $prev) {
            $this->previousExceptionHandler = $prev;
        }
    }

    public function registerErrorHandler(array $levelMap = array(), $callPrevious = true, $errorTypes = -1) {
        $prev = set_error_handler(array($this, 'handleError'), $errorTypes);
        $this->errorLevelMap = array_replace($this->defaultErrorLevelMap(), $levelMap);
        if ($callPrevious) {
            $this->previousErrorHandler = $prev ? : true;
        }
    }

    public function registerFatalHandler($level = null, $reservedMemorySize = 20) {
        register_shutdown_function(array($this, 'handleFatalError'));

        $this->reservedMemory = str_repeat(' ', 1024 * $reservedMemorySize);
        $this->fatalLevel = $level;
    }

    /**
     * @private
     */
    public function handleException(\Exception $e) {
        $this->logger->log(
          $this->uncaughtExceptionLevel === null ? LogLevel::ERROR : $this->uncaughtExceptionLevel,
          $this->log_id . sprintf('Uncaught Exception %s: "%s" at %s line %s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()),
          array('exception' => $e)
        );

        if ($this->previousExceptionHandler) {
            call_user_func($this->previousExceptionHandler, $e);
        }
    }

    /**
     * @private
     */
    public function handleError($code, $message, $file = '', $line = 0, $context = array()) {
        if (!(error_reporting() & $code)) {
            return;
        }

        $level = isset($this->errorLevelMap[$code]) ? $this->errorLevelMap[$code] : LogLevel::CRITICAL;
        $this->logger->log($level, $this->log_id . self::codeToString($code) . ': ' . $message, array('code' => $code, 'message' => $message, 'file' => $file, 'line' => $line));

        if ($this->previousErrorHandler === true) {
            return false;
        } elseif ($this->previousErrorHandler) {
            return call_user_func($this->previousErrorHandler, $code, $message, $file, $line, $context);
        }
    }

    /**
     * @private
     */
    public function handleFatalError() {
        $this->reservedMemory = null;

        $lastError = error_get_last();
        if ($lastError && in_array($lastError['type'], self::$fatalErrors)) {
            $this->logger->log(
              $this->fatalLevel === null ? LogLevel::ALERT : $this->fatalLevel,
              $this->log_id . 'Fatal Error (' . self::codeToString($lastError['type']) . '): ' . $lastError['message'],
              array('code' => $lastError['type'], 'message' => $lastError['message'], 'file' => $lastError['file'], 'line' => $lastError['line'])
            );
        }
    }

    private static function codeToString($code) {
        switch ($code) {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
        }

        return 'Unknown PHP error';
    }
}