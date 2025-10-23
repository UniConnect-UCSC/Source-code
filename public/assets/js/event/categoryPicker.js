(function(){
  const picker = document.getElementById('categoryPicker');
  const input = document.getElementById('categoryInput');
  const suggestions = document.getElementById('categorySuggestions');
  const tagsContainer = document.getElementById('selectedCategories');
  const hidden = document.getElementById('eventCategories');

  if (!picker || !input || !suggestions || !tagsContainer || !hidden) return;

  // Default category suggestions (can be replaced by API later)
  const defaultCategories = [
    'All','Sports','Music','Computer Science','Business','Arts','Networking','Wellness',
    'Tech','Career','Workshop','Seminar','Lecture','Competition','Hackathon','Meetup','Health','Finance','Culture'
  ];

  let selected = [];
  try { selected = JSON.parse(hidden.value || '[]'); } catch(_) { selected = []; }

  function syncHidden(){
    hidden.value = JSON.stringify(selected);
  }

  function renderTags(){
    tagsContainer.innerHTML = '';
    selected.forEach((name, idx) => {
      const chip = document.createElement('span');
      chip.className = 'tag-chip';
      chip.textContent = name;

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'remove-btn';
      btn.setAttribute('aria-label', `Remove ${name}`);
      btn.textContent = '×';
      btn.addEventListener('click', () => {
        selected.splice(idx,1);
        renderTags();
        syncHidden();
        filterAndRender(input.value.trim());
      });

      chip.appendChild(document.createTextNode(' '));
      chip.appendChild(btn);
      tagsContainer.appendChild(chip);
    });
  }

  function uniqueNormalized(list){
    const seen = new Set();
    const out = [];
    list.forEach(v => {
      const k = v.toLowerCase();
      if(!seen.has(k)) { seen.add(k); out.push(v); }
    });
    return out;
  }

  function filterAndRender(q){
    const ql = q.toLowerCase();
    const pool = defaultCategories.filter(c => !selected.some(s => s.toLowerCase() === c.toLowerCase()));
    const matches = ql ? pool.filter(c => c.toLowerCase().includes(ql)) : pool.slice(0,8);
    renderSuggestions(matches);
  }

  function renderSuggestions(items){
    suggestions.innerHTML = '';
    if(!items.length){
      suggestions.classList.remove('show');
      input.setAttribute('aria-expanded','false');
      return;
    }

    items.forEach((name, i) => {
      const div = document.createElement('div');
      div.className = 'suggestion-item';
      div.setAttribute('role','option');
      div.setAttribute('id', `cat-opt-${i}`);
      div.textContent = name;
      div.addEventListener('click', () => {
        addCategory(name);
      });
      suggestions.appendChild(div);
    });
    suggestions.classList.add('show');
    input.setAttribute('aria-expanded','true');
  }

  function addCategory(name){
    if (!name) return;
    if (selected.some(s => s.toLowerCase() === name.toLowerCase())) return;
    selected.push(name);
    selected = uniqueNormalized(selected).slice(0, 12); // hard cap to avoid abuse
    renderTags();
    syncHidden();
    input.value = '';
    filterAndRender('');
  }

  input.addEventListener('input', (e) => {
    filterAndRender(e.target.value.trim());
  });

  input.addEventListener('focus', () => {
    filterAndRender(input.value.trim());
  });

  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && input.value.trim()) {
      e.preventDefault();
      addCategory(input.value.trim());
    } else if (e.key === 'Backspace' && !input.value && selected.length) {
      selected.pop();
      renderTags();
      syncHidden();
      filterAndRender('');
    }
  });

  document.addEventListener('click', (e) => {
    if (!picker.contains(e.target)) {
      suggestions.classList.remove('show');
      input.setAttribute('aria-expanded','false');
    }
  });


  // Initial render
  renderTags();
  filterAndRender('');

  // Clear categories if the form is reset
  const eventForm = document.getElementById('eventForm');
  if (eventForm) {
    eventForm.addEventListener('reset', () => {
      selected = [];
      renderTags();
      syncHidden();
      filterAndRender('');
    });
  }

  // Image preview handling
  const imgInput = document.getElementById('eventImage');
  const previewWrapper = document.getElementById('eventImagePreviewWrapper');
  const previewImg = document.getElementById('eventImagePreview');
  const clearBtn = document.getElementById('clearEventImageBtn');

  function clearImage(){
    if (!imgInput) return;
    imgInput.value = '';
    if (previewWrapper && previewImg) {
      previewImg.removeAttribute('src');
      previewWrapper.style.display = 'none';
    }
  }

  if (imgInput) {
    imgInput.addEventListener('change', () => {
      const file = imgInput.files && imgInput.files[0];
      if (!file) { clearImage(); return; }
      const reader = new FileReader();
      reader.onload = () => {
        if (previewImg && previewWrapper) {
          previewImg.src = reader.result;
          previewWrapper.style.display = 'flex';
        }
      };
      reader.readAsDataURL(file);
    });
  }

  if (clearBtn) clearBtn.addEventListener('click', clearImage);
})();
