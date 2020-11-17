<?php
namespace App\Core;

use App\Core\Network\Request;
use App\Helper\FormHelper;
/**
 * @property FormHelper $form
 * @property Request $request
 * Renders a complete view with data from the controllers and all required helpers for the view.
 */
class Renderer
{
    private FormHelper $form;
    public Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->form = new FormHelper($this->request->data);
    }

    /**
     * Renders a view.
     * @param string $view         The path of the view.
     * @param array $viewVariables The variables that can be used in the view.
     * @param string|null $layout  The name of the layout.
     * @return void
     */
    public function render(string $view, array $viewVariables, string $layout = null): void
    {
        $view = explode('/', $view);
        echo str_replace(
            '{{ content }}',
            $this->__setView($view[0], $view[1], $viewVariables),
            $this->__setLayout($layout)
        );
    }

    /**
     * Sets the layout.
     * @param string|null $layout The name of the layout.
     * @return false|string
     */
    private function __setLayout(?string $layout)
    {
        $fullPath = LAYOUTS;
        $fullPath .= $layout !== null ? "$layout.php" : DEFAULT_LAYOUT;
        ob_start();
        require_once $fullPath;
        return ob_get_clean();
    }

    /**
     * Sets the view and view variables.
     * @param string $path       The folder where the view is located.
     * @param string $view         The name of the view.
     * @param array $viewVariables The view variables.
     * @return false|string
     */
    private function  __setView(string $path, string $view, array $viewVariables)
    {
        $fullPath = TEMPLATES;
        if (!empty($viewVariables)) {
            foreach ($viewVariables as $key => $value) {
                $$key = $value;
            }
        }
        $fullPath .= $path ?: null;
        $fullPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fullPath . "/$view.php");
        ob_start();
        require_once $fullPath;
        return ob_get_clean();
    }

    /**
     * Renders an element.
     * @param string $element The name of the element.
     * @param array $data     The variables for the element.
     * @return void
     */
    public function element(string $element, array $data = []): void
    {
        require ELEMENTS . "$element.php";
    }
}