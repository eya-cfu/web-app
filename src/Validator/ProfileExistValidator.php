<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ProfileExistValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {

        $client = new \GuzzleHttp\Client();
        $response = $client->get('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/profils/'.$value);

        $status =$response->getStatusCode();

        if($status != 200)
            return;

        /* @var $constraint \App\Validator\ProfileExist */

        if (null === $value || '' === $value) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
