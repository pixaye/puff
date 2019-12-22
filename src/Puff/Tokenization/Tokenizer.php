<?php

namespace Puff\Tokenization;

use Puff\Factory\ElementClassFactory;
use Puff\Compilation\Element\ElementInterface;
use Puff\Exception\InvalidKeywordException;
use Puff\Exception\PuffException;
use Puff\Tokenization\Entity\Token;
use Puff\Tokenization\Repository\TokenRepository;
use Puff\Exception\InvalidArgumentException;
use Puff\Tokenization\Repository\TokenRepositoryInterface;

/**
 * Class Tokenizer
 *
 * @package Puff\Tokenization
 */
class Tokenizer
{
    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    public function __construct(TokenRepositoryInterface $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * Parses string and gets tokens from it
     *
     * @param $string
     *
     * @return TokenRepositoryInterface
     *
     * @throws InvalidKeywordException
     * @throws PuffException
     */
    public function tokenize(string $string)
    {
        $expressionsRegexp = preg_quote(Configuration::EXPRESSION_SIGNATURE[0]) . "(.+?)" . preg_quote(Configuration::EXPRESSION_SIGNATURE[1]);
        $printRegexp = preg_quote(Configuration::PRINT_SIGNATURE[0]) . "(.+?)" . preg_quote(Configuration::PRINT_SIGNATURE[1]);

        preg_match_all("/{$expressionsRegexp}/m", $string, $expressions, PREG_SET_ORDER, 0);
        preg_match_all("/{$printRegexp}/m", $string, $print, PREG_SET_ORDER, 0);

        foreach($expressions as $expression) {
            $tokenAttributes = explode(" ", trim($expression[1]));
            $tokenName = array_shift($tokenAttributes);

            $elementClassFactory = new ElementClassFactory();
            $elementClass = $elementClassFactory->getElementClass($tokenName);

            $tokenAttributesArray = $elementClass->handleAttributes($tokenAttributes);

            $token = new Token($tokenName, $tokenAttributesArray, $expression[0]);
            $this->tokenRepository->push($token);
        }

        foreach($print as $item) {
            if(preg_match(Configuration::FILTER_SPLIT_REGEXP, $item[1])) {
                $itemExploded = preg_split(Configuration::FILTER_SPLIT_REGEXP, $item[1]);

                $source = trim($itemExploded[0]);
                $filters = array_slice(array_map('trim', $itemExploded), 1);
            } else {
                $source = trim($item[1]);
                $filters = null;
            }

            $this->tokenRepository->push(new Token('show',[
                'data-source' => $source,
                'filters' => $filters
            ], $item[0]));
        }

        return $this->tokenRepository;
    }
}