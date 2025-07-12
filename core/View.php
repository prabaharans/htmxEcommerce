<?php

class View {
    public function render($view, $data = [], $layout = 'main') {
        extract($data);
        
        ob_start();
        include "views/{$view}.php";
        $content = ob_get_clean();
        
        if ($layout && !$this->isHtmxRequest()) {
            include "views/layouts/{$layout}.php";
        } else {
            echo $content;
        }
    }
    
    private function isHtmxRequest() {
        return isset($_SERVER['HTTP_HX_REQUEST']);
    }
}
?>
