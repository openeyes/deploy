<?php

class m141112_151830_further_findings extends CDbMigration
{
	public function up()
	{
		$ids = array();

		foreach (array('No evidence of glaucoma','Glaucoma suspect','Primary angle closure suspect','Neovascularisation of the angle') as $i => $finding) {
			$display_order = $i+1;

			if (!$ff = Yii::app()->db->createCommand()->select("*")->from("ophciexamination_further_findings")->where("name = :a",array(":a" => $finding))->queryRow()) {
				Yii::app()->db->createCommand("insert into ophciexamination_further_findings (name,display_order,active) values ('$finding',$display_order,1);")->query();

				$ids[] = Yii::app()->db->createCommand("select max(id) from ophciexamination_further_findings")->queryScalar();
			} else {
				Yii::app()->db->createCommand("update ophciexamination_further_findings set display_order = $display_order, active = 1 where id = {$ff['id']}")->query();

				$ids[] = $ff['id'];
			}
		}

		Yii::app()->db->createCommand("update ophciexamination_further_findings set active = 0 where id not in (".implode(',',$ids).")")->query();
	}

	public function down()
	{
	}
}
