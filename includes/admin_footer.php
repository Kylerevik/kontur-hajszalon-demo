    </div>
</main>
<?php foreach ($scripts ?? [] as $script): ?>
<script src="<?= e(asset($script, '../')) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
