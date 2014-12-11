<?php

class m141201_104426_oe5154_3rd_group extends CDbMigration
{
	public $disorders = array('193570009','267718000','68478007','4855003','193387007','57190000','38101003','42059000','387742006','416300008');

	public function up()
	{
		$glaucoma_id = $this->dbConnection->createCommand()->select("id")->from("subspecialty")->where("name = :name",array(":name" => "Glaucoma"))->queryScalar();
		$display_order = $this->dbConnection->createCommand()->select("max(display_order)")->from("common_ophthalmic_disorder")->where("subspecialty_id = :si",array(":si" => $glaucoma_id))->queryScalar() + 1;

		foreach ($this->disorders as $snomed) {
			$this->insert('common_ophthalmic_disorder',array(
				'disorder_id' => $snomed,
				'subspecialty_id' => $glaucoma_id,
				'display_order' => $display_order++,
			));
		}
	}

	public function down()
	{
		$glaucoma_id = $this->dbConnection->createCommand()->select("id")->from("subspecialty")->where("name = :name",array(":name" => "Glaucoma"))->queryScalar();

		$this->delete('common_ophthalmic_disorder',"subspecialty_id = $glaucoma_id and disorder_id in (".implode(',',$this->disorders).")");
	}
}
