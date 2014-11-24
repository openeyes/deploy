<?php

class m141124_082934_other_further_finding extends CDbMigration
{
	public function up()
	{
		$this->insert('ophciexamination_further_findings',array('name' => 'Other', 'display_order' => 5,'active' => 1,'requires_description' => 1));
	}

	public function down()
	{
		$this->delete('ophciexamination_further_findings',"name = 'Other'");
	}
}
