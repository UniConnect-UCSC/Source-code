<script src="/assets/js/calender.js"></script>

<div class="simple-calendar">
  <div class="simple-calendar-header">
    <button id="prevMonthBtn">&lt;</button>
    <span id="calendarMonthYear"></span>
    <button id="nextMonthBtn">&gt;</button>
  </div>
  <table class="simple-calendar-table">
    <thead>
      <tr>
        <th>Su</th><th>Mo</th><th>Tu</th><th>We</th><th>Th</th><th>Fr</th><th>Sa</th>
      </tr>   
    </thead>
    <tbody id="calendarBody"></tbody>
  </table>
</div>
<script>
  // Make bookmarkedDates available to JS
  var bookmarkedDates = <?= json_encode($bookmarkedDates ?? []) ?>;
  var currentDate = new Date();
  // Dummy items: a few events and kuppis in the current month
  (function(){
    function pad(n){return n.toString().padStart(2,'0');}
    const base = new Date();
    const y = base.getFullYear();
    const m = base.getMonth() + 1; // 1-based for human-readable date string
    window.calendarItems = [
      { date: `${y}-${pad(m)}-03`, type: 'event', title: 'Welcome Fair', time: '09:00' },
      { date: `${y}-${pad(m)}-07`, type: 'kuppi', title: 'DSA Kuppi', time: '14:00' },
      { date: `${y}-${pad(m)}-12`, type: 'event', title: 'Tech Talk: AI', time: '16:30' },
      { date: `${y}-${pad(m)}-15`, type: 'kuppi', title: 'Algorithms Kuppi', time: '11:00' },
      { date: `${y}-${pad(m)}-20`, type: 'event', title: 'Career Expo', time: '10:00' },
      { date: `${y}-${pad(m)}-25`, type: 'kuppi', title: 'DBMS Kuppi', time: '13:00' }
    ];
  })();
</script>
