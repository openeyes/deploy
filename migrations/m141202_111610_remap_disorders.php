<?php

class m141202_111610_remap_disorders extends CDbMigration
{
	public function up()
	{
		$oph_id = $this->dbConnection->createCommand()->select("id")->from("specialty")->where("name = :name",array(":name" => 'Ophthalmology'))->queryScalar();
		$glaucoma_id = $this->dbConnection->createCommand()->select("id")->from("subspecialty")->where("specialty_id = :si and name = :name",array(":si" => $oph_id, ":name" => "Glaucoma"))->queryScalar();

		foreach (array(
			'37155002' => 'Uveitis',
			'1654001' => 'Steroid induced',
			'68241007' => 'Trauma',
			'27735002' => 'Tumors',
			'415297005' => 'Retinopathy of Prematurity',
			'232090003' => 'Post surgery other than cataract surgery',
		) as $snomed => $term) {
			$this->update('disorder',array('term' => $term),"id = $snomed");
		}
	}

	public function down()
	{
	}
}
