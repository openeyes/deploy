<?php

class m141202_112229_parentless_glaucoma_list extends CDbMigration
{
	public function up()
	{
		// define the list of disorders to be displayed for Glaucoma when no selected made in first list:
		// 1. create null entry in common opthalmic disorders
		$glaucoma = $this->dbConnection->createCommand("SELECT id FROM subspecialty WHERE ref_spec = 'GL'")->queryRow();
		$parent = $this->dbConnection->createCommand("SELECT id FROM common_ophthalmic_disorder WHERE disorder_id is null and finding_id is null and subspecialty_id = " . $glaucoma['id'])->queryRow();
		if (!$parent) {
			$this->insert('common_ophthalmic_disorder', array('subspecialty_id' => $glaucoma['id'], 'display_order' => 0));
			$parent_id = $this->dbConnection->getLastInsertID();

		}
		else {
			$parent_id = $parent['id'];
		}

		foreach (array('44219007', '392133001', '24010005', '128473001', '417746004') as $i => $snomed) {
			$this->insert('secondaryto_common_oph_disorder', array(
					'parent_id' => $parent_id,
					'disorder_id' => $snomed,
					'display_order' => $i+1
				));
		}
	}

	public function down()
	{
		$glaucoma = $this->dbConnection->createCommand("SELECT id FROM subspecialty WHERE ref_spec = 'GL'")->queryRow();
		$parent = $this->dbConnection->createCommand("SELECT id FROM common_ophthalmic_disorder WHERE disorder_id is null and finding_id is null and subspecialty_id = " . $glaucoma['id'])->queryRow();
		$parent_id = $parent['id'];
		$this->delete('secondaryto_common_oph_disorder', 'parent_id = ? ', array($parent_id));
		$this->delete('common_ophthalmic_disorder', 'id = ?', array($parent_id));
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}