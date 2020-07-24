<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use GuzzleHttp\Client;

class ToBeOrNoToBeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $client = new \GuzzleHttp\Client();
        try{
            $response = $client->get('https://app.167-172-50-144.plesk.page/profils/'.$value);

            $status =$response->getStatusCode();

                $response = $response->getBody()->getContents();
                $profil= (array)(json_decode($response));

                if ($profil['affectation'] == 'Boulangerie' || $profil['affectation'] == '' || $profil['affectation'] == 'boulangerie' )
                    return;


        }catch(\GuzzleHttp\Exception\RequestException $e){}




        /* @var $constraint \App\Validator\ToBeOrNoToBe */

        if (null === $value || '' === $value) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
