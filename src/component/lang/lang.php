<?php
/**
 * Created by Logan22
 * Github -> https://github.com/Cannabytes/SphereWeb
 * Date: 30.08.2022 / 20:15:10
 */

namespace Ofey\Logan22\component\lang;

use Ofey\Logan22\component\config\config;
use Ofey\Logan22\component\fileSys\fileSys;
use Ofey\Logan22\component\redirect;
use Ofey\Logan22\component\session\session;

class lang {

    private static array $lang_array = [];

    //Загрузка языкового пакета шаблона
    public static function load_template_lang_packet($tpl) {
        $lang_name = self::lang_user_default();
        $langs_array = require $tpl;
        if(array_key_exists($lang_name, $langs_array)) {
            self::$lang_array = array_merge(self::$lang_array, $langs_array[$lang_name]);
        }
    }

    //Смена языка
    public static function set_lang($lang): void {
        $allowLang = include fileSys::get_dir('/src/config/lang.php');
        if (in_array($lang, $allowLang)) {
            if (self::name($lang)) {
                session::add("lang", $lang);
            }
        }
//        $link = $_SERVER['HTTP_REFERER'] ?? "/main";
//        header("Location: {$link}");
        redirect::location($_SERVER['HTTP_REFERER'] ?? "/main");
    }

    private static function name($lang = 'ru') {
        if(empty($lang)) {
            error_log("Language name is empty");
            return null;
        }
        $filename = fileSys::get_dir("/src/component/lang/package/{$lang}.php");
        if(!empty($filename) && file_exists($filename)) {
            $lang_array = include $filename;
            return $lang_array['lang_name'] ?? null;
        }
        error_log("File $filename not found");
        return null;
    }

    public static function load_package($dir = null): void {
        $lang = $_SESSION['lang'] ?? 'ru';
        if ($dir == null) {
            self::$lang_array = require fileSys::get_dir("/src/component/lang/package/{$lang}.php");
        }
    }

    //Загрузка языковых пакетов плагинов
    //В функцию load_package_plugin должен передаваться только аргуменет __DIR__
    public static function load_package_plugin($__DIR__): void {
        $lang = $_SESSION['lang'] ?? 'ru';
        $fileLang = "{$__DIR__}/lang/{$lang}.php";
        if(file_exists($fileLang)){
            $new_lang_array = require $fileLang;
            self::$lang_array = array_replace_recursive(self::$lang_array, $new_lang_array);
        }
    }

    /**
     * Возвращается массив языков с параметрами
     * $remove_lang = название языка, которое удалим из списка
     *
     * @return array
     */
    public static function lang_list($remove_lang = null, $onlyLang = false): array {
        $lngs = fileSys::get_dir_files("src/component/lang/package/", [
            'basename' => false,
            'suffix'   => '.php',
            'fetchAll' => true,
        ]);
        if($onlyLang) {
            return $lngs;
        }
        $lang_name = self::lang_user_default();
        $langs = [];
        $allowLang = include 'src/config/lang.php';
        foreach(array_intersect($lngs, $allowLang) as $lng) {
            if($lng == $remove_lang) {
                continue;
            }
            $isActive = $lng == $lang_name;
            $langs[] = [
                'lang'     => $lng,
                'name'     => self::name($lng),
                'isActive' => $isActive,
            ];
        }
        array_multisort(array_column($langs, 'isActive'), SORT_DESC, $langs);
        return $langs;
    }

    protected static array $cache = [];

    /**
     * @param $key - передаем название строки (индикатор/ключ)
     * @param $values - список заменяющих слов
     *
     * @return string
     *
     * Получение языковой фразы
     */
    public static function get_phrase($key, ...$values): string {
        if(!array_key_exists($key, self::$lang_array)) {
            return "[Not phrase «{$key}»]";
        }
        if(array_key_exists($key, self::$cache)) {
            return sprintf(self::$cache[$key], ...$values);
        }

        $string = self::$lang_array[$key];
        $result = sprintf($string, ...$values);
        if(empty($values)){
            self::$cache[$key] = $result;
        }
        return $result;
    }

    //Язык пользователя по умолчанию
    public static function lang_user_default(): string {
        $lang_name = $_SESSION['lang'] ?? config::get_language_default();
        $_SESSION['lang'] = mb_strtolower($lang_name);
        return $_SESSION['lang'];
    }

    public static function show_all_lang_package() {
        return require "src/component/lang/package/". self::lang_user_default() . ".php";
    }
}