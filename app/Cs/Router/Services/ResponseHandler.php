<?php
namespace Cs\Router\Services;

use Psr\Http\Message\ResponseInterface as Response;
use Cs\Router\Util\Assert;
use Cs\Router\Traits\HttpStatus;

Class ResponseHandler extends Assert {
    use HttpStatus;
    /**
     * setResponse
     *
     * @param  mixed $response
     * @param  mixed $result
     * @param  mixed $type
     *
     * @return void
     */
    public function setResponse(
        Response $response, Array $result, $type = 'raw'
    ) {
        $this->body = $response;
        $code = $this->getStatusCode($result);
        switch(strtolower($type)) {
            case 'json':
                return $this->getJsonResponse($result, $code);
            case 'raw':
                return $this->getRawResponse($result, $code);
            case 'download':
                return $this->getDownloadResponse($result, $code);
            default:
                return $this->getRawResponse($result, $code);
        }
    }

    /**
     * jsonResponse
     *
     * @param  mixed $result
     * @param  mixed $statusCode
     *
     * @return void
     */
    private function getJsonResponse($result, $statusCode = 200) {
        $this->arrayNotEmpty($result, 'empty.result.given');
        $this->inArray('status', $result, 'result.does.not.have.status.key');
        $this->isBool($result['status'], 'result.status.is.not.a.boolean');

        return $this->body->withJson($result)->withStatus($statusCode);
    }

    /**
     * downloadResponse
     *
     * @param  mixed $result
     *
     * @return void
     */
    public function getDownloadResponse($result) {
        $this->arrayKeyExists('file', $result, 'file.key.not.found');
        $this->isEmpty($result['file'], 'file.stream.is.empty.found');
        $this->arrayKeyExists('contentType', $result, 'contentType.not.found');
        $this->arrayKeyExists('fileName', $result, 'fileName.not.found');
        $this->arrayKeyExists('fileSize', $result, 'fileSize.not.found');

        $response = $this->body
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Type', $result['contentType'])
            ->withHeader(
                'Content-Disposition',
                'attachment;filename="'. basename($result['fileName']) . '"'
            )
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate')
            ->withHeader('Pragma', 'public')
            ->withHeader('Content-Length', $result['fileSize']);

            echo $result['file'];

        return $response;
    }

    /**
     * rawResponse
     *
     * @param  mixed $result
     *
     * @return void
     */
    private function getRawResponse($result, $statusCode = 200) {
        return $this->body->write($result['data'])->withStatus($statusCode);
    }
}