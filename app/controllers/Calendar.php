<?php 
class calendar extends Controller
{

    private function getKuppiFavoritesAndParticipants()
    {
        $userId = $_SESSION['user_id'];

        require_once __DIR__ . '/../models/Kuppi.php';
        $kuppiModel = new KuppiModel();
        return $kuppiModel->getKuppiFavoritesAndParticipants($userId);

    }

    private function getEventsFavoritesAndParticipants()
    {
        require_once __DIR__ . '/../models/Event.php';        

        $userId = $_SESSION['user_id'];
        $eventModel = new EventModel();
        return $eventModel->getEventFavoritesAndParticipants($userId);
    }

    public function getData(){
        $data = [];

        $data['events'] = $this->getEventsFavoritesAndParticipants();
        $data['kuppi'] = $this->getKuppiFavoritesAndParticipants();

        header('Content-Type: application/json');
        echo json_encode($data);
        return;
    }

    public function index()
    {
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
        ]);
    }
}