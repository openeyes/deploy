<?php

$config = require(dirname(__FILE__).'/../../../../config/core/console.php');
$config['commandMap']['migrate']['class'] = 'MigrateCommand';
$config['commandMap']['migrate']['migrationTable'] = 'tbl_migration_deploy';

return $config;
