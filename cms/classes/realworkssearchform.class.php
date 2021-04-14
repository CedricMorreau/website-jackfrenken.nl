<?php

// Search form URL:
// https://www.jackfrenken.nl/diensten/inschrijven-als-zoeker

/**
 * An API wrapper class that relays searcher form submissions to the Realworks API.
 */
class RealworksSearchForm
{
    const FIELD_GENDER_MALE = 'MAN';
    const FIELD_GENDER_FEMALE = 'VROUW';
    const FIELD_GENDER_OTHER = 'ONBEKEND';

    /**
     * The constant base URL of the Realworks API.
     * @var string
     */
    const BASE_URL = 'https://api.realworks.nl/';

    /**
     * The payload buffer that contains any data to be send to the form endpoint.
     * @var array
     */
    private $payload = [];

    /**
     * The cached location data fetched from the locations endpoint.
     * @var array
     */
    private $locations = [
        'zoekgebieden' => [],
        'plaatsen' => [],
    ];

    /**
     * The Realworks authorization token for this organization.
     * @var string
     */
    private $token;

    /**
     * The Realworks department code ("afdelingscode") for this organization.
     * @var string
     */
    private $department;

    /**
     * Creates a new Realworks search form instance based on a valid Realworks token and
     * department code.
     * 
     * @param string $token The Realworks authorization token for this organization.
     * @param int $department The Realworks department code for this organization.
     */
    public function __construct($token, $department, $code = null)
    {
        $this->token = $token;
        $this->department = $department;

        $this->payload = [
            'zoekopdracht' => [
                'basis' => [
                    'afdelingscode' => strval($this->department),
                    'alleenEigenAanbod' => true,
                    'automatischeVerwerking' => false, // TODO: Check the meaning of this value
                    'betalendeKlant' => false,
                    'einddatum' => '2038-01-01',
                    'medewerkercode' => '',
                    'realtime' => false,
                    'status' => 'LOPEND',
                    'verstuurPerPost' => false,
                ],
                'diversen' => [
                    // If we leave out the below value, the API will not work for some reason...
                    'diversen' => 0,
                ],
                'locaties' => [
                    'zoekgebieden' => [],
                    'plaatsen' => [],
                ],
                'relatie' => [
                    'persoon' => [
                        'achternaam' => null,
                        'email' => 'example@example.com',
                        'geslacht' => 'ONBEKEND',
                        'huisnummer' => null,
                        'huisnummertoevoeging' => null,
                        'initialen' => null,
                        'land' => 'Nederland',
                        'mobielTelefoonnummer' => null,
                        'postcode' => null,
                        'roepnaam' => null,
                        'straat' => null,
                        'telefoonnummer' => null,
                        'titel' => 'De heer',
                        'tussenvoegsel' => null,
                        'woonplaats' => null,
                    ],
                    /*
                    'referentie' => [
                        'relatiesoort' => 'PARTICULIER',
                    ]
                    */
                ],
                'woonwens' => [
                    'aantalSlaapkamersVanaf' => 0,
                    'appartementsoorten' => [],
                    'badkamerOpBeganeGrond' => false,
                    'balkonPatioDakterras' => false,
                    'bestaandeBouw' => false,
                    'bouwjaarTotEnMet' => 0,
                    'bouwjaarVanaf' => 0,
                    'garage' => [
                        /*
                        'GARAGE',
                        'BERGING',
                        'PARKEERPLAATS',
                        'GARAGE_OF_BERGING',
                        'GARAGE_OF_PARKEERPLAATS',
                        'BERGING_OF_PARKEERPLAATS',
                        */
                        'GARAGE_OF_BERGING_OF_PARKEERPLAATS',
                    ],
                    'gedeeltelijkGestoffeerd' => false,
                    'gemeubileerd' => false,
                    'gestoffeerd' => false,
                    'huurprijsTotEnMet' => 0,
                    'huurprijsVanaf' => 0,
                    'koopprijsTotEnMet' => 0,
                    'koopprijsVanaf' => 0,
                    'lift' => false,
                    'liggingen' => [
                        'AAN_BOSRAND',
                        'AAN_WATER',
                        'AAN_PARK',
                        'AAN_DRUKKE_WEG',
                        'AAN_RUSTIGE_WEG',
                        'IN_CENTRUM',
                        'IN_WOONWIJK',
                        'VRIJ_UITZICHT',
                        'BESCHUTTE_LIGGING',
                        'OPEN_LIGGING',
                        'BUITEN_BEBOUWDE_KOM',
                        'AAN_VAARWATER',
                        'IN_BOSRIJKE_OMGEVING',
                        'LANDELIJK_GELEGEN',
                        'ZEEZICHT',
                        'BEDRIJVENTERREIN',
                    ],
                    'nieuwbouw' => false,
                    'objectsoort' => 'WOONHUIS_OF_APPARTEMENT',
                    'perceelOppervlakteVanaf' => 0,
                    'permanenteBewoning' => false,
                    'recreatiewoning' => false,
                    'slaapkamerOpBeganeGrond' => false,
                    'tuinliggingen' => [
                        'NOORD', 'OOST', 'ZUID', 'WEST',
                    ],
                    'woningsoorten' => [],
                    'woningtypes' => [],
                    'woonInhoudVanaf' => 0,
                    'woonOppervlakteVanaf' => 0,
                    'woonkamerOppervlakteVanaf' => 0
                ]
            ],
        ];

        $this->set_all_appartementsoorten();

        $this->set_all_woningsoorten();
        $this->set_all_woningtypes();

        if ($code !== null)
            $this->set_medewerkercode($code);
    }

    public function set_medewerkercode($code)
    {
        $this->set_payload_field('zoekopdracht.basis.medewerkercode', $code);
    }

    public function set_contact_info($fname, $infix, $lname, $phone, $mobile, $email, $gender = 'ONBEKEND')
    {
        $initials = strtoupper($fname[0]) . '.' . strtoupper($lname[0]) . '.';
        $phone = preg_replace('/[^\d]+/', '', $phone);

        $this->set_payload_fields_array([
            'zoekopdracht.relatie.persoon.roepnaam' => $fname,
            'zoekopdracht.relatie.persoon.achternaam' => $lname,
            'zoekopdracht.relatie.persoon.initialen' => $initials,
            'zoekopdracht.relatie.persoon.geslacht' => $gender,
            'zoekopdracht.relatie.persoon.telefoonnummer' => $phone,
            'zoekopdracht.relatie.persoon.email' => strtolower($email),
        ]);

        $title = [
            self::FIELD_GENDER_MALE => 'Dhr.',
            self::FIELD_GENDER_FEMALE => 'Mevr.',
            self::FIELD_GENDER_OTHER => 'Fam.',
        ];

        $title = isset($title[$gender]) ? $title[$gender] : 'Fam.';
        $this->set_payload_field('zoekopdracht.relatie.persoon.titel', $title);

        if ($infix !== null && !empty($infix))
            $this->set_payload_field('zoekopdracht.relatie.persoon.tussenvoegsel', $infix);

        if ($mobile !== null && !empty($mobile)) {
            $mobile = preg_replace('/[^\d]/', '', $mobile);
            $this->set_payload_field('zoekopdracht.relatie.persoon.mobielTelefoonnummer', $mobile);
        }
    }

    public function set_rent_range($min, $max)
    {
        if ($min !== null)
            $this->set_payload_field('zoekopdracht.woonwens.huurprijsVanaf', $min);

        if ($max !== null)
            $this->set_payload_field('zoekopdracht.woonwens.huurprijsTotEnMet', $max);
    }

    public function set_purchase_range($min, $max)
    {
        if ($min !== null)
            $this->set_payload_field('zoekopdracht.woonwens.koopprijsVanaf', $min);

        if ($max !== null)
            $this->set_payload_field('zoekopdracht.woonwens.koopprijsTotEnMet', $max);
    }

    public function set_locations($key, array $values)
    {
        if (!isset($this->locations[$key]))
            throw new \Exception("Location key '$key' does not exist");

        $keys = [];

        foreach ($this->locations[$key] as $id => $location) {
            foreach ($values as $value) {

                if (strtolower($location) === strtolower($value))
                    $keys[] = $id;
            }
        }

        $this->set_payload_field('zoekopdracht.locaties.' . $key, $keys);
    }

    public function set_all_locations($key)
    {
        if (!isset($this->locations[$key]))
            throw new \Exception("Location key '$key' does not exist");

        $this->set_payload_field('zoekopdracht.locaties.' . $key, array_keys($this->locations[$key]));
    }

    public function set_objectsoort($value)
    {
        $this->set_payload_field('zoekopdracht.woonwens.objectsoort', strtoupper($value));
    }

    public function set_appartementsoorten($values)
    {
        $this->set_payload_field('zoekopdracht.woonwens.appartementsoorten',
            array_map(function ($item) { return strtoupper($item); }, $values));
    }

    public function set_all_appartementsoorten()
    {
        $this->set_payload_field('zoekopdracht.woonwens.appartementsoorten', [
            'BOVENWONING',
            'BENEDENWONING',
            'MAISONNETTE',
            'GALERIJFLAT',
            'PORTIEKFLAT',
            'BENEDEN_PLUS_BOVENWONING',
            'PENTHOUSE',
            'PORTIEKWONING',
            'STUDENTENKAMER',
            'DUBBEL_BENEDENHUIS',
            'TUSSENVERDIEPING',
        ]);
    }

    public function set_woningsoorten($values)
    {
        $this->set_payload_field('zoekopdracht.woonwens.woningsoorten',
            array_map(function ($item) { return strtoupper($item); }, $values));
    }

    public function set_all_woningsoorten()
    {
        $this->set_payload_field('zoekopdracht.woonwens.woningsoorten', [
            'EENGEZINSWONING',
            'HERENHUIS',
            'VILLA',
            'LANDHUIS',
            'BUNGALOW',
            'WOONBOERDERIJ',
            'GRACHTENPAND',
            'WOONBOOT',
            'STACARAVAN',
            'WOONWAGEN',
            'LANDGOED',
        ]);
    }

    public function set_woningtype($value)
    {
        $value = strtoupper($value);

        $value = preg_replace('/[^A-Z\d]+/', '_', $value);
        $value = preg_replace('/_+/', '_', $value);

        $replace = [
            1 => 'EEN',
            2 => 'TWEE',
        ];

        $value = str_replace(array_keys($replace), array_values($replace), $value);
        $this->set_payload_field('zoekopdracht.woonwens.woningtypes', [$value]);
    }

    public function set_all_woningtypes()
    {
        $this->set_payload_field('zoekopdracht.woonwens.woningtypes', [
            'VRIJSTAANDE_WONING',
            'GESCHAKELDE_WONING',
            'TWEE_ONDER_EEN_KAPWONING',
            'TUSSENWONING',
            'HOEKWONING',
            'EINDWONING',
            'HALFVRIJSTAANDE_WONING',
            'GESCHAKELDE_TWEE_ONDER_EEN_KAPWONING',
            'VERSPRINGEND',
        ]);
    }

    public function set_min_perceeloppervlakte($value)
    {
        $this->set_payload_field('zoekopdracht.woonwens.perceelOppervlakteVanaf', strval($value));
    }

    public function set_min_slaapkamers($value)
    {
        $this->set_payload_field('zoekopdracht.woonwens.aantalSlaapkamersVanaf', strval($value));
    }

    public function set_contact_address($street, $number, $addition, $zipcode, $city)
    {
        $zipcode = preg_replace('/[^\dA-Z]+/', '', strtoupper($zipcode));

        $this->set_payload_fields_array([
            'zoekopdracht.relatie.persoon.straat' => $street,
            'zoekopdracht.relatie.persoon.huisnummer' => strval($number),
            'zoekopdracht.relatie.persoon.postcode' => $zipcode,
            'zoekopdracht.relatie.persoon.woonplaats' => $city,
        ]);

        if ($addition !== null && !empty($addition))
            $this->set_payload_field('zoekopdracht.relatie.persoon.huisnummertoevoeging', $addition);
    }

    /**
     * Gets a field from the payload based on a dot (.) separated path to the value.
     * 
     * @param string $path The path to the value.
     * @return mixed
     */
    public function get_payload_field($path)
    {
        $keys = explode('.', $path);
        $value = $this->payload;

        foreach ($keys as $key) {

            if (!isset($value[$key]))
                return null;

            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Sets a field from the payload based on a dot (.) separated path to the value.
     * 
     * @param string $path The path to the value.
     * @param mixed $value The value to set to the path.
     * 
     * @return void
     */
    public function set_payload_field($path, $value)
    {
        $keys = explode('.', $path);

        // Take a temporary copy of the value and get started with some memory address magic
        $temp = $value;
        $value = &$this->payload;

        foreach ($keys as $key) // Even more memory address magic to get the address of the new key in the array
            $value = &$value[$key];

        $value = $temp;
    }

    public function set_payload_fields_array($values)
    {
        foreach ($values as $path => $value)
            $this->set_payload_field($path, $value);
    }

    /**
     * Fetches the location data for this organization and cache them within the instance
     * to cache them, as multiple requests to this endpoint are probably not required.
     * 
     * @return void
     */
    public function fetch_locations()
    {
        // Send a request to the locations endpoint, along with this organization's department code
        $response = $this->request('/wonen/v1/zoekopdracht/locaties?afdelingscode=' . $this->department);

        $zoekgebieden = [];
        $plaatsen = [];

        // In the following loop, we are going to process all items in the results array
        // that we received from the API. We will then map each result's "naam" property
        //to its corresponding ID.
        foreach ($response['result']['resultaten'] as $result) {

            foreach ($result['zoekgebieden'] as $zoekgebied) {
                $key = intval($zoekgebied['id']);
                $zoekgebieden[$key] = $zoekgebied['naam'];
            }

            foreach ($result['plaatsen'] as $plaats) {
                $key = intval($plaats['id']);
                $plaatsen[$key] = $plaats['naam'];
            }
        }

        // Assign the fetched and parsed values to this instance's cache to fetch them later
        $this->locations['zoekgebieden'] = $zoekgebieden;
        $this->locations['plaatsen'] = $plaatsen;
    }

    /**
     * Sends a HTTP request to the provided endpoint, intended to fetch data from a GET
     * endpoint. The endpoint is appended to the BASE_URL.
     * 
     * @param string $endpoint The GET endpoint to send a request to.
     * @param bool $json Determines whether the output is expected to be JSON, and will be parsed as such.
     * 
     * @return string|array|null
     */
    public function request($endpoint, $json = true, $post_fields = [])
    {
        // Prepare the API URL and initialize a cURL handle for the request
        $ch = curl_init($this->url($endpoint));

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'Pixelplus/1.0', // Let's be nice and add a user agent string to our request
            CURLOPT_HTTPHEADER => [
                'Authorization: rwauth ' . $this->token,
                'Accept: application/json;charset=UTF-8',
                'Content-Type: application/json', // An undocumented header that has to be added, apparently...
            ],
            // Who cares about SSL, anyways? /s
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        if (count($post_fields) > 0) {
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($post_fields),
            ]);
        }

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);

        if ($json === true) // Check if the expected output is JSON and decode it as such
            $result = json_decode($result, true);

        return ['info' => $info, 'result' => $result];
    }

    /**
     * Sends the current payload to Realworks for further processing. It will
     * return a boolean according to whether the request was successful or not.
     * 
     * @return bool
     */
    public function send()
    {
        $response = $this->request('/wonen/v1/zoekopdracht', true, $this->payload);
        return $response['info']['http_code'] === 200;
    }

    /**
     * Builds the endpoint URL with the base URL appended to it based
     * on the provided endpoint URI.
     * 
     * @param string $endpoint The endpoint URI to process.
     * @return string
     */
    public function url($endpoint)
    {
        return rtrim(self::BASE_URL, '/') . '/' . trim($endpoint, '/');
    }

    /**
     * Returns the payload buffer that contains any data to be send to the form endpoint.
     * @return array
     */
    public function payload()
    {
        return $this->payload;
    }

    /**
     * Returns previously fetched location data from the locations endpoint. If no key is
     * provided or the key is null, the entire cache will be returned. Otherwise, a specific
     * value that was mapped to this key in the cache will be returned if it exists.
     * 
     * @param string|null $key The optional key within the cache of which the value should be returned.
     * 
     * @return array
     * @throws \Exception
     */
    public function locations($key = null)
    {
        if ($key === null)
            return $this->locations;

        if (!isset($this->locations[$key]))
            throw new \Exception("Invalid locations cache key: '$key'");

        return $this->locations[$key];
    }

    /**
     * Returns the Realworks authorization token for this organization.
     * @return string
     */
    public function token()
    {
        return $this->token;
    }

    /**
     * Returns the Realworks department code ("afdelingscode") for this organization.
     * @return string
     */
    public function department()
    {
        return $this->department;
    }
}
