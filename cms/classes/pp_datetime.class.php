<?php

// Base source: http://www.php.net/manual/en/class.datetime.php

class PP_DateTime extends DateTime {

    /**
     * Return Date in ISO8601 format
     *
     * @return String
     */
    public function __toString() {
        return $this->format('d-m-Y');
    }

    /**
     * Return difference between $this and $now
     *
     * @param Datetime|String $now
     * @return DateInterval
     */
    /*public function diff($now = 'NOW') {
        if(!($now instanceOf DateTime)) {
            $now = new DateTime($now);
        }
        return parent::diff($now);
    }*/

    /**
     * Return Age in Years
     *
     * @param Datetime|String $now
     * @return Integer
     */
    public function getAge($now = 'NOW') {
        return $this->diff($now)->format('%y');
    }
    
    static function convertDate($date, $format = 'Y-m-d H:i:s', $newFormat = 'd-m-Y H:i:s') {
    	
    	$d = DateTime::createFromFormat($format, $date);
    	
    	return $d->format($newFormat);
    }

}

?>