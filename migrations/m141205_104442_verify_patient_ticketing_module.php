<?php

class m141205_104442_verify_patient_ticketing_module extends CDbMigration
{
	public function up()
	{
		$file = dirname(__FILE__)."/../../../config/local/common.php";

		if (!file_exists($file)) {
			throw new Exception("Config file not found: $file");
		}

		$config = require $file;

		if (!isset($config['modules']['PatientTicketing'])) {
			throw new Exception("PatientTicketing module is not in the local config file.");
		}

		if (!file_exists(dirname(__FILE__)."/../../PatientTicketing")) {
			throw new Exception("PatientTicketing module not found in modules/ directory.");
		}
	}

	public function down()
	{
	}
}
