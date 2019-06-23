<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:15:50 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_Referer extends CTracker_AbstractRepository {

    /**
     * @var RefererParser
     */
    private $refererParser;

    /**
     * @var
     */
    private $currentUrl;

    /**
     * @var
     */
    private $searchTermModel;

    public function __construct() {
        $this->className = CTracker::config()->get('refererModel', 'CTracker_Model_Referer');
        $this->createModel();
        $this->refererParser = new CTracker_Parser_RefererParser();
        $this->currentUrl = curl::current(true);
        $searchTermModelClass = CTracker::config()->get('refererSearchTermModel', 'CTracker_Model_RefererSearchTerm');
        $this->searchTermModel = new $searchTermModelClass();
        parent::__construct();
    }

    /**
     * @param $refererUrl
     * @param $host
     * @param $domain_id
     *
     * @return mixed
     */
    public function store($refererUrl, $host, $domain_id) {
        $attributes = [
            'url' => $refererUrl,
            'host' => $host,
            'log_domain_id' => $domain_id,
            'medium' => null,
            'source' => null,
            'search_terms_hash' => null,
        ];
        $parsed = $this->refererParser->parse($refererUrl, $this->currentUrl);
        if ($parsed->isKnown()) {
            $attributes['medium'] = $parsed->getMedium();
            $attributes['source'] = $parsed->getSource();
            $attributes['search_terms_hash'] = sha1($parsed->getSearchTerm());
        }
        $referer = $this->findOrCreate(
                $attributes, ['url', 'search_terms_hash']
        );
        $referer = $this->find($referer);
        if ($parsed->isKnown()) {
            $this->storeSearchTerms($referer, $parsed);
        }
        return $referer->id;
    }

    private function storeSearchTerms($referer, $parsed) {
        foreach (explode(' ', $parsed->getSearchTerm()) as $term) {
            $this->findOrCreate(
                    [
                'referer_id' => $referer->id,
                'search_term' => $term,
                    ], ['referer_id', 'search_term'], $created, $this->searchTermModel
            );
        }
    }

}
