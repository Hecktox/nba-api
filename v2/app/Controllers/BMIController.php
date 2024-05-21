<?php

namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Exceptions\HttpInvalidInputException;
use Vanier\Api\Exceptions\HttpInvalidBMIUnitException;
use Vanier\Api\Helpers\InputsHelper;
use Vanier\Api\Exceptions\HttpNoContentException;
use Vanier\Api\Exceptions\HttpInvalidPaginationParameterException;
use Vanier\Api\Validations\Validator;
require_once("validation/validation/Validator.php");

class BMIController extends BaseController
{

    public function __construct(){

    }

    public function handleComputeBMI(Request $request, Response $response, array $uri_args): Response{
        //1. Retrieve results from the body of the request
        $params = $request->getParsedBody();

        //2. Implement the variables needed to compute BMI
        if (!empty($params)){
            $height = $params['height'];
            $weight = $params['weight'];
            $unit = $params['unit'];
            $gender = $params['gender'];
        }
        

        $v = new Validator($params);
        $rules = array(
            'height' => array(
                'required',
                array('regex', '/^\d+(\.\d+)?$/')
            ),
            'weight' => array(
                'required',
                array('regex', '/^\d+$/')
            ),
            'unit' => array(
                'required',
                array('regex', '/^(Metric|Imperial)$/')
            ),
            'gender' => [
                array('regex', '/^[A-Z][a-z]+$/'),
            ]
        );

        $v->mapFieldsRules($rules);

        //How to throw appropriate exception
        if($v->validate()){

            //3. Compute BMI using variables and formula
            if ($unit == "Metric"){
                $heightSqrd = $height * $height;
                $bmi = $weight / $heightSqrd;
            } else if ($unit == "Imperial"){
                $heightSqrd = $height * $height;
                $bmiUnprocessed = $weight / $heightSqrd;
                $bmi = $bmiUnprocessed * 703;

            } else {
                throw new HttpInvalidBMIUnitException($request);
            }

            //4. Return BMI chart result 
            switch($bmi){
                case ($bmi < 18.5):
                    $classification = "Underweight";
                    break;
                    case ($bmi >= 18.5 && $bmi <= 24.9):
                // case (18.5 <= $bmi <= 24.9):
                    $classification = "Normal";
                    break;
                    case ($bmi >= 25.0 && $bmi <= 29.9):
                //case (25.0 <= $bmi <= 29.9):
                    $classification = "Overweight";
                    break;
                    case ($bmi >= 30.0 && $bmi <= 40.0):
                //case (30.0 <= $bmi <= 40.0):
                    $classification = "Obese";
                    break;
                case (40.0 < $bmi):
                    $classification = "Extremely obese";
                    break;
            }
    
            //4. Return result and array keys
            return $this->makeResponse($response, ['Your BMI is' => round($bmi, 1), 'Your classification is' => $classification]);


        } else {
            // print_r($v->errors());
            
        }

        $response_data = array(
            "code" => "failure",
            "message" => "BMI computation error.",
            "errors" =>$v->errors()
        );

        return $this->makeResponse($response, $response_data, 500);
    }
        
}



