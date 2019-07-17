<?php
namespace Cs\Router\Services;

use Psr\Http\Message\ResponseInterface as Response;
use Cs\Router\Util\Assert;

Class ResponseHandler extends Assert {
    private $responseHandler;

    public function setResponse(Response $response, Array $result) {
        $this->responseHandler = $response;
        if (isset($result['responseType']) === false) {
            return $this->respondAsJson($result);
        }

        return $this->respondAsDownload($result);
    }

    public function respondAsJson($result) {
        $this->arrayNotEmpty($result, 'empty.result.given');
        $this->inArray('status', $result, 'result.does.not.have.status.key');
        $this->isBool($result['status'], 'result.status.is.not.a.boolean');

        return $this->responseHandler->withJson($result);
    }

    public function respondAsDownload($result) {
        $this->inArray('file', $result, 'file.key.not.found');
        $this->isEmpty($result['file'], 'file.steam.not.found');
        $this->inArray('contentType', $result, 'contentType.not.found');
        $this->inArray('fileName', $result, 'fileName.not.found');
        $this->inArray('fileSize', $result, 'fileSize.not.found');

        $response = $this->responseHandler
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
}