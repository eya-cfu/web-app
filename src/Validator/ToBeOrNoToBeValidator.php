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
        $response = $client->get('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/profils/'.$value);

        $status =$response->getStatusCode();


        if($status != 404)
        {
            $response = $response->getBody()->getContents();
            $profil= (array)(json_decode($response));

            if ($profil['affectation'] == 'Boulangerie' || $profil['affectation'] == '' )
            return;
        }

        /* @var $constraint \App\Validator\ToBeOrNoToBe */

        if (null === $value || '' === $value) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
