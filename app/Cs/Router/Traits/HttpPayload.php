<?php
namespace Cs\Router\Traits;

use Psr\Http\Message\ServerRequestInterface as Request;

Trait HttpPayload {
    /**
     * getPostData
     *
     * @param  mixed $req
     *
     * @return Array
     */
    private function getPostData(Request $req, $args): Array {
        $postData = [];
        $postData['data'] = $req->getParsedBody();
        $postData['headers'] = $this->getHeaders($req);
        $queryData = $this->getGetData($req, $args);
        $postData['params'] = $queryData['params'] ?? "";
        if (count($req->getUploadedFiles()) > 0) {
            $postData['files'] = $this->getFilesUploaded($req);
        }

        return $postData;
    }

     /**
     * getHeaders
     *
     * @param  mixed $req
     *
     * @return Array
     */
    private function getHeaders(Request $req): Array {
        return $req->getHeaders();
    }

    /**
     * getPutData
     *
     * @param  mixed $req
     * @param  mixed $args
     *
     * @return Array
     */
    private function getPutData(Request $req, $args): Array {
        $data = [];
        $size = $req->getBody->getSize();
        $data['data'] = $req->getBody()->read($size);
        $data['headers'] = $this->getHeaders($req);
        $queryData = $this->getGetData($req, $args);
        $data['params'] = $queryData['params'] ?? "";

        return $data;
    }

    /**
     * getFilesUploaded
     *
     * @param  mixed $request
     *
     * @return Array
     */
    private function getFilesUploaded(Request $request): Array {
        $files = [];
        $item = [];
        $uploadedFiles = $request->getUploadedFiles();
        foreach($uploadedFiles as $file) {
            $item['file'] = $file->getStream();
            $item['name'] = $file->getClientFilename();
            $item['mime'] = $file->getClientMediaType();
            $item['size'] = $file->getSize();
            $files[] = $item;
        }

        return $files;
    }

    /**
     * getPayload
     *
     * @param  mixed $request
     * @param  mixed $args
     *
     * @return Array
     */
    private function getPayload(Request $request, $args): Array {
        $methodsAllowed = ['get', 'post', 'put'];
        $method = \strtolower($request->getMethod());
        if (\in_array($method, $methodsAllowed) === false) {
            throw new InvalidMethodType("invalid.http.method");
        }

        $invoke = sprintf("get%sData", \ucfirst($method));

        return \call_user_func_array([$this, $invoke], [$request, $args]);
    }

    /**
     * getQueryParams
     *
     * @param  mixed $request
     * @param  mixed $args
     *
     * @return Array
     */
    private function getGetData(Request $request, $args): Array {
        $data['headers'] = $this->getHeaders($request);
        $data['params'] = $request->getQueryParams();
        if (is_array($args) === true && count($args) > 0) {
            $data['params'] = array_merge_recursive($data['params'], $args);
        }

        return $data;
    }
}
