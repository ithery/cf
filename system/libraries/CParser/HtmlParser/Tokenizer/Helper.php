<?php

use CParser_HtmlParser_Tokenizer_State as State;

class CParser_HtmlParser_Tokenizer_Helper {
    public static function isWhiteSpace($c) {
        return $c === ' ' || $c === "\n" || $c === "\t" || $c === "\f" || $c === "\r";
    }

    public static function ifElseState($upper, $successState, $failureState) {
        $lower = strtolower($upper);

        if ($upper === $lower) {
            return function (CParser_HtmlParser_Tokenizer $t, $c) use ($upper, $lower, $successState, $failureState) {
                if ($c === $lower) {
                    $t->setState($successState);
                } else {
                    $t->setState($failureState);
                    $t->decIndex();
                }
            };
        } else {
            return function (CParser_HtmlParser_Tokenizer $t, $c) use ($lower, $upper, $successState, $failureState) {
                if ($c === $lower || $c === $upper) {
                    $t->setState($successState);
                } else {
                    $t->setState($failureState);
                    $t->decIndex();
                }
            };
        }
    }

    public static function consumeSpecialNameChar($upper, $nextState) {
        $lower = strtolower($upper);

        return function (CParser_HtmlParser_Tokenizer $t, $c) use ($lower, $upper, $nextState) {
            if ($c === $lower || $c === $upper) {
                $t->setState($nextState);
            } else {
                $t->setState(State::InTagName);
                $t->decIndex(); //consume the token again
            }
        };
    }

    public static function stateBeforeCdata1() {
        return static::ifElseState('C', State::BeforeCdata2, State::InDeclaration);
    }

    public static function stateBeforeCdata2() {
        return static::ifElseState('D', State::BeforeCdata3, State::InDeclaration);
    }

    public static function stateBeforeCdata3() {
        return static::ifElseState('A', State::BeforeCdata4, State::InDeclaration);
    }

    public static function stateBeforeCdata4() {
        return static::ifElseState('T', State::BeforeCdata5, State::InDeclaration);
    }

    public static function stateBeforeCdata5() {
        return static::ifElseState('A', State::BeforeCdata6, State::InDeclaration);
    }

    public static function stateBeforeScript1() {
        static::consumeSpecialNameChar('R', State::BeforeScript2);
    }

    public static function stateBeforeScript2() {
        static::consumeSpecialNameChar('I', State::BeforeScript3);
    }

    public static function stateBeforeScript3() {
        static::consumeSpecialNameChar('P', State::BeforeScript4);
    }

    public static function stateBeforeScript4() {
        static::consumeSpecialNameChar('T', State::BeforeScript5);
    }

    public static function stateAfterScript1() {
        static::ifElseState('R', State::AfterScript2, State::Text);
    }

    public static function stateAfterScript2() {
        static::ifElseState('I', State::AfterScript3, State::Text);
    }

    public static function stateAfterScript3() {
        static::ifElseState('P', State::AfterScript4, State::Text);
    }

    public static function stateAfterScript4() {
        static::ifElseState('T', State::AfterScript5, State::Text);
    }

    public static function stateBeforeStyle1() {
        static::consumeSpecialNameChar('Y', State::BeforeStyle2);
    }

    public static function stateBeforeStyle2() {
        static::consumeSpecialNameChar('L', State::BeforeStyle3);
    }

    public static function stateBeforeStyle3() {
        static::consumeSpecialNameChar('E', State::BeforeStyle4);
    }

    public static function stateAfterStyle1() {
        static::ifElseState('Y', State::AfterStyle2, State::Text);
    }

    public static function stateAfterStyle2() {
        static::ifElseState('L', State::AfterStyle3, State::Text);
    }

    public static function stateAfterStyle3() {
        static::ifElseState('E', State::AfterStyle4, State::Text);
    }

    public static function stateBeforeEntity() {
        static::ifElseState(
            '#',
            State::BeforeNumericEntity,
            State::InNamedEntity
        );
    }

    public static function stateBeforeNumericEntity() {
        static::ifElseState(
            'X',
            State::InHexEntity,
            State::InNumericEntity
        );
    }
}
