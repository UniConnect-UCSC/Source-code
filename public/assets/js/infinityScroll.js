/** Constructor for InfinityScroll class
 * @param {string} scrollIdentifier (This is the ID that get's passed to the server for requesting which data is required)
 * @param {string} fetchUrl (URL to fetch data from)
 * @param {HTMLElement} parentElement (Element to append the fetched data to)
 * @param {(data: any) => HTMLElement} renderer (Function that takes data and returns an HTMLElement[card] to be appended)
 * @param {number} [offset] (Starting offset for data fetching defaults to 0)
 * @param {number} [limit] (Number of items to fetch per request defaults to 10)
 * 
 * 
*/


class InfinityScroll extends EventTarget {
    

    constructor(scrollIdentifier, fetchUrl, parentElement, renderer, offset = 0, limit = 10){
        super();
        if (!window.Ajax) throw new Error('InfinityScroll requires Ajax class to be loaded.');
        this.scrollIdentifier = scrollIdentifier;
        this.fetchUrl = fetchUrl;
        this.parentElement = parentElement;
        this.renderer = renderer;
        this.offset = offset;
        this.limit = limit;
        this.loading = false;
        this.endReached = false;
        this.maxRefreshLimit = 100;
        this.skeletonLoader = this.#defaultSkeletonLoader;
        this.abortController = null;

        //Observer for auto loading on scroll
        this.contextProvider = null;
        this.observer = null;
        this.prevObserverElement = null;
    }

    #observeLastElement(){
        if(this.observer){
            //Unobserve previous last element
            if(this.prevObserverElement){
                this.observer.unobserve(this.prevObserverElement);
            }

            const lastElement = this.parentElement.lastElementChild;
            this.observer.observe(lastElement);
            this.prevObserverElement = lastElement;
        }
    } 

    #defaultSkeletonLoader(){
        const loader = document.createElement('div');
        loader.className = 'infinity-scroll-loader';
        
        const spinner = document.createElement('div');
        spinner.className = 'infinity-scroll-spinner';
        
        loader.appendChild(spinner);
        
        return loader;
    }

    /**
     * Sets up an IntersectionObserver to automatically load the next set of elements 
     * when the last element in the list comes into view.
     */
    setupAutoLoadOnScroll() {
        this.observer = new IntersectionObserver((entries) => {
            const entry = entries[0];
            if (entry.isIntersecting) {
                this.loadNextElements();
            }
        }, {
            root: null,
            rootMargin: '0px',
            threshold: 0
        });
    }

    /**
     * Sets a function designed to provide context for the fetch request.
     * This function is called before every load request to gather dynamic parameters (like search terms or filters).
     * @param {() => object | Promise<object>} contextFunction - A function that returns an object containing context data. (async or sync is allowed)
     */
    setContextProvider(contextFunction) {
        this.contextProvider = contextFunction;
    }

    /**
     * Sets a custom function to create the skeleton loader element.
     * @param {() => HTMLElement} loaderFunction - A function that returns an HTMLElement to be used as a loader.
     */
    setSkeletonLoader(loaderFunction) {
        this.skeletonLoader = loaderFunction;
    }

    /**
     * Aborts the current active fetch request.
     * @param {string} [reason='Aborted by user'] - The reason for aborting the request.
     */
    abort(reason = 'Aborted by user') {
        if (this.abortController) {
            this.abortController.abort(reason);
        }
    }

    /**
     * Resets the scroll state.
     * Sets offset to 0, resets endReached flag, and clears the parent element's content.
     */
    resetScroll() {
        this.offset = 0;
        this.endReached = false;
        this.parentElement.innerHTML = '';
    }

    /**
     * Resets the scroll and immediately loads the initial set of elements.
     * Useful when filters or search terms change.
     * @param {object} [context=null] - Optional context data to pass to the initial load.
     * @returns {Promise<boolean>} - Resolves to true if data was successfully loaded.
     */
    async refresh(context = null) {
        this.resetScroll();
        return this.loadNextElements(context);
    }

    /**
     * Fetches and appends the next batch of elements to the parent element.
     * Handles loading states, skeleton loaders, and error handling.
     * 
     * Events dispatched:
     * - 'successfulLoad': Dispatched when new elements are successfully loaded and appended.
     * - 'endReached': Dispatched when there is no more data to load
     * 
     * @param {object} [context=null] - Extra data to be sent with the POST request (Passed in data is prioritized over contextProvider if defined)
     * @returns {Promise<boolean>} - Resolves to true if new data was loaded, false otherwise.
     */
    async loadNextElements(context = null){
        if (this.loading || this.endReached) return;

        this.abortController = new AbortController();
        this.loading = true;

        // Show skeleton loader
        const skeletonLoader = this.skeletonLoader();
        this.parentElement.appendChild(skeletonLoader);

        const data = {
            scrollIdentifier: this.scrollIdentifier,
            offset: this.offset,
            limit: this.limit
        }

        if(this.contextProvider && !context){
            context = await this.contextProvider();
        }
        if(context){data['context'] = context;}

        try {
            const response = await Ajax.jsonPost(this.fetchUrl, data, this.abortController.signal);

            // Remove skeleton loader
            skeletonLoader.remove();

            if(!Array.isArray(response) || response.length === 0){
                this.endReached = true;
                this.loading = false;
                console.warn('InfinityScroll.loadNextElements: No data received or data is not an array');
                this.dispatchEvent(new Event('endReached'));
                return false;
            }

            if(response.length < this.limit){
                this.endReached = true;
                console.warn('InfinityScroll.loadNextElements: No more data to load, end reached');
            }

            response.forEach(item => {
                const element = this.renderer(item);
                this.parentElement.appendChild(element);
            });

            this.#observeLastElement();

            this.offset += this.limit;

            this.dispatchEvent(new Event('successfulLoad'));
            if(this.endReached){this.dispatchEvent(new Event('endReached'));}

            this.abortController = null;
            this.loading = false;
            return true;

        } catch(error){
            if(!this.abortController.signal.aborted){
                console.warn('InfinityScroll.loadNextElements: Error fetching data', error);
            }

            // Remove skeleton loader on error
            skeletonLoader.remove();

            this.loading = false;
            this.abortController = null;
            return false;
        }
    }
}