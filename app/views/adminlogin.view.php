<section class="page">
    <div class="form">
        <div class="logo"> <img src="/assets/images/logo-min.svg" class="logo__image"> </div>

        <div class="form__header">
            <h1 class="form__heading">UniConnect Admin</h1>
            <p class="form__subheading">Log in to continue</p>
        </div>

        <form class="form__inputs" id="adminLoginForm">

            <div>
                <div class="input">
                    <input type="email" name="email" class="input__field" placeholder=" " id="admin-email">
                    <label class="input__label" for="admin-email">Email Address</label>
                </div>
                <div class="error-message" id="admin-email-error"></div>
            </div>

            <div>
                <div class="input">
                    <input type="password" name="password" class="input__field" placeholder=" " id="admin-password">
                    <label class="input__label" for="admin-password">Password</label>
                </div>
                <div class="error-message" id="admin-password-error"></div>
                <div class="error-message" id="admin-auth-error"></div>
            </div>

            <button type="submit" class="button">Log In</button>
        </form>
    </div>
</section>
<script src="/assets/js/ajax.js"></script>
<script src="/assets/js/admin/adminlogin.js"></script>