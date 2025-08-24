<section class="page">
    <div class="form">
        <div class="logo">
            <img src="/assets/images/logo-min.svg" class="logo__image">
        </div>

        <div class="form__header">
            <h1 class="form__heading">UniConnect</h1>
            <p class="form__subheading">Log in to continue</p>
        </div>

        <form class="form__inputs" id="loginForm" method="POST">

            <div>
                <div class="input">
                    <input type="email" name="email" class="input__field" placeholder=" " id="email"
                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    <label class="input__label" for="email">Email Address</label>
                </div>
                <div class="error-message" id="email-error"></div>
            </div>

            <div>
                <div class="input">
                    <input type="password" name="password" class="input__field" placeholder=" " id="password"
                        value="<?= isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '' ?>">
                    <label class="input__label" for="password">Password</label>
                </div>
                <div class="error-message" id="password-error"></div>

                <?php if (!empty($errors['email'])): ?>
                <div class="error-message show" id="email-incorrect-error">
                    <?= htmlspecialchars($errors['email']) ?>
                </div>
                <?php endif; ?>

            </div>



            <button type="submit" class="button">Log In</button>
        </form>


        <div class="signup">
            <p>Don't have an account? <a href="/signup" class="signup__link">Sign Up</a></p>
        </div>
    </div>
</section>
<script src="/assets/js/login.js"></script>