<?php require(__DIR__ . "/../../core/utils.php"); ?>
<div class="nav-panel">
    <div class="">
        <?php
        foreach ($navbarLinks as $link) {
            echo '<div>';
            echo '<a href="' . htmlspecialchars($link["pageLink"]) . '">';
            echo htmlspecialchars($link["pageName"]);
            echo '</a>';
            echo '</div>';
        }
        ?>
    </div>
</div>