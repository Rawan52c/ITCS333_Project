<?php if (!isLoggedIn()): ?>
    <section class="hero">
        <h1>Welcome to Our Website</h1>
        <p>Please log in or register to access exclusive content.</p>
    </section>
<?php else: ?>
    <section class="hero">
        <h1>Welcome Back, <?= htmlspecialchars($_SESSION['user_email']) ?>!</h1>
        <p>You are logged in and ready to explore your dashboard.</p>
    </section>
<?php endif; ?>
