#!/usr/bin/env php
<?php

/**
 * Iterates through <source> directory looking for jpeg files,
 * then resizes them according to --width=x (default=1920) where
 * height is calculated and saves into <destination> directory.
 * 
 * In short: Copy photo gallery structure with resize.
 * 
 * @author Nikolas Tsiongas <ntsiongas@gmail.com>
 */
use Echo511\GalleryMinificator\Command\MinifiCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

require_once __DIR__ . '/../vendor/autoload.php';

$console = new Application('Gallery minificator', '1.0.0');
$console->add(new MinifiCommand());
$console->run(new ArgvInput(), new ConsoleOutput());
