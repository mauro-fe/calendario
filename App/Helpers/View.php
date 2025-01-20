<?php

namespace App\Helpers;


class View {
    private $viewPath;
    private $data = [];
    private $header;
    private $footer;

    public function __construct($viewPath, $data = []) {
        $this->viewPath = __DIR__ . '/../../views/' . $viewPath . '.php';  // Caminho da view
        $this->data = $data;  // Dados para a view
    }

    public function setHeader($headerPath) {
        $this->header = __DIR__ . '/../../views/' . $headerPath . '.php';
    }

    public function setFooter($footerPath) {
        $this->footer =     __DIR__ . '/../../views/' . $footerPath . '.php';
    }

    public function render() {
        // Exibir o cabeçalho
        if ($this->header) {
            require_once $this->header;
        }

        // Extrair os dados para que as variáveis sejam acessíveis na view
        extract($this->data);

        // Exibir a view
        require_once $this->viewPath;

        // Exibir o rodapé
        if ($this->footer) {
            require_once $this->footer;
        }
    }
}
