<?php
/* @dhdj 明显代码风格就是dhdj的嘛2333 */
namespace org\hypergo\PVPGradingGo\Player;

use org\hypergo\PVPGradingGo\Main;
use pocketmine\utils\Config;

class Player{

	private static 
	$name,
	$conf,
	$data;

	public const 
	STAGE_NO_UPGRADE=1,
	STAGE_UPGRADE_LEVEL=2,
	STAGE_UPGRADE_RANK=3,
	STAGE_RANK_MAX=4;

	public function __construct($name){
		self::$name=$name;
		self::$conf=self::getConfig();
		self::$data=self::$conf->getAll();
	}
	private static function getConfig(){
		return new Config($this->getDataFolder()."玩家信息/".self::$name.".yml",Config::YAML,array(
     		"段位"=>"倔强青铜",
     		"段位等级"=>1,
     		"人头数"=>1,
     		"总人头数"=>1
     	));
	}
	private static function updateConfig(){
		self::$conf->setAll(self::$data);
		return true;
	}
	private static function initKills(){
		self::$data["人头数"]=0;
		return true;
	}
	private static function initLevel(){
		self::$data["段位等级"]=0;
		return true;
	}
	public function updateKills($Kills=1){
		self::$data["人头数"]+=$Kills;
		self::$data["总人头数"]+=$Kills;
		self::updateConfig();
		return true;
	}
	public function getKills(){
		return self::$data["人头数"];
	}
	public function getTotalKills(){
		return self::$data["总人头数"];
	}
	public function upgradeLevel(){
		self::$data["段位等级"]++;
		self::initKills();
		self::updateConfig();
		return true;
	}
	public function getLevel(){
		return self::$data["段位等级"];
	}
	public function updateRanking($list){
		self::initLevel();
		foreach($list as $key => $value){
               if(self::$data["段位"] == $value){
                  self::$data["段位"] = $list[$key+1];
               }
        }
        self::updateConfig();
        return true;
	}
	public function getRanking(){
		return self::$data["段位"];
	}
}