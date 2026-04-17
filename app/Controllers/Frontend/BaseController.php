<?php

namespace App\Controllers\Frontend;

use Throwable;

use App\Config\Controller;

use App\Exceptions\Exception;

/**
 * Base class to be extended by every controller that needs
 * to handle frontend HTTP calls in the application.
 *
 * Every controller that handles frontend HTTP calls needs to extend
 * this controller.
 */
class BaseController extends Controller
{
    /**
     * Initialize the API application and return the
     * response that the specific called controller
     * has prepared.
     *
     * The called controller must extend this class
     * to use all the common methods provided here.
     *
     * @throws Exception
     */
    public function init(string $controller, mixed ...$args): mixed
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
        if (!class_exists($this->controller)) {
            // Else include a 404 page
            header('HTTP/1.0 404 Not Found');
            http_response_code(404);
            return $this->renderView(404);
        }

        // Validate if the method identified exists
        if (!method_exists($this->controller, $method)) {
            // Else include a 404 page
            header('HTTP/1.0 404 Not Found');
            http_response_code(404);
            return $this->renderView(404);
        }

        try {
            // Call the method and output the response.
            return $this->run($this->controller, $method, $provided_method, $args);
        } catch (Throwable $e) {
            header('HTTP/1.0 404 Not Found');
            http_response_code(404);
            return $this->renderView('errors/404', ['title' => 'System Error'], exception: $e);
        }
    }

    /**
     * Identify the method to be used in the
     * controller. For example:
     *
     * * GET "/employees/facilities", "facilities" is the method.
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
            return 'view';

        // If not an ID, try adding 'get' to the method
        // name and capitalize the first letter to match
        // common method names
        if ($provided_method !== '' && method_exists($this->controller, $method = 'get' . ucfirst($provided_method)))
            return $method;

        return 'index';
    }

    /**
     * Render a view file with the provided data.
     *
     * @throws Exception
     */
    public function renderView(
        string|int $view,
        array $data = [],
        ?Throwable $exception = null
    ): string
    {
        if (is_int($view)) {
            http_response_code($view);
            $view = "errors/$view";
            $title = "$view | Not Found";
        }

        // Extract the data array to variables to be used in the view
        extract($data);

        $path = 'app/Views/' . $view . '.php';

        if (!file_exists($path)) {
            throw new Exception("The view file $path does not exist.");
        }

        /**
         * Clean the previous buffer, in case of any
         * previous output made before an Exception.
         *
         * An ob_start() should be called before
         * any output is made in the application.
         *
         * @see \App\Config\Application::initialize()
         */
        ob_clean();

        // Start a new buffer to include the view file
        ob_start();

        // Include the view file
        include($path);

        // Print the buffer, remove it, and return it's content.
        return ob_get_clean();
    }

    /**
     * Redirect to a specific URL.
     *
     * This function helps to avoid the use of the
     * header() and the exit() functions in the controllers.
     */
    public function redirect(string $url): string
    {
        str_starts_with($url, 'http') || str_starts_with($url, '/') || $url = "/$url";
        header("Location: $url") || exit();
        return '';
    }
}
