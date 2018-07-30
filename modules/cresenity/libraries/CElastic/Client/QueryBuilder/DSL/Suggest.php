<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 4:39:21 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * elasticsearch suggesters DSL.
 *
 * @author Manuel Andreo Garcia <andreo.garcia@googlemail.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters.html
 */
class CElastic_Client_QueryBuilder_DSL_Suggest implements CElastic_Client_QueryBuilder_DSL {

    /**
     * must return type for QueryBuilder usage.
     *
     * @return string
     */
    public function getType() {
        return self::TYPE_SUGGEST;
    }

    /**
     * term suggester.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters-term.html
     *
     * @param $name
     * @param $field
     *
     * @return Term
     */
    public function term($name, $field) {
        return new Term($name, $field);
    }

    /**
     * phrase suggester.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters-phrase.html
     *
     * @param $name
     * @param $field
     *
     * @return Phrase
     */
    public function phrase($name, $field) {
        return new Phrase($name, $field);
    }

    /**
     * completion suggester.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters-completion.html
     *
     * @param string $name
     * @param string $field
     *
     * @return Completion
     */
    public function completion($name, $field) {
        return new Completion($name, $field);
    }

    /**
     * context suggester.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/suggester-context.html
     */
    public function context() {
        throw new NotImplementedException();
    }

}
