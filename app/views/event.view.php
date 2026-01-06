<?php component('formEventModal'); ?>
<?php component('viewEventsModal', ['headerName' => 'My Events']); ?>


<?php component("navbar"); ?>

<div class="home-layout">

<?php component("navPanel"); ?>

<div class="feed">
    <header>
        <h1>University Events</h1>
        <p class="subtitle">Discover and connect with your university community</p>
    </header>

    <div class="controls">
            <div class="controls-top">
                <div class="search-container">
                
                    <!--change the icon to an actual icon image -->
                    <button class="search-btn" id="searchToggleBtn">🔍</button>
                    <div class="search-input-wrapper" id="searchInputWrapper">
                        <input type="text" class="search-input" id="searchInput" placeholder="Search events...">
                    </div>
                </div>
                <div class="filter-buttons" id="filterButtons">
                    <button class="btn active" data-filter="for-you">For You</button>
                    <button class="btn" data-filter="my-university">My University</button>
                </div>

                <button class="btn btn-primary" id="viewFavoritesBtn">+ My Favorites</button>

            </div>

            <div class="categories-wrapper" id="categoryWrapper">
                    <button class="category-btn active" id="allCategoriesBtn">All</button>
                <div class="categories-section" id="categoriesSection">

                </div>

            </div>

            <div class='rep-btn-wrapper'>
                <button class="btn btn-primary" id="createEventBtn">Create Event</button>
                <button class="btn btn-primary" id="viewEventBtn">Manage Events</button>
            </div>

        </div>

    <div class="events-grid" id="eventsGrid">
    </div>
</div>

<?php component("widgetPanel"); ?>

</div>


<script src="/assets/js/event/rendererModules.js"></script>
<script src="/assets/js/event/scrollable.js"></script>
<script src="/assets/js/event/form.js"></script>
<script src="/assets/js/event/viewRepEvents.js"></script>
<script src="/assets/js/event/eventCard.js"></script>

<script src="/assets/js/event/event.js"></script>