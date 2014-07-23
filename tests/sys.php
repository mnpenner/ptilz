#!/usr/bin/env php
<?php

use Ptilz\Sys;

require __DIR__ . '/../vendor/autoload.php';

//Sys::passthru('ls', array_fill_keys(['l', 'A', 'h', 't', 'r', 'G', 'F'], ''));
Sys::passthru(['ls','-lAhtrGF']);