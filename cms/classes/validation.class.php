<?php

class Validation {
    
    public function validateForm() {

	// Function accepts an unlimited amount of arguments, hence dynamic
	$arguments = func_get_args();
        $argcount = func_num_args();

        // First argument is always the type
        $typeVar = $arguments[0];

        // Second argument is always the error message
        $message = $arguments[1];

        // Third argument is always the full array
        $arrayData = $arguments[2];

        // Empty errorArray
        $errorArray = array();

        // Now loop through remaining arguments
        for ($i = 3; $i < $argcount; $i++) {

            if (isset($arguments[$i])) {

                $splitArg = explode('$', $arguments[$i]);
                
                // Explode the type?
                $multiTypes = explode('|', $typeVar);
                
                foreach ($multiTypes as $key => $type) {

                    switch($type) {

                        case 'empty':

                            if (!isset($arrayData[$splitArg[0]]) || (isset($arrayData[$splitArg[0]]) && empty($arrayData[$splitArg[0]]) && $arrayData[$splitArg[0]] != '0')) {

                                $errorArray[$splitArg[0] . '_errorStatus_empty'] = /*$splitArg[1] . ': ' . */$message;
                            }

                        break;

                        case 'date':

                            if (isset($arrayData[$splitArg[0]]) && !empty($arrayData[$splitArg[0]])) {

                                $date = Core::convertDateTimeFormat($arrayData[$splitArg[0]]);

                                if ($date) {

                                    $date = new PP_DateTime($date);

                                    if (!checkdate($date->format('n'), $date->format('j'), $date->format('Y')))
                                        $errorArray[$splitArg[0] . '_errorStatus_date'] = /*$splitArg[1] . ': ' . */$message;
                                }
                                else
                                    $errorArray[$splitArg[0] . '_errorStatus_date'] = /*$splitArg[1] . ': ' . */$message;
                            }

                        break;

                        case 'number':

                            if (isset($arrayData[$splitArg[0]]) && !empty($arrayData[$splitArg[0]])) {

                                if (!is_numeric($arrayData[$splitArg[0]]))
                                    $errorArray[$splitArg[0] . '_errorStatus_number'] = /*$splitArg[1] . ': ' . */$message;
                            }

                        break;

                        case 'email':

                            if (isset($arrayData[$splitArg[0]]) && !empty($arrayData[$splitArg[0]])) {

                                if (!filter_var($arrayData[$splitArg[0]], FILTER_VALIDATE_EMAIL))
                                    $errorArray[$splitArg[0] . '_errorStatus_email'] = /*$splitArg[1] . ': ' . */$message;
                            }

                        break;
                    }
                }
            }
        }

        return $errorArray;
    }
}

?>