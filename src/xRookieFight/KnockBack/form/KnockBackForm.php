<?php

namespace xRookieFight\KnockBack\form;

use JsonException;
use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use xRookieFight\KnockBack\Main;

class KnockBackForm implements Form {

    public function __construct(public string $world) {}

    public function jsonSerialize(): array
    {
       $config = new Config(Main::getInstance()->getDataFolder() . "/worlds/" . $this->world . ".yml");
       return [
           "type" => "custom_form",
           "title" => "Knockback Menu",
           "content" => [
               ["type" => "label", "text" => "World: $this->world\n"],
               ["type" => "input", "text" => "Knockback:", "placeholder" => "EX: 0.4", "default" => (string) $config->get("knockback", 0.4)],
               ["type" => "input", "text" => "Attack Cooldown:", "placeholder" => "EX: 8", "default" => (string) $config->get("attack-cooldown", 8)],
           ]
       ];
    }

    /**
     * @throws JsonException
     */
    public function handleResponse(Player $player, $data): void
    {
        if ($data === null) return;
        
        // Индекс 0 - это label, поэтому данные начинаются с индекса 1 и 2
        $kb = $data[1] ?? '';
        $ac = $data[2] ?? '';
        
        if (empty($kb) || empty($ac)) {
            $player->sendMessage(TextFormat::RED."Fill all the sections.");
            return;
        }
        
        $config = new Config(Main::getInstance()->getDataFolder(). "/worlds/". $this->world. ".yml", Config::YAML);
        $config->set("knockback", $kb);
        $config->set("attack-cooldown", $ac);
        $config->save();
        
        $player->sendMessage(TextFormat::GREEN . "World " . TextFormat::DARK_GREEN . $this->world . TextFormat::GREEN . "'s knockback/attack cooldown is now $kb/$ac");
    }
}
