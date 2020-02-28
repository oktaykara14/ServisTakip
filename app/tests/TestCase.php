<?php

/** @noinspection PhpUndefinedClassInspection */

use Symfony\Component\HttpKernel\HttpKernelInterface;

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	/**
	 * Creates the application.
	 *
	 * @return HttpKernelInterface
	 */
	public function createApplication()
	{
		//$unitTesting = true;

		//$testEnvironment = 'testing';

		return require __DIR__.'/../../bootstrap/start.php';
	}

}
