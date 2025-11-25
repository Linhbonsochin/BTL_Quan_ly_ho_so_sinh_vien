<?php
?>
</main>
<footer class="bg-light py-3 mt-auto">
    <div class="container text-muted small">
        &copy; <?php echo date('Y'); ?> Hệ thống quản lý sinh viên - BTL_17-09
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        document.addEventListener('DOMContentLoaded', function () {
            var alerts = document.querySelectorAll('.auto-dismiss');
            alerts.forEach(function (a) { setTimeout(function () { a.classList.add('fade'); a.addEventListener('transitionend', function () { a.remove(); }); a.style.opacity = 0; }, 4500); });
        });
    })();
</script>
</body>

</html>