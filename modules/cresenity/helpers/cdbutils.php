<?php

    class cdbutils {

        public static function table_exists($table, $db = null) {
            if ($db == null) $db = CDatabase::instance();
            $q = 'SHOW TABLES LIKE ' . $db->escape($table) . '';
            return cdbutils::get_value($q, $db);
        }

        public static function empty_table($table, $db = null) {
            if ($db == null) $db = CDatabase::instance();
            $db->query("delete from `" . $table . "`");
            $db->query("alter table `" . $table . "` AUTO_INCREMENT = 1;");
        }

        public static function get_row_count_from_base_query($query, $db = null) {
            if ($db == null) $db = CDatabase::instance();
            $qtotal = '';
            $qtotal .= 'select count(*) as cnt from (' . $query . ') as a';
            $cnt = 0;
            $r = $db->query($qtotal);
            if ($r->count() > 0) $cnt = $r[0]->cnt;
            return $cnt;
        }

        public static function row_exists($table, $where = array(), $db = null) {
            if ($db == null) $db = CDatabase::instance();
            $q = "select count(1) as cnt from " . $db->escape_table($table) . " where 1=1 ";
            foreach ($where as $k => $v) {
                $q.=" and " . $db->escape_column($k) . "=" . $db->escape($v);
            }
            $cnt = cdbutils::get_value($q, $db);
            return $cnt > 0;
        }

        public static function get_value($query, $db = null) {
            if ($db == null) $db = CDatabase::instance();
            $r = $db->query($query);
            $result = $r->result(false);
            $res = array();
            $value = null;
            foreach ($result as $row) {
                foreach ($row as $k => $v) {
                    $value = $v;
                    break;
                }
                break;
            }
            return $value;
        }

        public static function get_row($query, $db = null) {
            if ($db == null) $db = CDatabase::instance();
            $r = $db->query($query);
            $result = null;
            if ($r->count() > 0) {
                $result = $r[0];
            }
            return $result;
        }

        public static function get_array($query, $db = null) {
            if ($db == null) $db = CDatabase::instance();
            $r = $db->query($query);
            $result = $r->result(false);
            $res = array();
            foreach ($result as $row) {
                $cnt = 0;
                $arr_val = "";
                foreach ($row as $k => $v) {
                    if ($cnt == 0) $arr_val = $v;
                    $cnt++;
                    if ($cnt > 0) break;
                }
                $res[] = $arr_val;
            }
            return $res;
        }

        public static function get_list($query, $db = null) {
            if ($db == null) $db = CDatabase::instance();
            $r = $db->query($query);
            $result = $r->result(false);
            $res = array();
            foreach ($result as $row) {
                $cnt = 0;
                $arr_key = "";
                $arr_val = "";
                foreach ($row as $k => $v) {
                    if ($cnt == 0) $arr_key = $v;
                    if ($cnt == 1) $arr_val = $v;
                    $cnt++;
                    if ($cnt > 1) break;
                }
                $res[$arr_key] = $arr_val;
            }
            return $res;
        }

        public static function get_table_list($db = null) {
            if ($db == null) $db = CDatabase::instance();
            $tables = array();
            $array = cdbutils::get_array('SHOW TABLES');

            foreach ($array as $arr) {
                $tables[$arr] = $arr;
            }

            return $tables;
        }

        public function parse_column_type($str) {
            $result = array();
            $str = strtolower(trim($str));

            if (($open = strpos($str, '(')) !== FALSE) {
                // Find closing bracket
                $close = strpos($str, ')', $open) - 1;
                // Find the type without the size
                $type = substr($str, 0, $open);
            }
            else {
                // No length
                $type = $str;
            }
            $result['datatype'] = $type;
            $result['unsigned'] = false;
            $result['notnull'] = false;
            if ((strpos($str, 'unsigned')) !== FALSE) {
                $result['unsigned'] = true;
            }
        }

        public static function get_table_count($db = null) {
            if ($db == null) $db = CDatabase::instance();
            $q = 'SHOW table status';

            $res = cdbutils::get_array($q);
            return count($res);
        }

        public static function get_table_info() {
            $db = CDatabase::instance();
            $q = 'SHOW table status';

            $res = $db->query($q);
            $r = $res->result_array(TRUE);
            $result = array();
            foreach ($r as $row) {
                // Make an associative array
                $engine = strtolower($row->Engine);
                $version = strtolower($row->Version);
                $row_format = strtolower($row->Row_format);
                $collation = strtolower($row->Collation);
                $charset = 'utf8';
                if (strlen($collation) > 0) {
                    $collation_array = explode("_", $collation);
                    if (count($collation_array) > 0)
                            $charset = $collation_array[0];
                }
                $table_info = array();

                $table_info['engine'] = $engine;
                $table_info['version'] = $version;
                $table_info['row_format'] = $row_format;
                $table_info['charset'] = $charset;
                $table_info['collation'] = $collation;



                $result[$row->Name] = $table_info;
            }


            return $result;
        }

        public static function get_column_info($table) {
            $db = CDatabase::instance();
            $q = 'SHOW FULL COLUMNS FROM ' . $db->escape_table($table);

            $res = $db->query($q);
            $r = $res->result_array(TRUE);
            $result = array();
            foreach ($r as $row) {
                // Make an associative array
                $str_type = $row->Type;
                $comment = $row->Comment;

                $field_info = array();
                $str = strtolower(trim($str_type));

                if (($open = strpos($str, '(')) !== FALSE) {
                    // Find closing bracket
                    $close = strpos($str, ')', $open) - 1;
                    // Find the type without the size
                    $type = substr($str, 0, $open);
                }
                else {
                    // No length
                    $type = $str;
                }
                $field_info['datatype'] = $type;
                $field_info['default'] = null;
                $field_info['comment'] = $comment;
                $field_info['collation'] = null;
                $field_info['charset'] = null;


                $field_info['unsigned'] = FALSE;
                $field_info['notnull'] = FALSE;
                $field_info['primary_key'] = FALSE;
                $field_info['auto_increment'] = FALSE;
                $field_info['length'] = '';

                if ($row->Collation !== '(NULL)') {
                    $collation = $row->Collation;
                    $field_info['collation'] = $collation;

                    $charset = 'utf8';
                    if (strlen($collation) > 0) {
                        $collation_array = explode("_", $collation);
                        if (count($collation_array) > 0)
                                $charset = $collation_array[0];
                    }
                    $field_info['charset'] = $charset;
                }
                if ($row->Default !== '(NULL)') {
                    $field_info['default'] = $row->Default;
                }
                if ((strpos($str, '(')) !== FALSE) {
                    // Add the length to the field info
                    $field_info['length'] = substr($str, $open + 1, $close - $open);
                }
                if ((strpos($str, 'unsigned')) !== FALSE) {
                    $field_info['unsigned'] = true;
                }
                if ($row->Extra === 'auto_increment') {
                    // For sequenced (AUTO_INCREMENT) tables
                    $field_info['auto_increment'] = TRUE;
                }
                if ($row->Key === 'PRI') {
                    // For sequenced (AUTO_INCREMENT) tables
                    $field_info['primary_key'] = TRUE;
                }
                if ($row->Null === 'YES') {
                    // Set NULL status
                    $field_info['notnull'] = FALSE;
                }
                $result[$row->Field] = $field_info;
            }


            return $result;
        }

        public static function convert_table_engine($engine = "InnoDB") {
            $db = CDatabase::instance();
            $tables = cdbutils::get_array("show tables");
            foreach ($tables as $table) {
                $db->query("alter table `" . $table . "` ENGINE=" . $engine);
            }
        }

        public static function backup($file, $type = "sql") {
            $error = 0;
            $starttime = microtime(true);
            if ($error == 0) {
                try {
                    $log_filename = "db_backup";
                    $db = CDatabase::instance();
                    $headers = "-- Mysql Data Dump\r\n";
                    $headers .= "-- Dumping started at : " . date("Y-m-d h:i:s") . "\r\n\r\n";
                    $mysql = "";
                    $mysql .= "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\r\n";
                    $mysql .= "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\r\n";
                    $mysql .= "\r\n\r\n";
                    $tables = cdbutils::get_array("show tables");
                    foreach ($tables as $table) {
                        $sqlct = cdbutils::get_value('show create table `' . $table . '`');
                        $mysql.="DROP TABLE IF EXISTS `$table`;\r\n\r\n";
                        $mysql.=$sqlct . ";\r\n\r\n";
                        $r = $db->query("select * from `" . $table . "`");
                        $result = $r->result(false);
                        foreach ($result as $row) {
                            $ckey = array();
                            $cval = array();
                            foreach ($row as $k => $v) {
                                $ckey[] = "`" . $k . "`";
                                $cval[] = $db->escape($v);
                            }
                            $keys = join(",", $ckey);
                            $vals = join(",", $cval);
                            $mysql.="insert into `$table`($keys) values($vals);\r\n";
                        }
                    }
                }
                catch (Exception $ex) {
                    $error++;
                    clogger::log("backup.log", "ERROR", $ex->getMessage());
                }
            }
            if ($error == 0) {
                $mysql .= "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\r\n";
                $mysql .= "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\r\n";

                $headers .= "-- Dumping finished at : " . date("Y-m-d h:i:s") . "\r\n\r\n";
                $endtime = microtime(true);
                $diff = $endtime - $starttime;
                $headers .= "-- Dumping data took : " . $diff . " Sec\r\n\r\n";
                $headers .= "-- --------------------------------------------------------\r\n\r\n";


                $datadump = $headers . $mysql;

                if ($type == "zip") {
                    $zip = new ZipArchive();
                    $overwrite = ZIPARCHIVE::OVERWRITE;
                    if (file_exists($file)) {
                        $overwrite = ZIPARCHIVE::CREATE;
                    }
                    if ($zip->open($file, $overwrite) !== TRUE) {
                        exit("cannot open <$filename>\n");
                    }
                    $zip->addFromString("data_" . date("YmdHis") . ".sql", $datadump);
                    $zip->close();
                }
                else {
                    $fp = fopen($fullfilename, 'w');
                    fputs($fp, $datadump);
                    fclose($fp);
                }
            }
            if ($error == 0) {
                clogger::log($log_filename, "MSG", "Success Backup " . $file);
            }
            return $error;
        }

        public static function native_backup($file, $type = "sql") {
            $log_filename = "db_backup";
            $mysql_username = CF::config("database.default.connection.user");
            $mysql_password = CF::config("database.default.connection.pass");
            $mysql_database = CF::config("database.default.connection.database");
            $mysql_host = CF::config("database.default.connection.host");

            $starttime = microtime(true);
            $headers = "-- Mysql Data Dump\r\n";
            $headers .= "-- Dumping started at : " . date("Y-m-d h:i:s") . "\r\n\r\n";

            //connect database
            $link = @mysql_connect($mysql_host, $mysql_username, $mysql_password);
            $error = 0;
            if (!$link) {
                $error++;
                clogger::log("backup.log", "ERROR", @mysql_error());
            }
            //select db
            if ($error == 0) {
                $db_selected = @mysql_select_db($mysql_database, $link);
                if (!$db_selected) {
                    $error++;
                    clogger::log($log_filename, "ERROR", @mysql_error());
                }
            }
            $mysql = "";
            $mysql .= "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\r\n";
            $mysql .= "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\r\n";

            if ($error == 0) {
                //retrieve meta data
                $q1 = mysql_query("show tables");
                while ($t = mysql_fetch_array($q1)) {
                    $table = $t[0];
                    $q2 = mysql_query("show create table `$table`");
                    $sql = mysql_fetch_array($q2);

                    $mysql.="DROP TABLE IF EXISTS `$table`;\r\n\r\n";
                    $mysql.=$sql['Create Table'] . ";\r\n\r\n";

                    $q3 = mysql_query("select * from `$table`");
                    while ($data = mysql_fetch_assoc($q3)) {
                        $keys = array_keys($data);
                        $keys = array_map('addslashes', $keys);
                        $keys = join('`,`', $keys);
                        $keys = "`" . $keys . "`";
                        $vals = array_values($data);

                        $vals = array_map('addslashes', $vals);
                        $vals = join("','", $vals);
                        $vals = "'" . $vals . "'";

                        $mysql.="insert into `$table`($keys) values($vals);\r\n";
                    }
                    $mysql.="\r\n";
                }
            }
            if ($error == 0) {
                $mysql .= "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\r\n";
                $mysql .= "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\r\n";

                $headers .= "-- Dumping finished at : " . date("Y-m-d h:i:s") . "\r\n\r\n";
                $endtime = microtime(true);
                $diff = $endtime - $starttime;
                $headers .= "-- Dumping data took : " . $diff . " Sec\r\n\r\n";
                $headers .= "-- --------------------------------------------------------\r\n\r\n";


                $datadump = $headers . $mysql;

                if ($type == "zip") {
                    $zip = new ZipArchive();
                    $overwrite = ZIPARCHIVE::OVERWRITE;
                    if (file_exists($file)) {
                        $overwrite = ZIPARCHIVE::CREATE;
                    }
                    if ($zip->open($file, $overwrite) !== TRUE) {
                        exit("cannot open <$filename>\n");
                    }
                    $zip->addFromString("data_" . date("YmdHis") . ".sql", $datadump);
                    $zip->close();
                }
                else {
                    $fp = fopen($fullfilename, 'w');
                    fputs($fp, $datadump);
                    fclose($fp);
                }
            }
            //close database
            @mysql_close($link);
            if ($error == 0) {
                clogger::log($log_filename, "MSG", "Success Backup " . $file);
            }
            return $error;
        }

        public static function restore($fullfilename) {
            $db = CDatabase::instance();
            $filepath = $fullfilename;
            $pathinfo = pathinfo($filepath);

            $dirname = $pathinfo["dirname"];
            $basename = $pathinfo["basename"];
            $extension = $pathinfo["extension"];
            $filename = $pathinfo["filename"];
            if ($extension == "zip") {
                $extract_dir = $dirname . "/extract";
                if (!is_dir($extract_dir)) mkdir($extract_dir);
                $zip = new ZipArchive();
                if ($zip->open($filepath) === true) {
                    if ($zip->numFiles > 0) {
                        $zip->extractTo($extract_dir, array($zip->getNameIndex(0)));
                        $filepath = $extract_dir . "/" . $zip->getNameIndex(0);
                    }
                    $zip->close();
                }
            }
            $lines = file($filepath);
            $templine = "";
            foreach ($lines as $line) {
                // Skip it if it's a comment
                if (substr($line, 0, 2) == "--" || $line == "") continue;
                // Add this line to the current segment
                $templine .= $line;
                // If it has a semicolon at the end, it's the end of the query 
                if (substr(trim($line), -1, 1) == ";") {
                    // Perform the query
                    $db->query($templine);
                    //mysql_query($templine) or print("Error performing query '<strong>" . $templine . "\": " . mysql_error() . "<br /><br />");
                    // Reset temp variable to empty
                    $templine = "";
                }
            }//end foreach 
        }

        public static function load_sql($sql) {
            $db = CDatabase::instance();
            $sql = explode("\n", $sql);

            $buffer = '';
            foreach ($sql as $line) {
                $buffer .= $line;
                if (preg_match('/;$/', $line)) {
                    $db->query($buffer);
                    $buffer = '';
                }
            }
        }

    }

?>
