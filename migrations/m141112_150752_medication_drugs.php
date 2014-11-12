<?php

class m141112_150752_medication_drugs extends CDbMigration
{
	public function up()
	{
		$mdi = new MedicationDrugImport;

		$mdi->import(Yii::app()->basePath."/modules/deploy/data/dmd/f_vtm2_3310714.xml", "vtm");
		$mdi->import(Yii::app()->basePath."/modules/deploy/data/dmd/f_vmp2_3310714.xml", "vmp");
	}

	public function down()
	{
	}
}
