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
        try{
            $response = $client->get('https://app.167-172-50-144.plesk.page/profils/'.$value);

            $status =$response->getStatusCode();

            if($status >= 200 && $status<300)
            {$response = $response->getBody()->getContents();
                $profil= (array)(json_decode($response));

                if ($profil['affectation'] == 'Livreur' || $profil['affectation'] == '' || $profil['affectation'] == 'livreur' )
                    return;
            }

        }catch (\GuzzleHttp\Exception\RequestException $e) {}


        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
