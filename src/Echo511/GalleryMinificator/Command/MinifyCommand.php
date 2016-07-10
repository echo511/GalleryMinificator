<?php

namespace Echo511\GalleryMinificator\Command;

use Echo511\GalleryMinificator\Path;
use Echo511\GalleryMinificator\StructureMap;
use Nette\Utils\FileSystem;
use Nette\Utils\Image;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Nikolas Tsiongas <ntsiongas@gmail.com>
 */
class MinifiCommand extends Command
{

	protected function configure()
	{
		$this->setName('minify')
			->setDescription('Minify gallery. By default runs in test mode. For actual run please add --write option!')
			->addArgument('source', InputArgument::REQUIRED)
			->addArgument('destination', InputArgument::REQUIRED)
			->addOption('width', null, InputOption::VALUE_REQUIRED, 'Width of image. Height is calculated.', '1920')
			->addOption('write', null, null, 'Write minified gallery.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$map = new StructureMap($input->getArgument('source'), $input->getArgument('destination'), getcwd());

		if ($input->getOption('write')) {
			$progressBar = new ProgressBar($output, $map->getCount());
			$progressBar->setBarCharacter('<fg=magenta>=</>');

			$passed = [];
			$rewritten = [];
			foreach ($map as $source => $destination) {
				if (file_exists($destination)) {
					$rewritten[] = $destination;
				} else {
					$passed[] = $destination;
				}

				FileSystem::createDir(Path::upperDir($destination));
				$this->resize($source, $destination, $input->getOption('width'));

				if ($input->getOption('verbose')) {
					$output->writeln('');
					$output->writeln("<fg=blue>$source</> -> <fg=yellow>$destination</>");
				}

				$progressBar->advance();
			}
			$progressBar->finish();

			$output->writeln('');
			foreach ($rewritten as $destination) {
				$output->writeln('<fg=red>FILE REWRITTEN:</> ' . $destination);
			}

			$output->writeln('');
			$output->writeln('<fg=red>Rewritten: ' . count($rewritten) . '</>, <fg=green>Passed: ' . count($passed) . '</> from total: ' . $map->getCount());
		} else {
			$passed = [];
			$exists = [];
			foreach ($map as $source => $destination) {
				if (file_exists($destination)) {
					$exists[] = $destination;
				} else {
					$passed[] = $destination;
				}

				if ($input->getOption('verbose')) {
					$output->writeln("<fg=blue>$source</> -> <fg=yellow>$destination</>");
				}
			}

			if ($input->getOption('verbose')) {
				$output->writeln('');
			}

			foreach ($exists as $destination) {
				$output->writeln('<fg=red>FILE ALREADY EXISTS:</> ' . $destination);
			}

			// nice formatting
			if (!empty($exists)) {
				$output->writeln('');
			}

			$output->writeln('<fg=red>Already exists: ' . count($exists) . '</>, <fg=green>Passed: ' . count($passed) . '</> from total: ' . $map->getCount());
		}
		$output->writeln('');
	}

	private function resize($source, $destination, $width)
	{
		Image::fromFile($source)
			->resize($width, null)
			->save($destination, 80, Image::JPEG);
	}

}
