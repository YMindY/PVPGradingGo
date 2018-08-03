<?php
namespace org\hypergo\PVPGradingGo;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
class Main extends PluginBase{
   private static $instance;
   public static function getInstance(){
      return self::$instance;
   }
   private function registerEvents(Listener $l){
      $this->getServer()->getPluginManager()->registerEvents($l,$this);
   }
   public function onLoad(){
      $this->getLogger()->info("正在加载!");
   }
   public function onEnable(){
      self::$instance = $this;
      $this->getLogger()->notice("插件 已启动! \n§b开发人员: HypergoStdio(Creay,xMing)");
   }
   public function onDisable(){
      $this->getLogger()->warning("插件 已关闭!");
   }
}
