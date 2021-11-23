<?php
namespace Cs\Router\Traits;

use \Exception;

Trait HttpStatus {
    private function getStatusCode($result): Int {
        $code = 200;
        if (
            \is_array($result)  === true && 
            \array_key_exists('statusCode', $result) === true
        ) {
            $this->isNumber(
                $result['statusCode'], 'status.code.should.be.an.integer'
            );

            $code = $result['statusCode'];
            if ($result['statusCode'] > 599) {
                $code = 500;
            }
        }

        return $code;
    }

    private function getStatus(Int $code): Bool {
        $status = false;
        if ($code >= 200 && $code < 300) {
            $status = true;
        }

        return $status;
    }
}