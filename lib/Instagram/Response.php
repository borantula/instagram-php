<?php

namespace Webmakersteve\Instagram;

class Response {

    protected
        $formattedData = null,
        $rawData = null,
        $isJSON = false,
        $response;

    public function __construct(\GuzzleHttp\Psr7\Response $response) {
        $json = false;
        $data = $response->getBody();

        $this->rawData = $data;
        $this->response = $response;

        if ($response->hasHeader('Content-Type')) {
            // Let's see if it is JSON
            $contentType = $response->getHeader('Content-Type');
            if (strstr($contentType[0], 'json')) {
                $json = true;
                $data = json_decode($data);
            }
        }

        if (!$json) {
            // We can do another test here
            $decoded = json_decode($response->getBody());
            if ($decoded) {
                $json = true;
                $data = $decoded;
            }
        }

        $this->setData($data);
        $this->setIsJson($json);

    }

    public function __toString() {
        return (string) $this->getData();
    }

    public function getData() {
        return $this->formattedData;
    }

    public function setData( $data ) {
        $this->formattedData = $data;
    }

    public function setIsJson( $isJson ) {
        $this->isJSON = (bool) $isJson;
    }

    public function isJson() {
        return (boolean) $this->isJSON;
    }

    public function getRawData() {
        return $this->rawData;
    }

    public function getResponse() {
        return $this->response;
    }

    public function getStatusCode() {
        return $this->getResponse()->getStatusCode();
    }

    public function getProperty($property, $default = '') {

        if (!$this->isJson()) return $default;

        // Split property by dots
        $splitProperty = explode('.', $property);

        $data = $this->getData();

        foreach($splitProperty as $property) {
            if (property_exists($data, $property)) {
                $data = $data->$property;
            } else {
                return $default;
            }
        }

        return $data;

    }

    public function __get ( $name ) {
        return $this->getProperty($name, false);
    }

}
