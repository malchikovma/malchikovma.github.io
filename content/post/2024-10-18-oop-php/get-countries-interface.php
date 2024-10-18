<?php

class Country {
	public $name;
	public $area;
	public $population;
}

interface CountryRepositoryInterface {
    public function findByName($name);
}

class CountryRepository implements CountryRepositoryInterface {
	public function findByName($name) {
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
}

class FakeCountryRepository implements CountryRepositoryInterface {
    /** @var Country[] */
    private $countries;
    public function __construct($countries) {
    	$this->countries = $countries;
    }
    public function findByName($name) {
    	foreach ($this->countries as $country) {
    		if ($country->name === $name) {
    			return $country;
    		}
    	}
    	return null;
    }
}

class CountryService {
	/** @var CountryRepositoryInterface */
	private $countries;
	
	/** 
	 * @param CountryRepositoryInterface $countries
	 */
	public function __construct($countries) {
		$this->countries = $countries;
	}
	
	public function getCountryDensity($countryName) {
		$country = $this->countries->findByName($countryName);
		$density = $country->population / $country->area;
		if ($density < 100) {
			return 'low';
		} else if ($density < 300) {
			return 'medium';
		} else {
			return 'high';
		}
	}
}

$uk = new Country();
$uk->name = 'uk';
$uk->area = 10000.0;
$uk->population = 3000;
$countries = new FakeCountryRepository([$uk]);
$countryService = new CountryService($countries);
echo $countryService->getCountryDensity('uk');
