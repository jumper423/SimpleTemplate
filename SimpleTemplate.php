<?php

namespace jumper423\SimpleTemplate;

/**
 * Class SimpleTemplate
 * @package jumper423\SimpleTemplate
 */
class SimpleTemplate
{
    public $mainFields = [];
    public $mainTemplate = [];
    public $mainFile = null;
    public $blockFields = [];
    public $blockTemplate = [];
    public $blockFile = null;

    /**
     * Задаём путь до главного шаблона
     * @param $path
     */
    public function mainTemplate($path)
    {
        $this->mainFile = file_get_contents($path);
        preg_match_all('/{([^{}]*)}/', $this->blockFile, $matches);
        if (count($matches[1]) > 0) {
            //Проходимся по ним
            foreach ($matches[1] as $marker) {
                //Перестраиваем массив
                $this->mainFields[$marker] = null;
            }
        }
    }

    /**
     * Вставляем в метку главного шаблона текуший блок или строку
     * @param $marker
     * @param null $html
     */
    public function insertBlock($marker, $html = null)
    {
        if (is_null($html)) {
            $html = $this->blockOutput();
        }
        if (array_key_exists($marker, $this->mainFields)) {
            $this->mainFields[$marker] .= $html; //Записываем получившийся html  в метку
        } else {
            $this->mainFields[$marker] = $html; //Записываем получившийся html  в метку
        }
    }

    /**
     * Задаём путь до блока
     * @param $path
     */
    public function blockTemplate($path)
    {
        $this->blockFile = file_get_contents($path);
        while (preg_match('/<!-- BEGIN ([^BEGIN|END]*) -->([^BEGIN|END]*)\<!-- END ([^BEGIN|END]*) -->/', $this->blockFile, $matches)) {//Если есть строка со скобками в скоторой нет скобок, то записываем её в массив
            $name = $matches[1];
            $html = trim($matches[2]);
            $this->blockTemplate[$name] = $html;
            $this->blockFile = str_replace($matches[0], "{" . $name . "}", $this->blockFile);
        }
    }

    /**
     * Задаём знчение метки
     * @param $field
     * @param $value
     */
    public function setVar($field, $value)
    {
        $this->blockFields[$field] = $value;
    }

    /**
     * Парсим блок
     * @param $block
     */
    public function parse($block)
    {
        //достаём шаблон
        $template = $this->blockTemplate[$block];
        //Заменяем все метки
        if (!array_key_exists($block, $this->blockFields)) {
            $this->blockFields[$block] = '';
        }
        $this->blockFields[$block] .= $this->conversion($template, $this->blockFields); //Записываем получившийся html  в метку
    }

    /**
     * Блок
     * @return string
     */
    public function blockOutput()
    {
        $result = $this->conversion($this->blockFile, $this->blockFields);
        unset($this->blockFile);
        unset($this->blockTemplate);
        unset($this->blockFields);
        return $result;
    }

    /**
     * Млаынй шаблон
     * @param bool|true $view
     * @return string
     */
    public function mainOutput($view = true)
    {
        $result = $this->conversion($this->mainFile, $this->mainFields);
        unset($this->mainFile);
        unset($this->mainTemplate);
        unset($this->mainFields);
        if ($view) {
            echo $result;
        }
        return $result;
    }

    /**
     * @param string $template
     * @param array $fields
     * @return string
     */
    private function conversion($template, $fields)
    {
        while (preg_match_all('/{([^{}]*)}/', $template, $matchesTags)) {
            if (count($matchesTags[1]) > 0) {
                //Проходимся по ним
                foreach ($matchesTags[1] as $marker) {
                    //Перестраиваем массив
                    if (array_key_exists($marker, $fields)) {
                        $template = str_replace("{" . $marker . "}", $fields[$marker], $template);
                    } else {
                        $template = str_replace("{" . $marker . "}", null, $template);
                    }
                }
            }
        }
        return $template;
    }
}