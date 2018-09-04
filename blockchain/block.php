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
    private $random_str;
    private $hash;

    public function __construct($index, $timestamp, $info, $random_str, $previous_hash) {
        $this->index = $index;
        $this->timestamp = $timestamp;
        $this->info = $info;
        $this->previous_hash = $previous_hash;
        $this->random_str = $random_str;
        $this->hash = $this->hash_block();
    }

    public function __get($name) {
        return $this->$name;
    }

    private function hash_block() {
        $str = $this->index . $this->timestamp . $this->info . $this->random_str . $this->previous_hash;
        return hash("sha256", $str);
    }
}

/**
 * 创造区块链头
 * @return block
 */
function create_block_header() {
    return new block(0, time(), "第一个区块", 0, 0);
}

/**
 * 挖矿，生成下一个区块
 * 这应该是一个很复杂的算法，但为了简单，我们这里挖到前1位是数字就挖矿成功。
 */
function dig_new_blcok($last_block_obj) {
    $random_str = $last_block_obj->hash . get_random();
    $index = $last_block_obj->index + 1;
    $timestamp = time();
    $info = 'Hello, My name is block_' . $index;
    $block_obj = new block($index, $timestamp, $info, $random_str, $last_block_obj->hash);
    //前一位不是数字
    if (!is_numeric($block_obj->hash{0})) {
        return false;
    }
    //是数字，返回区块
    return $block_obj;
}

/**
 * 验证区块
 * @desc 这也是一个复杂的过程，为了简单，我们这里直接返回正确
 */
function verify($last_block_obj) {
    return true;
}

/**
 * 生成随机字符串
 */
function get_random($len = 32) {
    $str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $key = "";
    for ($i = 0; $i < $len; $i++) {
        $key .= $str{mt_rand(0, 32)};//随机数
    }
    return $key;
}

header("Content-type:text/html;charset=utf-8");
//生成第一个区块
$blockchain = (array)create_block_header();
//模拟生成其他区块,我们直接循环生成。实际中，还需要跟踪互联网上多台机器上链的变化,像比特币会有工作量证明等算法，达到条件了才生成区块等
//我们的链是一个数组，实际生产中应该保存下来
$previous_block = $blockchain[0];
for ($i = 0; $i <= 10; $i++) {
    if (!($new_block = dig_new_blcok($previous_block))) {
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
