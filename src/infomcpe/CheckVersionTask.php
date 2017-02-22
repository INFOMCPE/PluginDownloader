<?php //by SalmonGER (https://github.com/SalmonGER)
namespace infomcpe;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\Utils; 
use pocketmine\utils\Config;
use pocketmine\Server;


class CheckVersionTask extends AsyncTask
{
    public function __construct($owner, $id, $autoupdate = false){
        $this->name = $owner->getDescription()->getName();
        $this->cversion = $owner->getDescription()->getVersion();
        $this->website = $owner->getDescription()->getWebsite();
        $this->autoupdate = $owner->getConfig()->get('Auto-Update');
        $this->path = $owner->getDataFolder();
        $this->id = $id;
        $this->owner = $owner;
        $this->autoupdate = $autoupdate;
    }

    public function onRun(){
        $url =  json_decode(Utils::getURL("http://infomcpe.ru/api.php?action=getResource&value={$this->id}")); 
        $this->nversion = $url->version_string;
        $this->version_id = $url->version_id;
        if($this->nversion){
            if($this->cversion == $this->nversion){
                $this->setResult(false);
            }else{
                $this->setResult($this->nversion);
            }
        }else{
            $this->setResult('Empty');
        }
   }

    public function onCompletion(Server $server){
    	
        $urlh = Utils::getURL('http://infomcpe.ru/updater.php?pluginname=Casino_EN'); 
        $urll = json_decode($urlh);
         
   
        if($this->getResult() == 'Empty'){
            $server->getPluginManager()->getPlugin($this->name)->getLogger()->error(TF::RED.'Could not check for Update: "Empty Response" !');
        }elseif($this->getResult()){
            $server->getPluginManager()->getPlugin($this->name)->getLogger()->alert(TF::GOLD."$urll->update $this->name");
            $server->getPluginManager()->getPlugin($this->name)->getLogger()->alert(TF::RED."$urll->cversion $this->cversion");
            $server->getPluginManager()->getPlugin($this->name)->getLogger()->alert(TF::GREEN."$urll->newversion $this->nversion");
            
      $file = Utils::getURL("https://infomcpe.ru/resources/{$this->id}/download?version={$this->version_id}");
                       
           
      if($file){
      	foreach(glob($this->owner->getServer()->getPluginPath()."*{$this->name}*.phar") as $phar){
                    unlink($phar);
                    }
        $server->getPluginManager()->getPlugin('PluginDownloader')->install($this->owner->getServer()->getPluginPath() . "{$this->name} v{$this->nversion}.phar", $file);
      
        	}
       
           
        }else{
            $server->getPluginManager()->getPlugin($this->name)->getLogger()->notice(TF::GREEN.$urll->noupdate);
        }
    }
    
}
