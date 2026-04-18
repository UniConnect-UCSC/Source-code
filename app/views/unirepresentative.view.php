<?php component("navbar"); ?>

<div class="home-layout">
<?php component("navPanel"); ?>

<div class="feed">
    <?php if ($status === 'is_rep'): ?>
        <div class="rep-container">
            <h2>You are the current University Representative</h2>
            <p>If you wish to step down from your position, please click the button below.</p>
            <form action="/unirepresentative/stepdown" method="post">
                <button type="submit" class="btn btn-danger">Step Down</button>
            </form>
        </div>
    <?php elseif ($status === 'has_rep'): ?>
        <div class="rep-container">
            <h2>A University Representative is already in place.</h2>
            <p>Currently, applications are closed as a representative is already serving. Please check back later.</p>
        </div>
    <?php elseif ($status === 'pending_request'): ?>
        <div class="rep-container">
            <h2>Your application is under review.</h2>
            <p>You have already submitted an application. Please wait for the administration to review your request.</p>
        </div>
    <?php elseif ($status === 'no_rep'): ?>
        <div class="form-container">
            <h2>Become a University Representative</h2>
            <p>If you wish to become a university representative, please fill out the form below and upload a university approved letter for representation of University : <?= htmlspecialchars($university_name ?? 'University') ?></p>
            <form action="/unirepresentative/apply" method="post" enctype="multipart/form-data" class="form">
                <div class="form__inputs">
                    <div class="input">
                        <input type="file" name="proof_pdf" id="proof_pdf" class="input__field" accept=".pdf" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit Application</button>
            </form>
        </div>
    <?php endif; ?>
</div>


<?php component("widgetPanel"); ?>
</div>