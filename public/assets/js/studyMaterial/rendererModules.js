const formatDate = (date) => {
    if (!date || isNaN(date)) return '';
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

function smCardRenderer(data){
    const id = data.id || '';
    const title = data.title || '';
    const category = data.category || '';
    const type = data.type || '';
    const views = data.view_count || 0;
    const uploadedDate = data.created_at ? new Date(data.created_at) : null;
    const isUpdated = data.is_updated || false;
    const description = data.description || '';

    // Format views count (e.g., 1200 -> 1.2K)
    const formatViewCount = (count) => {
        if (count >= 1000) {
            return (count / 1000).toFixed(1) + 'K';
        }
        return count.toString();
    };

    var badge = '???';
    switch(type.toLowerCase()) {
        case 'document':
            badge = 'DOC';
            break;
        case 'video':
            badge = 'VIDEO';
            break;
        case 'link':
            badge = 'LINK';
            break;
    }

    // Root article
    const article = document.createElement('article');
    article.className = 'sm-material-card';
    article.setAttribute('data-type', type);
    article.setAttribute('data-category', category);
    article.setAttribute('data-views', views);
    if (uploadedDate && !isNaN(uploadedDate)) {
        article.setAttribute('data-ts', uploadedDate.toISOString());
    }

    // Header
    const header = document.createElement('div');
    header.className = 'sm-material-header';
    article.appendChild(header);

    const typeBadge = document.createElement('div');
    typeBadge.className = 'sm-type-badge ' + type.toLowerCase();
    typeBadge.textContent = badge;
    header.appendChild(typeBadge);

    // Share button
    const shareBtn = document.createElement('button');
    shareBtn.className = 'sm-share-btn';
    shareBtn.type = 'button';
    shareBtn.setAttribute('aria-label', 'Share');
    const shareIcon = document.createElement('i');
    shareIcon.setAttribute('data-lucide', 'share-2');
    shareIcon.className = 'sm-share-icon';
    shareBtn.appendChild(shareIcon);
    header.appendChild(shareBtn);

    shareBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const ev = new CustomEvent('sm:share-click', {
            bubbles: true,
            detail: {
                id: id,
            }
        });
        shareBtn.dispatchEvent(ev);
    });

    // Body
    const body = document.createElement('div');
    body.className = 'sm-material-body';
    article.appendChild(body);

    const titleElement = document.createElement('h4');
    titleElement.className = 'sm-material-title';
    titleElement.textContent = title;
    body.appendChild(titleElement);

    const categoryTag = document.createElement('span');
    categoryTag.className = 'sm-category-tag';
    categoryTag.textContent = category;
    body.appendChild(categoryTag);

    const descriptionElement = document.createElement('p');
    descriptionElement.className = 'sm-material-description';
    descriptionElement.textContent = description;
    body.appendChild(descriptionElement);

    // Footer
    const footer = document.createElement('div');
    footer.className = 'sm-material-footer';
    article.appendChild(footer);

    const footerItems = document.createElement('div');
    footerItems.className = 'sm-footer-items';
    footer.appendChild(footerItems);

    // Views item
    const viewsItem = document.createElement('div');
    viewsItem.className = 'sm-footer-item';
    const viewsIcon = document.createElement('i');
    viewsIcon.setAttribute('data-lucide', 'eye');
    viewsIcon.className = 'sm-footer-icon';
    const viewsText = document.createElement('span');
    viewsText.className = 'sm-footer-text';
    viewsText.textContent = formatViewCount(views) + ' views';
    viewsItem.appendChild(viewsIcon);
    viewsItem.appendChild(viewsText);
    footerItems.appendChild(viewsItem);

    // Date item
    const dateItem = document.createElement('div');
    dateItem.className = 'sm-footer-item';
    const dateIcon = document.createElement('i');
    dateIcon.setAttribute('data-lucide', 'calendar');
    dateIcon.className = 'sm-footer-icon';
    const dateText = document.createElement('span');
    dateText.className = 'sm-footer-text';
    dateText.textContent = isUpdated ? formatDate(uploadedDate) + ' (edited)' : formatDate(uploadedDate);

    dateItem.appendChild(dateIcon);
    dateItem.appendChild(dateText);
    footerItems.appendChild(dateItem);

    // View button
    const viewBtn = document.createElement('button');
    viewBtn.className = 'sm-view-btn';
    viewBtn.type = 'button';
    viewBtn.textContent = 'View';
    footer.appendChild(viewBtn);

    viewBtn.addEventListener('click', () => {
    const ev = new CustomEvent('sm:view-click', {
        bubbles: true,
        detail: {
            id: id,
        }
    });
    viewBtn.dispatchEvent(ev);
    });

    return article;
}

function smFormSuggestionRenderer(data){
    const id = data.id || '';
    const name = data.name || '';
    
    const suggestionItem = document.createElement('div');
    suggestionItem.className = 'suggestion-item';
    suggestionItem.setAttribute('data-id', id);
    suggestionItem.textContent = name;

    suggestionItem.addEventListener('mousedown', (e) => {
        e.preventDefault(); // Prevents the input from losing focus

        const ev = new CustomEvent('sm:category-suggestion-click', {
            bubbles: true,
            detail: {
                element: suggestionItem
            }
        });
        suggestionItem.dispatchEvent(ev);
    });

    return suggestionItem;
}

function mySMRecordRenderer(data){
    const id = data.id || '';
    const title = data.title || '';
    const category = data.category || '';
    const categoryId = data.category_id || '';
    const type = data.type || '';
    const description = data.description || '';
    const uploadedDate = data.created_at || '';



    // Determine icon based on type
    var typeIcon = '???';
    switch(type.toLowerCase()) {
        case 'document':
            typeIcon = 'PDF';
            break;
        case 'video':
            typeIcon = 'VID';
            break;
        case 'link':
            typeIcon = 'LNK';
            break;
    }

    // Root manage row with all data attributes
    const row = document.createElement('div');
    row.className = 'sm-manage-row';
    row.setAttribute('data-id', id);
    row.setAttribute('data-title', title);
    row.setAttribute('data-category', category);
    row.setAttribute('data-category-id', categoryId);
    row.setAttribute('data-description', description);

    // Material info cell
    const materialInfoCell = document.createElement('div');
    materialInfoCell.className = 'sm-manage-cell material-info';
    row.appendChild(materialInfoCell);

    const itemIcon = document.createElement('div');
    itemIcon.className = 'sm-item-icon';
    itemIcon.textContent = typeIcon;
    materialInfoCell.appendChild(itemIcon);

    const itemTitle = document.createElement('h4');
    itemTitle.className = 'sm-item-title';
    itemTitle.textContent = title;
    materialInfoCell.appendChild(itemTitle);

    // Type cell
    const typeCell = document.createElement('div');
    typeCell.className = 'sm-manage-cell';
    typeCell.textContent = type;
    row.appendChild(typeCell);

    // Category cell
    const categoryCell = document.createElement('div');
    categoryCell.className = 'sm-manage-cell';
    categoryCell.textContent = category;
    row.appendChild(categoryCell);

    // Date cell
    const dateCell = document.createElement('div');
    dateCell.className = 'sm-manage-cell';
    dateCell.textContent = formatDate(new Date(uploadedDate));
    row.appendChild(dateCell);

    // Actions cell
    const actionsCell = document.createElement('div');
    actionsCell.className = 'sm-manage-cell actions';
    row.appendChild(actionsCell);

    const editBtn = document.createElement('button');
    editBtn.className = 'icon-btn edit-btn';
    editBtn.type = 'button';
    const editIcon = document.createElement('i');
    editIcon.setAttribute('data-lucide', 'edit-2');
    editBtn.appendChild(editIcon);
    actionsCell.appendChild(editBtn);

    const deleteBtn = document.createElement('button');
    deleteBtn.className = 'icon-btn delete-btn';
    deleteBtn.type = 'button';
    const deleteIcon = document.createElement('i');
    deleteIcon.setAttribute('data-lucide', 'trash-2');
    deleteBtn.appendChild(deleteIcon);
    actionsCell.appendChild(deleteBtn);

    return row;
}