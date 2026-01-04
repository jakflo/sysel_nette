<?php

declare(strict_types=1);

namespace App;

use Nette;
use Nette\Bootstrap\Configurator;


class Bootstrap
{
	private Configurator $configurator;
	private string $rootDir;


	public function __construct(private BootstrapType $mode = BootstrapType::normal)
	{
		$this->rootDir = dirname(__DIR__);
		$this->configurator = new Configurator;
		$this->setTempDir();
	}

        /**
         * @param BootstrapType $mode - urci, jake neony maji byt nacteny
         * @return Nette\DI\Container
         */
	public function bootWebApplication(): Nette\DI\Container
	{
		$this->initializeEnvironment();
		$this->setupContainer();
		return $this->configurator->createContainer();
	}


	public function initializeEnvironment(): void
	{
		//$this->configurator->setDebugMode('secret@23.75.345.200'); // enable for your remote IP
		$this->configurator->enableTracy($this->rootDir . '/log');

		$this->configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();
	}
        
        private function setTempDir()
        {
            if ($this->mode === BootstrapType::test) {
                $this->configurator->setTempDirectory($this->rootDir . '/tempForTests');
            } else {
                $this->configurator->setTempDirectory($this->rootDir . '/temp');
            }
        }

	private function setupContainer(): void
	{
		$configDir = $this->rootDir . '/config';
		$this->configurator->addConfig($configDir . '/common.neon');
		$this->configurator->addConfig($configDir . '/services.neon');
                
                if ($this->mode === BootstrapType::test) {
                    $this->configurator->addConfig($configDir . '/test.neon');
                } elseif ($this->mode === BootstrapType::console) {
                    $this->configurator->addConfig($configDir . '/console.neon');
                }
	}
}
