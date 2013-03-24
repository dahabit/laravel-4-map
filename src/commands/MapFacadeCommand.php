<?php
namespace Experience\Map\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MapFacadeCommand extends Command
{
    protected $name = 'map:facade';
    protected $description = "Outputs a Facade to Class 'map'.";


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $map = $this->generateFacadeMap();

        if ($facade = $this->argument('facade')) {
            $this->outputSingleFacade($map, $facade);
        } else {
            $this->outputAllFacades($map);
        }
    }


    /**
     * Retrieve the command line arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return array(array(
            'facade', InputArgument::OPTIONAL,
            'Display the mapped class for the specified facade (optional).', ''
        ));
    }


    /**
     * Retrieve the command line options.
     *
     * @return array
     */
    public function getOptions()
    {
        return array(array(
            'path', 'p',
            InputOption::VALUE_NONE,
            'Include the full file path in the output.'
        ));
    }


    /**
     * Generate a 'map' of facades to classes.
     *
     * @return array
     */
    protected function generateFacadeMap()
    {
        $path = ($this->option('path'))
            ? realpath(base_path() .'/vendor/laravel/framework/src') .'/'
            : '';

        $aliases = \Config::get('app.aliases');
        $map     = array();

        foreach ($aliases as $alias => $facade) {
            try {
                $class = method_exists($alias, 'getFacadeRoot')
                    ? get_class($alias::getFacadeRoot())
                    : $facade;

                $class = $path .str_replace('\\', '/', $class) .'.php';
                $map[$alias] = $class;
            } catch (\Exception $e) {
                // We ignore errors for now.
                continue;
            }
        }

        return $map;
    }


    /**
     * Outputs a table of facade to class mappings.
     *
     * @param array $map The map.
     *
     * @return void
     */
    protected function outputAllFacades(array $map)
    {
        $map = $this->prepMapForTabularOutput($map);

        foreach ($map as $row) {
            $this->line($row);
        }
    }


    /**
     * Outputs a single facade from the given map.
     *
     * @param array $map The map.
     * @param string $facade The facade.
     *
     * @return void
     */
    protected function outputSingleFacade(array $map, $facade)
    {
        array_key_exists($facade, $map)
            ? $this->line($map[$facade])
            : $this->error("Facade {$facade} does not exist.");
    }


    /**
     * Prepares the keys and values in the given map for output.
     *
     * @param array $map The map.
     *
     * @return array
     */
    protected function prepMapForTabularOutput(array $map)
    {
        $newmap = array();

        // Determine the longest key and value. Add 2 for spacing.
        $longestKey = max(array_map('strlen', array_keys($map)));
        $longestVal = max(array_map('strlen', array_values($map)));
        $gutter     = ' ';
        $col        = '|';

        // Row separator.
        $rowLength = $longestKey + $longestVal
            + (strlen($gutter) * 4)
            + (strlen($col) * 3);

        $row = str_repeat('-', $rowLength);

        // Add the header row.
        $key = $gutter .str_pad('Facade', $longestKey) .$gutter;
        $val = $gutter .str_pad('Class', $longestVal) .$gutter;

        $newmap[] = $row;
        $newmap[] = $col .$key .$col .$val .$col;
        $newmap[] = $row;

        foreach ($map as $key => $val) {
            $key = $gutter .str_pad($key, $longestKey) .$gutter;
            $val = $gutter .str_pad($val, $longestVal) .$gutter;

            $newmap[] = $col .$key .$col .$val .$col;
        }

        $newmap[] = $row;
        return $newmap;
    }
}
