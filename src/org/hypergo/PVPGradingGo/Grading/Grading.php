<?php
namespace org\hypergo\PVPGradingGo\Grading;
use org\hypergo\PVPGradingGo\Main;
use pocketmine\utils\Config;
class Grading{
   private $main;
   private $conf;
   public function upPlayerGrade($name):int{
      /*
      1:加人头 2:升小段 3:升段位 4:段位已满
      */
      $list = array_keys($this->conf);
      $conf = $this->createPlayerConfig($name);
      $data = $conf->getAll();
      $data["人头数"]++;
      $data["总人头数"]++;
      $state = 1;
      if($data["人头数"] > $this->conf->get($data["段位"])["每小段所需人头"]){
         $data["人头数"] = 0;
         $data["段位等级"]++;
         $state =2;
         if($data["段位等级"] > 4){
            $data["段位等级"] = 0;
            foreach($list as $key => $value){
               if($data["段位"] == "最强王者"){
                  $state = 4;
                  break;
               }
               if($data["段位"] == $value){
                  $data["段位"] = $list[$key+1];
                  $state = 3;
               }
            }
         }
      }
      $conf->setAll($data);
      return $state;
   }
   /*
   public function getPlayerGrade($name):array{
      $grade = $this->getPlayerConfig($name);
      return array();
   }
   */
   public function __construct($main){
     $this->main = $main;
     $this->registerConfig();
   }
   private function getDataFolder(){
      return $this->main->getDataFolder()."Grading/";
   }
   private function registerConfig(){
      @mkdir($this->getDataFolder(),0777,true);
      //一玩家一文件
      @mkdir($this->getDataFolder()."玩家信息",0777,true);
      //总设置
      $this->conf = new Config($this->getDataFolder()."Config.yml",Config::YAML,array(
      "倔强青铜"=>[
         "每小段所需人头"=>2,
         "奖励"=>[
            "give @p 265 5",
            "给钱 @p 50"
         ]
      ],
      "秩序白银"=>[
         "每小段所需人头"=>5,
         "奖励"=>[
            "give @p 265 5",
            "给钱 @p 50"
         ]
      ],
      "荣耀黄金"=>[
         "每小段所需人头"=>10,
         "奖励"=>[
            "give @p 265 5",
            "给钱 @p 50"
         ]
      ],
      "尊贵铂金"=>[
         "每小段所需人头"=>25,
         "奖励"=>[
            "give @p 265 5",
            "给钱 @p 50"
         ]
      ],
      "永恒钻石"=>[
         "每小段所需人头"=>50,
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
   private function createPlayerConfig($name){
     return new Config($this->getDataFolder()."玩家信息/".$name."yml",Config::YAML,array(
     "段位"=>"倔强青铜",
     "段位等级"=>1,
     "人头数"=>1,
     "总人头数"=>1
     ));
   }
   private function getPlayerConfig($name){
     return $this->createPlayerConfig($name)->getAll();
   }
 }