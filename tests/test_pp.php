<?php
require __DIR__ . '/../vendor/autoload.php';

$pp = new \Ptilz\PrettyPrinter();


$pp->writeLine('Hello <fg:green>World</fg>!');
$pp->indent();
$pp->write("How are you\n<b>today</b>");
$pp->indent();
$pp->write("?\n");
$pp->writeLine("I'm great thanks!");
$pp->dedent();
$pp->writeLine("That's <i>fabuluous</i>!");
$pp->indent(2);
$pp->writeLine("Woaaahh\r\nnelly!");
$pp->dedent(2);
$pp->writeLine("Okay, back to <bg:red>sanity</bg:red>");
$pp->setIndentLevel();
$pp->writeLine("The end.");
