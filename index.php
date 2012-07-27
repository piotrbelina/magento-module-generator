#!/usr/bin/env php
<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Console\Application;

use Piotr\Generator\Command\GeneratorCommand;
use Piotr\Generator\Command\Model\Config;

$config = new Config('Piotr', 'local');

$command = new GeneratorCommand();
$command->setConfig($config);
$command->setPath('src/');

$console = new Application();
$console->add($command);

$console->run();