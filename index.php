#!/usr/bin/env php
<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Application;

use Piotr\Generator\Command\GeneratorCommand;

$console = new Application();

$console->add(new GeneratorCommand());

$console->run();