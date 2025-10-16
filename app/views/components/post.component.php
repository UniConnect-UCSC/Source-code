<div class="post">
    <div>
        <div>
            <div>Image</div>
            <div>
                <p><?= htmlspecialchars($author) ?></p>
                <span>Posted on: <?= htmlspecialchars($createdAt) ?></span>
            </div>
        </div>

        <div>- - -</div>
    </div>

    <div><?= htmlspecialchars($caption) ?></div>

    <div>
        <img src="<?= htmlspecialchars($mediaUrl) ?>" alt="Post Media">
    </div>

    <div>
        <div>Likes</div>
        <div>
            <div>Comment Count</div>
            <div>Share Count</div>
        </div>
    </div>

    <div>
        <button>Like</button>
        <button>Comment</button>
        <button>Share</button>
    </div>
</div>