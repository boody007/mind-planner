<?php
    function render_loading_screen($show) {
        if ($show) {
        ?>
            <div class="loading" id="loading">
                <div id="spinner" class="spinner"></div>
            </div>
        <?php
        }
    }
?>