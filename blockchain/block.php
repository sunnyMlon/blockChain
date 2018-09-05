<?php

/**
 * 简单的PHP区块链模型
 * @desc 何为区块链，独立功能小单元谓之区 ，环环相扣谓之链。
 * @author jie.zhang
 * @Email jie.zhang@fanli.com
 */

/**
 * 区块结构
 */
class block {
    private $index;
    private $timestamp;
    private $info;
    private $previous_hash;
    private $previous_hash_encrypt;
    private $hash;

    public function __construct($index, $timestamp, $info, $previous_hash_encrypt, $previous_hash) {
        $this->index = $index;
        $this->timestamp = $timestamp;
        $this->info = $info;
        $this->previous_hash = $previous_hash;
        $this->previous_hash_encrypt = $previous_hash_encrypt;
        $this->hash = $this->hash_block();
    }

    public function __get($name) {
        return $this->$name;
    }

    private function hash_block() {
        $str = $this->index . $this->timestamp . $this->info . $this->previous_hash_encrypt . $this->previous_hash;
        return hash("sha256", $str);
    }
}

/**
 * 创造区块链头
 * @desc  用于创建区块链的链头
 */
function create_block_header() {
    return new block(0, time(), "第一个区块", 0, 0);
}

/**
 * 挖矿，生成下一个区块
 * @desc 实际使用中，这里是一个很复杂的算法，此处做了简化，如果这里挖到前1位是数字的就算挖矿成功。
 */
function dig_new_blcok($last_block_obj) {
    $previous_hash_encrypt = $last_block_obj->hash . get_random();
    $index = $last_block_obj->index + 1;
    $timestamp = time();
    $info = 'Hello, My name is block_' . $index;
    $block_obj = new block($index, $timestamp, $info, $previous_hash_encrypt, $last_block_obj->hash);
    //前一位不是数字
    if (!is_numeric($block_obj->hash{0})) {
        return false;
    }
    //是数字，返回区块
    return $block_obj;
}

/**
 * 验证区块
 * @desc 实际应用中，这也是一个复杂的算法过程，这里做了简化，如果传入的是对象我们这里就算校验通过
 */
function verify($last_block_obj) {
    if (empty($last_block_obj) || !is_object($last_block_obj)) {
        return false;
    }
    return true;
}

/**
 * 生成随机字符串
 * @desc  加密字符串，由上一个区块的hash值和当前区块的随机字符组成, 用于给当前区块展示上一区块的唯一hash信息，但是需要反解密才能看到真实数据
 */
function get_random($len = 32) {
    $str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $key = "";
    for ($i = 0; $i < $len; $i++) {
        $key .= $str{mt_rand(0, $len)};//随机数
    }
    return $key;
}

header("Content-type:text/html;charset=utf-8");
//生成第一个区块
$blockchain = (array)create_block_header();
//模拟生成其他区块,我们直接循环生成。实际中，还需要跟踪互联网上多台机器上链的变化,像比特币会生成有工作量证明、破译时长等算法，达到条件才能生成区块
//我们的链是一个数组，实际生产中应该保存下来
$previous_block = $blockchain[0];
for ($i = 0; $i <= 10; $i++) {
    if (!($new_block = dig_new_blcok($previous_block)) || !verify($new_block)) {
        continue;
    }
    $blockchain[] = $new_block;
    $previous_block = $new_block;
    //告诉大家新增了一个区块
    echo "恭喜！新的区块已诞生.区块ID是 : {$new_block->index}<br/>";
    echo "新区块哈希值是 : {$new_block->hash}<br/>";
    echo "当前的区块结构，如下 ：<br/>";
    echo "<pre>";
    print_r($new_block);
    echo "<br/><hr/>";
}
