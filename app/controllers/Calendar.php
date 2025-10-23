<?php 
class calendar extends Controller
{
    public function index()
    {
        // Example bookmarked dates
        $bookmarkedDates = ['2024-10-15', '2024-10-20']; // Format: ['YYYY-MM-DD', 'YYYY-MM-DD', ...]

        $this->view('calendar', [
            'title' => 'Calendar • UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/feed.css">
            <link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
            <link rel="stylesheet" href="/assets/css/components/eventsWidget.css">
            <link rel="stylesheet" href="/assets/css/components/calender.css">
            ',
            'bookmarkedDates' => $bookmarkedDates,
        ]);
    }
}