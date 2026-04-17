<?php

namespace App\Controllers\Backend;

use Throwable;

use App\Config\Controller;
use App\Config\ORM;
use App\Config\Routes;
use App\Exceptions\HTTP\HttpException;

/**
 * Base class to be extended by every controller that needs
 * to handle endpoints in the application.
 *
 * Every controller that handles endpoints needs to extend
 * this controller.
 */
class APIController extends Controller
{
    /**
     * Status messages to be used in the API.
     */
    protected const array STATUS = [
        'INVALID_REQUEST' => 'Invalid API call',
        'SOMETHING_WENT_WRONG' => 'Something went wrong. Please try again later',
    ];

    /**
     * Sets the ORM and fill the $_POST
     * variable with the AJAX request data.
     */
    public function __construct(ORM $orm)
    {
        parent::__construct($orm);

        // Get the RAW content passed in the HTTP request
        // and enter the data to the $_POST variable
        $this->phpInput($_POST);
    }

    /**
     * Initialize the API application and return the
     * response that the specific called controller
     * has prepared.
     *
     * The called controller must extend this class
     * to use all the common methods provided here.
     *
     * @throws HttpException
     */
    public function init(string $controller, mixed ...$args): string
    {
        $args = empty($args) ? [null] : $args;

        // Get the first element of the ...args
        [$provided_method] = $args;

        // Auto cast the arguments
        $this->castTypes($args);

        // Get the qualified controller name with the namespace
        $this->controller = $this->getControllerClassName($controller);

        // If was provided more than one URL parameter, identify
        // the method to be used. 
        $method = $this->getMethodName($provided_method);

        // Validate if the controller exists
        if (!class_exists($this->controller))
            throw new HttpException(404);

        // Validate if the method identified exists
        if (!method_exists($this->controller, $method))
            return $this->sendOutput(['message' => static::STATUS['INVALID_REQUEST']], 400);

        try {
            // Call the method and output the response.
            return $this->run($this->controller, $method, $provided_method, $args);
        } catch (Throwable $e) {
            // If any unexpected exception error occurred,
            // return the message though the API.
            return $this->sendOutput(exception: $e);
        }
    }

    /**
     * Identify the method to be used in the
     * controller. For example:
     *
     * * GET "/employees/facilities", "facilities" is the method.
     *
     * * GET "/api/v1/employees/1232", "find" is the default method to find
     *   a particular employee with the ID 1232.
     *
     * * GET "/api/v1/employees/find/1232", "find" is the method to find
     *   a particular employee with the ID 1232.
     *
     * * DELETE "/api/v1/employees/1232", "delete" is the method to
     *   delete a particular employee with the ID 1232.
     */
    protected function getMethodName($provided_method): string
    {
        $provided_method = str_replace('-', '', $provided_method ?? '');

        // If the method exists, return it
        if (method_exists($this->controller, $provided_method))
            return $provided_method;

        // If is an integer, probably is an ID
        if (!empty($provided_method) && is_numeric($provided_method))
            // Return the method based on the HTTP method
            return match (true) {
                Routes::isDELETE() => 'delete',
                Routes::isPUT(), Routes::isPATCH() => 'update',
                default => 'find',
            };

        // If not an ID, try adding 'get' to the method
        // name and capitalize the first letter to match
        // common method names
        if (
            $provided_method !== ''
            && (
                method_exists($this->controller, $method = 'get' . ucfirst($provided_method))
                || method_exists($this->controller, $method = 'find' . ucfirst($provided_method))
            )
        )
            return $method;

        // If the method does not exist, try to identify
        // by the HTTP method 'DELETE' and 'GET' are already
        // identified at this point if $method is an ID.
        return match (true) {
            Routes::isOPTIONS() => 'preflight',
            Routes::isPOST() => 'insert',
            Routes::isDELETE() => 'delete',
            Routes::isPUT(), Routes::isPATCH() => 'update',
            default => 'findAll',
        };
    }

    /**
     * Output the API response in JSON format.
     */
    public function sendOutput(
        mixed $data = null,
        int $httpResponseCode = 200,
        array $http_headers = [],
        ?Throwable $exception = null
    ): string
    {
        if (is_array($http_headers) && count($http_headers)) {
            foreach ($http_headers as $httpHeader) {
                header($httpHeader);
            }
        }

        // Avoid sending a JSON response when the data is 404
        if ($data === 404 || (isset($data['message']) && $data['message'] == 404)) {
            http_response_code(404);
            return '';
        }

        // If the data has a code, use it as the HTTP response code
        $httpResponseCode = $data['code'] ?? $httpResponseCode;

        if ($exception instanceof Throwable) {
            // Determine the error message to be sent based
            // on the environment and the exception's class
            $data = [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];

            $httpResponseCode = $httpResponseCode === 200 ? $exception->getCode() : $httpResponseCode;
        }

        if (!($httpResponseCode) || !is_numeric($httpResponseCode)) {
            $httpResponseCode = 500;
        }

        http_response_code($httpResponseCode);

        !empty($data) && header('Content-Type: application/json; charset=UTF-8');
        return json_encode($data);
    }

    /**
     * Allow the server to respond to preflight
     * requests from the client.
     */
    public function preflight(): string
    {
        return $this->sendOutput('', 200, array('HTTP/1.1 200 OK'));
    }

    /**
     * Get the RAW content passed in the HTTP
     *  request and fill the variable passed.
     *
     * $_POST is the most common var.
     */
    protected function phpInput(array &$var): array
    {
        // Get the RAW content passed in the HTTP request
        $content = file_get_contents("php://input") ?? false;
        $content_type = $_SERVER['CONTENT_TYPE'] ?? false;

        // If no content is provided, don't do anything
        if (!$content)
            return [];

        // If no content type is provided, don't do anything
        if (!$content_type)
            return [];

        if (str_contains($content_type, 'multipart/form-data')) {
            $boundary = substr($content_type, strpos($content_type, 'boundary=') + 9);
            // $boundary = preg_replace('/(^multipart\/form-data; boundary=)(.*$)/ui', '$2', $contentType);
            $var = $this->parse_multipart_content($content, $boundary);
        }

        if (str_contains($content_type, 'application/x-www-form-urlencoded')) {
            parse_str($content, $var);
        }

        if (str_contains($content_type, 'application/json')) {
            $var = (array)json_decode($content, true);
        }

        return $var;
    }

    /**
     * Parse arbitrary multipart/form-data content.
     */
    public function parse_multipart_content(?string $content, ?string $boundary): ?array
    {
        // Exit if failed to get the input or if it's not compliant with the RFC2046
        if ($content === false || preg_match('/^\s*--' . $boundary . '.*\s*--' . $boundary . '--\s*$/muis', $content) !== 1) {
            return [];
        }

        // Strip ending boundary
        $content = preg_replace('/(^\s*--' . $boundary . '.*)(\s*--' . $boundary . '--\s*$)/muis', '$1', $content);

        // Split data into an array of fields
        $content = preg_split('/\s*--' . $boundary . '\s*Content-Disposition: form-data;\s*/muis', $content, 0, PREG_SPLIT_NO_EMPTY);

        // Convert to an associative array
        $parsed_content = [];

        foreach ($content as $field) {
            $name = preg_replace('/(name=")(?<name>[^"]+)("\s*)(?<value>.*$)/mui', '$2', $field);
            $value = preg_replace('/(name=")(?<name>[^"]+)("\s*)(?<value>.*$)/mui', '$4', $field);

            // Check if we have multiple keys
            if (str_contains($name, '[')) {
                // Explode keys into array
                $keys = explode('[', trim($name));
                $name = '';
                // Build JSON array string from keys
                foreach ($keys as $key) {
                    $name .= '{"' . rtrim($key, ']') . '":';
                }
                // Add the value itself (as string, since in this case it will always be a string) and closing brackets
                $name .= '"' . trim($value) . '"' . str_repeat('}', count($keys));
                // Convert into an actual PHP array
                $array = (array)json_decode($name, true);
                // Check if we actually got an array and did not fail
                if (!empty($array)) {
                    // "Merge" the array into existing data. Doing recursive replace, so that new fields will be added, and in case of duplicates, only the latest will be used
                    $parsed_content = array_replace_recursive($parsed_content, $array);
                }
            } else {
                // Single key - simple processing
                $parsed_content[trim($name)] = trim($value);
            }
        }

        return $parsed_content;
    }

    /**
     * Clean the input data from the request,
     * removing any HTML or PHP tags. Also,
     * if an element is an array, it will be
     * cleaned recursively.
     */
    protected function cleanInput(
        array &$data,
        bool $cleanEmpty = true,
        int $flags = ENT_QUOTES | ENT_SUBSTITUTE
    ): void
    {
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                $this->cleanInput($value);
            } else if (!is_object($value)) {
                $requestUri = explode('?', $_SERVER['REQUEST_URI'])[0] ?? '';
                $requestUri = substr($requestUri, 1);

                // Remove the key if it's exactly the same as the request URI
                // and remove the timestamp key passed in the URL (if any)
                if ($requestUri === $key || $key === '_') {
                    unset($data[$key]);
                }

                $value = htmlspecialchars(strip_tags($value), flags: $flags);
                $cleanEmpty && ($value = $value === '' ? null : $value);
            }
        }
    }

    /**
     * Magic method invoked automatically when
     * an undefined or inaccessible method is called.
     */
    public function __call($method, $args): string
    {
        return $this->sendOutput(['message' => static::STATUS['INVALID_REQUEST']], 400);
    }
}
