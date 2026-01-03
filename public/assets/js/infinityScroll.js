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
    }

    resetScroll() {
        this.offset = 0;
        this.endReached = false;
        this.parentElement.innerHTML = '';
    }

    async refresh() {
        this.resetScroll();
        return this.loadNextElements();
    }

    async loadNextElements(context = null){
        if (this.loading || this.endReached) return;
        this.loading = true;

        const data = {
            scrollIdentifier: this.scrollIdentifier,
            offset: this.offset,
            limit: this.limit
        }

        if(context){data['context'] = context;}

        try {
            const response = await Ajax.jsonPost(this.fetchUrl, data);

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
            return true;

        } catch(error){
            console.error('InfinityScroll.loadNextElements: Error fetching data', error);

            this.loading = false;
            return false;
        }
    }
}