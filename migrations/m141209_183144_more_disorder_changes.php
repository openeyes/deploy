<?php

class m141209_183144_more_disorder_changes extends CDbMigration
{
	public function up()
	{
		$oph = $this->dbConnection->createCommand()->select("id")->from("specialty")->where("name = :n",array(":n" => "Ophthalmology"))->queryScalar();
		$glaucoma = $this->dbConnection->createCommand()->select("id")->from("subspecialty")->where("specialty_id = :s and name = :n",array(":s" => $oph, ":n" => "Glaucoma"))->queryScalar();

		$angle_closure = $this->dbConnection->createCommand()->select("id")->from("common_ophthalmic_disorder")->where("subspecialty_id = :g and disorder_id = :d",array(":g" => $glaucoma, ":d" => 122))->queryScalar();

		$other = $this->dbConnection->createCommand()->select("id")->from("finding")->where("name = 'Other'")->queryScalar();

		$this->insert('secondaryto_common_oph_disorder',array(
			'parent_id' => $angle_closure,
			'finding_id' => $other,
			'display_order' => 2,
		));

		$acute_angle_closure = $this->dbConnection->createCommand()->select("id")->from("common_ophthalmic_disorder")->where("subspecialty_id = :g and disorder_id = :d",array(":g" => $glaucoma, ":d" => 30041005))->queryScalar();

		$this->update('secondaryto_common_oph_disorder',array('display_order' => 3),"parent_id = $acute_angle_closure and finding_id = 5");
		$this->update('disorder',array('term' => 'Phacomorphic'),"id = 392300000");

		$this->insert('secondaryto_common_oph_disorder',array(
			'parent_id' => $acute_angle_closure,
			'disorder_id' => '392300000',
			'display_order' => 2,
			'letter_macro_text' => 'Secondary Acute Angle Closure with phacomorphic',
		));

		$childhood = $this->dbConnection->createCommand()->select("id")->from("common_ophthalmic_disorder")->where("subspecialty_id = :g and disorder_id = :d",array(":g" => $glaucoma, ":d" => 113))->queryScalar();

		$this->insert('secondaryto_common_oph_disorder',array(
			'parent_id' => $childhood,
			'finding_id' => 5,
			'display_order' => 14,
		));

		$childhood2 = $this->dbConnection->createCommand()->select("id")->from("common_ophthalmic_disorder")->where("subspecialty_id = :g and disorder_id = :d",array(":g" => $glaucoma, ":d" => 114))->queryScalar();

		$this->insert('secondaryto_common_oph_disorder',array(
			'parent_id' => $childhood2,
			'finding_id' => 5,
			'display_order' => 13,
		));

		$childhood3 = $this->dbConnection->createCommand()->select("id")->from("common_ophthalmic_disorder")->where("subspecialty_id = :g and disorder_id = :d",array(":g" => $glaucoma, ":d" => 120))->queryScalar();

		$this->insert('secondaryto_common_oph_disorder',array(
			'parent_id' => $childhood3,
			'finding_id' => 5,
			'display_order' => 6,
		));
	}

	public function down()
	{
		$oph = $this->dbConnection->createCommand()->select("id")->from("specialty")->where("name = :n",array(":n" => "Ophthalmology"))->queryScalar();
		$glaucoma = $this->dbConnection->createCommand()->select("id")->from("subspecialty")->where("specialty_id = :s and name = :n",array(":s" => $oph, ":n" => "Glaucoma"))->queryScalar();

		$angle_closure = $this->dbConnection->createCommand()->select("id")->from("common_ophthalmic_disorder")->where("subspecialty_id = :g and disorder_id = :d",array(":g" => $glaucoma, ":d" => 122))->queryScalar();

		$other = $this->dbConnection->createCommand()->select("id")->from("finding")->where("name = 'Other'")->queryScalar();

		$this->delete('secondaryto_common_oph_disorder',"parent_id = $angle_closure and finding_id = $other");

		$acute_angle_closure = $this->dbConnection->createCommand()->select("id")->from("common_ophthalmic_disorder")->where("subspecialty_id = :g and disorder_id = :d",array(":g" => $glaucoma, ":d" => 30041005))->queryScalar();

		$this->update('secondaryto_common_oph_disorder',array('display_order' => 2),"parent_id = $acute_angle_closure and finding_id = 5");
		$this->update('disorder',array('term' => 'Phacomorphic glaucoma'),"id = 392300000");

		$this->delete('secondaryto_common_oph_disorder',"parent_id = $acute_angle_closure and disorder_id = 392300000");

		$childhood = $this->dbConnection->createCommand()->select("id")->from("common_ophthalmic_disorder")->where("subspecialty_id = :g and disorder_id = :d",array(":g" => $glaucoma, ":d" => 113))->queryScalar();

		$this->delete('secondaryto_common_oph_disorder',"parent_id = $childhood and finding_id = 5");

		$childhood2 = $this->dbConnection->createCommand()->select("id")->from("common_ophthalmic_disorder")->where("subspecialty_id = :g and disorder_id = :d",array(":g" => $glaucoma, ":d" => 114))->queryScalar();

		$this->delete('secondaryto_common_oph_disorder',"parent_id = $childhood2 and finding_id = 5");

		$childhood3 = $this->dbConnection->createCommand()->select("id")->from("common_ophthalmic_disorder")->where("subspecialty_id = :g and disorder_id = :d",array(":g" => $glaucoma, ":d" => 120))->queryScalar();

		$this->delete('secondaryto_common_oph_disorder',"parent_id = $childhood3 and finding_id = 5");
	}
}
