<?php

namespace LBWB\chatgames;

use pocketmine\block\BlueIce;
use pocketmine\event\Event;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\event\plugin\PluginEnableEvent;
use pocketmine\player\Player;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use onebone\economyapi\EconomyAPI;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{

    public array $rungame = [];
    public array $preis = [];
    public string $question = "";
    public string $gamename = "";
    public string $playersanswers = "";
    public string $hoster = "";
    public string $correct = "";
    private $cfg;

    protected function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->rungame["game"] = "";
        $this->preis["reward"] = "";
        $this->correct = "";
        $this->gamename = "";
        $this->hoster = "";
        $this->question = "";
        $this->playersanswers = "";
        $this->saveResource("config.yml");
        $this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()){
            case "chatgame":
                if ($sender instanceof Player){
                    if (!$sender->hasPermission("chatgame.cmd")){
                        $sender->sendMessage("No Perms");
                    }else{
                        $this->cgUI($sender);
                    }
                }else{
                    $sender->sendMessage("Run this Command ingame");
                }
        }
        return true;
    }

    public function cgUI($player){
        $form = new CustomForm(function (Player $player, array $data = null){
            if($data === null){
                return;
            }
                $p = $player->getName();
                $this->hoster = "$p";
                $this->rungame["game"] = "$data[3]";
                $this->preis["reward"] = "$data[4]";
                $this->question = "$data[2]";
                $this->correct = "$data[3]";
                $this->getServer()->broadcastMessage("§7§lBy LBWBDev, AD: herror.eu, all is selfmade");
                $this->getServer()->broadcastMessage($this->cfg->get("NewQuestion"));
                $this->getServer()->broadcastMessage("§cChat§eGames§7 | §r" . $data[2]);

        });
        $form->setTitle($this->cfg->get("FormTitleName"));
        $form->addLabel("§cYou can create here Own Chat Games §aPlugin Made By LBWBDeveloper Join Herror.eu");
        $form->addLabel($this->cfg->get("Rules"));
        $form->addInput("Your Question for the players", "like: Whats my age?");
        $form->addInput("Solution response", "like: 14");
        $form->addSlider("How much Money the Earn with correct answer", $this->cfg->get("min-reward"), $this->cfg->get("max-reward"));
        $player->sendForm($form);
    }

    public function onChat(PlayerChatEvent $e){
        $player = $e->getPlayer();
            if ($e->getMessage() === $this->rungame["game"]) {
                $gamen = $this->gamename;
                $betrag = $this->preis["reward"];
                $hostername = $this->hoster;
                $frage = $this->question;
                $antwort = $this->correct;
                $message = $e->getMessage();
                EconomyAPI::getInstance()->addMoney($player, $this->preis["reward"]);
                $this->getServer()->broadcastMessage("§cChat§eGames§7 | §a" . $player->getName() . " §chave answerd this Question correct and earn now §a" . $this->preis["reward"] . " §6Coins");
                if($e->getMessage()){
                    $this->getServer()->broadcastMessage($player->getName() . " §ahave send §e" . $this->correct . " §ato win this §6Game");
                    $this->correct = "";
                    $this->question = "";
                    $this->rungame["game"] = "";
                    $this->preis["reward"] = "";
                    $this->hoster = "";
                    $this->gamename = "";
                    $this->playersanswers = "";
                }
            }

    }

}