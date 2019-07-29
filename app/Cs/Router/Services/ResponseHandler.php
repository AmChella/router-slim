<?php
namespace Cs\Router\Services;

use Psr\Http\Message\ResponseInterface as Response;
use Cs\Router\Util\Assert;

Class ResponseHandler extends Assert {
    private $responseHandler;

    public function setResponse(Response $response, Array $result, $type = 'json') {
        $this->body = $response;
        switch(strtolower($type)) {
            case 'json':
                return $this->jsonResponse($result);
            case 'raw':
                return $this->rawResponse($result);
            case 'download':
                return $this->downloadResponse($result);
            default:
                return $this->jsonResponse($result);
        }
    }

    public function jsonResponse($result, $statusCode = 200) {
        $this->arrayNotEmpty($result, 'empty.result.given');
        $this->inArray('status', $result, 'result.does.not.have.status.key');
        $this->isBool($result['status'], 'result.status.is.not.a.boolean');

        return $this->body->withJson($result, $statusCode);
    }

    public function downloadResponse($result) {
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

    public function rawResponse($result) {
        return $this->body->write($result['data']);
    }
}