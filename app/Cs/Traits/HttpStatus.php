<?php
namespace Cs\Router\Util;

use \Exception;

Trait HttpStatus {
    private function getStatusCode(Array $result): Integer {
        $code = 200;
        if (\array_key_exists('statusCode', $result) === true) {
            $this->isNumber(
                $result['statusCode'], 'status.code.should.be.an.integer'
            );
            $code = $result['statusCode'];
        }

        return $code;
    }
}