<?php

namespace Puff\Modules\Core\Element;

use Puff\Compilation\Element\AbstractElement;
use Puff\Compilation\Service\VariableTransliterator;

/**
 * Class IfElement
 *
 * @package Puff\Compilation\Element
 */
class IfElement extends AbstractElement
{

    /**
     * @param array $attributes
     * @return mixed
     */
    public function process(array $attributes)
    {
        /** Checking predicate for PHP keywords and transliterating variables from Puff syntax to PHP */
        $predicate = preg_replace_callback('/\b(?:(?!null|true|false|or|and|isset|empty)(?!\"])[a-zA-Z][a-zA-Z.(->)0-9]+(?!\")+)+\b/', function($item) {
            return (!function_exists($item[0])) ? VariableTransliterator::transliterate($item[0]) : $item;
        }, $attributes['predicate']);

        return sprintf("<?php if(%s) { ?>", $predicate);
    }

    /**
     * @param $tokenAttributes
     *
     * @return array
     */
    public function handleAttributes($tokenAttributes)
    {
        return ["predicate" => implode(" ", $tokenAttributes)];
    }
}