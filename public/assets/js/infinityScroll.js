/** Constructor for InfinityScroll class
 * @param {string} scrollIdentifier (This is the ID that get's passed to the server for requesting which data is required)
 * @param {string} fetchUrl (URL to fetch data from)
 * @param {HTMLElement} parentElement (Element to append the fetched data to)
 * @param {(data: any) => HTMLElement} renderer (Function that takes data and returns an HTMLElement[card] to be appended)
 * @param {number} [offset] (Starting offset for data fetching defaults to 0)
 * @param {number} [limit] (Number of items to fetch per request defaults to 10)
*/


class InfinityScroll{
    

    constructor(scrollIdentifier, fetchUrl, parentElement, renderer, offset = 0, limit = 10){
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
    }

    #defaultSkeletonLoader(){
        const loader = document.createElement('div');
        loader.className = 'infinity-scroll-loader';
        
        const spinner = document.createElement('div');
        spinner.className = 'infinity-scroll-spinner';
        
        loader.appendChild(spinner);
        
        return loader;
    }

    setSkeletonLoader(loaderFunction) {
        this.skeletonLoader = loaderFunction;
    }

    abort(reason = 'Aborted by user') {
        if (this.abortController) {
            this.abortController.abort(reason);
        }
    }

    resetScroll() {
        this.offset = 0;
        this.endReached = false;
        this.parentElement.innerHTML = '';
    }

    async refresh(context = null) {
        this.resetScroll();
        return this.loadNextElements(context);
    }

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

        if(context){data['context'] = context;}

        try {
            const response = await Ajax.jsonPost(this.fetchUrl, data, this.abortController.signal);

            // Remove skeleton loader
            skeletonLoader.remove();

            if(!Array.isArray(response) || response.length === 0){
                this.endReached = true;
                this.loading = false;
                console.warn('InfinityScroll.loadNextElements: No data received or data is not an array');
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

            this.offset += this.limit;

            this.loading = false;
            this.abortController = null;
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