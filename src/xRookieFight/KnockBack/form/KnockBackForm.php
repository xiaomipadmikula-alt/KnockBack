<?php

namespace xRookieFight\KnockBack\form;

use JsonException;
use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use xRookieFight\KnockBack\Main;

class KnockBackForm implements Form {

    public function __construct(public string $world = "FFA") {} // Мир по умолчанию - FFA

    public function jsonSerialize(): array
    {
       $config = new Config(Main::getInstance()->getDataFolder() . "/worlds/" . $this->world . ".yml");
       return [
           "type" => "custom_form",
           "title" => "⚔️ Knockback Menu ⚔️",
           "content" => [
               ["type" => "label", "text" => "§l§6Мир: §e$this->world\n"],
               ["type" => "input", "text" => "§bСила отбрасывания:", "placeholder" => "EX: 0.6", "default" => (string) $config->get("knockback") ?? (string) 0.6], // Увеличено до 0.6
               ["type" => "input", "text" => "§cЗадержка атаки:", "placeholder" => "EX: 6", "default" => (string) $config->get("attack-cooldown") ?? (string) 6], // Уменьшено до 6
           ]
       ];
    }

    /**
     * @throws JsonException
     */
    public function handleResponse(Player $player, $data): void
    {
        if (is_null($data)) return;
        
        $kb = $data[0];
        $ac = $data[1];
        
        if (empty($kb) || empty($ac)) {
            $player->sendMessage(TextFormat::RED."❌ Заполните все поля!");
            return;
        }
        
        // Проверка на числовые значения
        if (!is_numeric($kb) || !is_numeric($ac)) {
            $player->sendMessage(TextFormat::RED."❌ Введите числовые значения!");
            return;
        }
        
        $config = new Config(Main::getInstance()->getDataFolder(). "/worlds/". $this->world. ".yml", Config::YAML);
        $config->set("knockback", (float)$kb);
        $config->set("attack-cooldown", (int)$ac);
        $config->save();
        
        $player->sendMessage(TextFormat::GREEN . "✅ Мир " . TextFormat::DARK_GREEN . $this->world . TextFormat::GREEN . " обновлен!");
        $player->sendMessage(TextFormat::AQUA . "⚡ Отбрасывание: §f$kb");
        $player->sendMessage(TextFormat::GOLD . "⏱️ Задержка атаки: §f$ac");
    }
}
