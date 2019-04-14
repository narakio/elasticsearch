<?php namespace Naraki\Elasticsearch\Index;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Container\Container;

abstract class Indexer
{
    /**
     * @var string
     */
    protected $modelClass;
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * The console command instance.
     *
     * @var \Illuminate\Console\Command
     */
    protected $command;


    protected function __construct()
    {
        if (!is_null($this->modelClass)) {
            $this->model = $this->createModel();
        }
    }

    /**
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function createModel(array $attributes = []): Model
    {
        $class = '\\' . ltrim($this->modelClass, '\\');

        if (class_exists($class)) {
            return new $class($attributes);
        }
        throw new \UnexpectedValueException('Model class could not be found or class variable has not been initialized.');
    }

    protected function getIndexName()
    {
        return sprintf('%s.%s', slugify(config('app.name'), '_'), $this->model->getTable());

    }

    /**
     * Seed the given connection from the given path.
     *
     * @param  array|string $class
     * @param  bool $silent
     * @return $this
     */
    public function call($class, $silent = false)
    {
        $classes = Arr::wrap($class);

        foreach ($classes as $class) {
            if ($silent === false && isset($this->command)) {
                $this->command->getOutput()->writeln("<info>Indexing:</info> $class");
            }

            $this->resolve($class)->__invoke();
        }

        return $this;
    }

    /**
     * Silently seed the given connection from the given path.
     *
     * @param  array|string $class
     * @return void
     */
    public function callSilent($class)
    {
        $this->call($class, true);
    }

    /**
     * Resolve an instance of the given seeder class.
     *
     * @param  string $class
     * @return \Illuminate\Database\Seeder
     */
    protected function resolve($class)
    {
        if (isset($this->container)) {
            $instance = $this->container->make($class);

            $instance->setContainer($this->container);
        } else {
            $instance = new $class;
        }

        if (isset($this->command)) {
            $instance->setCommand($this->command);
        }

        return $instance;
    }

    /**
     * Set the IoC container instance.
     *
     * @param  \Illuminate\Container\Container $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Set the console command instance.
     *
     * @param  \Illuminate\Console\Command $command
     * @return $this
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Run the database seeds.
     *
     * @return dynamic
     *
     * @throws \InvalidArgumentException
     */
    public function __invoke()
    {
        if (!method_exists($this, 'run')) {
            throw new InvalidArgumentException('Method [run] missing from ' . get_class($this));
        }

        return isset($this->container)
            ? $this->container->call([$this, 'run'])
            : $this->run();
    }

}