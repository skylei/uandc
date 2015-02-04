<?php
/**
 * Created by IntelliJ IDEA.
 * User: crab
 * Date: 2014/10/29
 * Time: 18:23
 */
interface class OunoMysql {

	private static $db;
	private $tableName;
	public $lastSql;
	
	public function __construct(){}
	
	public static function getInstance(){
	
	}
	
	public function query(){}
	
	public function execute(){}
	
	public function findOne(){}
	
	public function findAll(){}
	
	public function insert(){}
	
	public function insertMore(){}
	
	public function delete(){}
	
	public function update(){}
	
	public function findAndModify(){}
	
	public function trans_start(){}
	
	public function tracs_commit(){}
	
	public function escape(){}
	
	public function getError(){}
	
	

}