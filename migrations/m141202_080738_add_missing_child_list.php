<?php

class m141202_080738_add_missing_child_list extends CDbMigration
{
	public function up()
	{
		$oph_id = $this->dbConnection->createCommand()->select("id")->from("specialty")->where("name = :name",array(":name" => 'Ophthalmology'))->queryScalar();
		$glaucoma_id = $this->dbConnection->createCommand()->select("id")->from("subspecialty")->where("specialty_id = :si and name = :name",array(":si" => $oph_id, ":name" => "Glaucoma"))->queryScalar();

		$this->insert('disorder',array(
			'id' => 120,
			'fully_specified_name' => 'Childhood Glaucoma Associated with Acquired Condition (disorder)',
			'term' => 'Childhood Glaucoma Associated with Acquired Condition',
			'specialty_id' => $oph_id,
			'active' => 1
		));

		$this->insert('disorder',array(
			'id' => 121,
			'fully_specified_name' => 'Juvenile Open Angle Glaucoma (disorder)',
			'term' => 'Juvenile Open Angle Glaucoma',
			'specialty_id' => $oph_id,
			'active' => 1
		));

		$this->insert('common_ophthalmic_disorder',array(
			'subspecialty_id' => $glaucoma_id,
			'disorder_id' => 121,
			'group_id' => 2,
		));

		$this->dbConnection->createCommand("update common_ophthalmic_disorder set disorder_id = 120 where subspecialty_id = $glaucoma_id and display_order = 11")->query();
		$this->dbConnection->createCommand("update common_ophthalmic_disorder set display_order = display_order + 1 where subspecialty_id = $glaucoma_id and display_order >= 9")->query();
		$this->dbConnection->createCommand("update common_ophthalmic_disorder set display_order = 9 where subspecialty_id = $glaucoma_id and group_id = 2 and disorder_id = 121")->query();
	}

	public function down()
	{
	}
}
