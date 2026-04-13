<script>
const kuppiCategories = <?php echo json_encode(array_values((array)($kuppiCategories ?? []))); ?>;
</script>


<?php component("navbar"); ?>

<!-- Kuppi Modal styles -->
<link rel="stylesheet" href="/assets/css/components/kuppi/kuppiModal.css">
<link rel="stylesheet" href="/assets/css/components/kuppi/kuppiRibbon.css">
<link rel="stylesheet" href="/assets/css/components/kuppi/validation.css">

<div class="home-layout">
    <!-- Left Nav Panel -->
    <?php component("navPanel"); ?>

    <!-- Center Feed -->
    <div class="feed">
        <header class="kuppi-page-header">
            <h1>Kuppi Sessions</h1>
            <div class="subtitle">Discover and join peer-led study sessions</div>
        </header>
        <div class="controls-top">
            <div class="search-container">
                <div class="search-input-wrapper active" id="searchInputWrapper">
                    <button class="search-btn" id="searchToggleBtn" type="button" aria-label="Search">
                        <i data-lucide="search"></i>
                    </button>
                    <input type="text" class="search-input" id="searchInput" placeholder="Search Kuppi Sessions">
                    <div class="search-suggestions" id="searchSuggestions"></div>
                </div>
                <script>lucide.createIcons();</script>
            </div>
                <div class="filter-buttons" id="filterButtons">
                    <button class="btn active" id="for-you-btn" data-filter="for-you" onclick="onForYouClick(event)">For You</button>
            </div>

        </div>
        <div class="categories-wrapper" id="categoryWrapper">
            <button class="category-btn active" id="allCategoriesBtn">All</button>
            <div class="categories-section" id="categoriesSection">
            </div>
        </div>
        <div class="kuppi-feed-actions">
            <a href="javascript:void(0);" class="btn" onclick="openRequestKuppiModal(kuppiCategories)">Request a Kuppi</a>
            <a href="javascript:void(0);" class="btn" onclick="openHostKuppiModal(kuppiCategories)">Host Kuppi</a>
            <a href="javascript:void(0);" class="btn" onclick="openMyKuppisModal()">Manage Kuppi</a>
        </div>
        <div class="kuppi-grid" id="kuppi-container">
        </div>
    </div>


    <?php component("widgetPanel"); ?>
    <!-- show kuppi details  -->   
    <div id="kuppiModal" class="kuppi-modal-overlay" style="display:none;">
        <div class="kuppi-modal-content">
            <button class="kuppi-modal-close" onclick="closeKuppiModal()">&times;</button>
            <div id="kuppiModalBody"></div>
        </div>
    </div> 

    <!-- dedicated modal for My Kuppis -->
    <div id="myKuppisModal" class="kuppi-modal-overlay" style="display:none;">
        <div class="kuppi-modal-content" style="max-width:900px;">
            <button class="kuppi-modal-close" onclick="closeMyKuppisModal()">&times;</button>
    
            <div id="myKuppisModalBody">
                    <div class="kuppi-modal-header-row" style="display:flex; align-items:center; justify-content:center; gap:12px;">
                        <h2 style="margin: 20px;0;">My Hosts and Requests</h2>
                    </div>
                    <div class="kuppi-modal-tabs">
                            <button id="myHosts">My Hosts</button>
                            <button id="myRequests">My Requests</button>
                            <button id="reports">Reports</button>
                            <button id="myAttends">My Attends</button>
                            <button id="myFavourites">My Favourites</button>
                    </div>

                    <!-- Sub-filters per tab (only the active tab's row is visible) -->
                    <div class="kuppi-modal-filters" id="my-hosts-filters" data-tab="myHosts">
                        <button type="button" data-status-filter="" class="active">All</button>
                        <button type="button" data-status-filter="In Progress">In Progress</button>
                        <button type="button" data-status-filter="Completed">Completed</button>
                        <button type="button" data-status-filter="Upcoming">Upcoming</button>
                        <button type="button" data-status-filter="Cancelled">Cancelled</button>
                    </div>

                    <div class="kuppi-modal-filters" id="my-requests-filters" data-tab="myRequests">
                        <button type="button" data-status-filter="">All</button>
                        <button type="button" data-status-filter="Requested">Remain Request</button>
                        <button type="button" data-status-filter="In Progress">Volunteered</button>
                        <button type="button" data-status-filter="Completed">Completed</button>
                    </div>

                    <!-- Reports: no sub-filters -->

                    <div class="kuppi-modal-filters" id="my-attends-filters" data-tab="myAttends">
                        <button type="button" data-status-filter="">All</button>
                        <button type="button" data-status-filter="Upcoming">Upcoming</button>
                        <button type="button" data-status-filter="In Progress">In Progress</button>
                        <button type="button" data-status-filter="Completed">Completed</button>
                        <button type="button" data-status-filter="Reviewed">Reviewed</button>
                    </div>

                    <div class="kuppi-modal-filters" id="my-favourites-filters" data-tab="myFavourites">
                        <button type="button" class="manage-topics-btn" id="manageFavTopicsBtn">&#9733; Manage Topics</button>
                    </div>

                    <div class="kuppi-modal-list" id="my-kuppi-content"></div>
            </div>
        </div>
    </div>

    <div id="kuppiRequestsModal" class="kuppi-modal-overlay" style="display:none;">
        <div class="kuppi-modal-content" style="max-width:600px;">
            <button class="kuppi-modal-close" onclick="closeKuppiRequestsModal()">&times;</button>
            <div id="kuppiRequestsModalBody">
                <h2>Kuppi Requests</h2>
                <div class="kuppi-modal-list" id="kuppi-requests-content"></div>
            </div>
        </div>
    </div>

<div id="reportKuppiModal" class="kuppi-modal-overlay" style="display:none;">
  <div class="kuppi-modal-content" style="max-width:420px;">
    <button class="kuppi-modal-close" onclick="closeReportKuppiModal()">&times;</button>
    <div id="reportKuppiModalBody">
      <h3>Report Kuppi</h3>
      <p>Are you sure you want to report this Kuppi?</p>
      <div class="kuppi-modal-actions" style="display:flex; gap:12px; justify-content:flex-end; margin-top:16px;">
        <button class="btn btn-danger" id="confirmReportKuppi">Yes, Report</button>
        <button class="btn" onclick="closeReportKuppiModal()">Cancel</button>
      </div>
    </div>
  </div>
</div>

</div>

<script src="/assets/js/kuppi/kuppiModal.js"></script>
<script src="/assets/js/kuppi/reportKuppiModal.js"></script>
<script src="/assets/js/kuppi/validation.js"></script>
<script src="/assets/js/kuppi/hostKuppiModal.js"></script>
<script src="/assets/js/kuppi/requestKuppiModal.js"></script>
<script src="/assets/js/kuppi/volunteerKuppiModal.js"></script>
<script src="/assets/js/kuppi/editKuppiModal.js"></script>
<script src="/assets/js/kuppi/editKuppiRequestModal.js"></script>
<script src="/assets/js/kuppi/myKuppisModal.js"></script>
<script src="/assets/js/kuppi/kuppiRequestsModal.js"></script>
<script src="/assets/js/kuppi/filters.js"></script>
<script src="/assets/js/kuppi/openChangeStatusModal.js"></script>
<script src="/assets/js/kuppi/openReviewModal.js"></script>
<script src="/assets/js/kuppi/renderKuppiCard.js"></script>
<script src="/assets/js/kuppi/renderKuppiCategories.js"></script>
<script src="/assets/js/kuppi/searchBar.js" defer></script>
<script src="/assets/js/kuppi/kuppiCardActions.js"></script>
<script src="/assets/js/kuppi/index.js"></script>