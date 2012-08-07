#!/usr/bin/env php
<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Console\Application;

use Piotr\Generator\Command\GeneratorCommand;
use Piotr\Generator\Command\Model\Config;

$config = new Config('Piotr', 'local');
$config->setPath('src');

$command = new GeneratorCommand();
$command->setConfig($config);

$console = new Application();
$console->add($command);

$console->run();