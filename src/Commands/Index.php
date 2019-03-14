<?php namespace Naraki\Elasticsearch\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class Index extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'elastic:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create indices';


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle()
    {
        $this->getSeeder()->__invoke();

        $this->info('ElasticSearch Indexing completed successfully.');
    }

    /**
     * Get a seeder instance from the container.
     *
     * @return \Illuminate\Database\Seeder
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getSeeder()
    {
        $class = $this->laravel->make($this->input->getOption('class'));
        return $class->setContainer($this->laravel)->setCommand($this);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['class', null, InputOption::VALUE_OPTIONAL, 'The class name of the root indexer', 'ElasticIndexer'],
        ];
    }
}
