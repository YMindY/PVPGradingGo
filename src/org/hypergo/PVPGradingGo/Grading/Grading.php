<?php

namespace org\hypergo\PVPGradingGo\Grading;

use org\hypergo\PVPGradingGo\Main;
use org\hypergo\PVPGradingGo\Player\Player;
use pocketmine\utils\Config;

class Grading{
   private 
   $main,
   $conf;
   
   private static $dataFolder;

   public function __construct($main){
     $this->main = $main;
     self::$dataFolder = $this->main->getDataFolder()."Grading/";
     $this->registerConfig();
   }
   /* @dhdj 大魔王 魔法注入*/
   public function upPlayerGrade($name):int{
      $player = new \org\hypergo\PVPGradingGo\Player\Player($name);
      $player->updateKills();
      if(($kills=$player->getKills())>=$this->conf->get($player->getRanking())["每小段所需人头"]){
         $list = array_keys($this->conf->getAll());
         $player->upgradeLevel();
         if($player->getLevel()>4 && $player->getRanking()==end($list)){
            return $player::STAGE_RANK_MAX;
         }elseif($player->getLevel()>5){
            $player->updateRanking($list);
            return $player::STAGE_UPGRADE_RANK;
         }else{
            if($player->getLevel()==5){
               return $player::STAGE_JOIN_UPGRADING;
            }
            return $player::STAGE_UPGRADE_LEVEL;
         }
      }else{
         return $player::STAGE_NO_UPGRADE;
      }
   }

   public static function getDataFolder(){
      return self::$dataFolder;
   }
   
   public function getRankData($rank){
      return $this->conf->get($rank);
   }
      
   private function registerConfig(){
      @mkdir($this->getDataFolder(),0777,true);
      //一玩家一文件
      @mkdir($this->getDataFolder()."玩家信息",0777,true);
      //总设置
      $this->conf = new Config($this->getDataFolder()."Config.yml",Config::YAML,array(
      "废铜烂铁"=>[
         "每小段所需人头"=>0,
         "晋级赛人头"=>3,
         "奖励"=>[]
      ],
      "倔强青铜"=>[
         "每小段所需人头"=>2,
         "晋级赛人头"=>5,
         "奖励"=>[
            "give @p 265 5",
            "给钱 @p 50"
         ]
      ],
      "秩序白银"=>[
         "每小段所需人头"=>5,
         "晋级赛人头"=>20,
         "奖励"=>[
            "give @p 265 5",
            "给钱 @p 50"
         ]
      ],
      "荣耀黄金"=>[
         "每小段所需人头"=>10,
         "晋级赛人头"=>40,
         "奖励"=>[
            "give @p 265 5",
            "给钱 @p 50"
         ]
      ],
      "尊贵铂金"=>[
         "每小段所需人头"=>25,
         "晋级赛人头"=>70,
         "奖励"=>[
            "give @p 265 5",
            "给钱 @p 50"
         ]
      ],
      "永恒钻石"=>[
         "每小段所需人头"=>50,
         "晋级赛人头"=>135,
         "奖励"=>[
            "give @p 265 5",
            "给钱 @p 50"
         ]
      ],
      "最强王者"=>[
         "每小段所需人头"=>100,
         "奖励"=>[
            "give @p 265 5",
            "给钱 @p 50"
         ]
      ]
      ));
   }
 }