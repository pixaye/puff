<?php

namespace Puff\Modules\Core\Element;

use Puff\Compilation\Element\AbstractElement;
use Puff\Compilation\Service\VariableTransliterator;

/**
 * Class SetElement
 * @package Puff\Compilation\Element
 */
class SetElement extends AbstractElement
{

    /**
     * @param array $attributes
     * @return mixed
     */
    public function process(array $attributes)
    {
        return sprintf("<?php %s = %s; ?>",
                VariableTransliterator::transliterate($attributes['new-variable']),
                VariableTransliterator::transliterate($attributes['value']));
    }

    public function handleAttributes($tokenAttributes)
    {
        return ["new-variable" => $tokenAttributes[0], 'value' => $tokenAttributes[2]];
    }
}