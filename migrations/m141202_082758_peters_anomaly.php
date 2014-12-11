<?php

class m141202_082758_peters_anomaly extends CDbMigration
{
	public function up()
	{
		$oph_id = $this->dbConnection->createCommand()->select("id")->from("specialty")->where("name = :name",array(":name" => 'Ophthalmology'))->queryScalar();
		$glaucoma_id = $this->dbConnection->createCommand()->select("id")->from("subspecialty")->where("specialty_id = :si and name = :name",array(":si" => $oph_id, ":name" => "Glaucoma"))->queryScalar();

		$this->insert('disorder',array(
			'id' => 122,
			'fully_specified_name' => 'Peters anomaly (disorder)',
			'term' => 'Peters anomaly',
			'specialty_id' => $oph_id,
		));

		$this->dbConnection->createCommand("update secondaryto_common_oph_disorder set disorder_id = 122 where disorder_id = 204153003")->query();
	}

	public function down()
	{
	}
}
