<?php

class m141208_122634_disorder_wrangling extends CDbMigration
{
	public function up()
	{
		$glaucoma = $this->dbConnection->createCommand()->select("id")->from("subspecialty")->where("name = :n",array(":n" => "Glaucoma"))->queryScalar();

		$disorders = array();

		foreach (array(
				'Pseudoexfoliation glaucoma' => 'Pseudoexfoliation',
				'Uveitic glaucoma' => 'Uveitis',
				'Aphakic glaucoma' => 'Aphakia',
				'Neovascularisation of the Angle' => 'Neovascularisation of Angle',
				'Neovascular glaucoma' => 'Neovascularisation of Angle',
				'Neovascular glaucoma' => 'Neovascularisation of Angle',
				'Axenfeld anomaly' => 'Axenfeld Rieger anomaly',
				'Congenital ectropion uveae' => 'Congenital ectropion',
				'Congenital Rubella' => 'Rubella',
			) as $from => $to) {
			if ($disorder = $this->dbConnection->createCommand()->select("*")->from("disorder")->where("term = :t",array(":t" => $from))->queryRow()) {
				$this->update('disorder',array('term' => $to),"id = {$disorder['id']}");
				$disorders[$to] = $disorder;
			} elseif ($finding = $this->dbConnection->createCommand()->select("*")->from("finding")->where("name = :n",array(":n" => $from))->queryRow()) {
				$this->update('finding',array('name' => $to),"id = {$finding['id']}");
			} else {
				throw new Exception("Disorder/finding not found: $from");
			}
		}

		$oph = $this->dbConnection->createCommand()->select("id")->from("specialty")->where("name = :n",array(":n" => "Ophthalmology"))->queryScalar();

		$this->insert('disorder',array(
				'id' => 122,
				'fully_specified_name' => 'Angle closure (disorder)',
				'term' => 'Angle closure',
				'specialty_id' => $oph,
				'active' => 1,
		));

		$this->insert('disorder',array(
				'id' => 123,
				'fully_specified_name' => 'Primary Angle closure (disorder)',
				'term' => 'Primary angle closure',
				'specialty_id' => $oph,
				'active' => 1,
		));

		$this->execute("update common_ophthalmic_disorder set display_order = display_order + 1 where subspecialty_id = $glaucoma and display_order >= 6");

		$this->insert('common_ophthalmic_disorder',array(
				'disorder_id' => 122,
				'subspecialty_id' => $glaucoma,
				'display_order' => 6,
				'group_id' => 1,
				'alternate_disorder_id' => 123,
				'alternate_disorder_label' => 'Primary',
		));

		$id = $this->dbConnection->createCommand()->select("max(id)")->from("common_ophthalmic_disorder")->queryScalar();

		$this->insert('secondaryto_common_oph_disorder',array(
				'parent_id' => $id,
				'disorder_id' => $disorders['Uveitis']['id'],
				'display_order' => 0,
		));

		$this->insert('secondaryto_common_oph_disorder',array(
				'parent_id' => $id,
				'disorder_id' => $disorders['Neovascularisation of Angle']['id'],
				'display_order' => 1,
		));
	}

	public function down()
	{
		$glaucoma = $this->dbConnection->createCommand()->select("id")->from("subspecialty")->where("name = :n",array(":n" => "Glaucoma"))->queryScalar();

		$cod = $this->dbConnection->createCommand()->select("id")->from("common_ophthalmic_disorder")->where("subspecialty_id = :s and disorder_id = :d",array(":s"=> $glaucoma, ":d" => 122))->queryScalar();

		$this->delete('secondaryto_common_oph_disorder',"parent_id = $cod");
		$this->delete('common_ophthalmic_disorder',"id = $cod");

		$this->execute("update common_ophthalmic_disorder set display_order = display_order - 1 where subspecialty_id = $glaucoma and display_order >= 7");

		$this->delete('disorder','id in (122,123)');

		foreach (array(
				'Pseudoexfoliation glaucoma' => 'Pseudoexfoliation',
				'Uveitic glaucoma' => 'Uveitis',
				'Aphakic glaucoma' => 'Aphakia',
				'Neovascularisation of the Angle' => 'Neovascularisation of Angle',
				'Neovascular glaucoma' => 'Neovascularisation of Angle',
				'Neovascular glaucoma' => 'Neovascularisation of Angle',
				'Axenfeld anomaly' => 'Axenfeld Rieger anomaly',
				'Congenital ectropion uveae' => 'Congenital ectropion',
				'Congenital Rubella' => 'Rubella',
			) as $to => $from) {
			if ($disorder = $this->dbConnection->createCommand()->select("*")->from("disorder")->where("term = :t",array(":t" => $from))->queryRow()) {
				$this->update('disorder',array('term' => $to),"id = {$disorder['id']}");
			} elseif ($finding = $this->dbConnection->createCommand()->select("*")->from("finding")->where("name = :n",array(":n" => $from))->queryRow()) {
				$this->update('finding',array('name' => $to),"id = {$finding['id']}");
			} else {
				throw new Exception("Disorder/finding not found: $from");
			}
		} 
	}
}
