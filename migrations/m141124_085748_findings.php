<?php

class m141124_085748_findings extends CDbMigration
{
	public function up()
	{
		$ids = array();

		foreach (array('No evidence of glaucoma','Glaucoma suspect','Primary angle closure suspect','Neovascularisation of the angle','Other') as $i => $finding) {
			$display_order = $i+1;

			$requires_description = ($finding == 'Other') ? 1 : 0;

			if (!$ff = $this->dbConnection->createCommand()->select("*")->from("finding")->where("name = :a",array(":a" => $finding))->queryRow()) {
				$this->dbConnection->createCommand("insert into finding (name,display_order,active,requires_description) values ('$finding',$display_order,1,$requires_description);")->query();

				$ids[] = $this->dbConnection->createCommand("select max(id) from finding")->queryScalar();
			} else {
				$this->dbConnection->createCommand("update finding set display_order = $display_order, active = 1, requires_description = $requires_description where id = {$ff['id']}")->query();

				$ids[] = $ff['id'];
			}
		}

		$this->dbConnection->createCommand("update finding set active = 0 where id not in (".implode(',',$ids).")")->query();
	}

	public function down()
	{
	}
}
