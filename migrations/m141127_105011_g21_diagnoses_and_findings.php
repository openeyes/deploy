<?php

class m141127_105011_g21_diagnoses_and_findings extends CDbMigration
{
	public function up()
	{
		$glaucoma_id = $this->dbConnection->createCommand()->select("id")->from("subspecialty")->where("name=:name",array(":name" => "Glaucoma"))->queryScalar();

		foreach ($this->dbConnection->createCommand()->select("*")->from("common_ophthalmic_disorder")->where("subspecialty_id=$glaucoma_id")->queryAll() as $cod) {
			$this->dbConnection->createCommand("delete from secondaryto_common_oph_disorder where parent_id = {$cod['id']}")->query();
		}

		$this->dbConnection->createCommand("delete from common_ophthalmic_disorder where subspecialty_id=$glaucoma_id")->query();

		foreach (array(
			array('id' => 104,'fully_specified_name' => 'Primary Ocular hypertension (disorder)','term' => 'Primary Ocular hypertension'),
			array('id' => 105,'fully_specified_name' => 'Normal Tension Glaucoma (disorder)', 'term' => 'Normal Tension Glaucoma'),
			array('id' => 106,'fully_specified_name' => 'Primary acute angle-closure glaucoma (disorder)','term' => 'Primary acute angle-closure glaucoma')
			) as $disorder) {

			if (!$this->dbConnection->createCommand()->select("id")->from("disorder")->where("id = :id",array(":id" => $disorder['id']))->queryScalar()) {
				$this->insert('disorder', $disorder);
			}
		}

		$fp = fopen(dirname(__FILE__)."/../data/oe-5154.csv","r");

		fgetcsv($fp);

		$parent_do = 0;

		while ($data = fgetcsv($fp)) {
			if (preg_match('/^Childhood/',$data[0])) {
				echo "Child data reached, stopping.\n";
				break;
			}

			if (@$data[1]) {
				$cod = array();
				$cod['display_order'] = $parent_do++;
				$cod['subspecialty_id'] = $glaucoma_id;

				if ($data[4]) {
					if (!preg_match('/\[([0-9]+)\]/',$data[4],$m)) {
						throw new Exception("Unable to read disorder: {$data[4]}");
					}

					if (!$disorder = $this->dbConnection->createCommand()->select("*")->from("disorder")->where("id = :id",array(":id" => $m[1]))->queryRow()) {
						throw new Exception("Disorder not found: {$m[1]}");
					}

					$cod['disorder_id'] = $disorder['id'];

					if ($data[6]) {
						if (!preg_match('/\[([0-9]+)\]/',$data[6],$m)) {
							throw new Exception("Unable to read disorder: {$data[6]}");
						}
						if (!$alt_disorder = $this->dbConnection->createCommand()->select("*")->from("disorder")->where("id = :id",array(":id" => $m[1]))->queryRow()) {
							throw new Exception("Disorder not found: {$m[1]}");
						}
						$cod['alternate_disorder_id'] = $alt_disorder['id'];
						$cod['alternate_disorder_label'] = $data[2];
					}
				} else {
					if (!$finding = $this->dbConnection->createCommand()->select("*")->from("finding")->where("name = :name",array(":name" => $data[5]))->queryRow()) {
						throw new Exception("Finding not found: {$data[5]}");
					}

					$cod['finding_id'] = $finding['id'];
				}

				$this->insert('common_ophthalmic_disorder',$cod);

				$parent = $this->dbConnection->createCommand()->select("*")->from("common_ophthalmic_disorder")->order('id desc')->limit(1)->queryRow();
				$sec_do = 0;

			} else if (@$data[2]) {
				if (!isset($parent)) {
					throw new Exception("Arrived at secondary item without parent.");
				}

				$sec = array();
				$sec['parent_id'] = $parent['id'];
				$sec['display_order'] = $sec_do++;

				if ($data[7]) {
					if (!preg_match('/\[([0-9]+)\]/',$data[7],$m)) {
						throw new Exception("Unable to read disorder: {$data[7]}");
					}
					if (!$disorder = $this->dbConnection->createCommand()->select("*")->from("disorder")->where("id = :id",array(":id" => $m[1]))->queryRow()) {
						throw new Exception("Disorder not found: {$m[1]}");
					}

					$sec['disorder_id'] = $disorder['id'];

				} else {
					if (!$finding = $this->dbConnection->createCommand()->select("*")->from("finding")->where("name = :name",array(":name" => $data[8]))->queryRow()) {
						throw new Exception("Finding not found: {$data[8]}");
					}

					$sec['finding_id'] = $finding['id'];
				}

				if ($data[9]) {
					$sec['letter_macro_text'] = $data[9];
				}

				$this->insert('secondaryto_common_oph_disorder',$sec);
			}
		}

		fclose($fp);
	}

	public function down()
	{
		$glaucoma_id = $this->dbConnection->createCommand()->select("id")->from("subspecialty")->where("name=:name",array(":name" => "Glaucoma"))->queryScalar();

		foreach ($this->dbConnection->createCommand()->select("*")->from("common_ophthalmic_disorder")->where("subspecialty_id=$glaucoma_id")->queryAll() as $cod) {
			$this->dbConnection->createCommand("delete from secondaryto_common_oph_disorder where parent_id = {$cod['id']}")->query();
		}

		$this->dbConnection->createCommand("delete from common_ophthalmic_disorder where subspecialty_id=$glaucoma_id")->query();
	}
}
