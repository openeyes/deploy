<?php

class m141209_095850_paediatric_admission_letter_telephone_numbers extends CDbMigration
{
	public function up()
	{
		$child_type_id = $this->dbConnection->createCommand()->select("id")->from("ophtroperationbooking_admission_letter_warning_rule_type")->where("name = :n",array(":n" => "Child health advice"))->queryScalar();

		$this->insert('ophtroperationbooking_admission_letter_warning_rule',array(
				'rule_type_id' => $child_type_id,
				'parent_rule_id' => null,
				'rule_order' => 0,
				'is_child' => 1,
				'show_warning' => 1,
				'warning_text' => 'please telephone 020 7566 2595 and ask to speak to a nurse for advice',
		));

		$parent_id = $this->dbConnection->createCommand()->select("max(id)")->from("ophtroperationbooking_admission_letter_warning_rule")->queryScalar();

		$institution_id = $this->dbConnection->createCommand()->select("id")->from("institution")->where("remote_id = :ri",array(":ri" => "RP6"))->queryScalar();
		$site_id = $this->dbConnection->createCommand()->select("id")->from("site")->where("institution_id = :i and name = :n",array(":i" => $institution_id, ":n" => "St George's Hospital"))->queryScalar();

		$this->insert('ophtroperationbooking_admission_letter_warning_rule',array(
				'rule_type_id' => $child_type_id,
				'parent_rule_id' => $parent_id,
				'site_id' => $site_id,
				'rule_order' => 0,
				'is_child' => 1,
				'show_warning' => 1,
				'warning_text' => 'please telephone 020 7566 2595 and ask to speak to a nurse for advice',
		));
	}

	public function down()
	{
		$child_type_id = $this->dbConnection->createCommand()->select("id")->from("ophtroperationbooking_admission_letter_warning_rule_type")->where("name = :n",array(":n" => "Child health advice"))->queryScalar();

		$this->delete('ophtroperationbooking_admission_letter_warning_rule',"rule_type_id = $child_type_id and parent_rule_id is not null");
		$this->delete('ophtroperationbooking_admission_letter_warning_rule',"rule_type_id = $child_type_id");
	}
}
