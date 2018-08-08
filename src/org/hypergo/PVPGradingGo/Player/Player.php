<?php
/* @dhdj 明显代码风格就是dhdj的嘛2333 */
// dhdj6皮 dhdj能上天
namespace org\hypergo\PVPGradingGo\Player;
date_default_timezone_set('prc');

use org\hypergo\PVPGradingGo\Main;
use pocketmine\utils\Config;

class Player{

	private static 
	$name,
	$conf,
	$data;

	const 
	STAGE_NO_UPGRADE=1,
	STAGE_UPGRADE_LEVEL=2,
	STAGE_UPGRADE_RANK=3,
	STAGE_RANK_MAX=4;

	public function __construct($name){
		self::$name=$name;
		self::$conf=self::getConfig();
		self::$data=self::$conf->getAll();
	}
	public function getAllData(){
	   return "[".self::$data["段位"]."--".self::$data["段位等级"]."]\n 星数: ".self::$data["人头数"]."总击杀数: ".self::$data["总人头数"];
	}
	private static function getConfig(){
		return new Config(\org\hypergo\PVPGradingGo\Grading\Grading::getDataFolder()."玩家信息/".self::$name.".yml",Config::YAML,array(
     		"段位"=>"倔强青铜",
     		"段位等级"=>1,
     		"人头数"=>1,
     		"总人头数"=>1,
     		"领奖时间"=>""
     	));
	}
	private static function updateConfig(){
		self::$conf->setAll(self::$data);
		self::$conf->save();
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
	public function getPrizeTime(){
	   return self::$data["领奖时间"];
	}
 public function upPrizeTime(){
      self::$data["领奖时间"] = date("y-m-d");
      self::updateConfig();
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