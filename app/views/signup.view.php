<section class="page">
    <div class="form">
        <div class="logo-image__container">
            <img src="/assets/images/logo-min.svg" class="logo-image" alt="UniConnect Logo">
        </div>

        <div class="heading-container">
            <h1 class="heading">Create an Account</h1>
            <p class="sub-heading">Sign up to get started</p>
        </div>

        <form id="signupForm" class="input-field__container" method="POST">
            <!-- First Name -->
            <div>
                <div class="input-container">
                    <input type="text" id="fName" name="fName" class="input-field" placeholder="Enter your first name">
                    <label class="input-label">First Name</label>
                </div>
                <div class="error-message" id="fName-error"></div>
            </div>

            <!-- Last Name -->
            <div>
                <div class="input-container">
                    <input type="text" id="lName" name="lName" class="input-field" placeholder="Enter your last name">
                    <label class="input-label">Last Name</label>
                </div>
                <div class="error-message" id="lName-error"></div>
            </div>

            <!-- Birthday -->
            <div class="birthday-section">
                <div class="input-container">
                    <input type="date" id="birthday" name="birthday" class="input-field">
                    <label class="input-label">Select your birthday</label>
                </div>
                <div class="error-message" id="birthday-error"></div>
            </div>

            <!-- Email -->
            <div>
                <div class="input-container">
                    <input type="email" id="email" name="email" class="input-field" placeholder="Enter your email">
                    <label class="input-label">University Email Address</label>
                </div>
                <div class="error-message" id="email-error"></div>
            </div>

            <!-- Password -->
            <div>
                <div class="input-container">
                    <input type="password" id="password" name="password" class="input-field"
                        placeholder="Enter your password">
                    <label class="input-label">Password</label>
                </div>
                <div class="error-message" id="password-error"></div>
            </div>

            <!-- Confirm Password -->
            <div>
                <div class="input-container">
                    <input type="password" id="confirmPassword" name="confirmPassword" class="input-field"
                        placeholder="Confirm your password">
                    <label class="input-label">Confirm Password</label>
                </div>
                <div class="error-message" id="confirmPassword-error"></div>
                <?php if (!empty($errors['email'])): ?>
                <div class="error-message show" id="email-incorrect-error">
                    <?= htmlspecialchars($errors['email']) ?>
                </div>
                <?php endif; ?>

            </div>

            <!-- Submit -->
            <button type="submit" class="signup-button">Sign Up</button>
        </form>

        <div class="login-container">
            <p>Already have an account? <a href="/login" class="login-link">Log In</a></p>
        </div>
    </div>

</section>
<script src="/assets/js/signup.js"></script>