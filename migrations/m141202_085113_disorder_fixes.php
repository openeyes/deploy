<?php

class m141202_085113_disorder_fixes extends CDbMigration
{
	public function up()
	{
		$oph_id = $this->dbConnection->createCommand()->select("id")->from("specialty")->where("name = :name",array(":name" => 'Ophthalmology'))->queryScalar();
		$glaucoma_id = $this->dbConnection->createCommand()->select("id")->from("subspecialty")->where("specialty_id = :si and name = :name",array(":si" => $oph_id, ":name" => "Glaucoma"))->queryScalar();

		$this->dbConnection->createCommand("update secondaryto_common_oph_disorder set disorder_id = 204153003 where disorder_id = 122")->query();

		$this->delete('disorder',"id = 122");

		foreach (array(
				'204153003' => 'Peters anomaly',
				'400965007' => 'Congenital ectropion uveae',
				'95714006' => 'Congenital iris hypoplasia',
				'69278003' => 'Aniridia',
				'414929001' => 'Oculodermal melanocytosis (Nevus of Ota)',
				'29504002' => 'Posterior polymorphous dystrophy',
				'422311004' => 'Ectopia lentis simple (no systemic associations)',
				'26825009' => 'Cutis Marmorata Telangiectasia Congenita',
				'36653000' => 'Congenital Rubella',
			) as $snomed_code => $term) {
			$this->update('disorder',array('term' => $term),"id = $snomed_code");
		}

		$this->insert('common_ophthalmic_disorder',array(
			'disorder_id' => '41446000',
			'subspecialty_id' => $glaucoma_id,
			'display_order' => 24,
		));

		$this->update('common_ophthalmic_disorder',array('display_order' => 25),"subspecialty_id = 7 and disorder_id = 416300008");
	}

	public function down()
	{
	}
}
