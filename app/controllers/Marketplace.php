<?php
require_once(__DIR__ . "/../models/MarketplaceItem.php");
require_once(__DIR__ . "/../models/ItemCategory.php");

class Marketplace extends Controller
{
    public function index()
    {
        $allitemsModel = new MarketplaceItem();
        $items = $allitemsModel->getItems();

        $this->view('marketplace', [
            'title' => 'Marketplace - UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/marketplace.css">
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/createPost.css">
            <link rel="stylesheet" href="/assets/css/components/marketplaceFeed.css">
            <link rel="stylesheet" href="/assets/css/components/marketplaceCard.css">
            

            ',
            'items' => $items
        ]);
    }

    public function sellItem()
    {
        $categoryModel = new itemcategorymodel();
        $categories = $categoryModel->getAllCategories();

        $this->view('sellItem', [
            'title' => 'Sell an Item - UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
            <link rel="stylesheet" href="/assets/css/components/eventsWidget.css">
            <link rel="stylesheet" href="/assets/css/pages/marketplace.css">
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/feed.css">
            <link rel="stylesheet" href="/assets/css/components/createPost.css">
            ',
            'categories' => $categories
        ]);
    }

    public function myItems()
    {
        $itemModel = new MarketplaceItem();
        $items = $itemModel->getMyitems($_SESSION['user_id']);

        $this->view('myItems', [
            'title' => 'My Items - UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/marketplace.css">
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/feed.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/marketplaceItem.css">
            <link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
            <link rel="stylesheet" href="/assets/css/components/eventsWidget.css">
            <link rel="stylesheet" href="/assets/css/components/createPost.css">
            ',
            'items' => $items
        ]);
    }
    public function createItem()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemModel = new MarketplaceItem();
            $itemData = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'category_id' => $_POST['category_id'],
                'student_id' => $_SESSION['user_id'],
                'status' => 'available'
            ];
            $itemModel->create($itemData);
            header('Location: /marketplace');
            exit();
        } else {
            header('Location: /marketplace/sellItem');
            exit();
        }
    }

    public function editItem($itemId)
    {
        $itemModel = new MarketplaceItem();
        $item = $itemModel->getitemsById($itemId);
        $statusOptions = $itemModel->getStatusOptions();

        if (!$item) {
            echo "Item not found";
            header('Location: /marketplace');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemData = [
                'id' => $itemId,
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'updated_at' => date('Y-m-d H:i:s'),
                'status' => $_POST['status']
            ];
            $itemModel->updateItem($itemData);
            header('Location: /marketplace/myItems');
            exit();
        }

        // Render the edit form on GET
        $this->view('editItem', [
            'title' => 'Edit Item - UniConnect',
            'head' => '
                <link rel="stylesheet" href="/assets/css/pages/marketplace.css">
                <link rel="stylesheet" href="/assets/css/pages/home.css">
                <link rel="stylesheet" href="/assets/css/components/feed.css">
                <link rel="stylesheet" href="/assets/css/components/navbar.css">
                <link rel="stylesheet" href="/assets/css/components/navPanel.css">
                <link rel="stylesheet" href="/assets/css/components/marketplaceItem.css">
                <link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
                <link rel="stylesheet" href="/assets/css/components/eventsWidget.css">
                <link rel="stylesheet" href="/assets/css/components/createPost.css">
            ',
            'item' => $item,
            'statusOptions' => $statusOptions
        ]);
    }
    public function deleteItem($itemId)
    {
        $itemModel = new MarketplaceItem();
        $itemModel->deleteItem($itemId);
        header('Location: /marketplace/myItems');
        exit();
    }
}
