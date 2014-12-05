<?php

class m141205_104442_add_patient_ticketing_module extends CDbMigration
{
	public function up()
	{
		$file = dirname(__FILE__)."/../../../config/local/common.php";

		if (!file_exists($file)) {
			throw new Exception("Config file not found: $file");
		}

		$config = require $file;

		if (isset($config['modules']['PatientTicketing'])) {
			if ($config['modules']['PatientTicketing'] != array('class' => '\OEModule\PatientTicketing\PatientTicketingModule')) {
				throw new Exception("PatientTicketing module is already configured but wrongly, please fix.");
			}
		} else {
			$next = false;
			foreach ($config as $section => $stuff) {
				if ($next) {
					$next_section = $section;
					break;
				}
				if ($section == 'modules') {
					$next = true;
				}
			}

			if (!@$next_section) {
				throw new Exception("Unable to find config section after modules.");
			}

			$data = file_get_contents($file);

			if (!preg_match('/\'modules\'[\s\t]+=\>[\s\t]+array\(.*?\),[\s\t\r\n]+\''.$next_section.'\'/s',$data,$m)) {
				throw new Exception("Unable to find modules section in config file.");
			}

			$data = preg_replace('/\),[\s\t\r\n]+\''.$next_section.'\'/',"\t'PatientTicketing' => array(\n\t\t\t'class' => '\OEModule\PatientTicketing\PatientTicketingModule',\n\t\t),\n\t),\n\n\t'$next_section'",$data);

			file_put_contents($file, $data);
		}
	}

	public function down()
	{
	}
}
