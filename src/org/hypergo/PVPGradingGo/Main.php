<?php
namespace org\hypergo\PVPGradingGo;
use pocketmine\plugin\PluginBase; 
class Main extends PluginBase{
   public function onLoad(){
      $this->getLogger()->info("正在加载!");
   }
   public function onEnable(){
      $this->getLogger()->notice("插件 已启动! \n§b开发人员: HypergoStdio(Creay,xMing)");
   }
   public function onDisable(){
      $this->getLogger()->warning("插件 已关闭!");
   }
}