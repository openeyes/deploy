<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class DumpRefCommand extends CConsoleCommand {
	/* patterns for tables containing data which should not be exported (non-reference data) */
	public $data_tables = array(
		'address',
		'/^audit_/',
		'authassignment',
		'authitem',
		'authitem_type',
		'authitemchild',
		'commissioning_body',
		'contact',
		'contact_location',
		'contact_metadata',
		'element_type',
		'event_group',
		'event_type',
		'event_issue',
		'user',
		'event',
		'episode',
		'firm_user_assignment',
		'gp',
		'mehbookinglogger_log',
		'pas_assignment',
		'patient',
		'tbl_audit_trail',
		'tbl_migration',
		'tbl_migration_deploy',
		'/_version$/',
		'/^et_/',
		'/^user_/',
		'user_session',
		'person',
		'practice',
		'protected_file',
		'rtt',
	);

	/* If users are being included in the export, these patterns should be included */
	public $not_data_if_users_included = array(
		'user',
		'firm_user_assignment',
		'/^user_/',
	);

	/* If a table has a foreign key to any of these tables, it is considered not reference data and thus excluded */
	public $data_table_fks = array(
		'patient',
		'episode',
		'event',
		'event_type',
		'commissioning_body',
		'/^et_/',
		'protected_file',
	);

	/* Contacts are excluded unless they relate to any of these tables (in which case the contact and any addresses are exported) */
	public $allow_contacts_for = array(
		'site',
		'institution',
		'user',
	);

	/* If users are not being exported, null out any fields in the list of tables here that are foreign key'd to the user table */
	public $user_null_for = array(
		'firm',
	);

	/* If users are not being exported, for each of these tables skip rows which point to users other than the admin user */
	public $reduce_user_to_admin_for = array(
		'ophouanaestheticsataudit_anaesthetist_lookup',
		'ophtrintravitinjection_injectionuser',
		'ophtrlaser_laser_operator',
	);

	/* Fields in these tables which map to element types will be remapped during export so that they match the correct element types in the database they are imported into */
	/* Tables containing foreign keys to element types not in this list will be excluded from the export */
	public $remap_element_type_for = array(
		'ophciexamination_attribute_element',
		'ophciexamination_element_set_item',
		'setting_firm',
		'setting_installation',
		'setting_institution',
		'setting_metadata',
		'setting_site',
		'setting_specialty',
		'setting_subspecialty',
		'setting_user',
		'ophtroperationnote_procedure_element',
	);

	/* Same as above for event_type table */
	public $remap_event_type_for = array(
		'episode_summary_item',
	);

	/* When processing the table on the left, don't follow foreign keys for any tables in the list on the right. This is necessary because user<>firm foreign keys create an infinite loop */
	public $specifically_dont_recurse = array(
		'user' => array('firm'),
	);

	/* Specific rows to skip, currently just used to skip the admin user account */
	public $skip_rows = array(
		'user' => array(1)
	);

	/* Set static values on specific fields, this is currently used to null out user.last_firm_id to prevent a recursion infinite loop and to set all user passwords to 'password' */
	public $override_fields = array(
		'user' => array(
			'last_firm_id' => null,
			'salt' => 'fi3hf83',
			'password' => '7d3675bbedc5b0382c672489b99076f1',
		),
	);

	/* List of fields which denote that a row is soft-deleted */
	public $soft_delete_deleted_fields = array(
		'deleted',
		'discontinued',
	);

	/* List of fields which denote that a row is active (not soft-deleted) */
	public $soft_delete_active_fields = array(
		'active',
	);

	public $args;
	public $to_process = array();
	public $event_types = array();
	public $include_users = false;
	public $recurse_dependencies = false;
	public $soft_delete = false;

	public function usage()
	{
		echo "\nUsage: dumpref [-r] [-u] [-d] [tables]\n\n";
		echo "Where [tables] is \"all\" or any combination of \"core\" and module classnames.\n\n";
		echo "-r: recursively include tables referenced by selected tables.\n";
		echo "-u: include users in the dump (passwords will be set to \"password\"\n\n";
		echo "-d: soft delete pre-existing rows if not present in the exported data\n\n";
		echo "eg: dumpref core OphCiExamination OphCiPhasing\n\n";
		exit;
	}

	public function run($args)
	{
		$this->args = $args;

		if (empty($this->args)) {
			$this->usage();
		}

		foreach ($this->args as $arg) {
			if ($arg[0] == '-') {
				for ($i=1; $i<strlen($arg); $i++) {
					switch ($arg[$i]) {
						case 'r':
							$this->recurse_dependencies = true;
							break;
						case 'u':
							$this->include_users = true;
							break;
					}
				}
			}
		}

		if ($this->include_users) {
			$new_data_tables = array();

			foreach ($this->data_tables as $data_table) {
				if (!in_array($data_table, $this->not_data_if_users_included)) {
					$new_data_tables[] = $data_table;
				}
			}

			$this->data_tables = $new_data_tables;
		}

		foreach (Yii::app()->db->createCommand()->select("*")->from("event_type")->queryAll() as $et) {
			$this->event_types[] = strtolower($et['class_name']);
		}

		foreach (Yii::app()->db->schema->tables as $table) {
			if ($this->selected($table->name)) {
				if (!in_array($table->name,$this->to_process)) {
					if ($this->isReferenceTable($table)) {
						$this->scan($table);
					}
				}
			}
		}

		foreach ($this->to_process as $table) {
			$this->dump(Yii::app()->db->schema->getTable($table));
		}
	}

	/* Determine whether the passed table has been selected by the user's arguments */
	public function selected($table)
	{
		if (in_array('all',$this->args)) {
			return true;
		}

		if (preg_match('/^(.*?)_/',$table,$m) && in_array(strtolower($m[1]),$this->event_types)) {
			foreach ($this->args as $arg) {
				if (strcasecmp($arg,$m[1]) == 0) {
					return true;
				}
			}
			return false;
		}

		return in_array('core',$this->args);
	}

	public function isReferenceTable($table)
	{
		foreach ($this->data_tables as $match) {
			if ($match[0] == '/') {
				if (preg_match($match,$table->name)) {
					return false;
				}
			} else {
				if ($table->name == $match) {
					return false;
				}
			}
		}

		foreach ($table->foreignKeys as $field => $fk) {
			if ($fk[1] == 'id') {
				foreach ($this->data_table_fks as $match) {
					if ($match[0] == '/') {
						if (preg_match($match,$fk[0])) {
							return false;
						}
					} else {
						if ($fk[0] == $match) {
							if ($match != 'event_type' || !in_array($table->name,$this->remap_event_type_for)) {
								return false;
							}
						}
					}
				}
			}
		}

		return true;
	}

	/* This builds up a list of tables to process in the correct logical order to avoid foreign key issues */
	public function scan($table)
	{
		$process = true;

		foreach ($table->foreignKeys as $field => $fk) {
			if (!in_array($field,array('created_user_id','last_modified_user_id'))) {
				$_table = Yii::app()->db->schema->getTable($fk[0]);

				if ($table->name != $_table->name && !in_array($_table->name,$this->to_process) && ($this->recurse_dependencies || $this->selected($_table->name)) && (!isset($this->specifically_dont_recurse[$table->name]) || !in_array($_table->name,$this->specifically_dont_recurse[$table->name]))) {
					if ($this->isReferenceTable($_table)) {
						$this->scan($_table);
					} else {
						switch ($fk[0]) {
							case 'user':
								if (!$this->include_users && !in_array($table->name,$this->user_null_for) &&
									!in_array($table->name,$this->reduce_user_to_admin_for)) {
									$process = false;
								}
								break;
							case 'contact':
								if (!in_array($table->name,$this->allow_contacts_for)) {
									$process = false;
								}
								break;
							case 'element_type':
								if (!in_array($table->name,$this->remap_element_type_for)) {
									$process = false;
								}
								break;
							case 'event_type':
								if (!in_array($table->name,$this->remap_event_type_for)) {
									$process = false;
								}
								break;
							default:
								$process = false;
								break;
						}
					}
				}
			}
		}

		if ($process) {
			$this->to_process[] = $table->name;
		}
	}

	/* Dump the passed table out to stdout as SQL statements */
	public function dump($table)
	{
		$columns = array_keys($table->columns);

		$rows = Yii::app()->db->createCommand()->select("*")->from($table->name)->queryAll();

		if (in_array($table->name,$this->allow_contacts_for)) {
			$contact_ids = array();
			$address_ids = array();

			foreach ($rows as $row) {
				foreach ($table->foreignKeys as $field => $fk) {
					if ($fk[0] == 'contact') {
						if ($row[$field]) {
							$contact_ids[] = $row[$field];
						}
					}
				}
			}

			if (!empty($contact_ids)) {
				$contacts = Yii::app()->db->createCommand()->select("*")->from("contact")->where("id in (".implode(',',$contact_ids).")")->queryAll();

				if (count($contacts) >0) {
					$this->dump_rows(Yii::app()->db->schema->getTable('contact'),$contacts);
				}

				$addresses = Yii::app()->db->createCommand()->select("*")->from("address")->where("contact_id in (".implode(',',$contact_ids).")")->queryAll();

				if (count($addresses) >0) {
					$this->dump_rows(Yii::app()->db->schema->getTable('address'),$addresses);
				}
			}
		}

		$filtered_rows = array();

		foreach ($rows as $row) {
			if (!$this->include_users && in_array($table->name,$this->reduce_user_to_admin_for)) {
				$skip = false;
				foreach ($table->foreignKeys as $field => $fk) {
					if (!in_array($field,array('created_user_id','last_modified_user_id'))) {
						if ($fk[0] == 'user') {
							if ($row[$field] != 1) {
								$skip = true;
							}
						}
					}
				}

				if ($skip) {
					continue;
				}
			}

			if (@$table->primaryKey && isset($this->skip_rows[$table->name]) && in_array($row[$table->primaryKey],$this->skip_rows[$table->name])) {
				continue;
			}

			$filtered_rows[] = $row;
		}

		if (count($filtered_rows) >0) {
			if ($this->soft_delete && @$table->primaryKey) {
				$deleted_field = $active_field = false;

				foreach ($this->soft_delete_deleted_fields as $field) {
					if (isset($table->columns[$field])) {
						$deleted_field = $field;
						break;
					}
				}

				if (!$deleted_field) {
					foreach ($this->soft_delete_active_fields as $field) {
						if (isset($table->columns[$field])) {
							$active_field = $field;
						}
					}
				}

				if ($deleted_field || $active_field) {
					$field = $deleted_field ? $deleted_field : $active_field;
					$value = $deleted_field ? 1 : 0;

					echo "UPDATE `$table->name` SET `$field` = `$value`;\n";
				}
			}

			$this->dump_rows($table,$filtered_rows);
		}
	}

	public function dump_rows($table,$rows)
	{
		echo "INSERT INTO `$table->name` (`";

		echo implode('`,`',array_keys($table->columns));

		echo "`) VALUES ";

		$i = 0;
		foreach ($rows as $row) {
			if (!$this->include_users && in_array($table->name,$this->user_null_for)) {
				foreach ($table->foreignKeys as $field => $fk) {
					if (!in_array($field,array('created_user_id','last_modified_user_id'))) {
						if ($fk[0] == 'user') {
							$row[$field] = null;
						}
					}
				}
			}

			if (in_array($table->name,$this->remap_element_type_for)) {
				foreach ($table->foreignKeys as $field => $fk) {
					if ($fk[0] == 'element_type') {
						if ($row[$field]) {
							$ele = Yii::app()->db->createCommand()->select("*")->from("element_type")->where("id = :id",array(":id" => $row[$field]))->queryRow();
							$et = Yii::app()->db->createCommand()->select("*")->from("event_type")->where("id = :id",array(":id" => $ele['event_type_id']))->queryRow();

							$row[$field] = "{{ElementType,{$ele['class_name']},{$et['class_name']}}}";
						}
					}
				}
			}

			if (in_array($table->name,$this->remap_event_type_for)) {
				foreach ($table->foreignKeys as $field => $fk) {
					if ($fk[0] == 'event_type') {
						if ($row[$field]) {
							$et = Yii::app()->db->createCommand()->select("*")->from("event_type")->where("id = :id",array(":id" => $row[$field]))->queryRow();

							$row[$field] = "{{EventType,{$et['class_name']}}}";
						}
					}
				}
			}

			if ($i >0) {
				echo ",";
			}

			$this->dump_insert($table,$row);

			$i++;
		}

		echo " ON DUPLICATE KEY UPDATE ";

		$updates = array();

		foreach (array_keys($table->columns) as $key) {
			$updates[] = "`$key` = VALUES(`$key`)";
		}

		echo implode(',',$updates).";\n";
	}

	public function dump_insert($table, $row)
	{
		if (!$this->include_users) {
			$row['created_user_id'] = $row['last_modified_user_id'] = 1;
		}

		if (isset($this->override_fields[$table->name])) {
			foreach ($this->override_fields[$table->name] as $key => $value) {
				$row[$key] = $value;
			}
		}

		$columns = array_keys($table->columns);

		echo "(";

		$values = array();

		foreach ($row as $key => $value) {
			if (is_null($value)) {
				$values[] = "NULL";
			} else if (is_int($value)) {
				$values[] = $value;
			} else if (preg_match('/^\{\{ElementType,(.*?),(.*?)\}\}$/',$value,$m)) {
				$m[1] = str_replace('\\','\\\\',$m[1]);
				$values[] = "(select id from element_type where class_name = '{$m[1]}' and event_type_id = (select id from event_type where class_name = '{$m[2]}'))";
			} else if (preg_match('/^\{\{EventType,(.*?)\}\}$/',$value,$m)) {
				$values[] = "(select id from event_type where class_name = '{$m[1]}')";
			} else {
				$values[] = "'".mysql_escape_string($value)."'";
			}
		}

		echo implode(',',$values).")";
	}
}
