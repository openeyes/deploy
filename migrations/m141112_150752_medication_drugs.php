<?php

class m141112_150752_medication_drugs extends CDbMigration
{
	public function up()
	{
		$base_path = Yii::app()->basePath;
		$data_file = Yii::app()->basePath."/modules/deploy/data/dmd/f_vtm2_3310714.xml";

		shell_exec("$base_path/yiic medicationdrugimport --type=vtm import ".escapeshellarg($data_file)." 1>&2");

		$data_file = Yii::app()->basePath."/modules/deploy/data/dmd/f_vmp2_3310714.xml";

		shell_exec("$base_path/yiic medicationdrugimport --type=vmp import ".escapeshellarg($data_file)." 1>&2");
	}

	public function down()
	{
	}
}
