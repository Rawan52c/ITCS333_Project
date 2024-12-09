<?php if (!isLoggedIn()): ?>
    <section class="hero">
        <h1 class="text-center">Welcome to Our Website</h1>
        <p class="text-center">Please log in or register to access exclusive content.</p>
    </section>
<?php else: ?>
    <section class="hero">
        <h1 class="text-center">Welcome Back, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
        <p class="text-center">You are logged in and ready to explore your dashboard.</p>
    </section>
<?php endif; ?>
