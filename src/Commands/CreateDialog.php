<?php

namespace OsmScripts\Osm\Commands;

use OsmScripts\Osm\RouteCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/** @noinspection PhpUnused */

/**
 * `create:dialog` shell command class.
 *
 * @property string $id Template ID in kebab case
 * @property string $id_ Template ID in snake case
 * @property string $layer Layer which renders the dialog
 * @property string $css_modifier
 * @property string $js_class
 */
class CreateDialog extends RouteCommand
{
    #region Properties
    public function default($property) {
        switch ($property) {
            case 'route_method': return 'GET';
            case 'public': return true;
            case 'id': return $this->str->kebab($this->str->studly(
                $this->input->getArgument('id')));
            case 'id_': return $this->str->snake($this->str->studly($this->id));
            case 'route': return $this->getRoute();
            case 'layer': return "dialogs_{$this->id_}";
            case 'css_modifier': return "-{$this->id}";
            case 'js_class': return $this->input->getOption('js-class')
                ?: ucfirst($this->method);
        }

        return parent::default($property);
    }

    protected function getRoute() {
        return $this->input->getOption('route') ?: $this->getDefaultRoute();
    }

    protected function getDefaultRoute() {
        return "/dialogs/{$this->id}";
    }

    protected function getDefaultMethod() {
        return parent::getDefaultMethod() . 'Dialog';
    }
    #endregion

    protected function configure() {
        parent::configure();
        $this->setDescription("Creates new modal dialog");
        $this
            ->addArgument('id', InputArgument::REQUIRED, "Dialog ID")
            ->addOption('js-class', null, InputOption::VALUE_OPTIONAL,
                "Name of JS controller handling JS events. If omitted, inferred from the last segment of route name");
    }

    protected function configureRoute() {
        $this->addOption('route', null, InputOption::VALUE_OPTIONAL,
            "Route path. Example: '/templates/my-template'. If omitted, derived from template ID");
    }

    protected function configurePublic() {
        // all templates are public, no command-line option needed
    }

    protected function handle() {
        $this->shell->cd($this->module_->path, function() {
            $this->createControllerMethod();
            $this->registerRoute();
            $this->createLayer();
            $this->createJsController();
            $this->registerDialog();
            $this->registerJsController();
        });
    }

    protected function renderControllerMethod() {
        return $this->files->render('dialog_controller_method', [
            'method' => $this->method,
            'module' => $this->module,
            'layer' => $this->layer,
        ]);
    }

    protected function createLayer() {
        $filename = "{$this->area_->resource_path}/layers/{$this->layer}.php";
        if (is_file($filename)) {
            return;
        }

        $this->files->save($filename, $this->files->render("dialog_layer", [
            'css_modifier' => $this->css_modifier,
        ]));
    }

    protected function createJsController() {
        $filename = "{$this->area_->resource_path}/js/{$this->js_class}.js";
        if (is_file($filename)) {
            return;
        }

        $this->files->save($filename, $this->files->render("js_dialog_controller", [
            'class' => $this->js_class,
        ]));
    }

    protected function registerDialog() {
        $filename = "{$this->area_->resource_path}/js/index.js";

        $contents = is_file($filename) ? file_get_contents($filename) : '';

        $this->files->save($filename, $this->js->edit($contents, function() {
            $this->js->import('Osm_Framework_Js/vars/templates');
            $this->js->add("templates.add('dialog__{$this->id_}', {route: '{$this->route_method} {$this->route}'});\n");
        }));
    }

    protected function registerJsController() {
        $filename = "{$this->area_->resource_path}/js/index.js";

        $contents = is_file($filename) ? file_get_contents($filename) : '';

        $this->files->save($filename, $this->js->edit($contents, function() {
            $this->js->import('Osm_Framework_Js/vars/macaw');
            $this->js->import("./{$this->js_class}");
            $this->js->add("macaw.controller('.modal-dialog.{$this->css_modifier}', {$this->js_class});\n");
        }));
    }
}