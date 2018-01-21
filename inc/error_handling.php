<?php

define('ERROR_LOG_FILE', '../../error/'.date("Y-m-d").'-SYTH_PHP.log');

function handleError($code, $description, $file = null, $line = null, $context = null) {
  $displayErrors = ini_get("display_errors");
  $displayErrors = strtolower($displayErrors);
  list($error, $log) = mapErrorCode($code);
  $data = array(
    'time' => date("H:i:s"),
    'level' => $log,
    'code' => $code,
    'error' => $error,
    'description' => $description,
    'file' => $file,
    'line' => $line,
    'context' => $context,
    'path' => $file,
    'message' => $error . ' (' . $code . '): ' . $description . ' in [' . $file . ', line ' . $line . ']'
  );
  if ($data['error']!='Deprecated') {
    fileLog($data);
  }
  return false;
}

function fileLog($logData, $fileName = ERROR_LOG_FILE) {
  $fh = fopen($fileName, 'a+');
  if (is_array($logData)) {
    $logData = print_r($logData, 1);
    $logData.="\r\n";
  }
  $status = fwrite($fh, $logData);
  fclose($fh);
  return ($status) ? true : false;
}

function mapErrorCode($code) {
  $error = $log = null;
  switch ($code) {
    case E_PARSE:
    case E_ERROR:
    case E_CORE_ERROR:
    case E_COMPILE_ERROR:
    case E_USER_ERROR:
    $error = 'Fatal Error';
    $log = LOG_ERR;
    break;
    case E_WARNING:
    case E_USER_WARNING:
    case E_COMPILE_WARNING:
    case E_RECOVERABLE_ERROR:
    $error = 'Warning';
    $log = LOG_WARNING;
    break;
    case E_NOTICE:
    case E_USER_NOTICE:
    $error = 'Notice';
    $log = LOG_NOTICE;
    break;
    case E_STRICT:
    $error = 'Strict';
    $log = LOG_NOTICE;
    break;
    case E_DEPRECATED:
    case E_USER_DEPRECATED:
    $error = 'Deprecated';
    $log = LOG_NOTICE;
    break;
    default :
    break;
  }
  return array($error, $log);
}
set_error_handler("handleError");
?>