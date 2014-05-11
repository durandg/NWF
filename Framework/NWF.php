<?php
//
// ,ggg, ,ggggggg,    ,ggg,      gg      ,gg  ,gggggggggggggg
// dP""Y8,8P"""""Y8b  dP""Y8a     88     ,8P  dP""""""88"""""
// Yb, `8dP'     `88  Yb, `88     88     d8'  Yb,_    88      
//  `"  88'       88   `"  88     88     88    `""    88      
//      88        88       88     88     88        ggg88gggg  
//      88        88       88     88     88           88     
//      88        88       88     88     88           88      
//      88        88       Y8    ,88,    8P     gg,   88      
//      88        Y8,       Yb,,d8""8b,,dP       "Yb,,8P      
//      88        `Y8        "88"    "88"          "Y8P'      
//
// NOVALWAVE FRAMEWORK : NWF
//
// This software is developed by Novalwave. To use it,
// distribute or modify, you need to have an authorization
// licence or a written agreement. All right reserved.
//
// Copyright (c) Novalwave and Guillaume Durand.
// Visit our website on: http://www.novalwave.com

define("MOD_STATUS_ACTIVE",                        1);
define("MOD_STATUS_INACTIVE",                      0);
define("MOD_STATUS_IDX",                           0);
define("MOD_OBJECT_IDX",                           1);
define("MOD_ARRAY_NAME",                           0);
define("MOD_ARRAY_ID",                             1);
define("MOD_ARRAY_CLASS",                          2);
define("DEBUG_TXT",                                0);
define("DEBUG_FILE",                               1);
define("DEBUG_RETURN",                             2);
define("DEBUG_COMPLETE",                           3);
define("DEBUG_SYSCALL",                            4);
define("DEBUG_SESS_ARRAY_NAME",      "_LAST_SESSION");
define("MEMBER_ID",                                1);
define("MEMBER_USE",                               2);
define("MEMBER_AUTH",                              3);
define("MEMBER_CREATE",                            4);
define("FILE_MODE_APPEND",                         1);
define("FILE_MODE_TRUNC",                          2);
define("BYTE",                                     0);
define("KB",                                    1024);
define("MB",                                 1048576);
define("GB",                              1073741824);
define("OCTET",                                 BYTE);
define("KO",                                      KB);
define("MO",                                      MB);
define("GO",                                      GB);
define("COOKIES_ARRAY_NAME",          "_NWF_COOKIES");
define("COOKIES_TIME_IDX",                         0);
define("COOKIES_STATUS_IDX",                       1);
define("COOKIES_CONTENTS_IDX",                     2);
define("COOKIE_UNSET",                             0);
define("COOKIE_OK",                                1);
define("COOKIE_DEFAULT_TIME",     time() + 0x1e13380);
define("URL_ROOT",                                 0);
define("URL_SCRIPT",                               1);
define("URL",                             NWF::url());
define("GET_DELIMITER",                         "HA");
define("GET_DATA_LETTER",                        'G');
define("GET_ARRAY_NAME",                     "_sget");
define("GET_HASH_IDX",                             1);
define("GET_CONTENTS_IDX",                         2);
define("FRAMEWORK_NAME",       "Novalwave Framework");
define("NWF_FRAMEWORK",                         true);
define("FRAMEWORK_VERSION",                  "3.0.0");
define("APP_MAIN",                         "NWFMain");
define("APP_RENDER",                      "__render");
define("SECURE_DELIMITER",                      "HA");
define("SECURE_MIN",                        0x000001);
define("SECURE_MAJ",                        0x000002);
define("SECURE_NUM",                        0x000004);
define("SECURE_ALL",                      SECURE_MIN |
                                          SECURE_MAJ |
                                          SECURE_NUM);
define("FRAMEWORK_DEFAULT",      serialize(
   array(
      "install_mode"          =>    false,
      "warnings_show"         =>    true,
      "warnings_log"          =>    true,
      "folder_logs"           =>    "./logs/",
      "warnings_filename"     =>    "warnings.txt",
      "debug_filename"        =>    "debug_log.txt",
      "debug_active"          =>    false,
      "private_key"           =>    "1FxTrd8",
      "sql_type"              =>    "mysql",
      "sql_host"              =>    "localhost",
      "sql_username"          =>    null,
      "sql_password"          =>    null,
      "sql_database"          =>    null,
      "sql_debug"             =>    false,
      "tpl_varname"           =>    "_vars",
      "mvc_no_logged"         =>    "index",
      "mvc_error_page"        =>    "/404",
      "mvc_rights_page"       =>    "/index",
      "mvc_pid_var"           =>    "pid",
      "mvc_cache_header"      =>    0,
      "mvc_cache_view"        =>    0,
      "mvc_cache_footer"      =>    0,
      "mvc_view_folder"       =>    "./view/",
      "mvc_assets_folder"     =>    "./assets/",
      "mvc_model_folder"      =>    "./model/",
      "mvc_compiled_folder"   =>    "./compiled/",
      "mvc_controller_folder" =>    "./controller/",
      "mvc_preprocess_folder" =>    "./preprocess/",
      "member_table_name"     =>    "Members",
      "member_field_memberid" =>    "MemberId",
      "member_field_username" =>    "Username",
      "member_field_password" =>    "Password",
      "mod_default"           =>    array(),
   )
));

NWF::__kernel();
class       NWF {
   const    NWF_CLASS = true;
   private  $_main = null;
   private  $_modules = array();

   public   function __construct() {
      $args = func_get_args();
      if (!count($args))
         $mods = NWF::config()->mod_default;
      else
         $mods = $args;
      foreach ($mods as $key => $value)
         $this->loadModule($value);
      if (class_exists(APP_MAIN)) {
         $main = APP_MAIN;
         $this->_main = new $main($this); 
      }
   }

   public   static function config() {
      static   $obj = null;

      if ($obj == null)
         $obj = new NWF_Config;
      return $obj;
   }

   public   static function stack() {
      static   $obj = null;

      if ($obj == null)
         $obj = new NWF_Stack;
      return $obj;
   }

   public   static function setPath($pathName, $controllerPath) {
      NWF::stack()->set("__mvc_path[]", array($pathName, $controllerPath));
   }

   public   static function __kernel() {
      $kernel = new NWF_Kernel;
   }

   public   function __destruct() {
      global   $NWF_DEBUG_STACK;
      global   $NWF_WARNINGS_BUFF;
      
      if (NWF::config()->warnings_show && $NWF_WARNINGS_BUFF != null)
         echo "<pre>".$NWF_WARNINGS_BUFF."</pre>";
      if (NWF::config()->warnings_log && $NWF_WARNINGS_BUFF != null) {
         $file = NWF::file(dirname(__FILE__).'/'.NWF::config()->warnings_filename);
         $file->put("\r\n\r\n--- Begin of recording ---\r\n\r\n".$NWF_WARNINGS_BUFF.'--- End of recording ---');
      }
      if (NWF::config()->debug_active && $NWF_DEBUG_STACK != null) {
         $file = NWF::file(dirname(__FILE__).'/'.NWF::config()->debug_filename);
         $file->put("\r\n\r\n--- Begin of recording ---\r\n\r\n".$NWF_DEBUG_STACK.'--- End of recording ---');
      }      
      return true;
   }

   public   function __set($name, $value) {
      if ($name == "module")
         return $this->loadModule($value);
      NWF::warning("CORE00002", "You cannot set the attribute ".$name);
      return false;
   }

   public   function __invoke($value) {
      return $this->loadModule($value);
   }

   public   function __get($getName) {
      if ($getName == "version")
         return FRAMEWORK_VERSION;
      foreach (unserialize(MOD_ARRAY) as $key => $moduleProp) {
         if ($getName == $moduleProp[MOD_ARRAY_NAME]) {
            if (isset($this->_modules[$moduleProp[MOD_ARRAY_ID]]) &&
                isset($this->_modules[$moduleProp[MOD_ARRAY_ID]][MOD_STATUS_IDX]) &&
                $this->_modules[$moduleProp[MOD_ARRAY_ID]][MOD_STATUS_IDX] == MOD_STATUS_ACTIVE) {
                  return $this->_modules[$moduleProp[MOD_ARRAY_ID]][MOD_OBJECT_IDX];
             }
         }
      }
      $models = NWF::stack()->get("__mvc_model");
      foreach ($models as $key => $value) {
         if ($value['ModelName'] == $getName)
            return $value['ModelObject'];
      }
      return null;
   }

   public   function __isset($name) {
      if ($name == "version")
         return true;
      foreach (unserialize(MOD_ARRAY) as $key => $moduleProp) {
         if ($name == $moduleProp[MOD_ARRAY_NAME]) {
            if (isset($this->_modules[$moduleProp[MOD_ARRAY_ID]]) &&
                isset($this->_modules[$moduleProp[MOD_ARRAY_ID]][MOD_STATUS_IDX]) &&
                $this->_modules[$moduleProp[MOD_ARRAY_ID]][MOD_STATUS_IDX] == MOD_STATUS_ACTIVE) {
                  return true;
            }
         }
      }
      return false;
   }

   public   function __unset($name) {
      return $this->unloadModule($moduleId);
   }

   public   function __call($name, $args) {
      $lArgs = null;
      foreach ($args as $key => $value) {
         if ($lArgs != null)
            $lArgs .= ", ";
         $lArgs .= $value;
      }
      NWF::warning("CORE00003", "You try to call ".$name."(".$lArgs.")"." but it doesnot exists");
      return false;
   }

   public   static function __callStatic($name, $args) {
      $lArgs = null;
      foreach ($args as $key => $value) {
         if ($lArgs != null)
            $lArgs .= ", ";
         $lArgs .= $value;
      }
      NWF::warning("CORE00004", "You try to call static ".$name."(".$lArgs.")"." but it doesnot exists");
      return false;
   }

   public   function __toString() {
      return FRAMEWORK_NAME." ".FRAMEWORK_VERSION;
   }

   public   function loadModule($moduleId) {
      if (isset($this->_modules[$moduleId]) &&
          isset($this->_modules[$moduleId][MOD_STATUS_IDX]) &&
          $this->_modules[$moduleId][MOD_STATUS_IDX] == MOD_STATUS_ACTIVE) {
            NWF::warning("CORE00001", "This module is already loaded");
            return false;
      }
      $this->_modules[$moduleId][MOD_STATUS_IDX] = MOD_STATUS_ACTIVE;
      foreach (unserialize(MOD_ARRAY) as $key => $moduleProp) {
         if ($moduleId == $moduleProp[MOD_ARRAY_ID]) {
            $this->_modules[$moduleId][MOD_OBJECT_IDX] = new $moduleProp[MOD_ARRAY_CLASS]($this);
         }
      }
   }

   public   function unloadModule($moduleId) {
      if (isset($this->_modules[$moduleId]) &&
          isset($this->_modules[$moduleId][MOD_STATUS_IDX]) &&
          $this->_modules[$moduleId][MOD_STATUS_IDX] == MOD_STATUS_ACTIVE) {
            unset($this->_modules[$moduleId]);
            return true;
      }
      return false;
   }

   public   function isModuleLoaded($moduleId) {
      if (isset($this->_modules[$moduleId]) &&
          isset($this->_modules[$moduleId][MOD_STATUS_IDX]) &&
          $this->_modules[$moduleId][MOD_STATUS_IDX] == MOD_STATUS_ACTIVE) {
            return true;
      }
      return false;
   }

   public   static function file($fileName, $create = true) {
      return new NWFFiles($fileName, $create);
   }

   public   static function template($filename = null, $vars = null, $cacheTime = 0) {
      return new NWFTemplate($filename, $vars, $cacheTime);
   }

   public   function data($table) {
      return new NWFTemplate($this, $table);
   }

   public   static function member() {
      return new NWFMember(func_get_args());
   }

   public   static function xml($flux = null) {
      return new NWFXml($flux);
   }

   public   static function getArray($array, $k = -1542) {
      $args = func_get_args();
      $ret = unserialize(base64_decode($array));
      if (isset($args[1]) && isset($ret[$args[1]])) {
         return $ret[$args[1]];
      }
      return $ret;
   }

   public   static function warning($warningCode, $warningDescription) {
      global   $NWF_DEBUG_STACK;
      global   $NWF_WARNINGS_BUFF;

      $debug = debug_backtrace();
      if (isset($debug[1]['line'])) {
         $NWF_WARNINGS_BUFF .= "[".FRAMEWORK_NAME."] Warning on line ".$debug[1]['line'];
         $NWF_WARNINGS_BUFF .= ": [".$warningCode."] ".$warningDescription."\r\n\r\n";
         $NWF_DEBUG_STACK .= "[".FRAMEWORK_NAME."] Warning on line ".$debug[1]['line'];
         $NWF_DEBUG_STACK .= ": [".$warningCode."] ".$warningDescription."\r\n\r\n";         
      }
      else {
         $NWF_WARNINGS_BUFF .= "[".FRAMEWORK_NAME."] Warning: [".$warningCode."] ";
         $NWF_WARNINGS_BUFF .= $warningDescription."\r\n\r\n";
         $NWF_DEBUG_STACK .= "[".FRAMEWORK_NAME."] Warning: [".$warningCode."] ";
         $NWF_DEBUG_STACK .= $warningDescription."\r\n\r\n";         
      }
   }
   
   public   static function debugStack($moduleName, $debugText) {
      global   $NWF_DEBUG_STACK;

      $debug = debug_backtrace();
      if (isset($debug[1]['line'])) {
         $NWF_DEBUG_STACK .= "[".FRAMEWORK_NAME."] Debug on line ".$debug[1]['line'];
         $NWF_DEBUG_STACK .= ": [".$moduleName."] ".$debugText."\r\n\r\n";
      }
      else {
         $NWF_DEBUG_STACK .= "[".FRAMEWORK_NAME."] Debug: [".$moduleName."] ";
         $NWF_DEBUG_STACK .= $debugText."\r\n\r\n";
      }      
   }

   public   static function debug($debugMode = DEBUG_COMPLETE, $debugType = DEBUG_TXT) {
      $debugObj = new NWF_MOD_Debug;
      return $debugObj->calls($debugMode, $debugType);
   }

   public   static function printr($string) {
      echo $string;
   }

   public   static function url($type = URL_ROOT) { 
      $url = null;
      if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
         $hosts = explode(',', $_SERVER['HTTP_X_FORWARDED_HOST']);
         $url = $hosts[0];
      }
      else if (!empty($_SERVER['HTTP_X_FORWARDED_SERVER']))
         $url = $_SERVER['HTTP_X_FORWARDED_SERVER'];
      else { 
         if (empty($_SERVER['SERVER_NAME']))
           $url = $_SERVER['HTTP_HOST'];
       else
           $url = $_SERVER['SERVER_NAME'];
      }
      $path = null;
      if (!empty($_SERVER['REQUEST_URI']))
         $path = $_SERVER['REQUEST_URI'];
      else if (!empty($_SERVER['PHP_SELF']))
         $path = $_SERVER['PHP_SELF'];
      else if (!empty($_SERVER['SCRIPT_NAME']))
         $path = $_SERVER['SCRIPT_NAME'];

      if ($type == URL_ROOT) {
         $x = explode("/", $path);
         unset($x[count($x) - 1]);
         $path = implode("/", $x).'/';
      }
      $prefix = null;
      if (isset($_SERVER['SERVER_PORT'])) {
         if ($_SERVER['SERVER_PORT'] == 443)
            $prefix = "https://";
         else if ($_SERVER['SERVER_PORT'] == 80)
            $prefix = "http://";
      }
      return $prefix.$url.$path;
   }

   public   static function redirect($url, $time = 0) {
      echo '<meta http-equiv="Refresh" content="'.$time.'; url='.$url.'" />';
      exit;
   }
}

class       NWF_Config {
   private  $data;

   public   function __construct() {
      $this->data = unserialize(FRAMEWORK_DEFAULT);
   }

   public   function __set($name, $value) {
      if (!array_key_exists($name, $this->data)) {
         NWF::warning("CONFIG00001", "This configuration variable doesnot exists");
         return false;
      } 
      $this->data[$name] = $value;
      return true;
   }
   
   public   function __get($name) {
      if (array_key_exists($name, $this->data))
         return $this->data[$name];
      return null;
   }

   public   function show() {
      echo "<table id='config' style='border: 1px solid'>\n";
      foreach ($this->data as $key => $value) {
         $value = $value == null ? "null" : $value;
         echo "<tr>\n";
            echo "<td style='border: 1px solid; width: 200px'>".$key."</td>\n";
            echo "<td style='border: 1px solid; width: 200px'>";
            echo "<pre>".print_r($value, true)."</pre></td>\n";
         echo "</tr>\n";
      }
      echo "</table>";
   }
}

class       NWF_Stack {
   private  $_stack = array();

   public   function set($name, $value) {
      if (!strncmp(substr($name, -2), "[]", 2)) {
         $this->_stack[substr($name, 0, strlen($name) - 2)][] = $value;
         return true;
      }
      $this->_stack[$name] = $value;
      return true;
   }
   
   public   function get($name) {
      if (array_key_exists($name, $this->_stack))
         return $this->_stack[$name];
      return null;
   }

   public   function show() {
      var_dump($this->_stack);
      return true;
   }
}

class       NWF_Kernel {
   public   function __construct() {
      $this->__loadSession();
      $this->__proloadModules();
      $this->__loadSystemModules();
      $this->__loadMemory();
      $this->__loadProcessModules();
   }

   private  function __loadSession() {
      if (session_id() == null) {
         header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
         session_start();
      }      
   }

   private  function __proloadModules() {
      NWF_MOD_Cookies::__cookies();
   }

   private  function __loadSystemModules() {
      NWF::config();
   }

   private  function __loadMemory() {
      NWF::stack();
   }

   private  function __loadProcessModules() {
      $moduleId = 0;
      $moduleList = array();

      foreach (get_declared_classes() as $process) {
         $name = explode('_', $process);
         if (!strncmp($process, 'NWF_MOD_', 8) && count($name) == 3) {
            if (!defined(strtoupper($name[1].'_'.$name[2])))
               define(strtoupper($name[1].'_'.$name[2]), $moduleId++);
            $moduleList[] = array(strtolower($name[2]), $moduleId - 1, $process);
         }
      }
      define("MOD_ARRAY", serialize($moduleList));
      if (isset($_GET['isNWF']) && $_GET['isNWF'] == '42') {
         echo 'NWF Framework is used by this website.';
         exit;
      }    
   }
}

class       NWF_MOD_Debug {
   private  function _printCallsArgsTypes($sdebug, $svalue) {
      $sdebug .= "# ";
      is_int($svalue) ? $sdebug .= "Int(" : null;
      is_bool($svalue) ? $sdebug .= "Bool(" : null;
      is_string($svalue) ? $sdebug .= "String(\"" : null;
      $sdebug .= print_r($svalue, true);
      is_string($svalue) ? $sdebug .= "\"" : null;
      (is_string($svalue) || is_int($svalue) || is_bool($svalue)) ? $sdebug .= ")" : null;
      $sdebug .= "\n";
      return $sdebug;
   }
   
   private  function _printCallsInformations($value, $debug) {
      isset($value['file']) ? $debug .= ">File\t\t: [".$value['file']."]\n" : null;
      isset($value['line']) ? $debug .= ">Line\t\t: [".$value['line']."]\n" : null;
      isset($value['function']) ? $debug .= ">Function name\t: [".$value['function']."]\n" : null;
      if (isset($value['args'])) {
         $debug .= ">Arguments\t: [".count($value['args'])." argument(s)]:\n";
         if (count($value['args'])) {
            $sdebug = null;
            foreach ($value['args'] as $skey => $svalue)
               $this->_printCallsArgsTypes($sdebug, $svalue);
            $debug .= $sdebug;
         }
         else
            $debug .= "No arguments\n";
      }   
      return $debug;
   }

   public   function calls($debugMode = DEBUG_COMPLETE, $debugType = DEBUG_TXT) {
      $i = 0;

      if ($debugMode == DEBUG_COMPLETE) {
         $debugTrace = debug_backtrace();
         $debug = "[NWF DEBUG]\n";
         foreach($debugTrace as $key => $value) {
            ++$i;
            $debug .= "--Step ".$i."--\n";
            $debug = $this->_printCallsInformations($value, $debug);
            $debug .= "\n";
         }
         if ($debugType == DEBUG_TXT)
            echo "<pre>".$debug."</pre>";
         else if ($debugType == DEBUG_FILE) //! to do
            return null;
         else
            return $debug;
      }
      else {
         echo "<pre>[NWF DEBUG]\n";
         debug_print_backtrace();
         echo "</pre>";
      }
   }

   public   function saveSession() {
      $lastSession = array();
      foreach ($GLOBALS as $key => $value) {
         if ($key != "GLOBALS" && $key != "lastSession" &&
            $key[0] != '_' && $key != "key" && $key != "value") {
               $lastSession['VARS'][$key] = $value;
            }
      }
      $lastSession['POST'] = $GLOBALS['_POST'];
      $lastSession['GET'] = $GLOBALS['_GET'];
      $_SESSION[DEBUG_SESS_ARRAY_NAME] = serialize($lastSession);
   }

   public   function getLastSession() {
      return isset($_SESSION[DEBUG_SESS_ARRAY_NAME]) ? unserialize($_SESSION[DEBUG_SESS_ARRAY_NAME]) : null;
   }
}

class       NWF_MOD_Cookies {
   public   function __construct() {
      if (!isset($_SESSION[COOKIES_ARRAY_NAME]))
         $this->__cookies();
   }

   public   static function __cookies() {
      if (!isset($_SESSION[COOKIES_ARRAY_NAME]))
         $_SESSION[COOKIES_ARRAY_NAME] = array();
      foreach ($_SESSION[COOKIES_ARRAY_NAME] as $key => $value) {
         if ($value[COOKIES_STATUS_IDX] == COOKIE_UNSET) {
            setcookie($key, $value[COOKIES_CONTENTS_IDX], $value[COOKIES_TIME_IDX], null, null, false, true);
            $_SESSION[COOKIES_ARRAY_NAME][$key][COOKIES_STATUS_IDX] = COOKIE_OK;
         }
         if (time() >= $value[COOKIES_TIME_IDX]) {
            unset($_COOKIE[$key]);
            unset($_SESSION[COOKIES_ARRAY_NAME][$key]);
         }
      }   
   }
   
   public   function set($cookieName, $cookieValue = null, $cookieExpiration = COOKIE_DEFAULT_TIME) {
      $_SESSION[COOKIES_ARRAY_NAME][$cookieName] = array();
      $_SESSION[COOKIES_ARRAY_NAME][$cookieName][COOKIES_TIME_IDX] = $cookieExpiration;
      $_SESSION[COOKIES_ARRAY_NAME][$cookieName][COOKIES_STATUS_IDX] = COOKIE_UNSET;
      $_SESSION[COOKIES_ARRAY_NAME][$cookieName][COOKIES_CONTENTS_IDX] = $cookieValue;
   }
   
   public   function get($cookieName) {
      if (isset($_SESSION[COOKIES_ARRAY_NAME][$cookieName]))
         return $_SESSION[COOKIES_ARRAY_NAME][$cookieName][COOKIES_CONTENTS_IDX];  
      return isset($_COOKIE[$cookieName]) ? $_COOKIE[$cookieName] : null;
   }
   
   public   function delete($cookieName) {
   }
}

class       NWF_MOD_Math {
   public   function __construct() {
   }
}

class       NWF_MOD_Secure {
   private  function _keyGenerator($data, $encKey) {
      $i = 0;
      $encData = null;
      $encKey = md5($encKey);
      
      for ($j = 0; $j < strlen($data); ++$j) {
         if ($i == strlen($encKey))
            $i = 0;
         $encData .= substr($data, $j, 1) ^ substr($encKey, $i, 1);
         ++$i;
      }
      return $encData;      
   }

   public   function encode($data, $key) {
      $i = 0;
      $encData = null;
      $encKey = md5(uniqid());
      
      for ($j = 0; $j < strlen($data); ++$j) {
         if ($i == strlen($encKey))
            $i = 0;
         $encData .= substr($encKey, $i, 1).(substr($data, $j, 1) ^ substr($encKey, $i, 1));
         ++$i;
      }
      return base64_encode($this->_keyGenerator($encData, $key));
   }

   public   function decode($data, $key) {
      $decData = null;
      $data = $this->_keyGenerator(base64_decode($data), $key);
      
      for ($i = 0; $i < strlen($data); ++$i)
      {
         $md5 = substr($data, $i++, 1);
         $decData .= (substr($data, $i, 1) ^ $md5);
      }
      return $decData;
   }

   public   function key($length = 8, $mode = SECURE_ALL) {
      $cmode = $mode;
      if ($cmode &= SECURE_MAJ && $cmode = $mode)
         $keys  = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      if ($cmode &= SECURE_MIN && $cmode = $mode)
         $keys .= "abcdefghijklmnopqrstuvwxyz";
      if ($cmode &= SECURE_NUM && $cmode = $mode)
         $keys .= "0123456789";
      return substr(str_shuffle($keys), 0, $length);
   }

   public   function associatedKey($length) {
   }

   public   function timeKey($expirationTime = 3600) {
      $data = SECURE_DELIMITER."_".(time() + $expirationTime);
      return $this->encode($data, NWF::config()->private_key);
   }

   public   function getValueTimeKey($key) {
      $data = explode('_', $this->decode($key, NWF::config()->private_key));
      if (!isset($data[0]) || !isset($data[0]))
         return null;
      return $data[0] == SECURE_DELIMITER ? $data[1] : null;
   }

   public   function checkTimeKey($key) {
      if (time() >= (int)$this->getValueTimeKey($key))
         return false;
      return true;
   }
}

class       NWF_MOD_Form {
   public   function begin($method = "POST", $action = null, $params = array()) {
      if ($action == null)
         $action = $_SERVER['PHP_SELF'];
      $secure = new NWF_MOD_Secure;
      $render = "<form method=\"".$method."\" action=\"".$action."\" ";
      foreach ($params as $key => $value) {
         $render .= $key."=\"".$value."\" ";
      }
      $render .= " />\n";
      $render .= "<input type=\"hidden\" name=\"_form_key\" value=\"".$secure->timeKey()."\">\n";
      return $render;
   }

   public   function end() {
      return "</form>";
   }

   private  function _getFilename($inputName) {
      $filename = strtr(basename($_FILES[$inputName]['name']), 'ÁÀÂÄÃÅÇÉÈÊËÍÏÎÌÑÓÒÔÖÕÚÙÛÜÝ', 'AAAAAACEEEEEIIIINOOOOOUUUUY');
      $filename = strtr($filename, 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ', 'aaaaaaceeeeiiiinooooouuuuyy');
      $filename = str_replace(" ", "_", $filename);
      $filename = md5(time());
      $filename .= ".".substr(strrchr($_FILES[$inputName]['name'], '.'), 1);
      return $filename;
   }

   public   function uploadFile($inputName, $extensionArray, $path, &$filename, $maxFileSize = null) {
      if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] > 0)
         return -1;
      if ($maxFileSize != null && $_FILES[$inputName]['size'] > $maxFileSize)
         return -2;
      if (!in_array(strtolower(substr(strrchr($_FILES[$inputName]['name'], '.'), 1)), $extensionArray))
         return -3;
      $filename = $this->_getFilename($inputName);
      return move_uploaded_file($_FILES[$inputName]['tmp_name'], $path.$filename) ? true : false;
   }
}
 
class       NWF_MOD_Time {
   public   function timestamp() {
      return time();
   }
   
   public   function now() {
      return date("Y-m-d H:i:s");
   }
   
   public   function date() {
      return date("Y-m-d");
   }
   
   public   function time() {
      return date("H:i:s");
   }

   private  function _calcAge($day, $month, $year) {
      $actualDay = date("d", time());
      $actualMonth = date("m", time());
      $actualYear = date("Y", time());
      if ($actualMonth == $month) {
         if ($actualDay >= $day)
            return $actualYear - $year;
         else
            return $actualYear - $year - 1;
      } 
      else if ($actualMonth > $month)
         return $actualYear - $year;
      else
         return $actualYear - $year - 1;
   }

   private  function _formatDateAge($args) {
      if (preg_match("`^\d{1,2}/\d{1,2}/\d{4}$`", $args[0])) {
         $mode = 1;
         $date = explode("/", $args[0]);
      }
      else if (preg_match("`^\d{1,2}-\d{1,2}-\d{4}$`", $args[0])) {
         $mode = 1;
         $date = explode("-", $args[0]);
      }
      else if (preg_match("`^\d{4}/\d{1,2}/\d{1,2}$`", $args[0])) {
         $mode = 2;
         $date = explode("/", $args[0]);
      }
      else if (preg_match("`^\d{4}-\d{1,2}-\d{1,2}$`", $args[0])) {
         $mode = 2;
         $date = explode("-", $args[0]);
      }
      else
         return false;
      return array($mode, $date);
   }

   private  function _ageIntFormat($args) {
      if ((int)$args[2] > 1000) {
         $day = (int)$args[0];
         $month = (int)$args[1];
         $year = (int)$args[2];
      }
      else {
         $day = (int)$args[2];
         $month = (int)$args[1];
         $year = (int)$args[0];
      }
      return $this->_calcAge($day, $month, $year);
   }

   private  function _ageStringFormat($args) {
      if (($ret = $this->_formatDateAge($args)) == false)
         return false;
      $mode = $ret[0];
      $date = $ret[1];
      if ($mode == 1) {
         $day = (int)$date[0];
         $month = (int)$date[1];
         $year = (int)$date[2];
      }
      else {
         $day = (int)$date[2];
         $month = (int)$date[1];
         $year = (int)$date[0];            
      }
      return $this->_calcAge($day, $month, $year);
   }

   public   function getAge() {
      $args = func_get_args();
      if (count($args) == 3)
         return $this->_ageIntFormat($args);
      else if (count($args) == 1)
         return $this->_ageStringFormat($args);
      else
         return false;
   }   
}

class       NWFFiles {
   private  $_fileName;

   public   function __construct($fileName, $create) {
      if ($fileName == null) {
         NWF::warning("FILES0001", "File name need to be different of null");
         return false;
      }
      $this->_fileName = $fileName;
      if ($create && !$this->exists())
         file_put_contents($fileName, null);
   }

   public   function create() {
      if ($this->exists()) {
         NWF::warning("FILES0010", "The file already exists");
         return false;
      }
      file_put_contents($fileName, null);
   }

   public   function download($downloadSpeed = 1024, $downloadFileName = null) {
      $downloadFileName = $downloadFileName == null ? $this->_fileName : $downloadFileName;
      if (file_exists($this->_fileName) && is_file($this->_fileName)) {
         header('Cache-control: private');
         header('Content-Type: application/octet-stream');
         header('Content-Length: '.filesize($this->_fileName));
         header('Content-Disposition: filename='.$downloadFileName);
         flush();
         $fstream = fopen($this->_fileName, 'r');
         while (!feof($fstream)) {
            print fread($fstream, round($downloadSpeed * 1024));
            flush();
            sleep(1);
         }
         fclose($fstream);
         return true;
      }
      return false;
   }

   public   function size($format = BYTE, $round = 2) {
      clearstatcache();
      if ((!$format || !($format % 1024)) && $format <= GO) {
         if (!$format)
            $format = 1;
         return round(filesize($this->_fileName) / $format, $round);
      }
      return false;
   }

   public   function infos($clearCache = true) {
      if ($clearCache)
         clearstatcache();
      if (($fileStream = lstat($this->_fileName)) == false)
         return false;
      $infos = array(
         "rights"       => $this->perms(),
         "inode"        => $fileStream[1],
         "nb_links"     => $fileStream[3],
         "user_id"      => $fileStream[4],
         "group_id"     => $fileStream[5],
         "size"         => $fileStream[7],
         "access_time"  => $fileStream[8],
         "modif_time"   => $fileStream[9]
      );
      return $infos;
   }


   public   function chown($user) {
      return chown($this->_fileName, $user);
   }

   public   function chmod($mode) {
      return chmod($this->_fileName, $mode);
   }

   private  function _letterPerms($perms) {
      if (($perms & 0xC000) == 0xC000)
         $info = 's';
      elseif (($perms & 0xA000) == 0xA000)
         $info = 'l';
      elseif (($perms & 0x8000) == 0x8000)
         $info = '-';
      elseif (($perms & 0x6000) == 0x6000)
         $info = 'b';
      elseif (($perms & 0x4000) == 0x4000)
         $info = 'd';
      elseif (($perms & 0x2000) == 0x2000)
         $info = 'c';
      elseif (($perms & 0x1000) == 0x1000)
         $info = 'p';
      else
         $info = 'u';   
      return $info;
   }

   private  function _ownerPerms($perms) {
      $info  = (($perms & 0x0100) ? 'r' : '-');
      $info .= (($perms & 0x0080) ? 'w' : '-');
      $info .= (($perms & 0x0040) ?
               (($perms & 0x0800) ? 's' : 'x' ) :
               (($perms & 0x0800) ? 'S' : '-'));
      return $info;
   }

   private  function _groupPerms($perms) {
      $info  = (($perms & 0x0020) ? 'r' : '-');
      $info .= (($perms & 0x0010) ? 'w' : '-');
      $info .= (($perms & 0x0008) ?
               (($perms & 0x0400) ? 's' : 'x' ) :
               (($perms & 0x0400) ? 'S' : '-'));
      return $info;
   }
 
   private  function _allPerms($perms) {
      $info  = (($perms & 0x0004) ? 'r' : '-');
      $info .= (($perms & 0x0002) ? 'w' : '-');
      $info .= (($perms & 0x0001) ?
               (($perms & 0x0200) ? 't' : 'x' ) :
               (($perms & 0x0200) ? 'T' : '-'));
      return $info;
   }

   public   function printPerms($clearCache = true) {
      if ($clearCache)
         clearstatcache();
      $perms = fileperms($this->_fileName);
      $print  = $this->_letterPerms($perms);
      $print .= $this->_ownerPerms($perms);
      $print .= $this->_groupPerms($perms);
      $print .= $this->_allPerms($perms);
      echo $print;
      return true;
   }

   public   function perms($clearCache = true) {
      if ($clearCache)
         clearstatcache();
      return substr(sprintf('%o', fileperms($this->_fileName)), -4);
   }

   public   function exists() {
      return file_exists($this->_fileName);
   }

   public   function delete() {
      if (!$this->exists()) {
         NWF::warning("FILES0007", "The file doesnot exists: ".$this->_fileName);
         return false;        
      }
      if (!unlink($this->_fileName)) {
         NWF::warning("FILES0008", "Cannot unlink the file: ".$this->_fileName);
         return false;
      }
      return true;
   }

   public   function dir() {
      return dirname($this->_fileName);
   }

   public   function get($printContents = false) {
      if (!$this->exists()) {
         NWF::warning("FILES0009", "The file doesnot exists: ".$this->_fileName);
         return false;        
      }
      $contents = file_get_contents($this->_fileName);
      if ($printContents)
         echo $contents;
      return $contents;
   }

   public   function put($contents, $mode = FILE_MODE_APPEND, $offset = null) {
      if ($mode != FILE_MODE_APPEND && $mode != FILE_MODE_TRUNC) {
         NWF::warning("FILES0002", "Mode need to be FILE_MODE_APPEND or FILE_MODE_TRUNC");
         return false;
      }
      if ($mode == FILE_MODE_TRUNC && $this->exists()) {
         if (!unlink($this->_fileName)) {
            NWF::warning("FILES0003", "Cannot unlink the file: ".$this->_fileName);
            return false;
         }
      }
      if (!($fstream = fopen($this->_fileName, 'a'))) {
         NWF::warning("FILES0004", "Cannot open the file: ".$this->_fileName);
         return false;        
      }
      if ($offset != null) {
         if (!is_numeric($offset) || fseek($fstream, (int)$offset) == -1) {
            NWF::warning("FILES0005", "Cannot change file offset: ".$this->_fileName);
            return false;
         }
      }
      if (($ret = fputs($fstream, $contents)) == false) {
         NWF::warning("FILES0006", "Cannot write on file: ".$this->_fileName);
         return false;     
      }
      fclose($fstream);
      return $ret;
   }
   
   public   function set($contents, $mode = FILE_MODE_APPEND, $offset = null) {
      return $this->put($contents, $mode, $offset);
   }

   public   function encode($key) {
      $encMod = new NWF_MOD_Secure;
      $file = $this->get();
      return $this->put($encMod->encode($file, $key), FILE_MODE_TRUNC);
   }

   public   function decode($key) {
      $encMod = new NWF_MOD_Secure;
      $file = $this->get();
      return $this->put($encMod->decode($file, $key), FILE_MODE_TRUNC);
   }
   
   public   function getDecode($key) {
      $encMod = new NWF_MOD_Secure;
      $file = $this->get();
      return $encMod->decode($file, $key);
   }

   public   function putEncode($contents, $key, $mode = FILE_MODE_APPEND, $offset = null) {
      $encMod = new NWF_MOD_Secure;
      return $this->put($encMod->encode($contents, $key), $mode, $offset);
   }

   public   function setEncode($contents, $key, $mode = FILE_MODE_APPEND, $offset = null) {
      return $this->putEncode($contents, $key, $mode, $offset);
   }

   public   function checkExtensionName($extensionName, $withPoint = false) {
      if (!$withPoint)
         $extensionName = '.'.$extensionName;
      if (strlen($this->_fileName) != strlen($extensionName) &&
         strlen($this->_fileName) - strpos($this->_fileName, $extensionName) == strlen($extensionName)) {
         return true;
      }
      return false;
   }
   
   public   function getExtentionName() {
   }
}

class       NWF_MOD_Get {
   public   function __construct() {
      $hash = null;
      $secure = new NWF_MOD_Secure;
      $GLOBALS[GET_ARRAY_NAME] = array();
      if (isset($GLOBALS['_GET']) && count($GLOBALS['_GET'])) {
         foreach ($GLOBALS['_GET'] as $key => $value) {
            if (isset($value[0]) && $value[0] == GET_DATA_LETTER) {
               $decode = $secure->decode(substr($value, 1, strlen($value)), "test");
               $infos = explode('_', $decode);
               if (isset($infos[GET_CONTENTS_IDX])) {
                  $data = $infos[GET_CONTENTS_IDX];
                  $hash = substr(sha1(md5($data)), strlen($data) % 7, 3);
               }
               if (!strncmp(GET_DELIMITER, $decode, strlen(GET_DELIMITER)) &&
                   isset($infos[GET_HASH_IDX]) && isset($infos[GET_CONTENTS_IDX]) &&
                   $infos[GET_HASH_IDX] == $hash) {
                     $GLOBALS['g_'.$key] = unserialize($infos[GET_CONTENTS_IDX]);
                     $GLOBALS[GET_ARRAY_NAME][$key] = unserialize($infos[GET_CONTENTS_IDX]);
               }
            }
         }
      }
   }

   public   function getUrl($vars) {
      $url = '?';
      foreach ($vars as $key => $value) {
         $url .= $key.'='.$this->encode($value).'&';
      }
      return substr($url, 0, strlen($url) - 1);
   }

   public   function encode($data) {
      $data = serialize($data);
      $secure = new NWF_MOD_Secure;
      $hash = substr(sha1(md5($data)), strlen($data) % 7, 3);
      return urlencode(GET_DATA_LETTER.$secure->encode(GET_DELIMITER."_".$hash."_".$data, "test"));
   }
}

class       NWF_MOD_Sql_TablesQueries {
   private  $_sqlObject;
   private  $_tableName;

   public   function __construct($sqlObject, $tableName) {
      $this->_sqlObject = $sqlObject;
      $this->_tableName = $tableName;
   }

   public   function select($names = "*", $where = null, $params = null) {
      return $this->_sqlObject->query("SELECT ".$names." FROM ".$this->_tableName);
   }

   public   function update() {
   }
}

class       NWF_MOD_Sql_Queries {
   private  $_pdoObject;
   private  $_tables;

   public   function __construct($pdoObject) {
      $this->_tables = null;
      $this->_pdoObject = $pdoObject;
   }

   public   function __get($name) {
      if ($this->_tables == null) {
         $query = $this->query("SHOW tables");
         foreach ($query as $key => $value)
            $this->_tables[$value[0]] = true; 
      }
      if (isset($this->_tables[$name])) {
         return new NWF_MOD_Sql_TablesQueries($this, $name);
      }
   }

   private  function _sqlSyntaxChecker($query, $extendedKeywords) {
      $error = false;
      if ($extendedKeywords)
         $checkFirst = "#^SELECT|INSERT|UPDATE|SHOW|DROP|DELETE|TRUNCATE|REPLACE|ALTER|EXEC|CREATE|DESCRIBE#i";
      else
         $checkFirst = "#^SELECT|INSERT|UPDATE|SHOW|DELETE#i";
      $checkKeywords = "#SELECT|INSERT|UPDATE|SHOW|DROP|DELETE|TRUNCATE|REPLACE|ALTER|EXEC|CREATE|DESCRIBE#i";
      $checkQuotes = "#--|'|\"#";
      $first = preg_match($checkFirst, $query);
      preg_match_all($checkKeywords, $query, $nbKeywords);
      preg_match_all($checkQuotes, $query, $nbQuotes);
      if (!$first) {
         $error = true;
         NWF::warning("SQL00004", "The query need to begin with an authorized keyword");
      }
      if (count($nbKeywords[0]) != 1) {
         $error = true;
         NWF::warning("SQL00005", "You cannot have more than 1 authorized keyword");
      }
      if (count($nbQuotes[0])) {
         $error = true;
         NWF::warning("SQL00006", "Quotes are forbidden: use `?` operator");
      }
      return $error;
   }

   private  function _sqlParamsAddSlashes(array $params) {
      foreach ($params as $key => $value)
         $params[$key] = addslashes($value);
      return $params;
   }

   private  function _sqlParamsStripSlashes(array $retQuery) {
      if (is_array($retQuery)) {
         foreach ($retQuery as $key => $value) {
            if (is_array($retQuery[$key])) {
               foreach ($retQuery[$key] as $subKey => $subValue)
                  $retQuery[$key][$subKey] = stripcslashes($subValue);
            }
         }
      }
      return $retQuery;
   }

   public   function query($query, $params = null, $extendedKeywords = false) {
      if (NWF::config()->sql_debug) {
         $dparams = null;  
         if (is_array($params)) {
               foreach ($params as $key => $value)
               $dparams .= $key." = ".$value."\r\n";
         }
         if ($dparams == null)
            $dparams = "Values: none";
         else
            $dparams = "Values:\r\n".substr($dparams, 0, strlen($dparams) - 2);
         NWF::debugStack('SQL', " [".$_SERVER['REQUEST_URI']."] [".date('d/m/Y H:i:s')."]\r\nQuery: [".$query."]\r\n".$dparams);
      }
      try {
         if ($this->_sqlSyntaxChecker($query, $extendedKeywords))
            return false;
         $queryPdo = $this->_pdoObject->prepare($query);
         $queryPdo->execute($params != null ? $this->_sqlParamsAddSlashes($params) : null);
         $retQuery = array();
         if (!strncmp(strtoupper($query), "DESCRIBE", 8) ||
             !strncmp(strtoupper($query), "SELECT", 6) ||
             !strncmp(strtoupper($query), "SHOW", 4)) {
            while ($ret = $queryPdo->fetch())
               $retQuery[] = $ret;
            return $this->_sqlParamsStripSlashes($retQuery);
         }
         if (!strncmp(strtoupper($query), "INSERT", 6))
            return $this->_pdoObject->lastInsertId();         
         return true;
      }
      catch (Exception $e) {
         NWF::warning("SQL0003", $e->getMessage());
         return false;
      }
   }
}

class       NWF_MOD_Sql {
   private  $_defaultDatabase;
   private  $_databases;
 
   public   function __construct() {
      $this->_databases = array();
      $this->_defaultDatabase = null;
      if (NWF::config()->sql_database != null)
         $this->connect();
   }

   public   function __get($name) {
      if (isset($this->_databases[$name]))
         return $this->_databases[$name];
      if ($this->_defaultDatabase != null)
         return $this->_defaultDatabase->__get($name);
      return null;
   }

   public   function __call($name, $args) {
      if (method_exists($this->_defaultDatabase, $name))
         return call_user_func_array(array($this->_defaultDatabase, $name), $args);
      return null;
   }

   private  function _pdoConnect(array $db) {
      try {
         NWF::debugStack("MOD_SQL", "Connection ".$db['type']."to ".$db['database'].": ".$db['username']."@".$db['host']);
         $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
         $pdo = new PDO($db['type'].":host=".$db['host'].";dbname=".$db['database'],
                        $db['username'], $db['password'], $pdo_options);
         $this->_databases[$db['database']] = new NWF_MOD_Sql_Queries($pdo);
         $this->_defaultDatabase = $this->_databases[$db['database']];
      }
      catch (Exception $e) {
         NWF::warning("SQL0002", "PDO error on connect: ".$e->getMessage());
         return false; 
      }
   }

   public   function setDefaultDatabase($databaseName) {
      if (isset($this->_databases[$databaseName]))
         $this->_defaultDatabase = $this->_databases[$databaseName];
   }

   public   function connect() {
      $error = true;
      $arrayConnect = null;
      $args = func_get_args();
      if (is_array(isset($args[0]) ? $args[0] : null) && count($args) == 1) {
         $arrayConnect = $args[0];
         if (isset($arrayConnect['username']) &&
             isset($arrayConnect['password']) &&
             isset($arrayConnect['database'])) {
               $arrayConnect['type'] = isset($arrayConnect['type']) ? $arrayConnect['type'] : "mysql";
               $arrayConnect['host'] = isset($arrayConnect['host']) ? $arrayConnect['host'] : "localhost";
               $error = false;
         }
      }
      else if (count($args) >= 3) {
         $error = false;
         $arrayConnect['username'] = $args[0];
         $arrayConnect['password'] = $args[1];
         $arrayConnect['database'] = $args[2];
         $arrayConnect['type'] = isset($args[4]) ? $args[4] : "mysql";
         $arrayConnect['host'] = isset($args[3]) ? $args[3] : "localhost";
      }
      else {
         $error = false;
         $arrayConnect['type'] = NWF::config()->sql_type;
         $arrayConnect['host'] = NWF::config()->sql_host;
         $arrayConnect['username'] = NWF::config()->sql_username;
         $arrayConnect['password'] = NWF::config()->sql_password;
         $arrayConnect['database'] = NWF::config()->sql_database;
      }
      if ($error || $arrayConnect == null) {
         NWF::warning("SQL0001", "Bad connexion format");
         return false;
      }
      return $this->_pdoConnect($arrayConnect);
   }
}

class       NWF_MOD_Mvc {
   private  $_cnt = 0;
   private  $_pid = null;
   private  $_spid = null;
   private  $_args = null;
   private  $_nwf = null;
   private  $_rights = array();

   public   function __construct($nwf) {
      $this->_nwf = $nwf;
      $this->_pid = 'index';
      $this->_checkFolders();
      if (!$nwf->isModuleLoaded(MOD_MEMBER))
         $nwf->loadModule(MOD_MEMBER);
      $paths = NWF::stack()->get("__mvc_path");
      if (array_key_exists(NWF::config()->mvc_pid_var, $_GET)) {
         if (count($paths)) {
            foreach ($paths as $key => $value) {
               if ($value[0] == $_GET[NWF::config()->mvc_pid_var]) {
                  $this->_pid = $value[1];
                  $this->_spid = null;
                  return;
               }
            }
         }
         else {
            $p = explode('/', $_GET[NWF::config()->mvc_pid_var]);
            $this->_pid = $p[0];
            $this->_spid = isset($p[1]) ? $p[1] : null;
         }
      }
      $this->_args = $_GET;
      unset($this->_args[NWF::config()->mvc_pid_var]);
   }

   //  TO-DO
   private  function _checkFolders() {
      if (!is_dir(NWF::config()->mvc_compiled_folder))
         NWF::warning("MVC00001", "Compiled folder doesnot exists.");
   }

   public   function pagename() {
      return $this->_pid; 
   }

   public   function subpagename() {
      return $this->_spid;
   }

   public   function loadController($viewName, $cacheTime = 0) {
      NWF::stack()->set("__mvc_controller[]", array($viewName, $cacheTime));
   }

   public   function loadModel($modelName) {
      /* -- TO-DO
         -- Check if the model is already loaded.
         -- Check modules names
      if (NWF::stack()->get("__mvc_model") != null) {
         NWF::warning("MVC00002", "This model is already loaded.");
         return false;
      }*/
      if (!file_exists(NWF::config()->mvc_model_folder."/".$modelName.".php")) {
         NWF::warning("MVC00002", "This model does not exists (no file).");
         return false;
      }
      include_once(NWF::config()->mvc_model_folder."/".$modelName.".php");
      if (!class_exists($modelName)) {
         NWF::warning("MVC00003", "This model does not exists (no class).");
         return false;
      }
      $model = new $modelName($this);
      NWF::stack()->set("__mvc_model[]", array('ModelName' => strtolower($modelName), 'ModelObject' => $model));
      return true;
   }

   public   function setPageRights() {
      $this->_rights = func_get_args();
      if (count($this->_rights) && !in_array($this->_nwf->member->getRights(), $this->_rights))
         NWF::redirect(NWF::config()->mvc_no_logged);      
   }

   private  function _loadControllerView($controllerName, $controller = null, $cache = 0) {
      
      if ($controllerName != $this->_pid) {
         if (file_exists(NWF::config()->mvc_controller_folder."/".$controllerName.".php"))
            $file = file_get_contents(NWF::config()->mvc_controller_folder."/".$controllerName.".php");
         else
            return false;
         $controllerInst = "__Controller".$this->_cnt;
         $file = preg_replace("#function *Controller#", "function __Controller".$this->_cnt, $file);
         $file = preg_replace("#class *Controller#", "class __Controller".($this->_cnt++), $file);
         eval('?>'.$file);
         $controller = new $controllerInst($this->_nwf);
      }
      file_exists(NWF::config()->mvc_view_folder."/".$controllerName.".tpl");
      $view = NWF::template(NWF::config()->mvc_view_folder."/".$controllerName.".tpl", $controller->getViewVars(), $cache);
      $view->render();
   }

   public   function render() {
      if (!file_exists(NWF::config()->mvc_controller_folder."/".$this->_pid.".php"))
         header("Location: ".NWF::config()->mvc_error_page);
      //preprocess
      if (file_exists(NWF::config()->mvc_preprocess_folder."/".$this->_pid.".php"))
         include_once(NWF::config()->mvc_preprocess_folder."/".$this->_pid.".php");
      else if (file_exists(NWF::config()->mvc_preprocess_folder."/preprocess_default.php"))
         include_once(NWF::config()->mvc_preprocess_folder."/preprocess_default.php");
      //controller
      include_once(NWF::config()->mvc_controller_folder."/".$this->_pid.".php");
      $controller = new Controller($this->_nwf);
      //check rights
      if (count($this->_rights) && !in_array($this->_nwf->member->getRights(), $this->_rights))
         NWF::redirect(NWF::config()->mvc_no_logged);
      //view
      $allControllers = NWF::stack()->get("__mvc_controller");
      if (count($allControllers)) { //si des vues dans la stack
         foreach ($allControllers as $key => $value) {
            $this->_loadControllerView($value[0], $controller, $value[1]);
         }
      }
      else { //vues par defaut
         $this->_loadControllerView("header_default", $controller);
         $this->_loadControllerView($this->_pid, $controller);
         $this->_loadControllerView("footer_default", $controller);
      }
   }
}

class       NWFTemplate {
   private  $_vars = null;
   private  $_filename = null;
   private  $_cacheTime = 0;

   public   function __construct($filename = null, $vars = null, $cacheTime = 0) {
      if ($filename != null)
         $this->loadFile($filename);
      if ($vars != null)
         $this->loadVars($vars);
      $this->_cacheTime = $cacheTime;
      if ($cacheTime && !is_dir(NWF::config()->mvc_compiled_folder))
         mkdir(NWF::config()->mvc_compiled_folder);
   }

   public   function setCache($timeTime = 0) {
      $this->_cacheTime = $cacheTime;
      if ($cacheTime && !is_dir(NWF::config()->mvc_compiled_folder))
         mkdir(NWF::config()->mvc_compiled_folder);
   }

   public   function loadFile($filename) {
      $file = NWF::file($filename, false);
      if (!$file->exists()) {
         NWF::warning("TPL0001", "This template file doesnot exists: ".$filename);
         return false;
      }
      $this->_filename = $filename;
      return true;
   }

   public   function loadVars($vars) {
      if (!is_array($vars)) {
         NWF::warning("TPL0002", "The vars variable need to be an array");
         return false;
      }
      $this->_vars = $vars;
      return true;
   }

   private  function _patchIf($contents) {
      /*$contents = preg_replace('#\[\{if(.+)\}\}#iUs', '<?php if ($1): ?>', $contents);
      $contents = str_replace("[{endif}]", $value, $contents);*/
   }

   private  function _patchVars($contents, $vars) {
      if (!is_array($vars))
         return $contents;
      foreach ($vars as $key => $value) {
         if (!is_array($vars[$key]))
            $contents = str_replace("{{".$key."}}", $value, $contents);
      }
      foreach ($vars as $key => $value) {
         if (is_array($vars[$key])) {
            $value = base64_encode(serialize($value));
            $contents = preg_replace('#\{\{'.$key.'\}\}\[(.+)\]#iUs', '<?php echo NWF::getArray(\''.$value.'\', $1); ?>', $contents);
            $value = 'NWF::getArray(\''.$value.'\')';
            $contents = str_replace("{{".$key."}}", $value, $contents);
         }
      }
      return $contents;
   }

   public   function render() {
      $render = null;
      if ($this->_filename == null) {
         NWF::warning("TPL0003", "You need to load a template file before rendering");
         return false;         
      }
      if ($this->_cacheTime && file_exists(NWF::config()->mvc_compiled_folder."/".md5($this->_filename).".tpl")) {

      }
      else {
         //echo NWF::config()->mvc_compiled_folder."/".md5($this->_filename);
         $file = NWF::file($this->_filename, false);
         $render = $file->get();
         if ($this->_vars != null)
            $render = $this->_patchVars($render, $this->_vars);
         $vars = base64_encode(serialize($this->_vars));
         $code = '<?php $'.NWF::config()->tpl_varname.' = unserialize(base64_decode(\''.$vars.'\')); ?>';

	 //echo $code.$render;
         return eval('?>'.$code.$render);
      }
   }
}

class       NWF_MOD_Member {
   private  $_nwf;
   private  $_data = array();
   private  $_rights = 0;

   public   function __construct($nwf) {
      $this->_nwf = $nwf;
      if (isset($_SESSION['__Member_data']))
         $this->_data = $_SESSION['__Member_data'];
      if (isset($_SESSION['__Member_rights']))
         $this->_rights = $_SESSION['__Member_rights'];
   }

   public   function load($query, $args = null) {
      if (!$this->_nwf->isModuleLoaded(MOD_SQL)) {
         NWF::warning("MEMBER00003", "This method needs the MOD_SQL");
         return false;
      }
      $ret = $this->_nwf->sql->query($query, $args);
      if (isset($ret[0]) && is_array($ret[0])) {
         foreach ($ret[0] as $key => $value) {
            if (!is_int($key))
               $this->_data[$key] = $value;
         }
      }
      else {
         NWF::warning("MEMBER00004", "Invalid member query sql");
         return false;
      }
   }

   public   function __destruct() {
      $_SESSION['__Member_data'] = $this->_data;
      $_SESSION['__Member_rights'] = $this->_rights;
   }

   public   function delete() {
      $this->_data = array();
      $this->_rights = 0;
      unset($_SESSION['__Member_data']);
      unset($_SESSION['__Member_rights']);
   }

   public   function __set($name, $value) {
      $this->_data[$name] = $value;
      return true;
   }
   
   public   function __get($name) {
      if (array_key_exists($name, $this->_data))
         return $this->_data[$name];
      else
         NWF::warning("MEMBER00001", "This variable doesnot exists");
      return null;
   }

   public   function setRights($rights) {
      if (is_int($rights)) {
         $this->_rights = $rights;
         return true;
      }
      else {
         NWF::warning("MEMBER00002", "Right might be an integer");
         return false;
      }
   }

   public   function getRights() {
      return $this->_rights;
   }
}

class       NWFData {
   private  $_nwf;

   public   function __construct($nwf, $table) {
      $this->_nwf = $nwf;
      if (!isset($this->_nwf->sql)) {
         NWF::warning("DATA00001", "NWF requires MOD_SQL to run");
         return false;         
      }
   }
}

class       NWFXml {
   private  $_flux = null;
   private  $_xmlContents = null;

   public   function __construct($flux = null) {
      $this->_flux = $flux;
   }

   public   function loadFile($filename = null) {
      if ($filename != null)
         $this->_flux = $filename;
      if (!file_exists($this->_flux)) {
         NWF::warning("XML00001", "This file doesnot exists");
         return false;
      }
      $this->_xmlContents = file_get_contents($this->_flux);
      return $this;
   }

   public   function loadUrl($url = null) {
      if ($url != null)
         $this->_flux = $url;
      $this->_xmlContents = file_get_contents($this->_flux);
      return $this;
   } 

   public   function toArray() {
      $readXml = (array) simplexml_load_string($this->_xmlContents);
      $xmlToJson = json_encode($readXml);
      return json_decode($xmlToJson, true);
   }

   public   function toJson() {
      $readXml = (array) simplexml_load_string($this->_xmlContents);
      $xmlToJson = json_encode($readXml);
      return json_encode(json_decode($xmlToJson, true));
   }   
}