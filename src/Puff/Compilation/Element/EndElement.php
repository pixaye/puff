<?php


namespace Puff\Compilation\Element;

/**
 * Class EndElement
 * @package Puff\Compilation\Element
 */
class EndElement extends AbstractElement
{
    /**
     * @param array $attributes
     * @return mixed
     */
    public function process(array $attributes)
    {
        return "<?php } ?>";
    }
}