<?php

  require_once(dirname(__FILE__) . '/../vendor/autoload.php');
  require_once(dirname(__FILE__) . '/TestDefinition.php');
  

  use Symfony\Component\Dotenv\Dotenv;

  $env = new Dotenv();
  $env->loadEnv(__DIR__ . '/.env');
