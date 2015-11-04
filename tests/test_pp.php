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

$pp->writeLine();
$pp->writeLine("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris mattis nisl et consequat vestibulum. Sed semper augue ipsum, eget ullamcorper eros convallis vel. Curabitur sed luctus est, at tristique augue. Aenean fermentum, ipsum ut molestie porta, diam tortor bibendum nunc, vitae commodo urna neque id lacus. Curabitur sapien tortor, tempus in vulputate sit amet, porttitor quis est. Vestibulum suscipit egestas leo non ullamcorper. Nunc dictum magna sit amet erat tristique cursus. Nam auctor ac orci ut efficitur. Fusce sit amet sapien eget massa placerat porta non ut orci.\n");
$pp->indent();
$pp->writeLine("Proin quis auctor sem. Vivamus et magna ut neque vehicula euismod. Phasellus cursus dui in mi posuere, ac ultricies purus consequat. Quisque at quam nec sapien sagittis rhoncus at quis sapien. Proin vel odio ornare, placerat tellus sed, interdum elit. Suspendisse eget imperdiet sem. Mauris aliquam lorem vitae tellus mattis tempor. Donec aliquet mi quis euismod rhoncus. In at ultricies arcu, quis consectetur mauris. Nullam dignissim augue id arcu euismod, vel dignissim ligula faucibus.\n");
$pp->setIndentLevel(10);
$pp->writeLine("Fusce ultricies ligula vitae pharetra imperdiet. Fusce molestie ipsum a eros ultrices suscipit. Duis placerat tincidunt tempus. Phasellus a tristique neque, vitae condimentum felis. Sed sagittis nibh in nunc tempor pharetra. Donec nec elementum ligula. Suspendisse in efficitur sapien, at gravida ipsum. Nam laoreet vehicula dolor id viverra. Donec viverra, mi vehicula pulvinar fringilla, diam urna convallis eros, at hendrerit massa turpis vitae neque. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aenean quis nisi lectus. Fusce faucibus tempus efficitur.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris vestibulum libero sed nibh consequat, in tempor lacus congue. Sed urna nunc, commodo nec molestie vitae, laoreet nec enim. Morbi tempus, metus vel euismod varius, risus magna posuere magna, et dapibus diam eros eget nisi. Nulla commodo feugiat diam sed porta. Aenean leo nisi, sodales eu hendrerit vel, tempus sit amet dolor. Phasellus dignissim magna in diam lacinia dictum. Curabitur quis leo vel mi pretium mattis. Cras hendrerit varius pulvinar. Integer lacus augue, dapibus et elit in, venenatis lobortis sapien. In hac habitasse platea dictumst. Mauris cursus consequat lacus, vitae interdum urna elementum eget. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Etiam ultricies dui nec vehicula aliquam. Ut vestibulum commodo sodales. In hac habitasse platea dictumst.\n");