<?php
/**
 * Created by Logan22
 * Github -> https://github.com/Cannabytes/SphereWeb
 * Date: 15.12.2022 / 9:01:58
 */

/**
 * Мануал использования
 *
 *
 * ========
 * Чтоб установить указать для логина или геймсервера запрос необходимо установи аннотацию #[db('game')] или
 * #[db('game')] если не указать, запросы по-умолчанию будут для геймсервера.
 * ========
 *
 */

namespace Ofey\Logan22\component\base\source;

use Ofey\Logan22\component\base\structure;

class L2jLovely implements structure {

    public static function hash(): string {
        return 'sha1';
    }

    public static function chronicle(): array {
        return [
            746,
        ];
    }

    public static function need_logout_player_for_item_add(): bool {
        return false;
    }

    #[db("login")]
    public static function account_is_exist(): string {
        return 'SELECT `login`, `password` FROM `accounts` WHERE login=?;';
    }

    #[db("login")]
    public static function account_registration(): string {
        return 'INSERT INTO `accounts` (`login`, `password`, `accessLevel`, `l2email` ) VALUES (?, ?, 0, ?);';
    }

    #[db("login")]
    public static function account_change_password(): string {
        return 'UPDATE `accounts` SET `password` = ? WHERE `login` = ?;';
    }

    #[db("login")]
    public static function accounts_email(): string {
        return 'SELECT login, password FROM accounts WHERE l2email = ?';
    }

    #[db('game')]
    static public function statistic_top_pvp(): string {
        return 'SELECT
                        characters.charId AS player_id,
                        characters.char_name AS player_name,
                        characters.`level`,
                        characters.x,
                        characters.y,
                        characters.z,
                        characters.pvpkills AS pvp, 
                        characters.pkkills AS pk, 
                        characters.clanid AS clan_id, 
                        characters.base_class AS class_id,
                        characters.`online`,
                        characters.onlinetime AS time_in_game, 
                        characters.sex,
                        clan_data.clan_name

                    FROM
                        characters
                        LEFT JOIN clan_data ON characters.clanid = clan_data.clan_id
                    ORDER BY
                        pvp DESC
                    LIMIT 100;';
    }

    static public function statistic_top_pk(): string {
        return 'SELECT
                        characters.charId AS player_id,
                        characters.char_name AS player_name,
                        characters.`level`,
                        characters.x,
                        characters.y,
                        characters.z,
                        characters.pvpkills AS pvp, 
                        characters.pkkills AS pk, 
                        characters.clanid AS clan_id, 
                        characters.base_class AS class_id,
                        characters.`online`,
                        characters.onlinetime AS time_in_game, 
                        characters.sex,
                        clan_data.clan_name
                    FROM
                        characters
                        LEFT JOIN clan_data ON characters.clanid = clan_data.clan_id
                    ORDER BY
                        pk DESC
                    LIMIT 100;';
    }

    static public function statistic_top_clan(): string {
        return 'SELECT
                    clan_data.clan_id,
                    clan_data.clan_name,
                    clan_data.clan_level,
                    clan_data.reputation_score,
                    clan_data.hasCastle,
                    clan_data.leader_id,
                    characters.char_name AS player_name,
                    characters.pvpkills AS pvp, 
                    characters.pkkills AS pk, 
                    characters.`online`,
                    characters.onlinetime AS time_in_game,

                    ( SELECT COUNT(*) FROM characters WHERE clanid = clan_data.clan_id ) AS clan_count_members 
                FROM
                    clan_data
                    LEFT JOIN characters ON clan_data.leader_id = characters.charId 
                ORDER BY
                    clan_count_members DESC,
                    clan_data.reputation_score DESC 
                    LIMIT 100;';
    }

    static public function statistic_clan_data(): string {
        return 'SELECT
                    clan_data.clan_id,
                    clan_data.clan_name,
                    clan_data.clan_level,
                    clan_data.reputation_score,
                    clan_data.hasCastle AS castle_id,
                    characters.char_name AS player_name_leader_clan,
                    clanhall.id AS clanhall_id

                FROM
                    clan_data
                    LEFT JOIN characters ON clan_data.leader_id = characters.charId
                    LEFT JOIN clanhall ON clan_data.clan_id = clanhall.ownerId

                WHERE
                    clan_data.clan_name = ? LIMIT 1';
    }

    public static function statistic_clan_skills(): string {
        return 'SELECT
                        clan_skills.skill_id, 
                        clan_skills.skill_level
                    FROM
                        clan_skills
                    WHERE
                        clan_skills.clan_id = ?';
    }

    public static function statistic_clan_players(): string {
        return 'SELECT
                        characters.char_name AS player_name, 
                        characters.pvpkills AS pvp, 
                        characters.pkkills AS pk, 
                        characters.title AS player_title, 
                        characters.`online`, 
                        characters.onlinetime AS time_in_game
                    FROM
                        characters
                    WHERE
                        characters.clanid = ?';
    }

    public static function statistic_top_player(): string {
        return 'SELECT characters.*, clan_data.* FROM characters LEFT JOIN clan_data ON characters.clanid = clan_data.clan_id ORDER BY onlinetime DESC LIMIT 100';
    }

    static public function statistic_top_heroes(): string {
        return 'SELECT
                        characters.char_name AS player_name,
                        characters.pvpkills AS pvp,
                        characters.pkkills AS pk,
                        characters.`online`,
                        characters.onlinetime AS time_in_game,
                        clan_data.clan_name,
                        characters.base_class AS classid,
                        characters.`level` 
                    FROM
                        heroes
                        LEFT JOIN characters ON heroes.char_id = characters.obj_Id
                        LEFT JOIN clan_data ON characters.clanid = clan_data.clan_id 
                    ORDER BY
                        characters.onlinetime DESC 
                        LIMIT 100';
    }

    static public function statistic_top_castle(): string {
        return 'SELECT
                    castle.id AS castle_id,
                    castle.`name`,
                    castle.taxPercent AS tax,
                    castle.treasury,
                    castle.siegeDate AS dataSiege,
                    clan_data.clan_name,
                    clan_data.clan_level,
                    clan_data.leader_id,
                    clan_data.reputation_score,
                    characters.char_name AS player_name,
                    characters.pvpkills AS pvp,
                    characters.pkkills AS pk,
                    clan_data.clan_id,
                    characters.`online`,
                    characters.onlinetime AS time_in_game
                FROM
                    castle
                    LEFT JOIN clan_data ON castle.id = clan_data.hasCastle
                    LEFT JOIN characters ON clan_data.leader_id = characters.charId';
    }

    public static function statistic_top_block(): string {
        return 'SELECT
                    characters.char_name AS player_name,
                    characters.pvpkills AS pvp,
                    characters.pkkills AS pk,
                    characters.onlinetime AS time_in_game,
                    characters.accesslevel,
                    clan_data.crest AS clan_crest,
                    ally_data.crest AS alliance_crest,
                    clan_subpledges.`name` AS `clan_name` 
                FROM
                    characters
                    LEFT JOIN clan_data ON characters.clanid = clan_data.clan_id
                    INNER JOIN ally_data ON clan_data.ally_id = ally_data.ally_id
                    LEFT JOIN clan_subpledges ON characters.clanid = clan_subpledges.clan_id 
                WHERE
                    characters.accesslevel < 0';
    }

    static public function statistic_top_onlinetime(): string {
        return 'SELECT
                    characters.charId AS player_id,
                    characters.char_name AS player_name,
                    characters.pvpkills AS pvp,
                    characters.pkkills AS pk,
                    characters.clanid,
                    characters.level,
                    characters.`online`,
                    characters.onlinetime AS time_in_game,
                    characters.base_class AS class_id,
                    clan_data.clan_name,
                    clan_data.clan_level,
                    clan_data.hasCastle AS castle_id,
                    clan_data.reputation_score AS clan_reputation_score
                FROM
                    characters
                    LEFT JOIN clan_data ON characters.clanid = clan_data.clan_id 
                ORDER BY
                    characters.onlinetime DESC 
                    LIMIT 100;';
    }

    static public function statistic_player_info(): string {
        return 'SELECT
        	        characters.account_name,
                    characters.charId AS player_id,
                    characters.char_name AS player_name,
                    characters.karma,
                    characters.pvpkills AS pvp,
                    characters.pkkills AS pk,
                    characters.title,
                    characters.`online`,
                    characters.onlinetime AS time_in_game,
                    characters.base_class as class_id,
                    characters.`level`,
                    clan_data.clan_name
                FROM
                    characters
                  LEFT JOIN clan_data ON characters.clanid = clan_data.clan_id
                WHERE
                    characters.char_name = ? LIMIT 1';
    }

    public static function statistic_player_info_sub_class(): string {
        return 'SELECT
                        character_subclasses.class_id, 
                        character_subclasses.`level`
                    FROM
                        character_subclasses
                    WHERE
                        character_subclasses.char_obj_id = ? AND character_subclasses.isBase = 0';
    }

    public static function statistic_player_inventory_info(): string {
        return 'SELECT
                    items.item_type AS `item_id`,
                    items.amount as `count`,
                    items.location as `loc`,
                    items.enchant as `enchant_level` 
                FROM
                    items 
                WHERE
                    ( items.location = "PAPERDOLL" OR items.location = "INVENTORY" ) 
                    AND items.owner_id = ?';
    }

    public static function statistic_top_counter(): string {
        return 'SELECT
                        SUM( characters.onlinetime ) AS `count_onlinetime`,
                        SUM( characters.pvpkills ) AS `count_pvpkills`,
                        SUM( characters.pkkills ) AS `count_pkkills`,
                        ( SELECT count(*) FROM `clan_data` ) AS `count_clans`,
                        ( SELECT count(*) FROM `clan_data` WHERE hasCastle != 0 ) AS `count_clan_has_castle`,
                        ( SELECT count(*) FROM `castle` ) AS `count_castle`,
                        ( SELECT count(*) FROM `characters` WHERE characters.ONLINE = 1 ) AS `player_online`,
                        ( SELECT count(*) FROM `characters` ) AS `player_all` 
                    FROM
                        characters;';
    }

    static public function statistic_top_class(): string {
        return 'SELECT
                    characters.char_name AS player_name,
                    characters.pvpkills AS pvp,
                    characters.pkkills AS pk,
                    characters.onlinetime AS time_in_game,
                    characters.`level`,
                    clan_data.clan_name
                FROM
                characters
                    LEFT JOIN clan_data ON characters.clanid = clan_data.clan_id 
                WHERE
                    characters.base_class = ? 
                ORDER BY
                    characters.pvpkills DESC,
                    characters.`level` DESC,
                    time_in_game DESC 
                    LIMIT 100;';
    }

    public static function is_player(): string {
        return 'SELECT `account_name` AS `login`, obj_Id as player_id, online FROM characters WHERE char_name = ? LIMIT 1';
    }

    // Если need_logout_player_for_item_add = false тогда НЕ используется этот метод
    public static function max_value_item_object(): string {
        return 'SELECT MAX(object_id) + 1 AS `max_object_id` FROM `items`';
    }

    // Если need_logout_player_for_item_add = false тогда НЕ используется этот метод
    public static function check_item_player(): string {
        return 'SELECT count, object_id, owner_id, item_id FROM items WHERE item_id = ? AND owner_id = ? LIMIT 1';
    }

    // Если need_logout_player_for_item_add = false тогда НЕ используется этот метод
    public static function update_item_count_player(): string {
        return 'UPDATE `items` SET `count` = ? WHERE `object_id` = ?';
    }

    /**
     * Если в сборке реализована таблица items_delayed, тогда выдавайте через неё
     *
     * @return string
     */
    public static function add_item(): string {
        return "INSERT INTO `items_delayed` (`owner_id`, `item_id`, `count`, `enchant_level`, `payment_status`, `description`) VALUES (?, ?, ?, ?, 0, 'SphereWeb')";
    }

    public static function count_online_player(): string {
        return 'SELECT COUNT(1) AS `count_online_player` FROM characters WHERE characters.`online` = 1';
    }

    public static function account_players(): string {
        return 'SELECT
                    characters.account_name,
                    characters.charId AS player_id,
                    characters.char_name AS player_name,
                    characters.karma,
                    characters.pvpkills AS pvp,
                    characters.pkkills AS pk,
                    characters.createtime,
                    characters.title,
                    characters.sex,
                    characters.`online`,
                    characters.onlinetime AS time_in_game,
                    character_subclasses.class_id,
                    character_subclasses.`level`,
                    character_subclasses.isBase,
                    clan_data.crest AS clan_crest,
                    ally_data.crest AS alliance_crest,
                    clan_subpledges.`name` AS `clan_name` 
                FROM
                    characters
                    LEFT JOIN clan_data ON characters.clanid = clan_data.clan_id
                    LEFT JOIN ally_data ON clan_data.ally_id = ally_data.ally_id
                    LEFT JOIN character_subclasses ON characters.charId = character_subclasses.char_obj_id
                    LEFT JOIN clan_subpledges ON characters.clanid = clan_subpledges.clan_id 
                WHERE
                    characters.account_name = ? 
                    AND character_subclasses.isBase = 1';
    }
}