+++
title = 'Зачем нужно объектно-ориентированное программирование'
date = 2024-10-18T11:00:00+03:00
draft = false
tags = ['Разработка']
+++

В начале карьеры, я не понимал, зачем нужен объектно-ориентированный подход. У нас уже есть функции, они решают проблему структурирования кода! В чем может быть проблема? Давайте разбираться.

> Проверки ошибок опускаем для краткости. В промышленном коде они обязательны!

Напишем программу в процедурном стиле. Как пример, возьмем публичный API [restcountries.com](https://restcountries.com/), получим по нему страну и на основе ее данных рассчитаем грубую плотность населения.

```php
function getCountry($name)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$url = 'https://restcountries.com/v3.1/name/';
	curl_setopt($ch, CURLOPT_URL, $url . $name);
	$result=curl_exec($ch);
	curl_close($ch);
	return json_decode($result, true);
}

function getCountryDensity($countryName)
{
	$country = getCountry($countryName);
	$population = $country[0]['population'];
	$area = $country[0]['area'];
	$density = $population / $area;
	if ($density < 100) {
		return 'low';
	} else if ($density < 300) {
		return 'medium';
	} else {
		return 'high';
	}
}
echo getCountryDensity('uk');
```

Наш код работает, в нем грамотно распределены зоны ответственности: есть источник данных (getCountry), есть доменная логика (getCountryDensity). Такой подход подойдет для одноразовых скриптов или коротких программ, но с ним сложно работать в сколько-нибудь серьезных проектах.

## От ассоциативного массива к классу

Начнем с того, что мы не знаем, какие поля и типы данных может содержать ответ. Давайте добавим их. В языках с поддержкой типа данных `record`, мы бы использовали его. В PHP мы используем `class`.

```php
class Country {
    /** @var string */
	public $name;
	/** @var float */
	public $area;
	/** @var int */
	public $population;
}

function getCountry($name) {
	// ...
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
	// ...
}
```

[Полный пример](get-countries-record.php)

Теперь нам не нужно каждый раз делать сетевой запрос, чтобы увидеть структуру объекта (по крайней мере, интересующей нас его части). Как альтернатива, можно было бы комментариями расписать, какие поля вернет запрос, но для этого пришлось бы строить дополнительные ассоциации в уме, что увеличивает когнитивную нагрузку.

## От функции к классу

Так как источник данных отделен, при желании, мы можем переехать на другой: вместо API читать данные из БД или файла. Но мы не можем динамически изменять его, когда захотим. Наш код негибок: для выполнения функции `getCountryDensity` всегда нужен API.

Здесь нам и понадобится ООП. Для начала, выделим функции в отдельные самодостаточные модули - классы.

```php
class CountryRepository {
	public function findByName($name) {
	    // ...
		$countryArr = json_decode($result, true);
		$country = new Country();
		$country->name = $countryArr[0]['name']['common'];
		$country->area = $countryArr[0]['area'];
		$country->population = $countryArr[0]['population'];
		return $country;
	}
}

class CountryService {
    /** @var CountryRepository */
	private $countries;
	
    /** @param CountryRepository $countries */
	public function __construct($countries) {
		$this->countries = $countries;
	}
	
	public function getCountryDensity($countryName) {
		$country = $this->countries->findByName($countryName);
	    // ...
	}
}

$countries = new CountryRepository();
$countryService = new CountryService($countries);
echo $countryService->getCountryDensity('uk');
```

У ООП есть порог входа: программу стало сложнее понять, она занимает больше строк. Но взамен мы получаем модульность, основу качественного программирования. Каждый класс представляет собой небольшую законченную программу.

## От класса к интерфейсу

В таком варианте класс `CountryService` все еще полагается на `CountryRepository` и подменить его сложно. Что делать? Используем интерфейс: контракт, который служебный класс должен выполнить. Если класс реализует (implements) интерфейс `CountryRepositoryInterface`, то у него должен иметься метод `findByName` с правильными параметрами и модификатором доступа.

```php
interface CountryRepositoryInterface {
    public function findByName($name);
}

class ApiCountryRepository implements CountryRepositoryInterface {
    // ...
}

class DatabaseCountryRepository implements CountryRepositoryInterface {
    // ...
}

class FileCountryRepository implements CountryRepositoryInterface {
    // ...
}

class CountryService {
    /** @var CountryRepositoryInterface */
	private $countries;
	
    /** @param CountryRepositoryInterface $countries */
	public function __construct($countries) {
		$this->countries = $countries;
	}
	// ...
}
```

Такой прием называется полиморфизмом. `CountryService` больше не зависит от конкретной реализации `CountryRepository`. Он будет работать с любым, кто реализует интерфейс `CountryRepositoryInterface`.

Давайте подменим `CountryRepository` на тестовую реализацию, которая хранит страны в памяти.

```php
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

$uk = new Country();
$uk->name = 'uk';
$uk->area = 10000.0;
$uk->population = 3000;
$countries = new FakeCountryRepository([$uk]);
$countryService = new CountryService($countries);
echo $countryService->getCountryDensity('uk');
```

[Полный пример](get-countries-interface.php)

Больше нам не нужно иметь полноценный API для проверки нашего кода. Проверять работу API все еще нужно, но это уже другая история.

## Назначение обязанностей

В нашем коде еще есть возможность для улучшения: расчет плотности требует только данные из объекта `Country`. Давайте перенесем его из `CountryService`.

Заодно закроем доступ извне для свойств объекта. В ООП мы даем доступ к ним только через методы. Такой прием называется инкапсуляцией. Он нужен для защиты от несанкционированных изменений данных.

```php
class Country {
    // ...
    
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

class CountryService {
    // ...
    
	public function getCountryDensity($countryName) {
		$country = $this->countries->findByName($countryName);
		return $country->getDensity();
	}
}
```

[Полный пример](get-countries-incapsulation.php)


Подобный подход называется Information Expert: "Обязанности должны быть назначены объекту, который владеет максимумом необходимой информации для выполнения обязанности" [^1]

[^1]: https://ru.wikipedia.org/wiki/GRASP

Наш код стал модульным и гибким:

- Country не зависит ни от чего, знает как рассчитывать значения на основе своих данных;
- CountryRepository отвечает только за получение стран по API, мы без проблем можем заменить его;
- CountryService служит как посредник и содержит очень мало логики.

## Заключение

Мы не трогали поведение кода, при этом изменили его структуру. Такой процесс называется рефакторингом. В результате мы получили более модульный код. Отдельные классы не зависят друг от друга. В любое время мы можем изменить способ получения данных, при этом не внося изменения в код клиента.

ООП может быть сложно понять: как будто он по большей части состоит из делегирования методов. Но в разделении ответственности и заключается его мощь.

Часто ли приходится менять источники данных? Нет. Иногда происходит переезд с одной базы данных на другую, но это - редкий случай. Гораздо чаще этот прием используется в модульном тестировании. Но про тестирование в другой раз.
