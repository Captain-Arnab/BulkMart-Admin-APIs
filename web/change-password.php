<?php include('header.php'); ?>

<main class="vc-legal-page vc-help-page">
    <section class="vc-legal-hero">
        <div class="vc-legal-container">
            <span class="vc-legal-eyebrow"><i class="fa-solid fa-lock"></i> Account Security</span>
            <h1>Change <span>Password</span></h1>
            <p>Set or update the password used for Email &amp; Password login.</p>
        </div>
    </section>

    <section class="vc-legal-main">
        <div class="vc-legal-container" style="max-width:560px">
            <form id="vcChangePasswordForm" class="vc-support-panel vc-support-form">
                <div class="vc-support-panel-head">
                    <h2>Update password</h2>
                    <p>If you previously used only Mobile OTP, you can set a password here for email login.</p>
                </div>

                <label id="vcCurrentPasswordWrap">
                    Current password
                    <input type="password" name="current_password" id="vcCurrentPassword" autocomplete="current-password" placeholder="Leave blank if you never set one">
                </label>

                <label>
                    New password
                    <input type="password" name="password" id="vcNewPassword" required minlength="6" autocomplete="new-password" placeholder="At least 6 characters">
                </label>

                <label>
                    Confirm new password
                    <input type="password" name="password_confirmation" id="vcConfirmPassword" required minlength="6" autocomplete="new-password" placeholder="Re-enter new password">
                </label>

                <button type="submit" class="vc-help-cta-btn" id="vcChangePasswordBtn">
                    Save password
                    <i class="fa-solid fa-check"></i>
                </button>
            </form>
        </div>
    </section>
</main>

<?php include('footer.php'); ?>
