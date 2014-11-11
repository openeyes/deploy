<?php

class m141111_093443_glaucoma_common_disorders_secondary extends CDbMigration
{
	public function up()
	{
		$base_path = Yii::app()->basePath;
		$data_file = Yii::app()->basePath."/modules/deploy/data/oe-4745-v2.csv";

		shell_exec("$base_path/yiic secondarytoimport --reset_parent=true import ".escapeshellarg($data_file)." 1>&2");
	}

	public function down()
	{
	}
}
