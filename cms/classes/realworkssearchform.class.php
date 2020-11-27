<?php

/**
 * An API wrapper class that relays searcher form submissions to the Realworks API.
 */
class RealworksSearchForm
{
    /**
     * The constant base URL of the Realworks API.
     * @var string
     */
    public const BASE_URL = 'https://api.realworks.nl/';

    /**
     * The payload buffer that contains any data to be send to the form endpoint.
     * @var array
     */
    private array $payload = [];

    /**
     * The cached location data fetched from the locations endpoint.
     * @var array
     */
    private array $locations = [
        'zoekgebieden' => [],
        'plaatsen' => [],
    ];

    /**
     * The Realworks authorization token for this organization.
     * @var string
     */
    private string $token;

    /**
     * The Realworks department code ("afdelingscode") for this organization.
     * @var string
     */
    private int $department;

    /**
     * Creates a new Realworks search form instance based on a valid Realworks token and
     * department code.
     * 
     * @param string $token The Realworks authorization token for this organization.
     * @param int $department The Realworks department code for this organization.
     */
    public function __construct(string $token, int $department)
    {
        $this->token = $token;
        $this->department = $department;

        $this->payload = [
            'zoekopdracht' => [
                'basis' => [
                    'afdelingscode' => strval($department),
                    'alleenEigenAanbod' => true,
                    'automatischeVerwerking' => false, // TODO: Check the meaning of this value
                    'betalendeKlant' => false,
                    'einddatum' => '2018-04-18', // TODO: Fill out this value
                    'medewerkercode' => '100123', // TODO: Figure out what this value means
                    'realtime' => false,
                    'status' => 'LOPEND',
                    'verstuurPerPost' => false,
                ],
                'diversen' => [], // Including an empty object in your JSON... Very nice, Realworks
                'locaties' => [
                    // TODO: Fill out these two arrays here
                    'zoekgebieden' => [],
                    'plaatsen' => [],
                ],

                // TODO: Fill out all fields below this comment

                'relatie' => [
                    'persoon' => [
                        'achternaam' => 'Puk',
                        'email' => 'example@example.com',
                        'geslacht' => 'MAN',
                        'huisnummer' => '58',
                        'huisnummertoevoeging' => 'bis',
                        'initialen' => 'P.P.',
                        'land' => 'Nederland',
                        'mobielTelefoonnummer' => '06-12345678',
                        'postcode' => '1012AD',
                        'roepnaam' => 'Pietje',
                        'straat' => 'Prins Hendrikkade',
                        'telefoonnummer' => '020-1234567',
                        'titel' => 'De heer',
                        'tussenvoegsel' => 'van der',
                        'woonplaats' => 'Amsterdam'
                    ],
                    'referentie' => [
                        'relatiecode' => 'string',
                        'relatiesoort' => 'BEDRIJF'
                    ]
                ],
                'woonwens' => [
                    'aantalSlaapkamersVanaf' => 0,
                    'appartementsoorten' => [
                        'BOVENWONING'
                    ],
                    'badkamerOpBeganeGrond' => false,
                    'balkonPatioDakterras' => false,
                    'bestaandeBouw' => false,
                    'bouwjaarTotEnMet' => 0,
                    'bouwjaarVanaf' => 0,
                    'garage' => [
                        'GARAGE'
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
                        'AAN_BOSRAND'
                    ],
                    'nieuwbouw' => false,
                    'objectsoort' => 'WOONHUIS_OF_APPARTEMENT',
                    'perceelOppervlakteVanaf' => 0,
                    'permanenteBewoning' => false,
                    'recreatiewoning' => false,
                    'slaapkamerOpBeganeGrond' => false,
                    'tuinliggingen' => [
                        'NOORD'
                    ],
                    'woningsoorten' => [
                        'EENGEZINSWONING'
                    ],
                    'woningtypes' => [
                        'VRIJSTAANDE_WONING'
                    ],
                    'woonInhoudVanaf' => 0,
                    'woonOppervlakteVanaf' => 0,
                    'woonkamerOppervlakteVanaf' => 0
                ]
            ],
        ];
    }

    /**
     * Gets a field from the payload based on a dot (.) separated path to the value.
     * 
     * @param string $path The path to the value.
     * @return mixed
     */
    public function get_payload_field(string $path)
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
    public function set_payload_field(string $path, $value): void
    {
        $keys = explode('.', $path);

        // Take a temporary copy of the value and get started with some memory address magic
        $temp = $value;
        $value = &$this->payload;

        foreach ($keys as $key) // Even more memory address magic to get the address of the new key in the array
            $value = &$value[$key];

        $value = $temp;
    }

    /**
     * Fetches the location data for this organization and cache them within the instance
     * to cache them, as multiple requests to this endpoint are probably not required.
     * 
     * @return void
     */
    public function fetch_locations(): void
    {
        // Send a request to the locations endpoint, along with this organization's department code
        $response = $this->request('/wonen/v1/zoekopdracht/locaties?afdelingscode=' . $this->department);

        $zoekgebieden = [];
        $plaatsen = [];

        // In the following loop, we are going to process all items in the results array
        // that we received from the API. We will then map each result's "naam" property
        //to its corresponding ID.
        foreach ($response['resultaten'] as $result) {

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
    public function request(string $endpoint, bool $json = true)
    {
        // Prepare the API URL and initialize a cURL handle for the request
        $url = rtrim(self::BASE_URL, '/') . '/' . trim($endpoint, '/');
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'Pixelplus/1.0', // Let's be nice and add a user agent string to our request
            CURLOPT_HTTPHEADER => [
                'Authorization: rwauth ' . $this->token,
                'Accept: application/json;charset=UTF-8',
            ],
            // Who cares about SSL, anyways? /s
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);

        // If the request didn't return a successful response code, return null
        if ($info['http_code'] < 200 || $info['http_code'] >= 300)
            return null;

        if ($json === true) // Check if the expected output is JSON and decode it as such
            $result = json_decode($result, true);

        return $result;
    }

    /**
     * Returns the payload buffer that contains any data to be send to the form endpoint.
     * @return array
     */
    public function payload(): array
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
    public function locations(?string $key = null): array
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
    public function token(): string
    {
        return $this->token;
    }

    /**
     * Returns the Realworks department code ("afdelingscode") for this organization.
     * @return string
     */
    public function department(): int
    {
        return $this->department;
    }
}

$search_form = new RealworksSearchForm('e2ed5b0a-d544-409b-aa06-7f3a875c2403', 44003);
$search_form->fetch_locations();

var_dump($search_form->locations('zoekgebieden'));
var_dump($search_form->locations('plaatsen'));

echo $search_form->get_payload_field('zoekopdracht.basis.afdelingscode');

$search_form->set_payload_field('test.some.property', 'Hello');
var_dump($search_form->payload());
