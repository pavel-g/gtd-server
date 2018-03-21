<?php

namespace Gtd\Cli;

class Generate
{
	
	public function install()
	{
		echo "Generate...";
		$this->generate();
		echo " DONE" . PHP_EOL;
	}
	
	public function generate()
	{
		shell_exec("rm -rf generated-classes/Gtd/Propel/Base");
		shell_exec("rm -rf generated-classes/Gtd/Propel/Map");
		shell_exec("rm -rf generated-conf");
		shell_exec("rm -rf generated-sql");
		
		shell_exec("vendor/bin/propel sql:build");
		shell_exec("vendor/bin/propel model:build");
		shell_exec("vendor/bin/propel config:convert");
		shell_exec("vendor/bin/propel sql:insert");
	}
	
}