<?php component("navbar"); ?>

<div class="home-layout">
    <!-- Left Navigation -->
    <?php component("navPanel"); ?>

    <!-- Main Feed -->
    <div class="feed">
        <div class="feed-header">
            <p style="font-size: 25px;">Rooms & Boardings</p>
        </div>

        <!-- Post New Listing -->
        <section class="feed-section">
            <div class="feed-grid">
                <div class="post-listing-card">
                    <a href="/post-listing" class="post-btn">Post Your Listing</a>
                </div>
            </div>
        </section>

        <!-- Saved Listings -->
        <?php
        $savedListings = [
            [
                "title" => "Luxury Annex for 2 Students",
                "price" => 25000,
                "period" => "month",
                "location" => "Peradeniya Road, Kandy",
                "posted" => "3 days ago",
                "image" => "/assets/images/luxury-annex.jpg"
            ],
            [
                "title" => "Shared Apartment (3 Bedrooms)",
                "price" => 12000,
                "period" => "month",
                "location" => "Katubedda, Moratuwa",
                "posted" => "2 days ago",
                "image" => "/assets/images/apartment.jpg"
            ]
        ];
        if (!empty($savedListings)): ?>
            <section class="feed-section">
                <div class="feed-section-header">
                    <h3><b>Saved Listings</b></h3>
                </div>
                <div class="feed-grid">
                    <?php foreach ($savedListings as $s): ?>
                        <div class="feed-card">
                            <img src="<?= $s['image'] ?>" alt="Boarding Image" class="feed-card-img saved">
                            <div class="feed-card-body">
                                <h4><?= $s["title"] ?></h4>
                                <p class="price">Rs. <?= number_format($s["price"]) ?>/<?= $s["period"] ?></p>
                                <p class="loc"><?= $s["location"] ?></p>
                                <button class="view-btn">View Details</button>
                                <p class="posted">Posted <?= $s["posted"] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Featured Listings -->
        <section class="feed-section">
            <div class="feed-section-header">
                <h3><b>Featured Listings</b></h3>
                <a href="#">View All</a>
            </div>
            <div class="feed-grid">
                <?php
                $featured = [
                    [
                        "title" => "Single Room near UOC",
                        "price" => 15000,
                        "period" => "month",
                        "location" => "Thimbirigasyaya, Colombo 5",
                        "distance" => "0.5km from UOC",
                        "university" => "University of Colombo",
                        "type" => "Single Room",
                        "amenities" => ["Attached Bathroom", "WiFi", "Meals Available"],
                        "posted" => "1 day ago",
                        "image" => "/assets/images/single-room.jpg"
                    ],
                    [
                        "title" => "Luxury Annex for 2 Students",
                        "price" => 25000,
                        "period" => "month",
                        "location" => "Peradeniya Road, Kandy",
                        "distance" => "0.8km from UOP",
                        "university" => "University of Peradeniya",
                        "type" => "Full Annex",
                        "amenities" => ["Attached Bathroom", "AC", "Fully Furnished"],
                        "posted" => "3 days ago",
                        "image" => "/assets/images/luxury-annex.jpg"
                    ]
                ];

                foreach ($featured as $item): ?>
                    <div class="feed-card">
                        <img src="<?= $item['image'] ?>" alt="Boarding Image" class="feed-card-img featured">
                        <div class="feed-card-body">
                            <h4><?= $item["title"] ?></h4>
                            <p class="price">Rs. <?= number_format($item["price"]) ?>/<?= $item["period"] ?></p>
                            <p class="loc"><?= $item["location"] ?> • <?= $item["distance"] ?></p>
                            <p class="uni"><?= $item["university"] ?></p>
                            <div class="tags">
                                <span><?= $item["type"] ?></span>
                                <?php foreach (array_slice($item["amenities"], 0, 2) as $a): ?>
                                    <span><?= $a ?></span>
                                <?php endforeach; ?>
                            </div>
                            <button class="view-btn">View Details</button>
                            <p class="posted">Posted <?= $item["posted"] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Recent Listings -->
        <section class="feed-section">
            <div class="feed-section-header">
                <h3><b>Recent Listings</b></h3>
                <button class="sort-btn">Sort by ▼</button>
            </div>
            <div class="feed-grid">
                <?php
                $recent = [
                    [
                        "title" => "Shared Apartment (3 Bedrooms)",
                        "price" => 12000,
                        "period" => "month",
                        "location" => "Katubedda, Moratuwa",
                        "distance" => "0.3km from UOM",
                        "university" => "University of Moratuwa",
                        "type" => "Shared Room",
                        "amenities" => ["Common Bathroom", "WiFi", "Kitchen Access"],
                        "posted" => "2 days ago",
                        "image" => "/assets/images/apartment.jpg"
                    ],
                    [
                        "title" => "Studio Apartment",
                        "price" => 28000,
                        "period" => "month",
                        "location" => "Reid Avenue, Colombo 7",
                        "distance" => "1.2km from UOC",
                        "university" => "University of Colombo",
                        "type" => "Studio",
                        "amenities" => ["Attached Bathroom", "Kitchen", "WiFi", "Parking"],
                        "posted" => "3 days ago",
                        "image" => "/assets/images/studio-apartment.jpg"
                    ]
                ];

                foreach ($recent as $r): ?>
                    <div class="feed-card">
                        <img src="<?= $r['image'] ?>" alt="Boarding Image" class="feed-card-img">
                        <div class="feed-card-body">
                            <h4><?= $r["title"] ?></h4>
                            <p class="price">Rs. <?= number_format($r["price"]) ?>/<?= $r["period"] ?></p>
                            <p class="loc"><?= $r["location"] ?> • <?= $r["distance"] ?></p>
                            <p class="uni"><?= $r["university"] ?></p>
                            <div class="tags">
                                <span><?= $r["type"] ?></span>
                                <?php foreach (array_slice($r["amenities"], 0, 2) as $a): ?>
                                    <span><?= $a ?></span>
                                <?php endforeach; ?>
                            </div>
                            <button class="view-btn">View Details</button>
                            <p class="posted">Posted <?= $r["posted"] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="load-btn">Load More Listings</button>
        </section>

    </div>

 <?php component("widgetPanel"); ?>

</div>