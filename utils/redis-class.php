<?php
    class redisFunc{
        private $SERVER = "localhost";
        private $PORT = "6379";
        private $PASSWORD = "";
        public function __construct($SERVER,$PORT,$PASSWORD){
            $this->SERVER = $SERVER;
            $this->PORT = $PORT;
            $this->PASSWORD = $PASSWORD;
        }
        public function ping(){
            $redis = new Redis();
            try {
                $redis->connect($this->SERVER,$this->PORT);
                if($this->PASSWORD != ""){
                    $redis->auth($this->PASSWORD);
                }
            } catch(Exception $e) {
                return false;
            }
            if($redis->ping()){
                return true;
            }else{
                return false;
            }
        }
        public function check($key){
            $redis = new Redis();
            try {
                $redis->connect($this->SERVER,$this->PORT);
                if($this->PASSWORD != ""){
                    $redis->auth($this->PASSWORD);
                }
            } catch(Exception $e) {
                return false;
            }
            if($redis->exists($key)){
                return true;
            }else{
                return false;
            }
        }
        public function add($key,$value,$expire){
            $redis = new Redis();
            $time = $expire;
            try {
                $redis->connect($this->SERVER,$this->PORT);
                if($this->PASSWORD != ""){
                    $redis->auth($this->PASSWORD);
                }
                $redis->set($key,$value);
                $redis->expireAt($key,$time);
            } catch(Exception $e) {
                return false;
            }
            return true;
        }
        public function get($key){
            $redis = new Redis();
            try {
                $redis->connect($this->SERVER,$this->PORT);
                if($this->PASSWORD != ""){
                    $redis->auth($this->PASSWORD);
                }
                $value = $redis->get($key);
            } catch(Exception $e) {
                return null;
            }
            return $value;
        }
        public function del($key){
            $redis = new Redis();
            try {
                $redis->connect($this->SERVER,$this->PORT);
                if($this->PASSWORD != ""){
                    $redis->auth($this->PASSWORD);
                }
                $redis->del($key);
            } catch(Exception $e) {
                return false;
            }
            return true;
        }
        public function ttl($key){
            $redis = new Redis();
            try {
                $redis->connect($this->SERVER,$this->PORT);
                if($this->PASSWORD != ""){
                    $redis->auth($this->PASSWORD);
                }
                $ttl = $redis->ttl($key);
            } catch(Exception $e) {
                return -3;
            }
            return $ttl;
        }
    };
?>
