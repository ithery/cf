<?php

class CVendor_PosMalaysia {

	public function __construct() {
		# code...
	}

	public function posLocationWebApi($state, $location, $stype = null) {
		// $params = [
		//     'state' => 'my-01',
		//     'location' => '',
		//     // 'stype' => '',
		// ];

		$params = [
			'state' => $state,
			'location' => $location,
		];

		if ($stype) {
			$params['stype'] = $stype;
		}

		$curl = CCurl::factory('http://stagingsds.pos.com.my/apigateway/as2corporate/api/poslocationwebapi/v1?' . http_build_query($params));
		$curl->setHttpHeader(['X-User-Key: REIycDJnWFlHVHZkcTRzM2ROVnR2clVEeFpGMkhDcVM=']);

		$curl->exec();
		return $curl->response();
	}

	public function trackingOfficeList() {
		$curl = CCurl::factory('http://stagingsds.pos.com.my/apigateway/as2corporate/api/trackingofficelist/v1');
		$curl->setHttpHeader(['X-User-Key: aVhPaG1PeHBoSTdrTnNKQk92NEZPb2cyU3lqSHRVRWw=']);

		$curl->exec();
		return $curl->response();
	}

	public function trackAndTraceWebApiDetails($id, $culture = 'En') {
		$params = [
			'id' => $id,
			'Culture' => 'En',
		];

		$curl = CCurl::factory('http://stagingsds.pos.com.my/apigateway/as2corporate/api/trackntracewebapidetails/v1?' . http_build_query($params));
		$curl->setHttpHeader(['X-User-Key: cFIxODBsc2l0MjdRU0J5VE12SFVqNlhIMktzQjdhdVM=']);

		$curl->exec();
		return $curl->response();
	}

	public function posLajuDomestic($weight, $zone) {
		$params = [
			'Weight' => $weight,
			'Zone' => $zone,
		];

		$curl = CCurl::factory('http://stagingsds.pos.com.my/apigateway/as2corporate/api/poslajudomestic/v1?' . http_build_query($params));
		$curl->setHttpHeader(['X-User-Key: dm1henBwdmNZdWZxd3g2TmkzTVJHeXpTRmVZUUpQdEs=']);

		$curl->exec();
		return $curl->response();
	}

	public function typeOfZone() {
		$curl = CCurl::factory('http://stagingsds.pos.com.my/apigateway/as2corporate/api/typeofzone/v1');
		$curl->setHttpHeader(['X-User-Key: cUpodnJIYnpjVmlOTjRJVHNabkNicU5jSjM1U3VwTng=']);

		$curl->exec();
		return $curl->response();
	}

	public function getAllState() {
		$curl = CCurl::factory('http://stagingsds.pos.com.my/apigateway/as2corporate/api/getallstate/v1');
		$curl->setHttpHeader(['X-User-Key: allpaE5DVjlJcU10eHcxY1F6T3lrMFZqVlZmdDNtTGU=']);

		$curl->exec();
		return $curl->response();
	}

	public function calculateDeliveryCost($isMalaysiaAddress, $productType, $hasFrame, $stateId, $weight, $countryId) {
		$curl = CCurl::factory('http://stagingsds.pos.com.my/apigateway/as2corporate/api/calculatedeliverycost/v1');
		$curl->setHttpHeader(['X-User-Key: MTh0alVDNkZJeVZRTVNqTlg4OTIyZ0ttZGl6b0ppaEU=']);

		// $post = [
		//     'IsMalaysiaAddress' => true,
		//     'ProductType' => 'CUBE',    // CARD, CUBE, STAMP
		//     'HasFrame' => true,
		//     'StateId' => '344',
		//     'Weight' => '1',
		//     'CountryId' => '223',
		// ];

		$post = [
		    'IsMalaysiaAddress' => $isMalaysiaAddress,
		    'ProductType' => $productType,    // CARD, CUBE, STAMP
		    'HasFrame' => $hasFrame,
		    'StateId' => $stateId,
		    'Weight' => $weight,
		    'CountryId' => $countryId,
		];

		$curl->setPost($post);
		$curl->exec();
		return $curl->response();
	}

	public function getAllCountry() {
		$curl = CCurl::factory('http://stagingsds.pos.com.my/apigateway/as2corporate/api/getallcountry/v1');
		$curl->setHttpHeader(['X-User-Key: ZlFYM3h3aDRVejRoaGxFYlNIU0VyQkpEWEhpSlJwams=']);

		$curl->exec();
		return $curl->response();
	}

	public function listOfCountry() {
		$curl = CCurl::factory('http://stagingsds.pos.com.my/apigateway/as2corporate/api/country/v1');
		$curl->setHttpHeader(['X-User-Key: RGduT0RZMlByODkxRWZqYzUyd0l2eG1CcXk2Qk1tU2k=']);

		$curl->exec();
        return $curl->response();
	}

}