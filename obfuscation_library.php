<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
#################################################
# obfuscation-library
#################################################  
# @author Nico Redick 
# @copyright Nico Redick
# @license MIT 
#################################################
# DESCRIBTION:
# Obfuscates nearly everything with different
# levels of obfuscation.
# More Infos at => 
#################################################
# NOTICE:
# -All private Methods and Variables 
#  start with an underscore
# -All Variables start with an char and an 
#  underscore. The Char gives the primary type 
#  of the variable
#  eg: $b_checked => type boolean
#  b => boolean
#  o => object
#  i => integer
#  s => string
#  a => array
#  m => mixed
#################################################
# Licence-Part:
# http://opensource.org/licenses/MIT
# (c) 2015 Nico Redick
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
# 
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
# 
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
# THE SOFTWARE.
#################################################
# VERSION 1.0 Build 150123
#################################################
#  start of development
#################################################

class obfuscation_library {

    private $_s_salt;
    private $_a_switch;
    private $_i_sst_start;
    private $_i_sst_lenght;
    private $_s_splitter;
    private $_s_hashing_method;
    private $o_config = null;

    public function __construct($s_salt = null, $a_switch = null, $s_sst_start = null, $s_sst_lenght = null, $s_splitter = null, $s_hashtype = null) {
        $this->set_salt($s_salt);
        $this->set_switch($a_switch);
        $this->set_sst_start($s_sst_start);
        $this->set_sst_length($s_sst_lenght);
        $this->set_splitter($s_splitter);
        $this->set_hash_method($s_hashtype);
    }

    function obfuscate_url($array, $page = '') {
        $link = '';
        foreach ($array as $key => $value) {
            switch ($this->_s_hashing_method) {
                case 'md5' :
                    $secure_token = md5($key . $this->_s_splitter . $value);
                    $salted_secure_token = substr(md5($this->_s_salt . $secure_token), $this->_i_sst_start, $this->_i_sst_lenght) . substr(md5($this->_i_sst_start . 'md5' . $this->_s_salt), $this->_i_sst_lenght, 2);
                    break;
            }
            $param = $this->switch_char(base64_encode($key . $this->_s_splitter . $value . $this->_s_splitter . $salted_secure_token));
            if ($link == '') {
                $link .=$page . '' . $param;
            } else {
                $link .= '|' . $param;
            }
        }
        return base64_encode($link);
    }

    function clear_url($url) {
        if ($url != '') {
            
            $a_param = explode('|', base64_decode($url));
            $error = 0;
            $return = array();
            foreach ($a_param as $element) {
                $decoded = base64_decode($this->switch_char($element, 1));
                $a_decoded_elements = explode('|', $decoded);
                if (count($a_decoded_elements == 3)) {
                    if (strlen($a_decoded_elements[2]) == $this->_i_sst_lenght + 2) {
                        switch (substr($a_decoded_elements[2], -2)) {
                            case substr(md5($this->_i_sst_start . 'md5' . $this->_s_salt), $this->_i_sst_lenght, 2) :
                                $secure_token = md5($a_decoded_elements[0] . $this->_s_splitter . $a_decoded_elements[1]);
                                $salted_secure_token = substr(md5($this->_s_salt . $secure_token), $this->_i_sst_start, $this->_i_sst_lenght);
                                if ($salted_secure_token != substr($a_decoded_elements[2], 0, -2)) {
                                    $error = 1;
                                } else {
                                    $return[$a_decoded_elements[0]] = $a_decoded_elements[1];
                                }
                                break;
                        }
                    }
                }
            }
        } else {
            $error = 1;
        }
        if ($error == 1) {
            $return = array();
            return FALSE;
        } else {
            return $return;
        }
    }

    function crypt_url($array, $string = NULL, $iteration = NULL) {
        if ($string == NULL || strlen != 22) {
            $string = 'SHixLwMZlCgJruAVRhhsfC';
        }
        if ($iteration == NULL || $iteration < 10 || $iteration > 31) {
            $string = "14";
        }
    }

    function switch_char($str, $reverse = 0) {
        if ($reverse == 1) {
            $a_tmp = $this->reverse_switch_array();
        } else {
            $a_tmp = $this->_a_switch;
        }
        foreach ($a_tmp as $element) {
            foreach ($element as $key => $value) {
                if (strlen($str) > $key && strlen($str) > $value) {
                    for ($i = 0; $i < strlen($str); $i++) {
                        $a_str[$i] = substr($str, $i, 1);
                    }
                    if ($reverse == 0) {
                        $tmp = $a_str[$key];
                        $a_str[$key] = $a_str[$value];
                        $a_str[$value] = $tmp;
                    } else {
                        $tmp = $a_str[$value];
                        $a_str[$value] = $a_str[$key];
                        $a_str[$key] = $tmp;
                    }
                }
                $str = '';
                foreach ($a_str as $char) {
                    $str .= $char;
                }
            }
        }
        if ($reverse == 0) {
            return str_replace('=', '_', $str);
        } else {
            return str_replace('_', '=', $str);
        }
    }

    /**
     * (private)
     * reverse the switch array
     * @return array Array Containing the reversed switch-positions
     */
    private function reverse_switch_array() {
        $a_tmp = array();
        for ($i = 0; $i < count($this->_a_switch); $i++) {
            foreach ($this->_a_switch[count($this->_a_switch) - $i - 1] as $key => $value) {
                $a_tmp[$i][$key] = $value;
            }
        }
        return $a_tmp;
    }

    private function check_config() {
        if (file_exists('obfuscation_config.php')) {
            $s_config_file = file_get_contents('obfuscation_config.php');
            $this->check_config_functions($s_config_file, 'get_salt');
            $this->check_config_functions($s_config_file, 'get_sst_start');
            $this->check_config_functions($s_config_file, 'get_sst_length');
            $this->check_config_functions($s_config_file, 'get_switch_array');
        } else {
            $this->create_config_file();
        }
        require_once 'obfuscation_config.php';
        $this->o_config = new obfuscation_config();
    }

    private function check_config_functions($s_config_file, $s_check) {
        if (strpos($s_config_file, '//' . strtoupper($s_check) . ' START') === FALSE ||
                strpos($s_config_file, 'public function ' . $s_check . '(){') === FALSE ||
                strpos($s_config_file, '//' . strtoupper($s_check) . ' END') === FALSE) {
            echo "<br>".$s_check."fucked up!<br>";
            $this->create_config_file();
            //LATER
            /*
              switch ($s_check) {
              case 'get_salt' :
              $this->create_salt();
              break;
              case 'get_sst_start' :
              $this->create_sst_start();
              break;
              case 'get_sst_length' :
              $this->create_sst_length();
              break;
              case 'get_switch_array_short' :
              $this->create_switch_array('short');
              break;
              case 'get_switch_array_long' :
              $this->create_switch_array('long');
              break;
              }
             * 
             */
        }
    }

    private function set_hash_method($s_method) {
        if ($s_method == NULL) {
            $this->check_config();
            $this->_s_hashing_method = $this->o_config->get_hashmethod();
        } else {
            $this->_s_hashing_method = $s_method;
        }
    }

    private function set_switch($a_switch) {
        if ($a_switch == NULL) {
            $this->check_config();
            $this->_a_switch = $this->o_config->get_switch_array();
        } else {
            $this->_a_switch = $a_switch;
        }
    }

    private function set_sst_start($i_start) {
        if ($i_start == NULL) {
            $this->check_config();
            $this->_i_sst_start = $this->o_config->get_sst_start();
        } else {
            $this->_i_sst_start = $i_start;
        }
    }

    private function set_sst_length($i_length) {
        if ($i_length == NULL) {
            $this->check_config();
            $this->_i_sst_lenght = $this->o_config->get_sst_length();
        } else {
            $this->_i_sst_lenght = $i_length;
        }
    }

    private function set_splitter($s_splitter) {
        if ($s_splitter == NULL) {
            $this->check_config();
            $this->_s_splitter = $this->o_config->get_splitter();
        } else {
            $this->_s_splitter = $s_splitter;
        }
    }

    private function set_salt($s_salt) {
        if ($s_salt == NULL) {
            $this->check_config();
            $this->_s_salt = $this->o_config->get_salt();
        } else {
            $this->_s_salt = $s_salt;
        }
    }

    private function create_config_file() {
        file_put_contents('obfuscation_config.php', '
<?php
#################################################
# obfuscation-library
# configuration file
#################################################  
# @author Nico Redick 
# @copyright Nico Redick
# @license MIT 
#################################################
# DESCRIBTION:
# Config file which stores the generated 
# values for the obfuscation-library class
# More Infos at => 
#################################################
# NOTICE:
# -All private Methods and Variables 
#  start with an underscore
# -All Variables start with an char and an 
#  underscore. The Char gives the primary type 
#  of the variable
#  eg: $b_checked => type boolean
#  b => boolean
#  o => object
#  i => integer
#  s => string
#  a => array
#  m => mixed
#################################################
# Licence-Part:
# http://opensource.org/licenses/MIT
# (c) 2015 Nico Redick
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
# 
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
# 
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
# THE SOFTWARE.
#################################################
# CONFIG CREATION DATE:
# ' . date('Y/m/d H:i:s') . '
#################################################

class obfuscation_config{

    //GET_SWITCH_ARRAY START
    public function get_switch_array(){
        $a_switch = array();
' . $this->create_switch_array(true) . '
        return $a_switch;
    }
    //GET_SWITCH_ARRAY END
    
    //GET_SALT START
    public function get_salt(){
        return "' . $this->create_salt() . '";
    }
    //GET_SALT END
    
    //GET_SST_START START
    public function get_sst_start(){
        return ' . $this->create_sst_start() . ';
    }
    //GET_SST_START END
    
    //GET_SST_LENGTH START
    public function get_sst_length(){
        return ' . $this->create_sst_length() . ';
    }
    //GET_SST_LENGTH END
    
    //GET_SPLITTER START
    public function get_splitter(){
        return "' . $this->create_splitter() . '";
    }
    //GET_SPLITTER END
    
    //GET_HASHMETHOD START
    public function get_hashmethod(){
        return "' . $this->create_hashmethod() . '";
    }
    //GET_HASHMETHOD END
}');
    }

    /**
     * (private)
     * generates the switch array
     * @return array Array Containing the switch-positions
     */
    private function create_switch_array($b_return_string) {
        if ($b_return_string) {
            $m_return = '';
        } else {
            $m_return = array();
        }
        $i_length = 16;
        for ($i = 0; $i < $i_length; $i++) {
            $i_pos_1 = mt_rand(0, $i_length - 1);
            $i_pos_2 = $i_pos_1;
            while ($i_pos_1 == $i_pos_2) {
                $i_pos_2 = mt_rand(0, $i_length - 1);
            }
            if ($b_return_string) {
                $m_return .= '        $a_switch[' . $i . '][' . $i_pos_1 . '] = ' . $i_pos_2 . ';' . PHP_EOL;
            } else {
                $m_return[$i][$i_pos_1] = $i_pos_2;
            }
        }
        return $m_return;
    }

    private function create_sst_start() {
        return mt_rand(0, 15);
    }

    private function create_sst_length() {
        return mt_rand(0, 15);
    }

    private function create_splitter() {
        return '|';
    }

    private function create_salt() {
        return substr(str_shuffle(MD5(microtime())), 0, 10);
    }

    private function create_hashmethod() {
        return 'md5';
    }

}

$obj = new obfuscation_library;
$array['test'] = 'hallo';
echo "start<br>";
echo "obfuscate<br>";
echo $test = $obj->obfuscate_url($array);
echo "<br>clear<br>";
print_r($obj->clear_url($test));
echo "<br>Ende";

