<?php

namespace Misd\PhoneNumberBundle\Doctrine\ORM\DQL\Functions;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

/**
 * Class Phone
 * @package Misd\PhoneNumberBundle\Doctrine\ORM\DQL\Functions
 */
class Phone extends FunctionNode
{
    /**
     * @var PhoneNumber
     */
    public $phoneNumber;

    /**
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        return "'{$phoneUtil->format($this->phoneNumber, PhoneNumberFormat::E164)}'";
    }

    /**
     * @param \Doctrine\ORM\Query\Parser $parser
     */
    public function parse(Parser $parser)
    {
        $phoneNumber = new PhoneNumber();
        $lexer = $parser->getLexer();
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_INPUT_PARAMETER);
        $phoneNumber->setCountryCode($lexer->lookahead['value']);
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_INPUT_PARAMETER);
        $phoneNumber->setNationalNumber($lexer->lookahead['value']);
        $parser->match(Lexer::T_IDENTIFIER);

        if (
            $lexer->lookahead['type'] == Lexer::T_LEADING &&
            $lexer->lookahead['value'] == 'Leading'
        ) {
            $phoneNumber->setItalianLeadingZero(true);
            $parser->match(Lexer::T_IDENTIFIER);
            $parser->match(Lexer::T_IDENTIFIER);
            $parser->match(Lexer::T_OPEN_PARENTHESIS);
            $parser->match(Lexer::T_IDENTIFIER);
            $parser->match(Lexer::T_CLOSE_PARENTHESIS);
            $parser->match(Lexer::T_INPUT_PARAMETER);
            $parser->match(Lexer::T_TRUE);
        }

        if (
            $lexer->lookahead['type'] == Lexer::T_IDENTIFIER &&
            $lexer->lookahead['value'] == 'Number'
        ) {
            $parser->match(Lexer::T_IDENTIFIER);
            $parser->match(Lexer::T_OF);
            $parser->match(Lexer::T_LEADING);
            $parser->match(Lexer::T_IDENTIFIER);
            $parser->match(Lexer::T_INPUT_PARAMETER);
            $phoneNumber->setNumberOfLeadingZeros($lexer->lookahead['value']);
            $parser->match(Lexer::T_INTEGER);
        }

        if (
            $lexer->lookahead['type'] == Lexer::T_IDENTIFIER &&
            $lexer->lookahead['value'] == 'Extension'
        ) {
            $parser->match(Lexer::T_IDENTIFIER);
            $parser->match(Lexer::T_INPUT_PARAMETER);
            $phoneNumber->setExtension($lexer->lookahead['value']);
            $parser->match(Lexer::T_INTEGER);
        }

        if (
            $lexer->lookahead['type'] == Lexer::T_IDENTIFIER &&
            $lexer->lookahead['value'] == 'Country'
        ) {
            $parser->match(Lexer::T_IDENTIFIER);
            $parser->match(Lexer::T_IDENTIFIER);
            $parser->match(Lexer::T_IDENTIFIER);
            $parser->match(Lexer::T_INPUT_PARAMETER);
            $phoneNumber->setCountryCodeSource($lexer->lookahead['value']);
            $parser->match(Lexer::T_INTEGER);
        }

        if (
            $lexer->lookahead['type'] == Lexer::T_IDENTIFIER &&
            $lexer->lookahead['value'] == 'Preferred'
        ) {
            $parser->match(Lexer::T_IDENTIFIER);
            $parser->match(Lexer::T_IDENTIFIER);
            $parser->match(Lexer::T_IDENTIFIER);
            $parser->match(Lexer::T_IDENTIFIER);
            $parser->match(Lexer::T_INPUT_PARAMETER);
            $phoneNumber->setPreferredDomesticCarrierCode($lexer->lookahead['value']);
            $parser->match(Lexer::T_STRING);
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
        $this->phoneNumber = $phoneNumber;
    }
}
