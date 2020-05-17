<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TrueLivreurValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\TrueLivreur */

        if (null === $value || '' === $value) {
            return;
        }

        $client = new \GuzzleHttp\Client();
        $response = $client->get('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/profils/'.$value);

        $status =$response->getStatusCode();


        if($status != 404)
        {
            $response = $response->getBody()->getContents();
            $profil= (array)(json_decode($response));

            if ($profil['affectation'] == 'Livreur' || $profil['affectation'] == '' )
                return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
