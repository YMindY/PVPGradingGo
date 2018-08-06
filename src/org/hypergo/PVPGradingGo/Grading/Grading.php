<?php
namespace org\hypergo\PVPGradingGo\Grading;
use org\hypergo\PVPGradingGo\Main;
use pocketmine\utils\Config;
class Grading{
   private $main;
   private $conf;
   public function upPlayerGrade($name){
      
   }
   public function getPlayerGrade($name):array{
      return array();
   }
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
 }