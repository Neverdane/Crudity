<?php
namespace Neverdane\Crudity\Field;

use Neverdane\Crudity\Validator\EmailValidator;

class EmailField extends AbstractField implements FieldInterface {

    protected function initializeValidators()
    {
        $this->validators = array(
            new EmailValidator()
        );
    }
    public static function getIdentifiers() {
        return array(
            "tagName"       => "input",
            "attributes"    => array(
                "type"  => "email"
            )
        );
    }

}
