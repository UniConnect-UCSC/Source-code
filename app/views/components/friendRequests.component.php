<?php
$friendRequests = [
    [
        'id' => 1,
        'firstname' => 'John',
        'lastname' => 'Doe',
        'profile_picture' => 'https://images.unsplash.com/photo-1552058544-f2b08422138a?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=999',
        'university' => 'University of Moratuwa'
    ],
    [
        'id' => 2,
        'firstname' => 'Jane',
        'lastname' => 'Smith',
        'profile_picture' => 'https://images.unsplash.com/photo-1552058544-f2b08422138a?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=999',
        'university' => 'University of Colombo'
    ],
    [
        'id' => 3,
        'firstname' => 'Alice',
        'lastname' => 'Johnson',
        'profile_picture' => 'https://images.unsplash.com/photo-1552058544-f2b08422138a?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=999',
        'university' => 'University of Peradeniya'
    ],
    [
        'id' => 4,
        'firstname' => 'Bob',
        'lastname' => 'Brown',
        'profile_picture' => 'https://images.unsplash.com/photo-1552058544-f2b08422138a?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=999',
        'university' => 'University of Kelaniya'
    ],
    [
        'id' => 5,
        'firstname' => 'Charlie',
        'lastname' => 'Davis',
        'profile_picture' => 'https://images.unsplash.com/photo-1552058544-f2b08422138a?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=999',
        'university' => 'University of Jaffna'
    ],
    [
        'id' => 6,
        'firstname' => 'Eve',
        'lastname' => 'Wilson',
        'profile_picture' => 'https://images.unsplash.com/photo-1552058544-f2b08422138a?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=999',
        'university' => 'University of Sri Jayewardenepura'
    ],
    [
        'id' => 7,
        'firstname' => 'Frank',
        'lastname' => 'Miller',
        'profile_picture' => 'https://images.unsplash.com/photo-1552058544-f2b08422138a?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=999',
        'university' => 'University of Ruhuna'
    ],
    [
        'id' => 8,
        'firstname' => 'Grace',
        'lastname' => 'Taylor',
        'profile_picture' => 'https://images.unsplash.com/photo-1552058544-f2b08422138a?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=999',
        'university' => 'University of Eastern'
    ],
    [
        'id' => 9,
        'firstname' => 'Hank',
        'lastname' => 'Anderson',
        'profile_picture' => 'https://images.unsplash.com/photo-1552058544-f2b08422138a?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=999',
        'university' => 'University of South Asia'
    ],
    [
        'id' => 10,
        'firstname' => 'Ivy',
        'lastname' => 'Thomas',
        'profile_picture' => 'https://images.unsplash.com/photo-1552058544-f2b08422138a?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=999',
        'university' => 'University of the Visual and Performing Arts'
    ],
];
?>

<div class="friend-requests-container">
    <div class="friend-requests-header">Friend Requests</div>
    <div class="friend-requests">
        <?php foreach ($friendRequests as $request): ?>
        <div class="friend-request-card">
            <a href="/profile/<?php echo $request['id']; ?>">
                <img src="<?php echo $request['profile_picture']; ?>"
                    alt="<?php echo $request['firstname'] . ' ' . $request['lastname']; ?>"
                    class="friend-request-profile-picture">
            </a>
            <div class="friend-request-info">
                <h3 class="friend-request-name"><?php echo $request['firstname'] . ' ' . $request['lastname']; ?></h3>
                <p class="friend-request-university"><?php echo $request['university']; ?></p>
                <div class="friend-request-actions">
                    <button class="accept-friend-request">Accept</button>
                    <button class="decline-friend-request">Decline</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <div>
        </div>