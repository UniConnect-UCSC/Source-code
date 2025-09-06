<?php require(__DIR__ . "/../../core/utils.php"); ?>
<div class="nav-panel">
    <div class="">
        <?php foreach ($navbarPanelLinks as $link): ?>
        <div class="nav-panel__link__container no-select">
            <a class="nav-panel__link" href="<?= htmlspecialchars($link['pageLink']) ?>">
                <!-- <img src="<?= htmlspecialchars($link['icon']) ?>" width="30"
                    alt="<?= htmlspecialchars($link['pageName']) ?> icon"> -->
                <i data-lucide=<?= htmlspecialchars($link['icon']) ?>></i>
                <?= htmlspecialchars($link['pageName']) ?>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
lucide.createIcons();
</script>