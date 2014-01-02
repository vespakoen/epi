<?php namespace Vespakoen\Epi\Commands;

use Illuminate\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateEpiControllerCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:epicontroller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new epi controller.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $path = $this->getPath();
        $template = $this->option('template');

        $this->printResult($this->make($path, $template), $path);
    }

    public function make($path, $template)
    {
        $controller = $this->argument('controller');
        $controllerName = $this->getObjectName($controller);
        $baseController = $this->option('base-controller');
        $baseControllerName = $this->getObjectName($baseController);
        $model = $this->argument('model');
        $modelName = $this->getObjectName($model);
        $modelNamespace = $this->option('model-namespace') ? $this->option('model-namespace').'\\' : '';
        $namespace = $this->getNamespace();

        $options = compact('controller', 'controllerName', 'baseController', 'baseControllerName', 'model', 'modelName', 'modelNamespace', 'namespace');

        $contents = $this->getTemplate($template, $options);

        $directory = dirname($path);
        if( ! is_dir($directory))
        {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, $contents);

        return true;
    }

    protected function getObjectName($namespace)
    {
        $parts = explode('\\', $namespace);

        return end($parts);
    }

    /**
     * Provide user feedback, based on success or not.
     *
     * @param  boolean $successful
     * @param  string $path
     * @return void
     */
    protected function printResult($successful, $path)
    {
        if ($successful)
        {
            return $this->info("Created {$path}");
        }

        $this->error("Could not create {$path}");
    }

    /**
     * Fetch the compiled template for a controller
     *
     * @param  string $template Path to template
     * @param  string $name
     * @return string Compiled template
     */
    protected function getTemplate($template, $options)
    {
        $template = file_get_contents($template);

        foreach($options as $key => $value)
        {
            $template = str_replace('{{'.$key.'}}', $value, $template);
        }

        return $template;
    }

    /**
     * Get the path to the file that should be generated.
     *
     * @return string
     */
    protected function getPath()
    {
        $path = $this->option('path');

        if($this->option('bench'))
        {
            list($vendor, $package) = explode('/', $this->option('bench'));
            $ucVendor = ucwords($vendor);
            $ucPackage = ucwords($package);
            $basePath = base_path().'/workbench/'.$this->option('bench').'/src/'.$ucVendor.'/'.$ucPackage;
        }
        elseif($this->option('package'))
        {
            list($vendor, $package) = explode('/', $this->option('package'));
            $ucVendor = ucwords($vendor);
            $ucPackage = ucwords($package);
            $basePath = base_path().'/vendor/'.$this->option('package').'/src/'.$ucVendor.'/'.$ucPackage;
        }
        else
        {
            $basePath = app_path();
        }

        return $basePath . '/' . $path . '/' . $this->argument('controller') . '.php';
    }

    protected function getNamespace()
    {
        $path = $this->option('path');

        if(preg_match('/[A-Z]/', $path))
        {
            $option = $this->option('bench') ? 'bench' : 'package';

            list($vendor, $package) = explode('/', $this->option($option));
            $namespace = 'namespace '.ucwords($vendor).'\\'.ucwords($package);

            foreach (explode('/', $path) as $segment)
            {
                $namespace .= '\\'.$segment;
            }

            $namespace .= ';';

           return $namespace;
        }

        return '';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('controller', InputArgument::REQUIRED, 'Name of the controller to generate.'),
            array('model', InputArgument::REQUIRED, 'Name of the model to use for the controller.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
           array('bench', null, InputOption::VALUE_OPTIONAL, 'The name of the workbench to put the controller', null),
           array('package', null, InputOption::VALUE_OPTIONAL, 'The name of the package to put the controller.', null),
           array('path', null, InputOption::VALUE_OPTIONAL, 'The path to put the controller (relative to app/ or, if given, the package/workbench\'s src/Vendor/Package/ path)', 'controllers/api'),
           array('model-namespace', null, InputOption::VALUE_OPTIONAL, 'The namespace of the model', ''),
           array('template', null, InputOption::VALUE_OPTIONAL, 'The path to the template to use for generating the controller', __DIR__.'/../../../stubs/EpiController.php'),
           array('base-controller', null, InputOption::VALUE_OPTIONAL, 'The namespace of the base controller to use.', 'Vespakoen\Epi\Controllers\EpiController'),
        );
    }

}
