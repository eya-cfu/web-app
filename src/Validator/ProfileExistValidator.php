<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ProfileExistValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {

        $client = new \GuzzleHttp\Client();
        try{
            $response = $client->get('https://app.167-172-50-144.plesk.page/profils/'.$value);

            $status =$response->getStatusCode();

        }catch (\GuzzleHttp\Exception\RequestException $e)
        {
            return;
        }




        /* @var $constraint \App\Validator\ProfileExist */

        if (null === $value || '' === $value) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
