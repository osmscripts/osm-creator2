<?php

namespace OsmScripts\Osm\Commands;

use OsmScripts\Core\Script;
use OsmScripts\Osm\Class_;
use OsmScripts\Osm\ModuleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/** @noinspection PhpUnused */

/**
 * `create:migration` shell command class.
 *
 * @property string $migration
 * @property string $step
 * @property string $dir
 * @property string $index
 * @property string $filename
 * @property string $class
 * @property Class_ $class_
 */
class CreateMigration extends ModuleCommand
{
    #region Properties
    public function default($property) {
        /* @var Script $script */
        global $script;

        switch ($property) {
            case 'migration': return $this->input->getArgument('migration');
            case 'step': return $this->input->getOption('step');
            case 'dir': return "Migrations/{$this->step}";
            case 'index': return $this->getIndex();
            case 'filename': return "{$this->dir}/m{$this->index}_{$this->migration}.php";
            case 'class': return "{$this->module_->namespace}\\Migrations\\" .
                "{$this->step}\\m{$this->index}_{$this->migration}";
            case 'class_': return new Class_(['name' => $this->class, 'module' => $this->module_]);
        }

        return parent::default($property);
    }

    protected function getIndex() {
        $dir = $this->app->path("{$this->module_->path}/{$this->dir}");
        if (!is_dir($dir)) {
            return '01';
        }

        $result = 0;
        foreach (new \DirectoryIterator($dir) as $fileInfo) {
            if ($fileInfo->isDot() || $fileInfo->isDir()) {
                continue;
            }

            if ($fileInfo->getExtension() != 'php') {
                continue;
            }

            if (!preg_match('/^m(?<index>\d+)_.*$/',
                $fileInfo->getFilename(), $match))
            {
                continue;
            }

            $index = intval($match['index']);
            if ($index > $result) {
                $result = $index;
            }
        }

        return sprintf('%02d', $result + 1);
    }
    #endregion

    protected function configure() {
        parent::configure();
        $this
            ->setDescription("Creates new migration script")
            ->addArgument('migration', InputArgument::REQUIRED, "Name of the migration script. Should be snake_case.")
            ->addOption('step', null, InputOption::VALUE_REQUIRED,
                "Migration step", 'Schema');
    }

    protected function handle() {
        $this->shell->cd($this->module_->path, function() {
            $this->createMigration();
        });
    }

    protected function createMigration() {
        $this->files->save($this->filename, $this->files->render('migration_class', [
            'namespace' => $this->class_->namespace,
            'class' => $this->class_->short_name,
        ]));
    }
}