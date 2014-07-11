<?php
namespace Ptilz;

class Env {
    public static function isCli() {
        return php_sapi_name() === 'cli';
    }
}