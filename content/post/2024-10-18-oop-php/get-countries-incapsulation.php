<?php

class Country {
	private $name;
	private $area;
	private $population;
	
	public function __construct($name, $area, $population) {
		$this->name = $name;
		$this->area = $area;
		$this->population = $population;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getDensity() {
		$density = $this->population / $this->area;
		if ($density < 100) {
			return 'low';
		} else if ($density < 300) {
			return 'medium';
		} else {
			return 'high';
		}
	}
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
		return new Country(
			$countryArr[0]['name']['common'],
			$countryArr[0]['area'],
			$countryArr[0]['population']
		);
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
    		if ($country->getName() === $name) {
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
		return $country->getDensity();
	}
}

$uk = new Country('uk', 10000.0, 3000);
$countries = new FakeCountryRepository([$uk]);
$countryService = new CountryService($countries);
echo $countryService->getCountryDensity('uk');
