<?php
class Country {
    /** @var string */
	public $name;
	/** @var float */
	public $area;
	/** @var int */
	public $population;
}

function getCountry($name) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$url = 'https://restcountries.com/v3.1/name/';
	curl_setopt($ch, CURLOPT_URL, $url . $name);
	$result=curl_exec($ch);
	curl_close($ch);
	$countryArr = json_decode($result, true);
	$country = new Country();
	$country->name = $countryArr[0]['name']['common'];
	$country->area = $countryArr[0]['area'];
	$country->population = $countryArr[0]['population'];
	return $country;
}

function getCountryDensity($countryName) {
	$country = getCountry($countryName);
	$density = $country->population / $country->area;
	if ($density < 100) {
		return 'low';
	} else if ($density < 300) {
		return 'medium';
	} else {
		return 'high';
	}
}
echo getCountryDensity('uk');
