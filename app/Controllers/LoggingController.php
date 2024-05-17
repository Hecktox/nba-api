<?php

namespace Vanier\Api\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Vanier\Api\Helpers\DateTimeHelper;


class LoggingController
{
    private $logging_model = null;

    // public function __construct(){
    //     $this->player_model = new $logging_model();
    // }

    public static function logAccess(string $log_info, array $extras=[]){

        // 1) Initiate and configure a logger.
        $logger = new Logger('access');
        $logger->pushHandler(
            new StreamHandler(
                APP_LOGS_DIR . APP_ACCESS_LOG_FILE,
                Logger::DEBUG
            )
        );

        $logger->info($log_info, $extras);
    }

    public static function logError(string $exception, array $extras=[]){
        $logger = new Logger('error');
        $logger->pushHandler(
            new StreamHandler(
                APP_LOGS_DIR . APP_ERROR_LOG_FILE,
                Logger::DEBUG
            )
        );

        $logger->error($exception);
    }

}
