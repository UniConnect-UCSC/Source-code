
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
        <div class="search-wrapper">
            <i data-lucide="search" class="search-icon" aria-hidden="true"></i>
            <input type="search" id="search-bar" data-search class="search" placeholder="Search Kuppi" aria-label="Search" />
        </div> 
        <div class="kuppi-feed-actions">
            <a href="javascript:void(0);" class="btn" onclick="openRequestKuppiModal(kuppiCategories)">Request a Kuppi</a>
            <a href="javascript:void(0);" class="btn" onclick="openHostKuppiModal(kuppiCategories)">Host Kuppi</a>
            <a href="javascript:void(0);" class="btn" onclick="openMyKuppisModal()">My Kuppis</a>
            <a href="javascript:void(0);" class="btn" onclick="openKuppiRequestsModal()">Kuppi Requests</a>
        </div>
        <div class="kuppi-feed kuppi-fade-in" id="kuppi-container">
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
                    <div class="kuppi-modal-tabs" >
                            <button id="myHosts">My Hosts </button>
                            <button id="myRequests">My Requests </button>
                            <button id="reports">Reports </button>
                            <button id="myAttends">My Attends </button>
                            <button id="myFavourites">My Favourites </button>
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
<script src="/assets/js/kuppi/renderKuppiCard.js"></script>
<script src="/assets/js/kuppi/searchBar.js" defer></script>
<script src="/assets/js/kuppi/index.js"></script>
<script src="/assets/js/kuppi/kuppiCardActions.js"></script>