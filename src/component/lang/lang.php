<?php
/**
 * Created by Logan22
 * Github -> https://github.com/Cannabytes/TrashWeb
 * Date: 30.08.2022 / 20:15:10
 */

namespace Ofey\Logan22\component\lang;

use Ofey\Logan22\component\fileSys\fileSys;
use Ofey\Logan22\component\redirect;
use Ofey\Logan22\component\session\session;
use Ofey\Logan22\config\config;

class lang {

    static private array $lang_array = [];

    //Загрузка языкового пакета шаблона
    static public function load_template_lang_packet($tpl) {
        $lang_name = self::lang_user_default();
        $langs_array = require_once($tpl);
        if(array_key_exists($lang_name, $langs_array)) {
            self::$lang_array = array_merge(self::$lang_array, $langs_array[$lang_name]);
        }
    }

    static public function set_lang($lang): void {
        if(self::name($lang)) {
            session::add("lang", $lang);
        }
        if(isset($_SERVER['HTTP_REFERER'])){
            redirect::location($_SERVER['HTTP_REFERER']);
        }else{
            redirect::location("/main");
        }
    }

    static public function load_package(): void {
        if(empty($_SESSION['lang'])) {
            $lang = 'ru';
        } else {
            $lang = $_SESSION['lang'];
        }
        $lang_array = include_once $_SERVER['DOCUMENT_ROOT'] . "/src/component/lang/package/{$lang}.php";
        self::$lang_array = $lang_array;
    }


    static private function name($lang = 'ru') {
        $filename = $_SERVER['DOCUMENT_ROOT'] . "/src/component/lang/package/{$lang}.php";
        if(!file_exists($filename)) {
            echo "Файл НЕ $filename существует";
        }
        $lang_array = include $filename;
        return $lang_array['lang_name'] ?? null;
    }

    /**
     * Возвращается массив языков с параметрами
     * $remove_lang = название языка, которое удалим из списка
     * @return array
     */
    static public function lang_list($remove_lang = null, $onlyLang = false): array {
        $lngs = fileSys::get_dir_files("src/component/lang/package/", [
            'basename' => false,
            'suffix'   => '.php',
            'fetchAll' => true,
        ]);
        if($onlyLang){
            return $lngs;
        }
        $langs = [];
        $lang_name = self::lang_user_default();

        foreach($lngs as $lng) {
            if($lng == $remove_lang){
                continue;
            }
            $isActive = $lng == $lang_name;
            $langs[] = [
                'lang'     => $lng,
                'name'     => self::name($lng),
                'isActive' => $isActive,
            ];
        }
        $active = [];
        foreach($langs as $key => $arr) {
            $active[$key] = $arr['isActive'];
        }
        array_multisort($langs, SORT_DESC, $active);
        return $langs;
    }

    /**
     * @param $key - передаем название строки (индикатор/ключ)
     * @param $values - список заменяющих слов
     *
     * @return string
     *
     * Получение языковой фразы
     */
    static public function get_phrase($key, mixed ...$values): string {
        if(!array_key_exists($key, self::$lang_array)) {
            return "[Not phrase «{$key}»]";
        }
        $string = self::$lang_array[$key];
        return sprintf($string, ...$values);
    }

    //Язык пользователя по умолчанию
    static public function lang_user_default() {
        if(!isset($_SESSION['lang'])) {
            $lang_name = config::get_language_default();
            $_SESSION['lang'] = $lang_name;
        } else {
            $lang_name = $_SESSION['lang'];
        }
        return $lang_name;
    }

}