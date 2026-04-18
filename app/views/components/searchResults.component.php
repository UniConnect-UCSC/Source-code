<?php
$results = $results ?? [];
?>

<div class="search-results-container">
    <!-- <?php component('searchFilters'); ?> -->

    <div class="search-results">
        <?php if (empty($results)): ?>
            <p class="no-results-message">No results found.</p>
        <?php else: ?>
            <div class="user-search-results">
                <?php foreach ($results as $result): ?>
                    <?php
                    if ($result['type'] === 'post') {
                        component("post", [
                            "postId" => $result['id'],
                            "authorId" => $result['user_id'],
                            "author" => $result['is_anonymous'] ? "Anonymous" : $result['authorName'],
                            "caption" => $result['caption'],
                            "createdAt" => $result['created_at'],
                            "updatedAt" => $result['updated_at'],
                            "groupID" => $result['data']->group_id,
                            "mediaUrl" => $result['data']->media_url,
                            "isAnonymous" => $result['data']->is_anonymous,
                        ]);
                    } elseif ($result['type'] === 'user') {
                        component("userCard", [
                            "userId" => $result['data']->id,
                            "name" => $result['data']->f_name . " " . $result['data']->l_name,
                            "email" => $result['data']->email,
                            "profilePicUrl" => $result['data']->profile_picture,
                            "universityName" => $result['data']->university_name ?? "University not set",
                            "friendshipStatus" => $result['friendshipStatus'] ?? null,
                            "relationshipState" => $result['relationshipState'] ?? 'none',

                        ]);
                    }
                    ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>