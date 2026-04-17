<?php

namespace App\Config;

use Doctrine\ORM\EntityRepository;
use ReflectionClass;

use App\Exceptions\Exception;
use App\Controllers\Backend\APIController;
use App\Controllers\Frontend\BaseController;

/**
 * General class to be extended by API or Web controllers.
 */
abstract class Controller
{
    /**
     * Class to interact with the database.
     */
    protected ORM $orm;

    /**
     * Controller to be used in the API call.
     */
    protected ?string $controller = null;

    /**
     * Default namespace for the controllers.
     */
    protected string $namespace;

    /**
     * Default dir for the controller instance.
     */
    protected string $dir;

    /**
     * Default entity to instantiate the repository
     * class in the controller.
     */
    protected ?string $entity = null;

    /**
     * Default repository to be used in the controller,
     * based on the entity.
     */
    protected ?EntityRepository $repository = null;

    /**
     * Controller constructor.
     */
    public function __construct(ORM $orm)
    {
        // Set the ORM to interact with the database
        $this->orm = $orm;

        // Set the default namespace for the controllers
        // a reflection class is needed to be used to automatically
        // get the namespace of the class that extends this one.
        $reflection_class = new ReflectionClass(get_class($this));
        $this->namespace = $reflection_class->getNamespaceName();

        // Get the directory of the class that extends this one
        $this->dir = dirname($reflection_class->getFileName());

        // Avoid parents classes to set the entity and repository
        if (
            in_array(
                $reflection_class->getName(),
                [
                    Controller::class,
                    APIController::class,
                    BaseController::class
                ]
            )
        ) {
            return;
        }

        // Set the default entity to be used in the controller
        $this->setEntity();

        // Set the repository object instance to be used in the controller
        $this->setRepo();
    }

    /**
     * Initialize the application and instantiate the
     * controller class to handle the request.
     */
    abstract public function init(string $controller, mixed ...$args): mixed;

    /**
     * Identify the method to be used in the
     * controller.
     */
    abstract protected function getMethodName(string $provided_method): string;

    /**
     * Run a controller and its method with all the
     * required arguments.
     */
    protected function run(string $controller_name, string $identified_method, ?string $provided_method = null, array $args = []): mixed
    {
        // Create an instance of the controller
        $controller = new $controller_name($this->orm);
        $provided_method = $provided_method !== null ? str_replace('-', '', $provided_method) : null;

        // If the method is the same as the provided method, remove it
        // from the argument array.
        if (
            $provided_method !== null &&
            (
                $provided_method === $identified_method
                || str_contains(mb_strtolower($identified_method), mb_strtolower($provided_method))
            )
        ) {
            $args = array_slice($args, 1);
        }

        // Filter null/empty values
        $args = array_filter($args, fn ($arg) => $arg !== null && $arg !== '');

        // Call the method and output the response.
        return call_user_func_array([$controller, $identified_method], $args);
    }

    /**
     * Set the correct variable types of arguments.
     */
    protected function castTypes(&$args): void
    {
        foreach ($args as $key => &$value) {
            if (is_numeric($value)) {
                if (str_contains($value, '.')) {
                    $args[$key] = (float)$value;
                } else {
                    $args[$key] = (int)$value;
                }
            } elseif ($value === 'true') {
                $args[$key] = true;
            } elseif ($value === 'false') {
                $args[$key] = false;
            } elseif ($value === 'null') {
                $args[$key] = null;
            } elseif (is_array($value)) {
                $this->castTypes($value);
            } else {
                $args[$key] = $value;
            }
        }
    }

    /**
     * Identify the controller to be used to
     * handle the request.
     */
    protected function getControllerClassName(string $provided_controller): string
    {
        // If the class exists, return it
        if (class_exists($provided_controller)) {
            return $provided_controller;
        }

        // Remove all the hyphen '-' from the controller name
        $provided_controller = str_replace('-', '', $provided_controller);

        // First letter to uppercase to match the class name
        $name = ucfirst($provided_controller);

        // Add the namespace to the controller
        $controller = "{$this->namespace}\\{$name}Controller";

        // If the class does not exist, try to find it with plural name
        if (!class_exists($controller)) {
            $controller = $name . 'sController';
            $controller = "{$this->namespace}\\$controller";
        }

        // If the class does not exist, try to find it by scanning the directory
        if (!class_exists($controller)) {
            // Scan all the files in the directory where the parent class is located
            $controllers = scandir($this->dir);

            // Iterate over the files to find the controller
            foreach ($controllers as $controller) {
                // Remove .php from the class name
                $controller = str_replace('.php', '', $controller);

                if (strtolower($controller) === strtolower($provided_controller . 'controller')) {
                    // If the class exists, return it
                    if (class_exists("{$this->namespace}\\$controller")) {
                        return "{$this->namespace}\\$controller";
                    }
                }
            }
        }

        return $controller;
    }

    /**
     * Return the proper name of the current controller
     * based on the class name.
     */
    private function getThisControllerName(): string
    {
        $controller = explode('\\', get_class($this));
        $controller = end($controller);
        return str_replace('Controller', '', $controller);
    }

    /**
     * Return the ORM provider to be usually
     * used in the controller.
     */
    protected function getOrm(): object
    {
        return $this->orm->getProvider();
    }

    /**
     * Return the ORM class instance.
     */
    protected function getOrmClass(): ORM
    {
        return $this->orm;
    }

    /**
     * Return the repository to be used in the controller
     * or the repository for an entity class, if the class
     * name is provided.
     *
     * @throws Exception
     */
    public function getRepo(?string $className = null): ?EntityRepository
    {
        if ($className !== null) {
            if (!class_exists($className)) {
                throw new Exception("Class '$className' does not exist");
            }

            return $this->getOrmClass()->getRepo($className);
        }

        // If no entity was set, throw an exception to
        // prevent the app to show unexpected behavior.
        if ($this->repository === null) {
            // If the entity was not set, throw an exception
            if ($this->entity === null)
                throw new Exception('Repository not initialized and the Entity was not identified. Try manually setting the entity in the ' . get_class($this) . ' class');
            else
                throw new Exception('Repository not initialized');
        }

        return $this->repository;
    }

    /**
     * Return the repository based on the entity
     * set in the controller.
     */
    private function getEntityRepo(): ?object
    {
        // If the entity is not set, or does not exist, return null
        if ($this->entity === null) {
            return null;
        }

        // If the Entity was set, use it to find the repository
        return $this->getOrm()->getRepository($this->entity);
    }

    /**
     * Return the default repository to be used
     * in the controller based on the controller name.
     */
    private function getDefaultRepo(): ?object
    {
        // Check if a repository with the name of the controller exists
        $controller = $this->getThisControllerName();
        $repo = "App\\Repositories\\{$controller}Repo";

        // If the repository exists, set it
        if (class_exists($repo)) {
            // Check if the repository doesn't extend the EntityRepository
            if (!is_subclass_of($repo, 'Doctrine\ORM\EntityRepository')) {
                return new $repo($this->getOrmClass());
            }
        }

        // If the repository does not exist, try to find it with the plural name
        $repo = "App\\Repositories\\{$controller}sRepo";

        // If the repository exists, set it,
        if (class_exists($repo)) {
            // Check if the repository doesn't extend the EntityRepository
            if (!is_subclass_of($repo, 'Doctrine\ORM\EntityRepository')) {
                return new $repo($this->getOrmClass());
            }
        }

        // Return null if the repository does not exist
        return null;
    }

    /**
     * Set the entity to be used in the controller.
     */
    private function setEntity(): void
    {
        // If the entity is already set
        if ($this->entity !== null) {
            // Check if the entity exists, and return it
            if (class_exists($this->entity)) {
                return;
            }
        }

        // If the entity was not set or does not exist, try to find it
        // based on the controller name.
        $controller = $this->getThisControllerName();
        $entity = "App\\Entities\\$controller";

        // If the entity does not exist, try to find it with the singular name
        if (!class_exists($entity)) {
            $controller = rtrim($controller, 's');
            $entity = "App\\Entities\\{$controller}";
        }

        // Set the entity if it exists
        $this->entity = class_exists($entity) ? $entity : null;
    }

    /**
     * Set the repository to be used in the controller.
     */
    private function setRepo(): void
    {
        // Set the repository based on the entity
        // or set the default repository based on the controller name.
        $this->repository = $this->getEntityRepo() ?? $this->getDefaultRepo();
    }
}
