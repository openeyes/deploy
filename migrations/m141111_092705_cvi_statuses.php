<?php

class m141111_092705_cvi_statuses extends CDbMigration
{
	public function up()
	{
		$ids = array();

		foreach (array(
				1 => 'Not eligible',
				2 => 'Eligible but not offered',
				3 => 'Eligible but declined',
				4 => 'Sight Impaired',
				5 => 'Severely Sight Impaired',
				6 => 'Unknown',
			) as $display_order => $name) {

			if (!$cs = $this->dbConnection->createCommand()->select("*")->from("patient_oph_info_cvi_status")->where("name = :a",array(":a" => $name))->queryRow()) {
				$this->dbConnection->createCommand("insert into patient_oph_info_cvi_status (name,display_order,active) values ('$name',$display_order,1);")->query();

				$ids[] = $this->dbConnection->createCommand("select max(id) from patient_oph_info_cvi_status")->queryScalar();
			} else {
				$this->dbConnection->createCommand("update patient_oph_info_cvi_status set display_order = $display_order, active = 1 where id = {$cs['id']}")->query();

				$ids[] = $cs['id'];
			}
		}

		$this->dbConnection->createCommand("update patient_oph_info_cvi_status set active = 0 where id not in (".implode(',',$ids).")")->query();
	}

	public function down()
	{
	}
}
