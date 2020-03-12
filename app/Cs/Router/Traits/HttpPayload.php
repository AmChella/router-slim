<?php
namespace Cs\Router\Traits;

use Psr\Http\Message\ServerRequestInterface as Request;
use Cs\Router\Exception\InvalidMethodType;

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
     * getReturnMode
     *
     * @param  mixed $params
     *
     * @return String
     */
    private function getReturnMode(Array $params): String {
        $mode = 'raw';
        $allowed = ["raw", "json", "download"];
        if (count($params) === 0) {
            return $mode;
        }

        foreach($params as $key => $value) {
            if (
                strtolower($key) === "return" && 
                \in_array($value, $allowed) === true
            ) {
                $mode = $value;
            }
        }

        return $mode;
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
        $data['data'] = $req->getParsedBody();
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
