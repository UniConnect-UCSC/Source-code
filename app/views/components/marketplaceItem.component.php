<?php
require_once(__DIR__ . "/../../models/MarketplaceItemImage.php");

$imageModel = new MarketplaceItemImage();
$images = $imageModel->where([['marketplace_item_id', '=', $id]], null, null, ['id' => 'ASC']) ?: [];
$imageUrls = [];
foreach ($images as $image) {
  if (!empty($image->image_url)) {
    $imageUrls[] = $image->image_url;
  }
}
$imageUrl = $imageUrls[0] ?? null;


$descriptionSafe = isset($description) ? (string)$description : '';
$searchText = strtolower(trim(($title ?? '') . ' ' . $descriptionSafe));
$categoryIdSafe = (string)($categoryID ?? '');
$categoryNameSafe = strtolower(trim((string)($categoryName ?? '')));
$currentUserId = $_SESSION['user_id'] ?? null;
$isOwner = isset($studentID) && $currentUserId && $studentID === $currentUserId;




?>

<div
  class="marketplace-item-card <?= (isset($myItems) && $myItems) ? 'has-my-items-options' : '' ?>"
  role="link"
  tabindex="0"
  aria-label="View details for <?= htmlspecialchars((string)($title ?? 'item')) ?>"
  data-item-id="<?= htmlspecialchars($id) ?>"
  data-details-url="/marketplace/details/<?= htmlspecialchars($id) ?>"
  data-item-title="<?= htmlspecialchars((string)($title ?? '')) ?>"
  data-item-price="<?= htmlspecialchars((string)($price ?? '')) ?>"
  data-item-image-url="<?= htmlspecialchars((string)($imageUrl ?? '')) ?>"
  data-search-text="<?= htmlspecialchars($searchText) ?>"
  data-category-id="<?= htmlspecialchars($categoryIdSafe) ?>"
  data-category-name="<?= htmlspecialchars($categoryNameSafe) ?>"
  data-contact-number="<?= htmlspecialchars($contact_number ?? '') ?>">

  <?php if (!empty($categoryNameSafe)): ?>
    <div class="marketplace-item-category-badge">
      <?= htmlspecialchars(ucwords(str_replace(['-', '_'], ' ', $categoryNameSafe))) ?>
    </div>
  <?php endif; ?>
  <div class="marketplace-item-image">
    <?php if (!empty($imageUrl)): ?>
      <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($title ?? '') ?>" loading="lazy" />
    <?php else: ?>
      <div class="marketplace-item-no-image">No Image Available</div>
    <?php endif; ?>
  </div>

  <div class="marketplace-item-details">
    <h3 class="marketplace-item-title"><?= htmlspecialchars((string)($title ?? '')) ?></h3>
    <p class="marketplace-item-price">LKR <?= htmlspecialchars((string)($price ?? '')) ?></p>



  

    <?php
    $statusSafe = strtolower(trim((string)($status ?? '')));
    $allowedStatuses = ['available', 'sold', 'reserved'];
    if (!in_array($statusSafe, $allowedStatuses, true)) {
      $statusSafe = '';
    }
    ?>

    <?php if ($statusSafe): ?>
      <span class="marketplace-item-status <?= htmlspecialchars($statusSafe) ?>">
        <?= htmlspecialchars(strtoupper($statusSafe)) ?>
      </span>
    <?php endif; ?>


    <p class="marketplace-item-date">
      <?= htmlspecialchars(date("F j, Y", strtotime($created_at ?? ''))) ?>
    </p>
  </div>

  <?php if (isset($myItems) && $myItems): ?>
    <div class="my-items-options"
      onclick="event.stopPropagation(); toggleMyItemsOptions('<?= htmlspecialchars($id) ?>')">
      <i data-lucide="more-horizontal"></i>
    </div>
    <div class="my-items-options-dropdown" id="my-items-options-dropdown-<?= htmlspecialchars($id) ?>">
      <div class="edit-item-btn" data-item-id="<?= htmlspecialchars($id) ?>">Edit</div>
      <div class="delete-item-btn" data-item-id="<?= htmlspecialchars($id) ?>">Delete</div>
    </div>
  <?php endif; ?>

  <?php if (!$isOwner && !(isset($myItems) && $myItems)): ?>
    <button
      type="button"
      class="marketplace-save-btn"
      data-item-id="<?= htmlspecialchars($id) ?>"
      aria-label="Save item"
      aria-pressed="false"
      onclick="event.stopPropagation();">
      <i data-lucide="bookmark"></i>
    </button>
  <?php endif; ?>
</div>