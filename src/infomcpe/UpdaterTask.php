<?php //by SalmonGER (https://github.com/SalmonGER)
namespace infomcpe;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\Utils;
use pocketmine\utils\Config;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Server;
use Casino\Casino;

class UpdaterTask extends PluginTask
{
    public function __construct($owner, $version){
        $this->name = $owner->getDescription()->getName();
        parent::__construct($owner);
        $urlh = file_get_contents('http://infomcpe.ru/api.php?action=getResource&value=260'); 
        $urldata = json_decode($urlh); 
        $this->url = "http://infomcpe.ru/{$urldata['id']}/download?version={$urldata['version_id']}";
        $this->version = $owner->getDescription()->getVersion();
        $this->newversion = $urldata->version_string;
        $lang = $owner->getConfig()->get("lang");
        
    }

    public function onRun($currenttick){
        $file = Utils::getURL($this->url);
        $lang = 2;
        if($lang == 1){
$urlh = file_get_contents('http://infomcpe.ru/updater.php?pluginname=Casino_RU'); 
        $urll = json_decode($urlh);
        }
    if($lang == 2){
$urlh = file_get_contents('http://infomcpe.ru/updater.php?pluginname=Casino_EN'); 
        $urll = json_decode($urlh); 
                } 
        if($file){
            
                foreach(glob("plugins/*".$this->name."*.phar") as $phar){
                    unlink($phar);
                
                file_put_contents('plugins/'.$this->name.' v'.$this->newversion.'.phar', $file);
                if(!file_exists('plugins/'.$this->name.' v'.$this->newversion.'.phar')){
                        $this->getOwner()->getLogger()->error('Failed to download the update!');
                }else{
                    $this->getOwner()->getServer()->broadcastMessage(TF::RED.TF::BOLD."$urll->restart");
                    $this->getOwner()->getServer()->broadcastTip(TF::RED.TF::BOLD."$url->restart");
                    sleep(7);
                    // $command3 = "reload";
                        //  $this->getOwner()->dispatchCommand(new ConsoleCommandSender($command3));
                    $this->getOwner()->getServer()->reload();
                }
            }
        }else{
            $this->getOwner()->getLogger()->error('Error while downloading new phar!');
        }
    }
}
