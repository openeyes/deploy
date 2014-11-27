<?php

class m141127_105011_g21_diagnoses_and_findings extends CDbMigration
{
	public function up()
	{
		$glaucoma = Subspecialty::model()->find('name=?',array('Glaucoma'));

		foreach (Yii::app()->db->createCommand()->select("*")->from("common_ophthalmic_disorder")->where("subspecialty_id=$glaucoma->id")->queryAll() as $cod) {
			Yii::app()->db->createCommand("delete from secondaryto_common_oph_disorder where parent_id = {$cod['id']}")->query();
		}

		Yii::app()->db->createCommand("delete from common_ophthalmic_disorder where subspecialty_id=$glaucoma->id")->query();

		$this->insert('disorder',array('id' => 104,'fully_specified_name' => 'Primary Ocular hypertension (disorder)','term' => 'Primary Ocular hypertension'));
		$this->insert('disorder',array('id' => 105,'fully_specified_name' => 'Normal Tension Glaucoma (disorder)', 'term' => 'Normal Tension Glaucoma'));
		$this->insert('disorder',array('id' => 106,'fully_specified_name' => 'Angle Closure (disorder)','term' => 'Angle Closure'));
		$this->insert('disorder',array('id' => 107,'fully_specified_name' => 'Primary Angle Closure (disorder)','term' => 'Primary Angle Closure'));
		$this->insert('disorder',array('id' => 108,'fully_specified_name' => 'Primary acute angle-closure glaucoma (disorder)','term' => 'Primary acute angle-closure glaucoma'));
		$this->insert('disorder',array('id' => 109,'fully_specified_name' => 'Secondary acute angle-closure glaucoma (disorder)','term' => 'Secondary acute angle-closure glaucoma'));

		$fp = fopen(dirname(__FILE__)."/../data/oe-5154.csv","r");

		fgetcsv($fp);

		$parent_do = 0;

		while ($data = fgetcsv($fp)) {
			if (preg_match('/^Childhood/',$data[0])) {
				echo "Child data reached, stopping.\n";
				break;
			}

			if (@$data[1]) {
				$cod = new CommonOphthalmicDisorder;
				$cod->display_order = $parent_do++;
				$cod->subspecialty_id = $glaucoma->id;

				if ($data[4]) {
					if (!preg_match('/\[([0-9]+)\]/',$data[4],$m)) {
						throw new Exception("Unable to read disorder: {$data[4]}");
					}

					if (!$disorder = Disorder::model()->findByPk($m[1])) {
						throw new Exception("Disorder not found: {$m[1]}");
					}

					$cod->disorder_id = $disorder->id;

					if ($data[6]) {
						if (!preg_match('/\[([0-9]+)\]/',$data[6],$m)) {
							throw new Exception("Unable to read disorder: {$data[6]}");
						}
						if (!$alt_disorder = Disorder::model()->findByPk($m[1])) {
							throw new Exception("Disorder not found: {$m[1]}");
						}
						$cod->alternate_disorder_id = $alt_disorder->id;
						$cod->alternate_disorder_label = $data[2];
					}
				} else {
					if (!$finding = Finding::model()->find('name=?',array($data[5]))) {
						throw new Exception("Finding not found: {$data[5]}");
					}

					$cod->finding_id = $finding->id;
				}

				if (!$cod->save()) {
					throw new Exception("Unable to save cod: ".print_r($cod->errors,true));
				}

				$parent = $cod;
				$sec_do = 0;

			} else if (@$data[2]) {
				if (!isset($parent)) {
					throw new Exception("Arrived at secondary item without parent.");
				}

				$sec = new SecondaryToCommonOphthalmicDisorder;
				$sec->parent_id = $parent->id;
				$sec->display_order = $sec_do++;

				if ($data[7]) {
					if (!preg_match('/\[([0-9]+)\]/',$data[7],$m)) {
						throw new Exception("Unable to read disorder: {$data[7]}");
					}
					if (!$disorder = Disorder::model()->findByPk($m[1])) {
						throw new Exception("Disorder not found: {$m[1]}");
					}

					$sec->disorder_id = $disorder->id;

				} else {
					if (!$finding = Finding::model()->find('name=?',array($data[8]))) {
						throw new Exception("Finding not found: {$data[8]}");
					}

					$sec->finding_id = $finding->id;
				}

				if ($data[9]) {
					$sec->letter_macro_text = $data[9];
				}

				if (!$sec->save()) {
					throw new Exception("Unable to save secondary-to item: ".print_r($sec->errors,true));
				}
			}
		}

		fclose($fp);
	}

	public function down()
	{
		$glaucoma = Subspecialty::model()->find('name=?',array('Glaucoma'));

		foreach (Yii::app()->db->createCommand()->select("*")->from("common_ophthalmic_disorder")->where("subspecialty_id=$glaucoma->id")->queryAll() as $cod) {
			Yii::app()->db->createCommand("delete from secondaryto_common_oph_disorder where parent_id = {$cod['id']}")->query();
		}

		Yii::app()->db->createCommand("delete from common_ophthalmic_disorder where subspecialty_id=$glaucoma->id")->query();
		Yii::app()->db->createCommand("delete from disorder where id in (104,105,106,107,108,109)")->query();
	}
}
